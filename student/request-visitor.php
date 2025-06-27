<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include('../includes/header.php');
include('../includes/student-sidebar.php');
include('../includes/dbconn.php');

$student_id = $_SESSION['student_id'];
$success = '';
$error = '';

// Fetch student's parent information
$student_query = "SELECT parent_name, parent_phone FROM student WHERE student_id = ?";
$stmt = $conn->prepare($student_query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $visitor_name = $_POST['visitor_name'];
    $relation = $_POST['relation'];
    $date = $_POST['date'];
    $time_in = $_POST['time_in'];
    $time_out = $_POST['time_out'];
    $purpose = $_POST['purpose'];
    $note = $_POST['note'];

    if (empty($student['parent_phone'])) {
        $error = "⚠️ Parent contact information is not available. Please update your profile first.";
    } else {
        $query = "INSERT INTO visitor_log (student_id, visitor_name, relation, visit_date, time_in, time_out, purpose, note, status, parent_name, parent_phone, parent_permission)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending', ?, ?, 'Pending')";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssssss", 
            $student_id, 
            $visitor_name, 
            $relation, 
            $date, 
            $time_in, 
            $time_out, 
            $purpose, 
            $note,
            $student['parent_name'],
            $student['parent_phone']
        );
        
        if ($stmt->execute()) {
            $success = "✅ Visitor request submitted successfully! Waiting for parent's permission and warden's approval.";
        } else {
            $error = "❌ Error submitting request. Please try again.";
        }
    }
}

// Fetch existing visitor requests
$requests_query = "SELECT * FROM visitor_log WHERE student_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($requests_query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$requests = $stmt->get_result();
?>

<div class="main-content" style="margin-left: 250px; padding: 30px; background: #f9f9f9;">
    <h2 class="mb-4 fw-bold text-dark">📝 Request Visitor Permission</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if (empty($student['parent_phone'])): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            Parent contact information is missing. Please update your profile to add parent's contact details.
            <a href="../modules/edit-profile.php" class="btn btn-warning btn-sm ms-3">Update Profile</a>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <form method="POST" class="card p-4 shadow-sm bg-light">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Visitor Name</label>
                        <input type="text" name="visitor_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Relation</label>
                        <input type="text" name="relation" class="form-control" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Date of Visit</label>
                        <input type="date" name="date" class="form-control" required min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Time In</label>
                        <input type="time" name="time_in" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Time Out</label>
                        <input type="time" name="time_out" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Purpose of Visit</label>
                    <input type="text" name="purpose" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Additional Note (Optional)</label>
                    <textarea name="note" class="form-control" rows="3"></textarea>
                </div>
                <div class="parent-info mb-3 p-3 bg-light border rounded">
                    <h6 class="fw-bold"><i class="fas fa-info-circle"></i> Parent Contact Information</h6>
                    <p class="mb-1">Name: <?= htmlspecialchars($student['parent_name'] ?? 'Not available') ?></p>
                    <p class="mb-0">Phone: <?= htmlspecialchars($student['parent_phone'] ?? 'Not available') ?></p>
                </div>
                <button type="submit" class="btn btn-primary fw-bold" <?= empty($student['parent_phone']) ? 'disabled' : '' ?>>
                    Submit Request
                </button>
            </form>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">My Visitor Requests</h5>
                </div>
                <div class="card-body">
                    <?php if ($requests && $requests->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Visitor</th>
                                        <th>Date</th>
                                        <th>Parent Permission</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($request = $requests->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($request['visitor_name']) ?></strong>
                                                <br>
                                                <small class="text-muted"><?= htmlspecialchars($request['relation']) ?></small>
                                            </td>
                                            <td><?= date('d M Y', strtotime($request['visit_date'])) ?></td>
                                            <td>
                                                <span class="badge bg-<?= getStatusColor($request['parent_permission']) ?>">
                                                    <?= $request['parent_permission'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= getStatusColor($request['status']) ?>">
                                                    <?= $request['status'] ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center mb-0">No visitor requests found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
function getStatusColor($status) {
    switch ($status) {
        case 'Approved':
            return 'success';
        case 'Rejected':
            return 'danger';
        case 'Pending':
        default:
            return 'warning';
    }
}
?>

<?php include('../includes/footer.php'); ?>
