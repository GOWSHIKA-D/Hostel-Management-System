<?php
session_start();
include('../includes/dbconn.php');

if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Debug output
echo "<!-- Debug: Using student ID: " . $student_id . " -->";

// Get selected month or default to current month
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('m');
$selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// First check if the attendance table exists
$table_exists = $conn->query("SHOW TABLES LIKE 'attendance'");
if ($table_exists->num_rows == 0) {
    die("Please run <a href='../setup_attendance.php'>setup_attendance.php</a> first to create the attendance table.");
}

// Debug: Check what records exist for this student
$debug_query = "SELECT COUNT(*) as count FROM attendance WHERE student_id = ?";
$stmt = $conn->prepare($debug_query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$debug_result = $stmt->get_result()->fetch_assoc();
echo "<!-- Debug: Found " . $debug_result['count'] . " attendance records for student -->";

// Calculate attendance percentage for the selected month
$query = "SELECT 
    COUNT(*) as total_days,
    SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_days
FROM attendance 
WHERE student_id = ? 
AND MONTH(date) = ? 
AND YEAR(date) = ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error preparing query: " . $conn->error);
}

$stmt->bind_param("sss", $student_id, $selected_month, $selected_year);
$stmt->execute();
$result = $stmt->get_result();
$attendance = $result->fetch_assoc();

// Debug output
echo "<!-- Debug: Query results - ";
print_r($attendance);
echo " -->";

$total_days = $attendance['total_days'] ?? 0;
$present_days = $attendance['present_days'] ?? 0;
$attendance_percentage = $total_days > 0 ? round(($present_days / $total_days) * 100, 2) : 0;

// Get attendance details for the selected month
$query = "SELECT date, status, remark 
          FROM attendance 
          WHERE student_id = ? 
          AND MONTH(date) = ? 
          AND YEAR(date) = ?
          ORDER BY date";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error preparing query: " . $conn->error);
}

$stmt->bind_param("sss", $student_id, $selected_month, $selected_year);
$stmt->execute();
$attendance_details = $stmt->get_result();

// Debug output
echo "<!-- Debug: Found " . $attendance_details->num_rows . " detailed records for month -->";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .main-content {
            margin-left: 250px;
            padding: 2rem;
            background: #f8f9fa;
            min-height: 100vh;
        }
        .month-card {
            cursor: pointer;
            transition: transform 0.2s;
            height: 100%;
        }
        .month-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .selected-month {
            border: 2px solid #007bff;
            box-shadow: 0 0 15px rgba(0,123,255,0.2);
        }
        .attendance-percentage {
            font-size: 2.5rem;
            font-weight: bold;
            color: #28a745;
        }
        .status-present { color: #28a745; font-weight: 600; }
        .status-absent { color: #dc3545; font-weight: 600; }
        .status-leave { color: #ffc107; font-weight: 600; }
        .stats-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            height: 100%;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<?php include('../includes/student-sidebar.php'); ?>

<div class="main-content">
    <h2 class="mb-4">Attendance Record</h2>

    <!-- Month Selection -->
    <div class="row g-4 mb-4">
        <?php
        $months = [
            '01' => 'January', '02' => 'February', '03' => 'March',
            '04' => 'April', '05' => 'May', '06' => 'June',
            '07' => 'July', '08' => 'August', '09' => 'September',
            '10' => 'October', '11' => 'November', '12' => 'December'
        ];

        foreach ($months as $month_num => $month_name) {
            $selected = $month_num == $selected_month ? 'selected-month' : '';
            echo "<div class='col-md-3 mb-3'>
                    <div class='card month-card $selected' onclick='window.location.href=\"?month=$month_num&year=$selected_year\"'>
                        <div class='card-body text-center'>
                            <h5 class='card-title'>$month_name</h5>
                        </div>
                    </div>
                  </div>";
        }
        ?>
    </div>

    <!-- Attendance Summary -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stats-card">
                <div class="card-body text-center">
                    <h5 class="card-title">Attendance Percentage</h5>
                    <div class="attendance-percentage"><?php echo $attendance_percentage; ?>%</div>
                    <p class="card-text">
                        Present: <?php echo $present_days; ?> days<br>
                        Total: <?php echo $total_days; ?> days
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Details -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Detailed Attendance - <?php echo $months[$selected_month] . ' ' . $selected_year; ?></h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($attendance_details->num_rows > 0): ?>
                            <?php while ($row = $attendance_details->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('d M Y', strtotime($row['date'])); ?></td>
                                    <td>
                                        <span class="status-<?php echo strtolower($row['status']); ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $row['remark'] ?? '-'; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">No attendance records found for this month</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
