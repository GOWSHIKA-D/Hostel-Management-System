<?php
session_start();
include('../includes/dbconn.php');

if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$query = "SELECT * FROM student WHERE student_id = '$student_id'";
$result = mysqli_query($conn, $query);
$student = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #f3f9ff, #e5f0ff);
            font-family: 'Segoe UI', sans-serif;
        }

        .sidebar {
            background-color: #1f1f2e;
            min-height: 100vh;
            color: #fff;
        }

        .sidebar a {
            color: #fff;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            border-bottom: 1px solid #333;
        }

        .sidebar a:hover {
            background-color: #34344a;
        }

        .dashboard-box {
            border-radius: 10px;
            padding: 20px;
            color: white;
            text-align: center;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .name-box        { background-color: #5a4fcf; }
        .batch-box       { background-color: #00c2a8; }
        .dept-box        { background-color: #f9a825; }
        .mentor-box      { background-color: #d32f2f; }
        .family-box      { background-color: #2196f3; }
        .dob-box         { background-color: #757575; }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar p-3">
        <h4 class="text-center mb-4">Dashboard</h4>
        <a href="dashboard.php">Dashboard</a>
        <a href="../modules/profile.php">👤 My Profile</a>
        <a href="../modules/edit-profile.php">✏️ Edit Profile</a>
        <a href="../modules/change-password.php">🔑 Change Password</a>
        <a href="../modules/room-details.php">🛏️ Room Details</a>
        <a href="../modules/attendance.php">📅 Attendance</a>
        <a href="../student/leave_request.php">📝 Apply Leave</a>
        <a href="../modules/complaint-box.php">📢 Complaint Box</a>
        <a href="../modules/visitor-log.php">🧾 Visitor Log</a>
        <a href="../modules/payments.php">💳 Payments</a>
        <a href="../modules/mess-menu.php">🍽️ Mess Menu</a>
        <a href="../modules/notice-board.php">📌 Notice Board</a>
        <a href="../auth/logout.php">🚪 Logout</a>
    </div>

    <!-- Main content -->
    <div class="container-fluid p-4">
        <h2 class="mb-4">Dashboard <small class="text-muted">(Welcome <?php echo $student['student_id']; ?>)</small></h2>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="dashboard-box name-box">
                    👤<br>Name<br><?php echo strtoupper($student['name']); ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-box batch-box">
                    🎓<br>Batch<br><?php echo $student['batch']; ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-box dept-box">
                    🏢<br>Department<br><?php echo $student['department']; ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-box mentor-box">
                    🧑‍🏫<br>Mentor Name<br><?php echo $student['mentor_name']; ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-box family-box">
                    👨‍👩‍👧‍👦<br>Family Members<br><?php echo !empty($student['family_members']) ? $student['family_members'] : '4'; ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-box dob-box">
                    🎂<br>DOB<br><?php echo date('d - m - Y', strtotime($student['dob'])); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/script.js"></script>
<?php include('../includes/footer.php'); ?>
</body>
</html>
