<?php
// Security functions
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generate_token() {
    return bin2hex(random_bytes(32));
}

function verify_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Authentication functions
function is_logged_in() {
    return isset($_SESSION['user_id']) || isset($_SESSION['warden_id']) || isset($_SESSION['admin_id']);
}

function is_warden() {
    return isset($_SESSION['warden_id']);
}

function is_student() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['admin_id']);
}

function redirect($path) {
    header("Location: " . SITE_URL . "/" . $path);
    exit();
}

// Date and time functions
function format_date($date) {
    return date('F j, Y', strtotime($date));
}

function format_datetime($datetime) {
    return date('F j, Y g:i A', strtotime($datetime));
}

// Database helper functions
function get_student_details($pdo, $student_id) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$student_id]);
    return $stmt->fetch();
}

function get_room_details($pdo, $room_id) {
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->execute([$room_id]);
    return $stmt->fetch();
}

function get_room_occupants($pdo, $room_id) {
    $stmt = $pdo->prepare("
        SELECT s.* 
        FROM students s
        JOIN room_assignments ra ON s.id = ra.student_id
        WHERE ra.room_id = ? AND ra.status = 'active'
    ");
    $stmt->execute([$room_id]);
    return $stmt->fetchAll();
}

// Notification functions
function add_notification($pdo, $user_id, $message, $type = 'info') {
    $stmt = $pdo->prepare("
        INSERT INTO notifications (user_id, message, type, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    return $stmt->execute([$user_id, $message, $type]);
}

function get_notifications($pdo, $user_id, $limit = 5) {
    $stmt = $pdo->prepare("
        SELECT * FROM notifications 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT ?
    ");
    $stmt->execute([$user_id, $limit]);
    return $stmt->fetchAll();
}

// File upload function
function handle_file_upload($file, $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'], $max_size = 5242880) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload failed with error code ' . $file['error']];
    }

    $file_info = pathinfo($file['name']);
    $ext = strtolower($file_info['extension']);

    if (!in_array($ext, $allowed_types)) {
        return ['success' => false, 'error' => 'Invalid file type'];
    }

    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'File size exceeds limit'];
    }

    $new_filename = uniqid() . '.' . $ext;
    $upload_path = UPLOADS_PATH . '/' . $new_filename;

    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return ['success' => true, 'filename' => $new_filename];
    }

    return ['success' => false, 'error' => 'Failed to move uploaded file'];
}

// Validation functions
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_phone($phone) {
    return preg_match('/^[0-9]{10}$/', $phone);
}

function validate_registration_number($reg_no) {
    return preg_match('/^[A-Z0-9]{8,}$/', $reg_no);
}

// Error and success message handling
function set_message($message, $type = 'success') {
    $_SESSION[$type] = $message;
}

function display_message($type) {
    if (isset($_SESSION[$type])) {
        $message = $_SESSION[$type];
        unset($_SESSION[$type]);
        return $message;
    }
    return '';
}

// Room management functions
function is_room_available($pdo, $room_id) {
    $stmt = $pdo->prepare("
        SELECT capacity, occupied 
        FROM rooms 
        WHERE id = ? AND occupied < capacity
    ");
    $stmt->execute([$room_id]);
    return (bool) $stmt->fetch();
}

function update_room_occupancy($pdo, $room_id, $increment = true) {
    $sql = "UPDATE rooms SET occupied = occupied " . ($increment ? "+ 1" : "- 1") . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$room_id]);
}

// Utility functions for the application

/**
 * Sanitize user input
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Format currency
 */
function format_currency($amount) {
    return '₹' . number_format($amount, 2);
}

/**
 * Convert number to words for Indian currency
 */
function number_to_words($number) {
    $ones = array(
        0 => "", 1 => "One", 2 => "Two", 3 => "Three", 4 => "Four",
        5 => "Five", 6 => "Six", 7 => "Seven", 8 => "Eight", 9 => "Nine",
        10 => "Ten", 11 => "Eleven", 12 => "Twelve", 13 => "Thirteen",
        14 => "Fourteen", 15 => "Fifteen", 16 => "Sixteen", 17 => "Seventeen",
        18 => "Eighteen", 19 => "Nineteen"
    );
    $tens = array(
        0 => "", 2 => "Twenty", 3 => "Thirty", 4 => "Forty", 5 => "Fifty",
        6 => "Sixty", 7 => "Seventy", 8 => "Eighty", 9 => "Ninety"
    );
    
    if ($number == 0) return "Zero";
    
    $words = "";
    
    if ($number >= 10000000) {
        $words .= number_to_words(floor($number/10000000)) . " Crore ";
        $number %= 10000000;
    }
    
    if ($number >= 100000) {
        $words .= number_to_words(floor($number/100000)) . " Lakh ";
        $number %= 100000;
    }
    
    if ($number >= 1000) {
        $words .= number_to_words(floor($number/1000)) . " Thousand ";
        $number %= 1000;
    }
    
    if ($number >= 100) {
        $words .= number_to_words(floor($number/100)) . " Hundred ";
        $number %= 100;
    }
    
    if ($number >= 20) {
        $words .= $tens[floor($number/10)];
        if ($number % 10) $words .= " " . $ones[$number % 10];
    } else {
        $words .= $ones[$number];
    }
    
    return trim($words);
}

/**
 * Generate a random string
 */
function generate_random_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $string;
}

/**
 * Redirect with message
 */
function redirect_with_message($url, $message, $type = 'success') {
    $_SESSION[$type] = $message;
    header("Location: $url");
    exit();
}
?> 