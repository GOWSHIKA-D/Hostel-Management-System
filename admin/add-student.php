<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include('../includes/dbconn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = !empty($_POST['mobile']) ? mysqli_real_escape_string($conn, $_POST['mobile']) : NULL;
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Check if student ID already exists
    $check = $conn->query("SELECT student_id FROM student WHERE student_id='$student_id'");
    if ($check->num_rows > 0) {
        header("Location: manage-students.php?error=exists");
        exit();
    }

    // Insert new student
    $query = "INSERT INTO student (student_id, name, email, mobile, password) 
              VALUES ('$student_id', '$name', '$email', " . ($mobile ? "'$mobile'" : "NULL") . ", '$password')";
    
    if ($conn->query($query)) {
        header("Location: manage-students.php?msg=added");
    } else {
        header("Location: manage-students.php?error=failed");
    }
} else {
    header("Location: manage-students.php");
}
exit();
?> 