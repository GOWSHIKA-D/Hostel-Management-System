<?php
session_start();
include('../includes/dbconn.php');

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$student_id = $_SESSION['student_id'];
$query = mysqli_query($conn, "SELECT * FROM student WHERE student_id='$student_id'");

if (!$query) {
    die('Query failed: ' . mysqli_error($conn));
}

$student = mysqli_fetch_assoc($query);

if (!$student) {
    die('No student record found.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $languages_known = mysqli_real_escape_string($conn, $_POST['languages_known']);
    $comm_address = mysqli_real_escape_string($conn, $_POST['comm_address']);
    $perm_address = mysqli_real_escape_string($conn, $_POST['perm_address']);

    $update = mysqli_query($conn, "UPDATE student SET 
        name='$name', 
        mobile='$mobile', 
        email='$email', 
        languages_known='$languages_known', 
        comm_address='$comm_address', 
        perm_address='$perm_address' 
        WHERE student_id='$student_id'");

    if ($update) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='edit-profile.php';</script>";
    } else {
        echo "<script>alert('Update failed. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f0f2f5;">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Edit Profile</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($student['name'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mobile</label>
                            <input type="text" name="mobile" class="form-control" value="<?php echo htmlspecialchars($student['mobile'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student['email'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Languages Known</label>
                            <input type="text" name="languages_known" class="form-control" value="<?php echo htmlspecialchars($student['languages_known'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Communication Address</label>
                            <textarea name="comm_address" class="form-control" rows="2"><?php echo htmlspecialchars($student['comm_address'] ?? ''); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Permanent Address</label>
                            <textarea name="perm_address" class="form-control" rows="2"><?php echo htmlspecialchars($student['perm_address'] ?? ''); ?></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-muted text-center">
                    <a href="../student/dashboard.php" class="btn btn-link">← Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include('../includes/footer.php'); ?>
</body>
</html>
