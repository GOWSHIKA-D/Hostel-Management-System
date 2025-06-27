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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $visitor_name = $_POST['visitor_name'];
    $relation = $_POST['relation'];
    $date = $_POST['date'];
    $time_in = $_POST['time_in'];
    $time_out = $_POST['time_out'];
    $purpose = $_POST['purpose'];
    $note = $_POST['note'];
    $parents_permission = $_POST['parents_permission'] ?? 'Pending';  // fallback default

    $query = "INSERT INTO visitor_log (student_id, visitor_name, relation, date, time_in, time_out, purpose, note, status, parents_permission)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending', ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssss", $student_id, $visitor_name, $relation, $date, $time_in, $time_out, $purpose, $note, $parents_permission);
    $stmt->execute();

    $success = "✅ Visitor request submitted successfully! Waiting for approval.";
}
?>

<div class="main-content" style="margin-left: 250px; padding: 30px; background: #f9f9f9;">
    <h2 class="mb-4 fw-bold text-dark">📝 Request Visitor Permission</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

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
                <input type="date" name="date" class="form-control" required>
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

        <!-- ⭐️ NEW: Parent's Permission Section -->
        <div class="mb-3 border rounded p-3 bg-warning-subtle">
            <label class="form-label fw-bold d-block mb-2 text-dark">👪 Parent's Permission</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="parents_permission" id="permissionYes" value="Yes" required>
                <label class="form-check-label fw-bold text-success" for="permissionYes">Yes</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="parents_permission" id="permissionNo" value="No">
                <label class="form-check-label fw-bold text-danger" for="permissionNo">No</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="parents_permission" id="permissionPending" value="Pending" checked>
                <label class="form-check-label fw-bold text-secondary" for="permissionPending">Pending</label>
            </div>
            <small class="text-muted d-block mt-2 fst-italic">* Choose based on your parent's consent status. "Pending" means you'll submit proof later.</small>
        </div>
        <!-- END Parent's Permission Section -->

        <button type="submit" class="btn btn-primary fw-bold">Submit Request</button>
    </form>
</div>

<?php include('../includes/footer.php'); ?>
