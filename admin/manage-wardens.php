<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include('../includes/dbconn.php');

// Handle warden deletion
if (isset($_GET['delete'])) {
    $warden_id = $_GET['delete'];
    $delete_query = "DELETE FROM warden WHERE warden_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("s", $warden_id);
    
    if ($stmt->execute()) {
        header("Location: manage-wardens.php?msg=deleted");
    } else {
        header("Location: manage-wardens.php?error=delete_failed");
    }
    exit();
}

// Fetch all wardens with error handling
$query = "SELECT * FROM warden ORDER BY warden_id";
$result = $conn->query($query);

if (!$result) {
    $error_message = "Error fetching wardens: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Wardens | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
        }
        .sidebar {
            height: 100vh;
            background: #1a237e;
            color: white;
            position: fixed;
            width: 250px;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 4px 0;
        }
        .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.1);
        }
        .nav-link i {
            width: 25px;
        }
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar p-3">
        <h4 class="text-center mb-4">Admin Panel</h4>
        <div class="nav flex-column">
            <a class="nav-link" href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a class="nav-link" href="manage-students.php"><i class="fas fa-users"></i> Manage Students</a>
            <a class="nav-link active" href="manage-wardens.php"><i class="fas fa-user-shield"></i> Manage Wardens</a>
            <a class="nav-link" href="room-management.php"><i class="fas fa-bed"></i> Room Management</a>
            <a class="nav-link" href="complaints.php"><i class="fas fa-exclamation-circle"></i> Complaints</a>
            <a class="nav-link" href="payments.php"><i class="fas fa-money-bill"></i> Payments</a>
            <a class="nav-link" href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            <a class="nav-link" href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Wardens</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWardenModal">
                <i class="fas fa-plus"></i> Add New Warden
            </button>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                    if ($_GET['msg'] === 'added') echo "Warden added successfully!";
                    if ($_GET['msg'] === 'deleted') echo "Warden deleted successfully!";
                    if ($_GET['msg'] === 'updated') echo "Warden updated successfully!";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                    if ($_GET['error'] === 'exists') echo "Warden ID already exists!";
                    if ($_GET['error'] === 'failed') echo "Operation failed. Please try again.";
                    if ($_GET['error'] === 'delete_failed') echo "Failed to delete warden. Please try again.";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Warden ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($result && $result->num_rows > 0):
                        while ($warden = $result->fetch_assoc()): 
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($warden['warden_id'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($warden['name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($warden['email'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($warden['mobile'] ?? ''); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-info me-2" onclick="editWarden('<?php echo htmlspecialchars($warden['warden_id']); ?>')">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteWarden('<?php echo htmlspecialchars($warden['warden_id']); ?>')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php 
                        endwhile;
                    else: 
                    ?>
                        <tr>
                            <td colspan="5" class="text-center">No wardens found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Warden Modal -->
<div class="modal fade" id="addWardenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Warden</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="add-warden.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Warden ID</label>
                        <input type="text" class="form-control" name="warden_id" required>
                    </div>
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label>Mobile</label>
                        <input type="text" class="form-control" name="mobile" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Warden</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function deleteWarden(wardenId) {
    if (confirm('Are you sure you want to delete this warden?')) {
        window.location.href = `manage-wardens.php?delete=${wardenId}`;
    }
}

function editWarden(wardenId) {
    window.location.href = `edit-warden.php?id=${wardenId}`;
}
</script>
</body>
</html> 