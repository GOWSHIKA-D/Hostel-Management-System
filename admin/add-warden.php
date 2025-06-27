<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include('../includes/dbconn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $warden_id = $_POST['warden_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if warden ID already exists
    $check_query = "SELECT warden_id FROM warden WHERE warden_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $warden_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: manage-wardens.php?error=exists");
        exit();
    }

    // Insert new warden
    $query = "INSERT INTO warden (warden_id, name, email, mobile, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $warden_id, $name, $email, $mobile, $password);

    if ($stmt->execute()) {
        header("Location: manage-wardens.php?msg=added");
    } else {
        header("Location: manage-wardens.php?error=failed");
    }
    exit();
}

header("Location: manage-wardens.php");
exit();
?> 