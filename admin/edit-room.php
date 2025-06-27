<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include('../includes/dbconn.php');

$room_id = $_GET['id'] ?? '';
if (empty($room_id)) {
    header("Location: room-management.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_number = mysqli_real_escape_string($conn, $_POST['room_number']);
    $room_type = mysqli_real_escape_string($conn, $_POST['room_type']);
    $capacity = mysqli_real_escape_string($conn, $_POST['capacity']);
    $fees = mysqli_real_escape_string($conn, $_POST['fees']);

    $query = "UPDATE room SET 
              room_number='$room_number',
              room_type='$room_type',
              capacity='$capacity',
              fees='$fees'
              WHERE room_id='$room_id'";

    if ($conn->query($query)) {
        header("Location: room-management.php?msg=updated");
        exit();
    }
}

// Fetch room details
$query = "SELECT * FROM room WHERE room_id='$room_id'";
$result = $conn->query($query);
$room = $result->fetch_assoc();

if (!$room) {
    header("Location: room-management.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Room | Admin Panel</title>
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
        .edit-form {
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
            <a class="nav-link" href="manage-wardens.php"><i class="fas fa-user-shield"></i> Manage Wardens</a>
            <a class="nav-link active" href="room-management.php"><i class="fas fa-bed"></i> Room Management</a>
            <a class="nav-link" href="complaints.php"><i class="fas fa-exclamation-circle"></i> Complaints</a>
            <a class="nav-link" href="payments.php"><i class="fas fa-money-bill"></i> Payments</a>
            <a class="nav-link" href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            <a class="nav-link" href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Room</h2>
            <a href="room-management.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="edit-form">
            <form method="POST">
                <div class="mb-3">
                    <label>Room Number</label>
                    <input type="text" class="form-control" name="room_number" value="<?php echo $room['room_number']; ?>" required>
                </div>
                <div class="mb-3">
                    <label>Room Type</label>
                    <select class="form-control" name="room_type" required>
                        <option value="Single" <?php echo $room['room_type'] === 'Single' ? 'selected' : ''; ?>>Single</option>
                        <option value="Double" <?php echo $room['room_type'] === 'Double' ? 'selected' : ''; ?>>Double</option>
                        <option value="Triple" <?php echo $room['room_type'] === 'Triple' ? 'selected' : ''; ?>>Triple</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Capacity</label>
                    <input type="number" class="form-control" name="capacity" value="<?php echo $room['capacity']; ?>" required>
                </div>
                <div class="mb-3">
                    <label>Fees (per semester)</label>
                    <input type="number" class="form-control" name="fees" value="<?php echo $room['fees']; ?>" required>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Update Room</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 