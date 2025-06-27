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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'];
    $description = $_POST['description'];
    $query = "INSERT INTO complaints (student_id, category, description, status, created_at) VALUES (?, ?, ?, 'Pending', NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $student_id, $category, $description);
    $stmt->execute();
    $stmt->close();
}

// Fetch complaints
$complaintsQuery = "SELECT * FROM complaints WHERE student_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($complaintsQuery);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculate progress
$total = 0; $resolved = 0;
$complaints = [];
while ($row = $result->fetch_assoc()) {
    $complaints[] = $row;
    $total++;
    if ($row['status'] === 'Resolved') $resolved++;
}
$progress = ($total > 0) ? round(($resolved / $total) * 100, 2) : 0;

$stmt->close();
?>

<div class="main-content" style="margin-left: 250px; padding: 30px; background: #f0f2f5;">
    <h2 class="mb-4 fw-bold text-dark">🛠 Complaint Box</h2>

    <!-- Progress Bar -->
    <div class="mb-4">
        <label class="fw-bold">Resolution Progress:</label>
        <div class="progress" style="height: 25px;">
            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $progress ?>%;">
                Resolved <?= $progress ?>%
            </div>
            <div class="progress-bar bg-warning" role="progressbar" style="width: <?= 100 - $progress ?>%;">
                Pending <?= 100 - $progress ?>%
            </div>
        </div>
    </div>

    <!-- Complaint Form -->
    <div class="card p-4 mb-4 shadow-sm">
        <h5 class="fw-bold mb-3">➕ Submit a New Complaint</h5>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Category</label>
                <select name="category" class="form-control" required>
                    <option value="">-- Select Category --</option>
                    <option value="Room Issue">Room Issue</option>
                    <option value="Mess Food">Mess Food</option>
                    <option value="Cleanliness">Cleanliness</option>
                    <option value="Internet/WiFi">Internet/WiFi</option>
                    <option value="Others">Others</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Description</label>
                <textarea name="description" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary fw-bold">Submit Complaint 🚀</button>
        </form>
    </div>

    <!-- Complaint History -->
    <div class="card p-4 shadow-sm">
        <h5 class="fw-bold mb-3">📜 Your Complaint History</h5>
        <?php if (count($complaints) > 0): ?>
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($complaints as $c): 
                        $badge = match ($c['status']) {
                            'Pending' => 'warning',
                            'In Progress' => 'info',
                            'Resolved' => 'success',
                            default => 'secondary'
                        };
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($c['category']) ?></td>
                            <td><?= nl2br(htmlspecialchars($c['description'])) ?></td>
                            <td><span class="badge bg-<?= $badge ?>"><?= $c['status'] ?></span></td>
                            <td><?= date('d M Y, H:i', strtotime($c['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">No complaints submitted yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
