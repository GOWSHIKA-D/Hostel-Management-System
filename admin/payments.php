<?php
require_once('../includes/session.php');
require_once('../includes/config.php');
require_once('../includes/functions.php');
require_once('../includes/system_constants.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Handle payment generation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['generate_fee'])) {
        $student_id = $_POST['student_id'];
        $amount = $_POST['amount'];
        $payment_type = $_POST['payment_type'];
        $semester = $_POST['semester'];
        $academic_year = $_POST['academic_year'];
        
        // Generate unique receipt number
        $receipt_number = 'RCP' . date('Ymd') . rand(1000, 9999);
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO payments (
                    student_id, amount, payment_type, payment_date, 
                    semester, academic_year, receipt_number, 
                    payment_status, created_by
                ) VALUES (
                    ?, ?, ?, CURDATE(), 
                    ?, ?, ?, 
                    'Pending', ?
                )
            ");
            
            $stmt->execute([
                $student_id, $amount, $payment_type,
                $semester, $academic_year, $receipt_number,
                SYSTEM_WARDEN_ID
            ]);
            
            $_SESSION['success'] = "Fee generated successfully. Receipt Number: " . $receipt_number;
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error generating fee: " . $e->getMessage();
        }
        
        header('Location: payments.php');
        exit();
    }
    
    // Handle payment status update
    if (isset($_POST['update_status'])) {
        $payment_id = $_POST['payment_id'];
        $payment_status = $_POST['payment_status'];
        $payment_method = $_POST['payment_method'];
        $transaction_id = $_POST['transaction_id'];
        
        try {
            $stmt = $pdo->prepare("
                UPDATE payments 
                SET payment_status = ?,
                    payment_method = ?,
                    transaction_id = ?
                WHERE id = ?
            ");
            
            $stmt->execute([$payment_status, $payment_method, $transaction_id, $payment_id]);
            $_SESSION['success'] = "Payment status updated successfully";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error updating payment: " . $e->getMessage();
        }
        
        header('Location: payments.php');
        exit();
    }
}

$page_title = "Payment Management";
include('../includes/header.php');
include('../includes/admin-sidebar.php');

// Get list of students for debugging
try {
    $debug_stmt = $pdo->query("SELECT COUNT(*) as count FROM students");
    $student_count = $debug_stmt->fetch()['count'];
} catch (PDOException $e) {
    $student_count = "Error: " . $e->getMessage();
}
?>

