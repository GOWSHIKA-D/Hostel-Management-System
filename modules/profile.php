<?php
session_start();
include('../includes/dbconn.php');
include('../includes/student-navigation.php');
include('../includes/student-sidebar.php');

$student_id = $_SESSION['student_id'];
$query = "SELECT * FROM student WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
?>

<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background-color: #f5f7fa;
    }

    .main-wrapper {
        display: flex;
    }

    .sidebar {
        width: 250px;
        background-color: #1e1e2f;
        color: white;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        padding-top: 60px;
        z-index: 1;
    }

    .main-content {
        margin-left: 250px;
        padding: 40px 20px;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }

    .profile-card {
        background: white;
        border-radius: 10px;
        padding: 30px;
        max-width: 700px;
        width: 100%;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .profile-img {
        width: 150px;
        height: 150px;
        border: 4px solid #1e90ff;
        border-radius: 50%;
        object-fit: cover;
        display: block;
        margin: 20px auto;
    }

    form.upload-form {
        text-align: center;
        margin-bottom: 20px;
    }

    form.upload-form input[type="file"] {
        margin-bottom: 10px;
    }

    .btn {
        padding: 8px 16px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn:disabled {
        background-color: #b8d4fc;
        cursor: not-allowed;
    }

    h4, p {
        text-align: center;
    }

    .details p {
        text-align: left;
        padding: 3px 0;
        line-height: 1.6;
    }

    hr {
        margin: 20px 0;
    }
</style>

<div class="main-wrapper">
    <div class="sidebar">
        <?php include('../includes/student-sidebar.php'); ?>
    </div>

    <div class="main-content">
        <div class="profile-card">
            <form class="upload-form" action="update-profile-pic.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="profile_pic" id="profile_pic" accept="image/*" required />
                <br>
                <button type="submit" name="upload" id="uploadBtn" class="btn" disabled>Upload New Profile Picture</button>
            </form>

            <?php if (!empty($row['profile_picture'])): ?>
                <img src="../assets/uploads/<?php echo $row['profile_picture']; ?>" alt="Profile Picture" class="profile-img">
            <?php else: ?>
                <img src="../assets/img/default.jpg" alt="Default" class="profile-img">
            <?php endif; ?>

            <h4><?php echo htmlspecialchars($row['name'] ?? ''); ?></h4>
            <p><strong><?php echo htmlspecialchars($row['student_id'] ?? ''); ?></strong> | Batch: <?php echo htmlspecialchars($row['batch'] ?? ''); ?></p>
            <hr>
            <div class="details">
                <p><strong>Gender:</strong> <?php echo htmlspecialchars($row['gender'] ?? 'Not specified'); ?></p>
                <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($row['dob'] ?? 'Not specified'); ?></p>
                <p><strong>Mobile:</strong> <?php echo htmlspecialchars($row['phone'] ?? 'Not specified'); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email'] ?? 'Not specified'); ?></p>
                <p><strong>Languages Known:</strong> <?php echo htmlspecialchars($row['languages_known'] ?? 'Not specified'); ?></p>
                <p><strong>Blood Group:</strong> <?php echo htmlspecialchars($row['blood_group'] ?? 'Not specified'); ?></p>
                <p><strong>Aadhar Number:</strong> <?php echo htmlspecialchars($row['aadhar_number'] ?? 'Not specified'); ?></p>
                <p><strong>Communication Address:</strong> <?php echo htmlspecialchars($row['communication_address'] ?? 'Not specified'); ?></p>
                <p><strong>Permanent Address:</strong> <?php echo htmlspecialchars($row['permanent_address'] ?? 'Not specified'); ?></p>
                <p><strong>Room Number:</strong> <?php echo htmlspecialchars($row['room_number'] ?? 'Not specified'); ?></p>
            </div>
        </div>
    </div>
</div>

<script>
// Disable Upload button until file is selected
document.getElementById('profile_pic').addEventListener('change', function () {
    const uploadBtn = document.getElementById('uploadBtn');
    uploadBtn.disabled = !this.value;
});
</script>

<?php include('../includes/footer.php'); ?>
