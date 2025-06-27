<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | HostelIMS</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    /* Your CSS styles remain the same */
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
    body {
      background: #e6f9f7;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .card {
      background: #fff;
      padding: 40px 35px;
      width: 400px;
      border-radius: 20px;
      box-shadow: 0px 8px 25px rgba(0,0,0,0.1);
    }
    .card h2 {
      text-align: center;
      color: #008080;
      margin-bottom: 25px;
    }
    .role-tabs {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
    }
    .role-tabs input[type="radio"] {
      display: none;
    }
    .role-tabs label {
      flex: 1;
      text-align: center;
      padding: 10px 0;
      border: 2px solid #008080;
      color: #008080;
      border-radius: 8px;
      cursor: pointer;
      margin: 0 5px;
      transition: all 0.3s ease;
    }
    .role-tabs input[type="radio"]:checked + label {
      background-color: #008080;
      color: white;
      font-weight: bold;
    }
    .form-group {
      margin-bottom: 15px;
      position: relative;
    }
    .form-group input {
      width: 100%;
      padding: 12px 15px;
      padding-left: 40px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
    }
    .form-group i {
      position: absolute;
      top: 50%;
      left: 12px;
      transform: translateY(-50%);
      color: #777;
    }
    .forgot {
      font-size: 13px;
      text-align: right;
      margin-top: 5px;
    }
    .forgot a {
      color: #008080;
      text-decoration: none;
    }
    .login-btn {
      background: linear-gradient(to right, #00c6a7, #00796b);
      color: white;
      border: none;
      padding: 12px;
      border-radius: 8px;
      width: 100%;
      font-size: 16px;
      cursor: pointer;
      margin-top: 15px;
      transition: background 0.3s;
    }
    .login-btn:hover {
      background: #00796b;
    }
    .error-msg {
      text-align: center;
      color: red;
      font-size: 14px;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>

  <form class="card" method="POST" action="../includes/check-login.php">
    <h2>Login to HostelIMS</h2>

    <?php if (isset($_SESSION['login_error'])): ?>
      <div class="error-msg">
        <?php 
          echo $_SESSION['login_error']; 
          unset($_SESSION['login_error']);
        ?>
      </div>
    <?php endif; ?>

    <div class="role-tabs">
      <input type="radio" name="role" id="student" value="student" checked>
      <label for="student">Student</label>

      <input type="radio" name="role" id="warden" value="warden">
      <label for="warden">Warden</label>

      <input type="radio" name="role" id="admin" value="admin">
      <label for="admin">Admin</label>
    </div>

    <div class="form-group">
      <i class="fas fa-id-card"></i>
      <input type="text" name="userid" id="userid" placeholder="Enter your ID" required>
    </div>

    <div class="form-group">
      <i class="fas fa-lock"></i>
      <input type="password" name="password" placeholder="Enter your Password" required>
    </div>

    <div class="forgot">
      <a href="#" onclick="forgotPassword()">Forgot Password?</a>
    </div>

    <button class="login-btn" type="submit">Login</button>
  </form>

  <script>
    function forgotPassword() {
      const role = document.querySelector('input[name="role"]:checked').value;
      if (role === "student") {
        window.location.href = "forgot-student.php";
      } else if (role === "warden") {
        window.location.href = "forgot-warden.php";
      } else {
        window.location.href = "forgot-admin.php";
      }
    }
  </script>

</body>
</html>