<div class="content" style="margin-left: 240px; padding: 20px;">
    <div class="container">
        <h2 class="mb-4">Payment Management</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Debug Info -->
        <div class="alert alert-info">
            Total students in database: <?php echo $student_count; ?>
        </div>

        <!-- Generate Fee Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Generate Fee</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="payments.php">
                    <input type="hidden" name="generate_fee" value="1">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="student_id">Select Student:</label>
                                <select class="form-control" id="student_id" name="student_id" required>
                                    <option value="">Select a student...</option>
                                    <?php
                                    try {
                                        $stmt = $pdo->query("SELECT * FROM students ORDER BY name");
                                        while ($student = $stmt->fetch()) {
                                            echo "<option value='" . $student['id'] . "'>" . 
                                                 htmlspecialchars($student['name'] . ' (' . $student['registration_number'] . ')') . 
                                                 "</option>";
                                        }
                                    } catch (PDOException $e) {
                                        echo "<option value=''>Error loading students: " . $e->getMessage() . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="payment_type">Fee Type:</label>
                                <select class="form-control" id="payment_type" name="payment_type" required>
                                    <option value="Hostel Fee">Hostel Fee</option>
                                    <option value="Mess Fee">Mess Fee</option>
                                    <option value="Security Deposit">Security Deposit</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="amount">Amount:</label>
                                <input type="number" class="form-control" id="amount" name="amount" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="semester">Semester:</label>
                                <select class="form-control" id="semester" name="semester" required>
                                    <option value="Semester 1">Semester 1</option>
                                    <option value="Semester 2">Semester 2</option>
                                    <option value="Semester 3">Semester 3</option>
                                    <option value="Semester 4">Semester 4</option>
                                    <option value="Semester 5">Semester 5</option>
                                    <option value="Semester 6">Semester 6</option>
                                    <option value="Semester 7">Semester 7</option>
                                    <option value="Semester 8">Semester 8</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="academic_year">Academic Year:</label>
                                <select class="form-control" id="academic_year" name="academic_year" required>
                                    <?php
                                    $current_year = date('Y');
                                    for ($i = 0; $i < 5; $i++) {
                                        $year = $current_year - $i;
                                        $academic_year = $year . '-' . ($year + 1);
                                        echo "<option value='$academic_year'>$academic_year</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Generate Fee</button>
                </form>
            </div>
        </div>

        <!-- Payments List -->
        <div class="card">
            <div class="card-header">
                <h4>Payment Records</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Receipt No.</th>
                                <th>Student</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Payment Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $stmt = $pdo->query("
                                    SELECT p.*, s.name as student_name, s.registration_number 
                                    FROM payments p
                                    JOIN students s ON p.student_id = s.id
                                    ORDER BY p.created_at DESC
                                ");
                                
                                while ($payment = $stmt->fetch()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($payment['receipt_number']) . "</td>";
                                    echo "<td>" . htmlspecialchars($payment['student_name']) . 
                                         "<br><small class='text-muted'>" . htmlspecialchars($payment['registration_number']) . "</small></td>";
                                    echo "<td>" . htmlspecialchars($payment['payment_type']) . "</td>";
                                    echo "<td>₹" . number_format($payment['amount'], 2) . "</td>";
                                    echo "<td>";
                                    $status_class = '';
                                    switch ($payment['payment_status']) {
                                        case 'Completed':
                                            $status_class = 'success';
                                            break;
                                        case 'Pending':
                                            $status_class = 'warning';
                                            break;
                                        case 'Failed':
                                            $status_class = 'danger';
                                            break;
                                        case 'Refunded':
                                            $status_class = 'info';
                                            break;
                                    }
                                    echo "<span class='badge bg-" . $status_class . "'>" . 
                                         htmlspecialchars($payment['payment_status']) . "</span>";
                                    echo "</td>";
                                    echo "<td>" . date('d-m-Y', strtotime($payment['payment_date'])) . "</td>";
                                    echo "<td>";
                                    echo "<div class='btn-group'>";
                                    echo "<button type='button' class='btn btn-sm btn-primary' onclick='viewReceipt(" . 
                                         $payment['id'] . ")'><i class='fas fa-file-invoice'></i> Receipt</button>";
                                    if ($payment['payment_status'] === 'Pending') {
                                        echo "<button type='button' class='btn btn-sm btn-success' onclick='updatePayment(" . 
                                             $payment['id'] . ")'><i class='fas fa-check'></i> Update</button>";
                                    }
                                    echo "</div>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } catch (PDOException $e) {
                                echo "<tr><td colspan='7' class='text-center text-danger'>Error loading payments: " . $e->getMessage() . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Payment Modal -->
<div class="modal fade" id="updatePaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Payment Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="payments.php">
                <div class="modal-body">
                    <input type="hidden" name="update_status" value="1">
                    <input type="hidden" name="payment_id" id="modal_payment_id">
                    
                    <div class="form-group mb-3">
                        <label>Payment Status:</label>
                        <select class="form-control" name="payment_status" required>
                            <option value="Completed">Completed</option>
                            <option value="Failed">Failed</option>
                            <option value="Refunded">Refunded</option>
                        </select>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label>Payment Method:</label>
                        <select class="form-control" name="payment_method" required>
                            <option value="Cash">Cash</option>
                            <option value="Online Transfer">Online Transfer</option>
                            <option value="UPI">UPI</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label>Transaction ID:</label>
                        <input type="text" class="form-control" name="transaction_id">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updatePayment(paymentId) {
    document.getElementById('modal_payment_id').value = paymentId;
    new bootstrap.Modal(document.getElementById('updatePaymentModal')).show();
}

function viewReceipt(paymentId) {
    window.open('generate-receipt.php?id=' + paymentId, '_blank');
}
</script>

<?php include('../includes/footer.php'); ?> 