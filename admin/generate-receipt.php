<?php
require_once('../includes/session.php');
require_once('../includes/config.php');
require_once('../includes/functions.php');

// Check if admin or warden is logged in
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['warden_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Check if payment ID is provided
if (!isset($_GET['id'])) {
    die("Payment ID not provided");
}

// Get payment details
try {
    $stmt = $pdo->prepare("
        SELECT p.*, s.name as student_name, s.registration_number, s.email, s.phone,
               COALESCE(w.name, 'System Admin') as warden_name
        FROM payments p
        JOIN students s ON p.student_id = s.id
        LEFT JOIN wardens w ON p.created_by = w.id
        WHERE p.id = ?
    ");
    $stmt->execute([$_GET['id']]);
    $payment = $stmt->fetch();
    
    if (!$payment) {
        die("Payment not found");
    }
} catch (PDOException $e) {
    die("Error fetching payment: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fee Receipt - <?php echo $payment['receipt_number']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .receipt {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #dee2e6;
        }
        .receipt-title {
            font-size: 24px;
            color: #333;
            margin: 10px 0;
        }
        .college-name {
            font-size: 28px;
            color: #1a237e;
            margin-bottom: 5px;
        }
        .receipt-body {
            margin-bottom: 30px;
        }
        .receipt-table th {
            width: 200px;
        }
        .amount-in-words {
            font-style: italic;
            color: #555;
        }
        .signature-section {
            margin-top: 50px;
            text-align: right;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            opacity: 0.03;
            color: #000;
            pointer-events: none;
            white-space: nowrap;
        }
        @media print {
            body {
                background: white;
            }
            .receipt {
                box-shadow: none;
                margin: 0;
                padding: 15px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="receipt position-relative">
            <div class="watermark">
                <?php echo $payment['payment_status']; ?>
            </div>
            
            <div class="receipt-header">
                <h1 class="college-name">HOSTEL MANAGEMENT SYSTEM</h1>
                <h2 class="receipt-title">FEE RECEIPT</h2>
                <p class="mb-0">Receipt No: <?php echo htmlspecialchars($payment['receipt_number']); ?></p>
                <p>Date: <?php echo date('d-m-Y', strtotime($payment['payment_date'])); ?></p>
            </div>
            
            <div class="receipt-body">
                <table class="table receipt-table">
                    <tr>
                        <th>Student Name:</th>
                        <td><?php echo htmlspecialchars($payment['student_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Registration Number:</th>
                        <td><?php echo htmlspecialchars($payment['registration_number']); ?></td>
                    </tr>
                    <tr>
                        <th>Contact:</th>
                        <td>
                            <?php echo htmlspecialchars($payment['phone']); ?><br>
                            <?php echo htmlspecialchars($payment['email']); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Payment Type:</th>
                        <td><?php echo htmlspecialchars($payment['payment_type']); ?></td>
                    </tr>
                    <tr>
                        <th>Semester:</th>
                        <td><?php echo htmlspecialchars($payment['semester']); ?></td>
                    </tr>
                    <tr>
                        <th>Academic Year:</th>
                        <td><?php echo htmlspecialchars($payment['academic_year']); ?></td>
                    </tr>
                    <tr>
                        <th>Amount:</th>
                        <td>₹<?php echo number_format($payment['amount'], 2); ?></td>
                    </tr>
                    <tr>
                        <th>Amount in Words:</th>
                        <td class="amount-in-words">
                            <?php
                            function numberToWords($number) {
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
                                $hundreds = array(
                                    "Hundred", "Thousand", "Lakh", "Crore"
                                );
                                
                                if ($number == 0) return "Zero";
                                
                                $words = "";
                                
                                if ($number >= 10000000) {
                                    $words .= numberToWords(floor($number/10000000)) . " Crore ";
                                    $number %= 10000000;
                                }
                                
                                if ($number >= 100000) {
                                    $words .= numberToWords(floor($number/100000)) . " Lakh ";
                                    $number %= 100000;
                                }
                                
                                if ($number >= 1000) {
                                    $words .= numberToWords(floor($number/1000)) . " Thousand ";
                                    $number %= 1000;
                                }
                                
                                if ($number >= 100) {
                                    $words .= numberToWords(floor($number/100)) . " Hundred ";
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
                            
                            echo numberToWords($payment['amount']) . " Rupees Only";
                            ?>
                        </td>
                    </tr>
                    <?php if ($payment['payment_status'] === 'Completed'): ?>
                    <tr>
                        <th>Payment Method:</th>
                        <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                    </tr>
                    <?php if ($payment['transaction_id']): ?>
                    <tr>
                        <th>Transaction ID:</th>
                        <td><?php echo htmlspecialchars($payment['transaction_id']); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php endif; ?>
                </table>
            </div>
            
            <div class="signature-section">
                <p>
                    Generated by: <?php echo htmlspecialchars($payment['warden_name']); ?><br>
                    Warden
                </p>
                <div style="margin-top: 30px;">
                    _____________________<br>
                    Authorized Signature
                </div>
            </div>
        </div>
        
        <div class="text-center mb-4 no-print">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print Receipt
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
    </div>
</body>
</html> 