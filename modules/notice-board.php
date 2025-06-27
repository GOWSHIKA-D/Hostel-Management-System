<?php
include('../includes/dbconn.php');
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notice Board</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { font-family: Arial, sans-serif; }
        .notice-container { margin-left: 260px; padding: 20px; }
        .notice { background: #f5f5f5; padding: 15px; margin-bottom: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .notice h3 { margin: 0 0 10px; color: #333; }
        .notice p { margin: 0; color: #555; }
        .notice small { display: block; margin-top: 10px; color: #888; }
    </style>
</head>
<body>

<?php include('../includes/student-sidebar.php'); ?>

<div class="notice-container">
    <h2>📌 Notice Board</h2>

    <?php
    $query = mysqli_query($conn, "SELECT * FROM notices ORDER BY posted_on DESC");
    if (mysqli_num_rows($query) > 0) {
        while ($notice = mysqli_fetch_assoc($query)) {
            echo "<div class='notice'>
                    <h3>" . htmlspecialchars($notice['title']) . "</h3>
                    <p>" . nl2br(htmlspecialchars($notice['content'])) . "</p>
                    <small>Posted by: " . htmlspecialchars($notice['posted_by']) . " on " . date('d M Y, H:i', strtotime($notice['posted_on'])) . "</small>
                </div>";
        }
    } else {
        echo "<p>No notices available at the moment. Please check back later!</p>";
    }
    ?>
</div>

</body>
</html>
