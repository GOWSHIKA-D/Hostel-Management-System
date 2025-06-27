<?php
session_start();
include('../includes/dbconn.php');

if (!isset($_SESSION['student_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

if (isset($_POST['upload']) && isset($_FILES['profile_pic'])) {
    $student_id = $_SESSION['student_id'];
    $file = $_FILES['profile_pic'];
    
    // Check for errors
    if ($file['error'] === 0) {
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Allowed extensions
        $allowed = ['jpg', 'jpeg', 'png'];
        
        if (in_array($file_ext, $allowed)) {
            if ($file_size <= 5000000) { // 5MB max
                // Create upload directory if it doesn't exist
                $upload_dir = '../assets/uploads/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Generate unique filename
                $new_file_name = $student_id . '_' . time() . '.' . $file_ext;
                $destination = $upload_dir . $new_file_name;
                
                if (move_uploaded_file($file_tmp, $destination)) {
                    // Update database
                    $stmt = $conn->prepare("UPDATE student SET profile_picture = ? WHERE student_id = ?");
                    $stmt->bind_param("ss", $new_file_name, $student_id);
                    
                    if ($stmt->execute()) {
                        $_SESSION['success'] = "Profile picture updated successfully!";
                    } else {
                        $_SESSION['error'] = "Failed to update database record.";
                    }
                    $stmt->close();
                } else {
                    $_SESSION['error'] = "Failed to upload file.";
                }
            } else {
                $_SESSION['error'] = "File is too large. Maximum size is 5MB.";
            }
        } else {
            $_SESSION['error'] = "Invalid file type. Only JPG, JPEG & PNG files are allowed.";
        }
    } else {
        $_SESSION['error'] = "Error uploading file.";
    }
}

header('Location: profile.php');
exit;
