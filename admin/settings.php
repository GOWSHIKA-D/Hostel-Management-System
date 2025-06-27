<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include('../includes/dbconn.php');

$admin_id = $_SESSION['admin_id'];
$success_message = '';
$error_message = '';

// Fetch admin details
$query = "SELECT admin_id, username, fullname, email, mobile FROM admin WHERE admin_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Handle profile update
if (isset($_POST['update_profile'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];

    $update_query = "UPDATE admin SET fullname = ?, email = ?, mobile = ? WHERE admin_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ssss", $fullname, $email, $mobile, $admin_id);

    if ($update_stmt->execute()) {
        $success_message = "Profile updated successfully!";
        // Refresh admin details
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
    } else {
        $error_message = "Error updating profile: " . $conn->error;
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // First verify current password
    $verify_query = "SELECT password FROM admin WHERE admin_id = ?";
    $verify_stmt = $conn->prepare($verify_query);
    $verify_stmt->bind_param("s", $admin_id);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();
    $admin_data = $verify_result->fetch_assoc();

    if (password_verify($current_password, $admin_data['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $password_query = "UPDATE admin SET password = ? WHERE admin_id = ?";
            $password_stmt = $conn->prepare($password_query);
            $password_stmt->bind_param("ss", $hashed_password, $admin_id);

            if ($password_stmt->execute()) {
                $success_message = "Password changed successfully!";
            } else {
                $error_message = "Error changing password: " . $conn->error;
            }
        } else {
            $error_message = "New passwords do not match!";
        }
    } else {
        $error_message = "Current password is incorrect!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings | Admin Panel</title>
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
        .card {
            border: none;
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
            <a class="nav-link" href="room-management.php"><i class="fas fa-bed"></i> Room Management</a>
            <a class="nav-link" href="complaints.php"><i class="fas fa-exclamation-circle"></i> Complaints</a>
            <a class="nav-link" href="payments.php"><i class="fas fa-money-bill"></i> Payments</a>
            <a class="nav-link active" href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            <a class="nav-link" href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Profile Settings -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">Profile Settings</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label>Admin ID</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($admin['admin_id'] ?? ''); ?>" disabled>
                                </div>
                                <div class="mb-3">
                                    <label>Username</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($admin['username'] ?? ''); ?>" disabled>
                                </div>
                                <div class="mb-3">
                                    <label>Full Name</label>
                                    <input type="text" class="form-control" name="fullname" value="<?php echo htmlspecialchars($admin['fullname'] ?? ''); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($admin['email'] ?? ''); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label>Mobile</label>
                                    <input type="text" class="form-control" name="mobile" value="<?php echo htmlspecialchars($admin['mobile'] ?? ''); ?>" required>
                                </div>
                                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">Change Password</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label>Current Password</label>
                                    <input type="password" class="form-control" name="current_password" required>
                                </div>
                                <div class="mb-3">
                                    <label>New Password</label>
                                    <input type="password" class="form-control" name="new_password" required>
                                </div>
                                <div class="mb-3">
                                    <label>Confirm New Password</label>
                                    <input type="password" class="form-control" name="confirm_password" required>
                                </div>
                                <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 