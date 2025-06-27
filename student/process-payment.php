<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include('../includes/dbconn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_SESSION['student_id'];
    $amount = floatval($_POST['amount']);
    $payment_method = $_POST['payment_method'];
    $reference_no = $_POST['reference_no'];
    $semester = $_POST['semester'];
    $mobile = $_POST['mobile'] ?? '';
    $email = $_POST['email'] ?? '';
    $payer_name = $_POST['payer_name'] ?? '';
    
    // Calculate fees
    $convenience_fee = 0;
    switch($payment_method) {
        case 'Credit Card':
            $convenience_fee = $amount * 0.02; // 2%
            break;
        case 'Debit Card':
            $convenience_fee = $amount * 0.015; // 1.5%
            break;
        case 'Net Banking':
        case 'UPI':
            $convenience_fee = $amount * 0.01; // 1%
            break;
    }
    
    $gst = $convenience_fee * 0.18; // 18% GST on convenience fee
    $total_amount = $amount + $convenience_fee + $gst;
    
    // Additional fields for Cheque/DD
    $cheque_details = [];
    if ($payment_method === 'Cheque/DD') {
        $payment_method = $_POST['payment_type']; // Either 'Cheque' or 'DD'
        $cheque_details = [
            'drawer_name' => $_POST['drawer_name'] ?? '',
            'drawee_name' => $_POST['drawee_name'] ?? '',
            'cheque_date' => $_POST['cheque_date'] ?? '',
            'cheque_number' => $_POST['cheque_number'] ?? ''
        ];
        
        // Validate cheque/DD details
        if (empty($cheque_details['drawer_name']) || empty($cheque_details['cheque_date']) || empty($cheque_details['cheque_number'])) {
            $_SESSION['error'] = "Please fill all required fields for Cheque/DD payment";
            header("Location: payments.php");
            exit();
        }
    }
    
    // Validate amount
    if ($amount < 1000) {
        $_SESSION['error'] = "Amount must be at least ₹1,000";
        header("Location: payments.php");
        exit();
    }
    
    // Check for duplicate reference number
    $check = $conn->prepare("SELECT id FROM payments WHERE reference_no = ?");
    $check->bind_param("s", $reference_no);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $_SESSION['error'] = "This reference number has already been used";
        header("Location: payments.php");
        exit();
    }
    
    // Prepare payment details as JSON
    $payment_details = json_encode([
        'convenience_fee' => $convenience_fee,
        'gst' => $gst,
        'total_amount' => $total_amount,
        'mobile' => $mobile,
        'email' => $email,
        'payer_name' => $payer_name,
        'cheque_details' => $cheque_details
    ]);
    
    // Insert payment record
    $stmt = $conn->prepare("INSERT INTO payments (
        student_id, amount, payment_method, reference_no, 
        semester, description, payment_details
    ) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $description = "Hostel Fee Payment for " . $semester;
    $stmt->bind_param("sdsssss", 
        $student_id, $amount, $payment_method, $reference_no, 
        $semester, $description, $payment_details
    );
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Payment of ₹" . number_format($total_amount, 2) . " submitted successfully! Reference: " . $reference_no;
    } else {
        $_SESSION['error'] = "Error submitting payment: " . $conn->error;
    }
    
    header("Location: payments.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: payments.php");
    exit();
} 