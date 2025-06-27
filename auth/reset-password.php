<?php
include("../includes/dbconn.php");

$role = $_GET['role'] ?? '';
$email = $_GET['email'] ?? '';
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];
    $email = $_POST['email'];
    $otp = $_POST['otp'];
    $newpass = $_POST['new_password'];
    $confirmpass = $_POST['confirm_password'];

    if ($otp !== '123456') {
        $message = "Invalid OTP. Please try again.";
    } elseif ($newpass !== $confirmpass) {
        $message = "Passwords do not match.";
    } else {
        // Set table and email field based on role
        if ($role === 'student') {
            $table = "student";
            $email_field = "email";
            $id_field = "student_id";
        } elseif ($role === 'admin') {
            $table = "admin";
            $email_field = "email";
            $id_field = "admin_id";
        } elseif ($role === 'warden') {
            $table = "warden";
            $email_field = "email";
            $id_field = "warden_id";
        } else {
            die("Invalid role");
        }

        $update = "UPDATE $table SET password='$newpass' WHERE $email_field='$email'";
        if (mysqli_query($conn, $update)) {
            $message = "Password updated successfully. <a href='login.php'>Login now</a>";
        } else {
            $message = "Failed to update password.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Reset Password</title>
  <style>
    body {
      background: #e0f7fa;
      font-family: 'Segoe UI', sans-serif;
    }
    .box {
      max-width: 450px;
      margin: 80px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    input[type=text], input[type=password] {
      width: 90%;
      padding: 10px;
      margin: 10px 0;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    .btn {
      background: #28a745;
      color: white;
      padding: 10px 20px;
      border: none;
      margin-top: 10px;
      border-radius: 5px;
    }
    .message {
      margin-top: 15px;
      color: red;
    }
  </style>
</head>
<body>
  <div class="box">
    <h2>Reset Password</h2>
    <?php if ($message): ?>
      <p class="message"><?= $message ?></p>
    <?php endif; ?>
    <form method="POST">
      <input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>">
      <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">

      <input type="text" name="otp" placeholder="Enter OTP (use 123456)" required>
      <input type="password" name="new_password" placeholder="New Password" required>
      <input type="password" name="confirm_password" placeholder="Confirm Password" required>

      <button type="submit" class="btn">Reset Password</button>
    </form>
  </div>
</body>
</html>
