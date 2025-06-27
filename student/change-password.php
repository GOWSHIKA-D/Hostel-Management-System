<?php
session_start();
include("../includes/dbconn.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current = mysqli_real_escape_string($conn, $_POST['current_password']);
    $new = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Check current password
    $check = mysqli_query($conn, "SELECT * FROM student WHERE student_id='$student_id' AND password='$current'");
    if (mysqli_num_rows($check) === 1) {
        if ($new === $confirm) {
            $update = mysqli_query($conn, "UPDATE student SET password='$new' WHERE student_id='$student_id'");
            if ($update) {
                $message = "<div style='color: green;'>Password updated successfully.</div>";
            } else {
                $message = "<div style='color: red;'>Error updating password.</div>";
            }
        } else {
            $message = "<div style='color: red;'>New passwords do not match.</div>";
        }
    } else {
        $message = "<div style='color: red;'>Current password is incorrect.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password - Student</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include("../includes/student-navigation.php"); ?>
    <div class="container">
        <h2>Change Password</h2>
        <?php echo $message; ?>
        <form method="POST" action="">
            <label>Current Password</label><br>
            <input type="password" name="current_password" required><br><br>

            <label>New Password</label><br>
            <input type="password" name="new_password" required><br><br>

            <label>Confirm New Password</label><br>
            <input type="password" name="confirm_password" required><br><br>

            <button type="submit">Update Password</button>
        </form>
    </div>
    <?php include("../includes/footer.php"); ?>
</body>
</html>
