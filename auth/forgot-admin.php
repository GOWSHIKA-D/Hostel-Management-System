<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    header("Location: reset-password.php?role=student&email=" . urlencode($email));
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Forgot Password - Admin</title>
  <style>
    body {
      background: #f0f2f5;
      font-family: 'Segoe UI', sans-serif;
    }
    .box {
      max-width: 400px;
      margin: 100px auto;
      background: #fff;
      padding: 30px;
      text-align: center;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    input[type=email] {
      width: 90%;
      padding: 10px;
      margin-top: 15px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    .btn {
      margin-top: 20px;
      background: #17a2b8;
      border: none;
      color: white;
      padding: 10px 20px;
      border-radius: 5px;
    }
  </style>
</head>
<body>
  <div class="box">
    <h2>Forgot Admin Password</h2>
    <form method="post">
      <p>Enter your registered email address</p>
      <input type="email" name="email" placeholder="Enter Email Address" required>
      <br>
      <button type="submit" class="btn">Next</button>
    </form>
  </div>
</body>
</html>
