<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include('../includes/dbconn.php');

// Get warden details
$warden_id = $_GET['id'] ?? '';
$warden = null;
$error = null;

if ($warden_id) {
    $query = "SELECT warden_id, name, email, mobile FROM warden WHERE warden_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $warden_id);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $warden = $result->fetch_assoc();
    } else {
        $error = "Error fetching warden details: " . $conn->error;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $warden_id = $_POST['warden_id'];
    
    // Update password only if provided
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query = "UPDATE warden SET name = ?, email = ?, mobile = ?, password = ? WHERE warden_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $name, $email, $mobile, $password, $warden_id);
    } else {
        $query = "UPDATE warden SET name = ?, email = ?, mobile = ? WHERE warden_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $name, $email, $mobile, $warden_id);
    }

    if ($stmt->execute()) {
        header("Location: manage-wardens.php?msg=updated");
        exit();
    } else {
        $error = "Error updating warden: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Warden | Admin Panel</title>
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
        .card {
            border: none;
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
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">Edit Warden</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>

                            <?php if ($warden): ?>
                                <form action="edit-warden.php" method="POST">
                                    <input type="hidden" name="warden_id" value="<?php echo htmlspecialchars($warden['warden_id']); ?>">
                                    
                                    <div class="mb-3">
                                        <label>Warden ID</label>
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($warden['warden_id']); ?>" disabled>
                                    </div>

                                    <div class="mb-3">
                                        <label>Name</label>
                                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($warden['name']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label>Email</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($warden['email']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label>Mobile</label>
                                        <input type="text" class="form-control" name="mobile" value="<?php echo htmlspecialchars($warden['mobile']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label>New Password (leave blank to keep current password)</label>
                                        <input type="password" class="form-control" name="password">
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <a href="manage-wardens.php" class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Update Warden</button>
                                    </div>
                                </form>
                            <?php else: ?>
                                <div class="alert alert-danger">
                                    Warden not found. <a href="manage-wardens.php">Return to warden list</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 