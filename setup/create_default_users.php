<?php
include('../includes/dbconn.php');

// Create admin user
$admin_username = "admin";
$admin_password = password_hash("admin123", PASSWORD_DEFAULT);
$admin_role = "admin";

$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $admin_username, $admin_password, $admin_role);
$stmt->execute();

// Create warden user
$warden_username = "warden";
$warden_password = password_hash("warden123", PASSWORD_DEFAULT);
$warden_role = "warden";

$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $warden_username, $warden_password, $warden_role);
$stmt->execute();

// Create a default student
$student_id = "STU001";
$student_password = password_hash("student123", PASSWORD_DEFAULT);
$student_role = "student";

// Insert into users table
$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $student_id, $student_password, $student_role);
$stmt->execute();

// Insert student details
$student_name = "John Doe";
$student_email = "john.doe@example.com";
$student_phone = "1234567890";
$student_department = "Computer Science";
$student_batch = "2023";
$student_dob = "2000-01-01";

$stmt = $conn->prepare("INSERT INTO student (student_id, name, email, phone, department, batch, dob) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $student_id, $student_name, $student_email, $student_phone, $student_department, $student_batch, $student_dob);
$stmt->execute();

echo "Default users created successfully!\n";
echo "\nLogin Credentials:\n";
echo "Admin - Username: admin, Password: admin123\n";
echo "Warden - Username: warden, Password: warden123\n";
echo "Student - ID: STU001, Password: student123\n";
?> 