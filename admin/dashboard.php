<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include('../includes/dbconn.php');

// Get total students
$studentQuery = "SELECT COUNT(*) AS total_students FROM student";
$studentResult = $conn->query($studentQuery);
$totalStudents = $studentResult->fetch_assoc()['total_students'];

// Get total rooms
$roomQuery = "SELECT COUNT(*) AS total_rooms FROM room";
$roomResult = $conn->query($roomQuery);
$totalRooms = $roomResult->fetch_assoc()['total_rooms'];

// Get total wardens
$wardenQuery = "SELECT COUNT(*) AS total_wardens FROM warden";
$wardenResult = $conn->query($wardenQuery);
$totalWardens = $wardenResult->fetch_assoc()['total_wardens'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | HostelIMS</title>
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
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stat-card i {
            font-size: 2.5rem;
            color: #1a237e;
        }
        .stat-card h3 {
            font-size: 2rem;
            margin: 10px 0;
            color: #1a237e;
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
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar p-3">
        <h4 class="text-center mb-4">Admin Panel</h4>
        <div class="nav flex-column">
            <a class="nav-link active" href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a class="nav-link" href="manage-students.php"><i class="fas fa-users"></i> Manage Students</a>
            <a class="nav-link" href="manage-wardens.php"><i class="fas fa-user-shield"></i> Manage Wardens</a>
            <a class="nav-link" href="room-management.php"><i class="fas fa-bed"></i> Room Management</a>
            <a class="nav-link" href="complaints.php"><i class="fas fa-exclamation-circle"></i> Complaints</a>
            <a class="nav-link" href="payments.php"><i class="fas fa-money-bill"></i> Payments</a>
            <a class="nav-link" href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            <a class="nav-link" href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2 class="mb-4">Dashboard Overview</h2>
        
        <div class="row">
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <i class="fas fa-users mb-3"></i>
                    <h3><?php echo $totalStudents; ?></h3>
                    <p class="text-muted">Total Students</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <i class="fas fa-bed mb-3"></i>
                    <h3><?php echo $totalRooms; ?></h3>
                    <p class="text-muted">Total Rooms</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <i class="fas fa-user-shield mb-3"></i>
                    <h3><?php echo $totalWardens; ?></h3>
                    <p class="text-muted">Total Wardens</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
