<?php
session_start();
include("dbconn.php");

// Sanitize inputs
$role = $_POST['role'] ?? '';
$userid = mysqli_real_escape_string($conn, trim($_POST['userid'] ?? ''));
$password = trim($_POST['password'] ?? '');

// Validate required fields
if (empty($role) || empty($userid) || empty($password)) {
    $_SESSION['login_error'] = "All fields are required!";
    header("Location: ../auth/login.php");
    exit;
}

// Determine query and handle login based on role
switch ($role) {
    case 'student':
        $query = "SELECT * FROM student WHERE student_id=? AND dob=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $userid, $password);
        $redirect = "../student/dashboard.php";
        $session_key = 'student_id';
        break;

    case 'admin':
        $query = "SELECT * FROM admin WHERE admin_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $userid);
        $redirect = "../admin/dashboard.php";
        $session_key = 'admin_id';
        break;

    case 'warden':
        $query = "SELECT * FROM wardens WHERE warden_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $userid);
        $redirect = "../warden/dashboard.php";
        $session_key = 'warden_id';
        break;

    default:
        $_SESSION['login_error'] = "Invalid role selected!";
        header("Location: ../auth/login.php");
        exit;
}

// Execute query
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Password verification based on role
    $valid_password = false;
    
    if ($role === 'student') {
        $valid_password = ($password === $user['dob']); // Students use DOB as password
    } else {
        // Both admin and warden use password_verify
        $valid_password = password_verify($password, $user['password']);
    }
    
    if ($valid_password) {
        // Set session variables
        $_SESSION['role'] = $role;
        $_SESSION[$session_key] = $userid;
        
        // Set name in session based on role
        switch ($role) {
            case 'student':
                $_SESSION['student_name'] = $user['name'];
                break;
            case 'warden':
                $_SESSION['warden_name'] = $user['name'];
                break;
            case 'admin':
                $_SESSION['admin_name'] = $user['fullname'];
                break;
        }
        
        header("Location: $redirect");
        exit;
    }
}

$_SESSION['login_error'] = "Invalid credentials!";
header("Location: ../auth/login.php");
exit;

$stmt->close();
$conn->close();

function check_login() {
    // Check if user is logged in and is a warden
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'warden' || !isset($_SESSION['warden_id'])) {
        header("Location: ../auth/login.php");
        exit();
    }
}
?>
