<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include('../includes/dbconn.php');

$student_id = $_SESSION['student_id'];

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount']);
    $payment_method = $_POST['payment_method'];
    $semester = $_POST['semester'];
    $reference_no = $_POST['reference_no'];
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
        semester, description, payment_date, payment_details
    ) VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)");
    
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
}

// Get student details with room info
$stmt = $conn->prepare("
    SELECT s.*, r.room_number, r.room_type, r.fees as monthly_rent
    FROM student s 
    LEFT JOIN room r ON s.room_id = r.room_id 
    WHERE s.student_id = ?
");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// Calculate total fees (assuming 6 months per semester)
$monthly_rent = $student['monthly_rent'] ?? 25000; // Default to 25000 if not set
$semester_fee = $monthly_rent * 6;
$total_fee = $semester_fee * 8; // Total for 4 years (8 semesters)

// Get payment statistics
$stats = $conn->prepare("
    SELECT 
        COUNT(*) as total_payments,
        SUM(CASE WHEN status = 'Verified' THEN amount ELSE 0 END) as total_paid,
        SUM(CASE WHEN status = 'Pending' THEN amount ELSE 0 END) as pending_amount,
        COUNT(CASE WHEN status = 'Pending' THEN 1 END) as pending_count
    FROM payments 
    WHERE student_id = ?
");
$stats->bind_param("s", $student_id);
$stats->execute();
$payment_stats = $stats->get_result()->fetch_assoc();

$total_paid = $payment_stats['total_paid'] ?? 0;
$balance = $total_fee - $total_paid;

// Get payment history
$history = $conn->prepare("
    SELECT * FROM payments 
    WHERE student_id = ? 
    ORDER BY created_at DESC
");
$history->bind_param("s", $student_id);
$history->execute();
$payment_history = $history->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Dashboard - Student</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f5f5;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        .merchant-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .merchant-header h3 {
            color: #dc3545;
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 24px;
        }
        .merchant-header h5 {
            color: #666;
            margin-bottom: 5px;
        }
        .page-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .left-column, .right-column {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .info-row {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
            height: fit-content;
        }
        .info-header {
            background: #0d6efd;
            color: white;
            padding: 15px 20px;
            font-size: 18px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .info-content {
            padding: 20px;
        }
        .student-details {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }
        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }
        .info-label {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .info-value {
            color: #333;
            font-weight: 500;
            font-size: 16px;
        }
        .fees-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .payment-method-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
        }
        .payment-method-btn:hover {
            background: #f8f9fa;
        }
        .payment-method-btn.active {
            background: #dc3545;
            color: white;
            border-color: #dc3545;
        }
        .payment-method-btn i {
            font-size: 20px;
        }
        .payment-history {
            width: 100%;
            border-collapse: collapse;
        }
        .payment-history th {
            background: #f8f9fa;
            padding: 12px 15px;
            text-align: left;
            font-weight: 500;
        }
        .payment-history td {
            padding: 12px 15px;
            border-top: 1px solid #dee2e6;
        }
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 12px;
        }
    </style>
</head>
<body class="bg-light">

<?php include('../includes/student-sidebar.php'); ?>

<div class="container">
    <!-- College Header -->
    <div class="merchant-header">
        <h3>M. KUMARASAMY</h3>
        <h3>COLLEGE OF ENGINEERING</h3>
        <h5>KARUR</h5>
        <small class="text-muted">Payment Portal</small>
    </div>

    <div class="page-layout">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Student Details -->
            <div class="info-row">
                <div class="info-header">
                    <i class="fas fa-user-graduate"></i>
                    Student Details
                </div>
                <div class="info-content">
                    <div class="student-details">
                        <div class="info-item">
                            <div class="info-label">Name</div>
                            <div class="info-value"><?= $student['name'] ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Student ID</div>
                            <div class="info-value"><?= $student['student_id'] ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Room Number</div>
                            <div class="info-value"><?= $student['room_number'] ?? 'Not Assigned' ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Monthly Rent</div>
                            <div class="info-value">₹<?= number_format($monthly_rent) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fees Details -->
            <div class="info-row">
                <div class="info-header">
                    <i class="fas fa-money-bill"></i>
                    Fees Details
                </div>
                <div class="info-content">
                    <div class="fees-grid">
                        <div class="info-item">
                            <div class="info-label">Total Fees</div>
                            <div class="info-value">₹<?= number_format($total_fee) ?></div>
                            <small class="text-muted">For 4 years</small>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Paid Amount</div>
                            <div class="info-value text-success">₹<?= number_format($total_paid) ?></div>
                            <small class="text-muted">Verified payments</small>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Pending Amount</div>
                            <div class="info-value text-warning">₹<?= number_format($payment_stats['pending_amount']) ?></div>
                            <small class="text-muted"><?= $payment_stats['pending_count'] ?> pending</small>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Balance Due</div>
                            <div class="info-value text-danger">₹<?= number_format($balance) ?></div>
                            <small class="text-muted">Remaining amount</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Types -->
            <div class="info-row">
                <div class="info-header">
                    <i class="fas fa-credit-card"></i>
                    Payment Types
                </div>
                <div class="info-content">
                    <div class="payment-methods">
                        <button type="button" class="payment-method-btn" data-method="Cash">
                            <i class="fas fa-money-bill-wave"></i>
                            Cash
                        </button>
                        <button type="button" class="payment-method-btn" data-method="Cheque/DD">
                            <i class="fas fa-money-check"></i>
                            Cheque / DD
                        </button>
                        <button type="button" class="payment-method-btn" data-method="Net Banking">
                            <i class="fas fa-university"></i>
                            Net Banking
                        </button>
                        <button type="button" class="payment-method-btn" data-method="Debit Card">
                            <i class="fas fa-credit-card"></i>
                            Debit Card
                        </button>
                        <button type="button" class="payment-method-btn" data-method="Credit Card">
                            <i class="fas fa-credit-card"></i>
                            Credit Card
                        </button>
                        <button type="button" class="payment-method-btn" data-method="UPI">
                            <i class="fas fa-mobile-alt"></i>
                            UPI
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="right-column">
            <!-- Payment Form -->
            <div class="info-row">
                <div class="info-header">
                    <i class="fas fa-file-invoice"></i>
                    Make Payment
                </div>
                <div class="info-content">
                    <div id="paymentForm" style="display: none;">
                        <form method="POST">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Transaction ID</label>
                                    <input type="text" class="form-control" name="reference_no" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Amount (₹)</label>
                                    <input type="number" class="form-control" name="amount" id="baseAmount" value="<?= $monthly_rent ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Semester</label>
                                    <select name="semester" class="form-select" required>
                                        <option value="">Select Semester</option>
                                        <?php 
                                        $current_year = date('Y');
                                        for($year = $current_year; $year <= $current_year + 3; $year++): 
                                            for($sem = 1; $sem <= 2; $sem++):
                                                $season = ($sem == 1) ? 'Fall' : 'Spring';
                                                $value = "$season $year";
                                        ?>
                                            <option value="<?= $value ?>"><?= $value ?></option>
                                        <?php 
                                            endfor;
                                        endfor; 
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="info-item mb-4">
                                <div class="row mb-2">
                                    <div class="col-md-8">Base Amount</div>
                                    <div class="col-md-4 text-end">₹<span id="displayAmount">0.00</span></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-8">Convenience Fee</div>
                                    <div class="col-md-4 text-end">₹<span id="convenienceFee">0.00</span></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-8">GST (18%)</div>
                                    <div class="col-md-4 text-end">₹<span id="gst">0.00</span></div>
                                </div>
                                <div class="row fw-bold pt-2 border-top">
                                    <div class="col-md-8">Total Amount</div>
                                    <div class="col-md-4 text-end">₹<span id="totalAmount">0.00</span></div>
                                </div>
                            </div>

                            <div id="chequeFields" style="display: none;">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="payment_type" id="chequeType" value="Cheque" checked>
                                            <label class="form-check-label" for="chequeType">Cheque</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="payment_type" id="ddType" value="DD">
                                            <label class="form-check-label" for="ddType">DD (Demand Draft)</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Drawer Name</label>
                                        <input type="text" class="form-control" name="drawer_name">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Drawee Name</label>
                                        <input type="text" class="form-control" name="drawee_name" 
                                               value="INSTITUTE OF INFORMATION AND COMMUNICATION TECHNOLOGY" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Cheque/DD Date</label>
                                        <input type="date" class="form-control" name="cheque_date">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Cheque/DD Number</label>
                                        <input type="text" class="form-control" name="cheque_number">
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="payment_method" id="paymentMethod">
                            
                            <div class="text-end">
                                <button type="button" class="btn btn-secondary" onclick="window.location.reload()">Cancel</button>
                                <button type="submit" class="btn btn-primary">Submit Payment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            <div class="info-row">
                <div class="info-header">
                    <i class="fas fa-history"></i>
                    Payment History
                </div>
                <div class="info-content">
                    <table class="payment-history">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Reference</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($payment = $payment_history->fetch_assoc()): ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($payment['created_at'])) ?></td>
                                    <td>₹<?= number_format($payment['amount'], 2) ?></td>
                                    <td><?= $payment['reference_no'] ?></td>
                                    <td>
                                        <span class="badge <?= strtolower($payment['status']) === 'verified' ? 'bg-success' : (strtolower($payment['status']) === 'pending' ? 'bg-warning' : 'bg-danger') ?>">
                                            <?= $payment['status'] ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Receipt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="receipt" id="receipt">
                    <div class="receipt-header">
                        <h4>Hostel Management System</h4>
                        <h5>Payment Receipt</h5>
                    </div>
                    <div class="receipt-body">
                        <div class="row mb-3">
                            <div class="col-6">
                                <strong>Student Name:</strong> <?= $student['name'] ?><br>
                                <strong>Student ID:</strong> <?= $student['student_id'] ?><br>
                                <strong>Room Number:</strong> <?= $student['room_number'] ?? 'Not Assigned' ?>
                            </div>
                            <div class="col-6 text-end">
                                <strong>Receipt Date:</strong> <span id="receipt-date"></span><br>
                                <strong>Reference No:</strong> <span id="receipt-ref"></span>
                            </div>
                        </div>
                        <table class="table table-bordered">
                            <tr>
                                <th>Description</th>
                                <th class="text-end">Amount</th>
                            </tr>
                            <tr>
                                <td>Semester <span id="receipt-semester"></span> Hostel Fee</td>
                                <td class="text-end">₹<span id="receipt-amount"></span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="receipt-footer">
                        <p class="mb-0">Thank you for your payment!</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">Print Receipt</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showReceipt(id, date, amount, ref, semester) {
    document.getElementById('receipt-date').textContent = new Date(date).toLocaleDateString();
    document.getElementById('receipt-amount').textContent = new Intl.NumberFormat('en-IN').format(amount);
    document.getElementById('receipt-ref').textContent = ref;
    document.getElementById('receipt-semester').textContent = semester;
    
    new bootstrap.Modal(document.getElementById('receiptModal')).show();
}

document.addEventListener('DOMContentLoaded', function() {
    const paymentModes = document.querySelectorAll('.payment-method-btn');
    const paymentForm = document.getElementById('paymentForm');
    const paymentMethod = document.getElementById('paymentMethod');
    const chequeFields = document.getElementById('chequeFields');
    const baseAmountInput = document.getElementById('baseAmount');
    const displayAmount = document.getElementById('displayAmount');
    const convenienceFee = document.getElementById('convenienceFee');
    const gst = document.getElementById('gst');
    const totalAmount = document.getElementById('totalAmount');

    // Payment mode selection
    paymentModes.forEach(mode => {
        mode.addEventListener('click', function() {
            paymentModes.forEach(m => m.classList.remove('active'));
            this.classList.add('active');
            
            const method = this.dataset.method;
            paymentMethod.value = method;
            paymentForm.style.display = 'block';
            
            document.getElementById('chequeFields').style.display = method === 'Cheque/DD' ? 'block' : 'none';
            calculateFees();
            
            paymentForm.scrollIntoView({ behavior: 'smooth' });
        });
    });

    // Calculate fees when amount changes
    baseAmountInput.addEventListener('input', calculateFees);

    function calculateFees() {
        const baseAmount = parseFloat(baseAmountInput.value) || 0;
        const method = paymentMethod.value;
        
        let convenienceRate = 0;
        switch(method) {
            case 'Credit Card':
                convenienceRate = 0.02; // 2%
                break;
            case 'Debit Card':
                convenienceRate = 0.015; // 1.5%
                break;
            case 'Net Banking':
            case 'UPI':
                convenienceRate = 0.01; // 1%
                break;
            default:
                convenienceRate = 0;
        }

        const convenienceFeeAmount = baseAmount * convenienceRate;
        const gstAmount = convenienceFeeAmount * 0.18;
        const total = baseAmount + convenienceFeeAmount + gstAmount;

        displayAmount.textContent = baseAmount.toFixed(2);
        convenienceFee.textContent = convenienceFeeAmount.toFixed(2);
        gst.textContent = gstAmount.toFixed(2);
        totalAmount.textContent = total.toFixed(2);
    }
});
</script>
</body>
</html>
