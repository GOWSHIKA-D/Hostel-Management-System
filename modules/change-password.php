<?php
session_start();
include('../includes/dbconn.php');
include('../includes/student-navigation.php');
?>

<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background-color: #f2f6fc;
    }

    .container-flex {
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        width: 250px;
        background-color: #1c1c1c;
    }

    .main-content {
        flex: 1;
        padding: 40px;
        background: #f2f6fc;
    }

    .card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        max-width: 600px;
        margin: auto;
        padding: 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
    }

    .btn-primary {
        background-color: #00bcd4;
        border: none;
        padding: 10px 25px;
        color: white;
        font-weight: bold;
        border-radius: 6px;
        cursor: pointer;
    }

    .btn-primary:hover {
        background-color: #0097a7;
    }
</style>

<div class="container-flex">
    <div class="sidebar">
        <?php include('../includes/student-sidebar.php'); ?>
    </div>

    <div class="main-content">
        <div class="card">
            <h3 style="text-align: center; font-weight: 600; margin-bottom: 30px;">🔒 Change Password</h3>
            <form action="" method="post">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <div class="text-center">
                    <button type="submit" name="update_password" class="btn btn-primary">Update Password</button>
                </div>
            </form>

            <?php
            if (isset($_POST['update_password'])) {
                $student_id = $_SESSION['student_id'];
                $current = mysqli_real_escape_string($conn, $_POST['current_password']);
                $new = mysqli_real_escape_string($conn, $_POST['new_password']);
                $confirm = mysqli_real_escape_string($conn, $_POST['confirm_password']);

                if ($new !== $confirm) {
                    echo "<p style='color: red; text-align: center; margin-top: 20px;'>❌ New passwords do not match.</p>";
                } else {
                    $query = "SELECT password FROM student WHERE student_id='$student_id'";
                    $result = mysqli_query($conn, $query);
                    $data = mysqli_fetch_assoc($result);

                    if ($data['password'] === $current) {
                        $update = "UPDATE student SET password='$new' WHERE student_id='$student_id'";
                        if (mysqli_query($conn, $update)) {
                            echo "<p style='color: green; text-align: center; margin-top: 20px;'>✅ Password updated successfully.</p>";
                        } else {
                            echo "<p style='color: red; text-align: center; margin-top: 20px;'>❌ Failed to update password.</p>";
                        }
                    } else {
                        echo "<p style='color: red; text-align: center; margin-top: 20px;'>❌ Current password is incorrect.</p>";
                    }
                }
            }
            ?>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
