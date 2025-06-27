<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include('../includes/dbconn.php');

$student_id = $_GET['id'] ?? '';
if (empty($student_id)) {
    header("Location: manage-students.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $room_id = mysqli_real_escape_string($conn, $_POST['room_id']);

    $query = "UPDATE student SET 
              name='$name', 
              email='$email', 
              mobile='$mobile',
              room_id=" . ($room_id ? "'$room_id'" : "NULL") . "
              WHERE student_id='$student_id'";

    if ($conn->query($query)) {
        header("Location: manage-students.php?msg=updated");
        exit();
    }
}

// Fetch student details
$query = "SELECT s.*, r.room_number 
          FROM student s 
          LEFT JOIN room r ON s.room_id = r.room_id 
          WHERE s.student_id='$student_id'";
$result = $conn->query($query);
$student = $result->fetch_assoc();

if (!$student) {
    header("Location: manage-students.php");
    exit();
}

// Fetch available rooms
$roomsQuery = "SELECT room_id, room_number FROM room WHERE availability='Available' OR room_id=" . ($student['room_id'] ?? 'NULL');
$rooms = $conn->query($roomsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student | Admin Panel</title>
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
            <a class="nav-link active" href="manage-students.php"><i class="fas fa-users"></i> Manage Students</a>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Student</h2>
            <a href="manage-students.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="edit-form">
            <form method="POST">
                <div class="mb-3">
                    <label>Student ID</label>
                    <input type="text" class="form-control" value="<?php echo $student['student_id']; ?>" readonly>
                </div>
                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" value="<?php echo $student['name']; ?>" required>
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo $student['email']; ?>" required>
                </div>
                <div class="mb-3">
                    <label>Mobile</label>
                    <input type="text" class="form-control" name="mobile" value="<?php echo $student['mobile']; ?>" required>
                </div>
                <div class="mb-3">
                    <label>Assign Room</label>
                    <select class="form-control" name="room_id">
                        <option value="">-- Select Room --</option>
                        <?php while ($room = $rooms->fetch_assoc()): ?>
                            <option value="<?php echo $room['room_id']; ?>" 
                                    <?php echo ($student['room_id'] == $room['room_id']) ? 'selected' : ''; ?>>
                                <?php echo $room['room_number']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Update Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 