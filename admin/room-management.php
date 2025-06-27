<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include('../includes/dbconn.php');

// Handle room deletion
if (isset($_GET['delete'])) {
    $room_id = $_GET['delete'];
    $conn->query("DELETE FROM room WHERE room_id='$room_id'");
    header("Location: room-management.php?msg=deleted");
    exit();
}

// Handle room addition
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_number = mysqli_real_escape_string($conn, $_POST['room_number']);
    $room_type = mysqli_real_escape_string($conn, $_POST['room_type']);
    $capacity = mysqli_real_escape_string($conn, $_POST['capacity']);
    $fees = mysqli_real_escape_string($conn, $_POST['fees']);

    $query = "INSERT INTO room (room_number, room_type, capacity, fees, availability) 
              VALUES ('$room_number', '$room_type', '$capacity', '$fees', 'Available')";
    
    if ($conn->query($query)) {
        header("Location: room-management.php?msg=added");
        exit();
    }
}

// Fetch all rooms with occupancy info
$query = "SELECT r.*, 
          COUNT(s.student_id) as occupied_beds,
          GROUP_CONCAT(s.name SEPARATOR ', ') as occupants
          FROM room r
          LEFT JOIN student s ON r.room_id = s.room_id
          GROUP BY r.room_id
          ORDER BY r.room_number";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Room Management | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
        }
        .sidebar {
            height: 100vh;
            background: #1a237e;
            color: white;
            position: fixed;
            width: 250px;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 4px 0;
        }
        .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.1);
        }
        .nav-link i {
            width: 25px;
        }
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .room-status {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .status-available { background: #e8f5e9; color: #2e7d32; }
        .status-partial { background: #fff3e0; color: #ef6c00; }
        .status-full { background: #ffebee; color: #c62828; }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar p-3">
        <h4 class="text-center mb-4">Admin Panel</h4>
        <div class="nav flex-column">
            <a class="nav-link" href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a class="nav-link" href="manage-students.php"><i class="fas fa-users"></i> Manage Students</a>
            <a class="nav-link" href="manage-wardens.php"><i class="fas fa-user-shield"></i> Manage Wardens</a>
            <a class="nav-link active" href="room-management.php"><i class="fas fa-bed"></i> Room Management</a>
            <a class="nav-link" href="complaints.php"><i class="fas fa-exclamation-circle"></i> Complaints</a>
            <a class="nav-link" href="payments.php"><i class="fas fa-money-bill"></i> Payments</a>
            <a class="nav-link" href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            <a class="nav-link" href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Room Management</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                <i class="fas fa-plus"></i> Add New Room
            </button>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                    if ($_GET['msg'] === 'added') echo "Room added successfully!";
                    if ($_GET['msg'] === 'deleted') echo "Room deleted successfully!";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Room Number</th>
                        <th>Type</th>
                        <th>Capacity</th>
                        <th>Occupancy</th>
                        <th>Status</th>
                        <th>Fees</th>
                        <th>Occupants</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($room = $result->fetch_assoc()): 
                        $occupancy_percentage = ($room['capacity'] > 0) ? ($room['occupied_beds'] / $room['capacity']) * 100 : 0;
                        if ($occupancy_percentage == 0) {
                            $status_class = 'status-available';
                            $status_text = 'Available';
                        } elseif ($occupancy_percentage < 100) {
                            $status_class = 'status-partial';
                            $status_text = 'Partially Filled';
                        } else {
                            $status_class = 'status-full';
                            $status_text = 'Full';
                        }
                    ?>
                        <tr>
                            <td><?php echo $room['room_number']; ?></td>
                            <td><?php echo $room['room_type']; ?></td>
                            <td><?php echo $room['capacity']; ?></td>
                            <td><?php echo $room['occupied_beds'] . '/' . $room['capacity']; ?></td>
                            <td><span class="room-status <?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                            <td>₹<?php echo isset($room['fees']) ? number_format($room['fees']) : '0'; ?></td>
                            <td><?php echo $room['occupants'] ?: 'None'; ?></td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="editRoom('<?php echo $room['room_id']; ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if ($room['occupied_beds'] == 0): ?>
                                    <button class="btn btn-sm btn-danger" onclick="deleteRoom('<?php echo $room['room_id']; ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Room Modal -->
<div class="modal fade" id="addRoomModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Room Number</label>
                        <input type="text" class="form-control" name="room_number" required>
                    </div>
                    <div class="mb-3">
                        <label>Room Type</label>
                        <select class="form-control" name="room_type" required>
                            <option value="Single">Single</option>
                            <option value="Double">Double</option>
                            <option value="Triple">Triple</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Capacity</label>
                        <input type="number" class="form-control" name="capacity" required>
                    </div>
                    <div class="mb-3">
                        <label>Fees (per semester)</label>
                        <input type="number" class="form-control" name="fees" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Room</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function deleteRoom(roomId) {
    if (confirm('Are you sure you want to delete this room?')) {
        window.location.href = `room-management.php?delete=${roomId}`;
    }
}

function editRoom(roomId) {
    window.location.href = `edit-room.php?id=${roomId}`;
}
</script>
</body>
</html>
