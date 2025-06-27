<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include('../includes/dbconn.php');

// Handle status update
if (isset($_GET['id']) && isset($_GET['status'])) {
    $complaint_id = $_GET['id'];
    $status = $_GET['status'];
    $conn->query("UPDATE complaints SET status='$status' WHERE id='$complaint_id'");
    header("Location: complaints.php?msg=updated");
    exit();
}

// Fetch all complaints with student details
$query = "SELECT c.id, c.student_id, c.category, c.description, c.status, c.created_at, 
          s.name as student_name, s.room_id, r.room_number 
          FROM complaints c
          LEFT JOIN student s ON c.student_id = s.student_id
          LEFT JOIN room r ON s.room_id = r.room_id
          ORDER BY c.id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complaints | Admin Panel</title>
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
        .complaint-status {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .status-pending { background: #fff3e0; color: #ef6c00; }
        .status-inprogress { background: #e3f2fd; color: #1565c0; }
        .status-resolved { background: #e8f5e9; color: #2e7d32; }
        .status-rejected { background: #ffebee; color: #c62828; }
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
            <a class="nav-link" href="manage-wardens.php"><i class="fas fa-user-shield"></i> Manage Wardens</a>
            <a class="nav-link" href="room-management.php"><i class="fas fa-bed"></i> Room Management</a>
            <a class="nav-link active" href="complaints.php"><i class="fas fa-exclamation-circle"></i> Complaints</a>
            <a class="nav-link" href="payments.php"><i class="fas fa-money-bill"></i> Payments</a>
            <a class="nav-link" href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            <a class="nav-link" href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2 class="mb-4">Complaints Management</h2>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Complaint status updated successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Student</th>
                        <th>Room</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($complaint = $result->fetch_assoc()): 
                        $status_class = 'status-' . strtolower($complaint['status']);
                    ?>
                        <tr>
                            <td><?php echo isset($complaint['created_at']) ? date('d M Y', strtotime($complaint['created_at'])) : date('d M Y'); ?></td>
                            <td><?php echo $complaint['student_name']; ?></td>
                            <td><?php echo $complaint['room_number'] ?? 'Not Assigned'; ?></td>
                            <td><?php echo $complaint['category']; ?></td>
                            <td><?php echo $complaint['description']; ?></td>
                            <td>
                                <span class="complaint-status <?php echo $status_class; ?>">
                                    <?php echo $complaint['status']; ?>
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        Update Status
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="?id=<?php echo $complaint['id']; ?>&status=Pending">Mark as Pending</a></li>
                                        <li><a class="dropdown-item" href="?id=<?php echo $complaint['id']; ?>&status=In Progress">Mark as In Progress</a></li>
                                        <li><a class="dropdown-item" href="?id=<?php echo $complaint['id']; ?>&status=Resolved">Mark as Resolved</a></li>
                                        <li><a class="dropdown-item" href="?id=<?php echo $complaint['id']; ?>&status=Rejected">Mark as Rejected</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 