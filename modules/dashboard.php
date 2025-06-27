<?php
session_start();
include('../includes/dbconn.php');

// ✅ Fix: Check the correct session key as set in check-login.php
if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include('../includes/student-navigation.php'); ?>

<div class="container mt-4">
    <?php include('../includes/student-sidebar.php'); ?>

    <div class="main-content">
        <h1 class="text-center">Welcome to Student Dashboard</h1>

        <div class="dashboard-cards d-flex flex-wrap gap-3 justify-content-center mt-4">
            <div class="card p-3 bg-info text-white">
                <h4>Today's Attendance</h4>
                <p>Marked Present ✅</p>
            </div>
            <div class="card p-3 bg-warning text-dark">
                <h4>Weekly Mess Menu</h4>
                <p><a href="../modules/mess-menu.php" class="text-dark">View Menu</a></p>
            </div>
            <div class="card p-3 bg-success text-white">
                <h4>Leave Requests</h4>
                <p><a href="../modules/leave-request.php" class="text-white">Submit Leave</a></p>
            </div>
            <div class="card p-3 bg-danger text-white">
                <h4>Complaints</h4>
                <p><a href="../modules/complaint-box.php" class="text-white">Raise a Complaint</a></p>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/script.js"></script>
<?php include('../includes/footer.php'); ?>
</body>
</html>
