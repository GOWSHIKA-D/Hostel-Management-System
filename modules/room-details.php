<?php
session_start();
include('../includes/dbconn.php');

if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Get all rooms with their details and occupancy
$query = "SELECT r.*, 
          COUNT(s.student_id) as current_occupants,
          GROUP_CONCAT(s.name SEPARATOR ', ') as roommates
          FROM room r
          LEFT JOIN student s ON r.room_id = s.room_id
          GROUP BY r.room_id
          ORDER BY r.room_number";
$result = $conn->query($query);

// Get student's current room
$student_query = "SELECT r.*, s.room_id 
                 FROM student s 
                 LEFT JOIN room r ON s.room_id = r.room_id 
                 WHERE s.student_id = ?";
$stmt = $conn->prepare($student_query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$student_room = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Room Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .main-content {
            margin-left: 250px;
            padding: 2rem;
            background: #f8f9fa;
            min-height: 100vh;
        }
        .room-card {
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .room-card:hover {
            transform: translateY(-5px);
        }
        .room-status {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .status-available { background: #d4edda; color: #155724; }
        .status-occupied { background: #f8d7da; color: #721c24; }
        .status-maintenance { background: #fff3cd; color: #856404; }
    </style>
</head>
<body>

<?php include('../includes/student-sidebar.php'); ?>

<div class="main-content">
    <h2 class="mb-4">Room Details</h2>

    <!-- Current Room Info -->
    <?php if ($student_room): ?>
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title">Your Current Room</h4>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Room Number:</strong> <?php echo $student_room['room_number']; ?></p>
                    <p><strong>Room Type:</strong> <?php echo $student_room['room_type']; ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Floor:</strong> <?php echo $student_room['floor']; ?></p>
                    <p><strong>Monthly Rent:</strong> ₹<?php echo $student_room['fees']; ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- All Rooms -->
    <div class="row g-4">
        <?php while ($room = $result->fetch_assoc()): ?>
            <div class="col-md-4">
                <div class="card room-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Room <?php echo $room['room_number']; ?></h5>
                            <span class="room-status status-<?php echo strtolower($room['availability']); ?>">
                                <?php echo $room['availability']; ?>
                            </span>
                        </div>
                        <p class="card-text">
                            <strong>Type:</strong> <?php echo $room['room_type']; ?><br>
                            <strong>Floor:</strong> <?php echo $room['floor']; ?><br>
                            <strong>Monthly Rent:</strong> ₹<?php echo $room['fees']; ?><br>
                            <strong>Occupants:</strong> <?php echo $room['current_occupants']; ?>/<?php echo $room['capacity']; ?>
                        </p>
                        <?php if ($room['roommates']): ?>
                            <small class="text-muted">Current residents: <?php echo $room['roommates']; ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
