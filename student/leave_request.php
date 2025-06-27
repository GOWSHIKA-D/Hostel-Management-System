<?php
session_start();
include('../includes/header.php');
include('../includes/student-sidebar.php');
include('../includes/dbconn.php');

$student_id = $_SESSION['student_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $leave_type = $_POST['leave_type'];
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $reason = $_POST['reason'];
    $status = 'Pending';

    $stmt = $conn->prepare("INSERT INTO leave_requests (student_id, leave_type, from_date, to_date, reason, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $student_id, $leave_type, $from_date, $to_date, $reason, $status);

    if ($stmt->execute()) {
        $success = "✅ Leave request submitted successfully!";
    } else {
        $error = "❌ Failed to submit leave request!";
    }

    $stmt->close();
}

// Fetch leave history
$query = "SELECT * FROM leave_requests WHERE student_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="main-content" style="margin-left: 250px; padding: 30px; background: #f0f2f5;">
    <h2 class="fw-bold mb-4">📝 Leave Request</h2>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <!-- Leave Form -->
    <div class="card p-4 mb-4 shadow-sm">
        <h5 class="fw-bold mb-3">Apply for Leave</h5>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Leave Type:</label>
                <select name="leave_type" class="form-control" required>
                    <option value="">Select Leave Type</option>
                    <option value="Medical">Medical Leave</option>
                    <option value="Personal">Personal Leave</option>
                    <option value="Family Emergency">Family Emergency</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">From Date:</label>
                <input type="date" name="from_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">To Date:</label>
                <input type="date" name="to_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Reason:</label>
                <textarea name="reason" class="form-control" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Request</button>
        </form>
    </div>

    <!-- Leave History -->
    <div class="card p-4 shadow-sm">
        <h5 class="fw-bold mb-3">📜 Leave History</h5>
        <table class="table table-bordered table-striped">
            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Type</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['leave_type']) ?></td>
                        <td><?= htmlspecialchars($row['from_date']) ?></td>
                        <td><?= htmlspecialchars($row['to_date']) ?></td>
                        <td><?= htmlspecialchars($row['reason']) ?></td>
                        <td>
                            <?php
                                $badgeClass = match ($row['status']) {
                                    'Approved' => 'success',
                                    'Rejected' => 'danger',
                                    default => 'warning',
                                };
                            ?>
                            <span class="badge bg-<?= $badgeClass ?>"><?= htmlspecialchars($row['status']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($row['warden_remarks'] ?? 'No remarks') ?></td>
                    </tr>
                <?php endwhile; ?>
                <?php if ($result->num_rows == 0): ?>
                    <tr><td colspan="7" class="text-center text-muted">No leave requests found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('../includes/footer.php'); ?> 