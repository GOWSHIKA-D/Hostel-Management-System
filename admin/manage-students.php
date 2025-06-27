<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include('../includes/dbconn.php');

// Handle student deletion
if (isset($_GET['delete'])) {
    $student_id = $_GET['delete'];
    $conn->query("DELETE FROM student WHERE student_id='$student_id'");
    header("Location: manage-students.php?msg=deleted");
    exit();
}

// Fetch all students
$query = "SELECT s.*, r.room_number 
          FROM student s 
          LEFT JOIN room r ON s.room_id = r.room_id 
          ORDER BY s.student_id";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students | Admin Panel</title>
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
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar p-3">
        <h4 class="text-center mb-4">Admin Panel</h4>
        <div class="nav flex-column">
            <a class="nav-link" href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a class="nav-link active" href="manage-students.php"><i class="fas fa-users"></i> Manage Students</a>
            <a class="nav-link" href="manage-wardens.php"><i class="fas fa-user-shield"></i> Manage Wardens</a>
            <a class="nav-link" href="room-management.php"><i class="fas fa-bed"></i> Room Management</a>
            <a class="nav-link" href="complaints.php"><i class="fas fa-exclamation-circle"></i> Complaints</a>
            <a class="nav-link" href="payments.php"><i class="fas fa-money-bill"></i> Payments</a>
            <a class="nav-link" href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            <a class="nav-link" href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Students</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                <i class="fas fa-plus"></i> Add New Student
            </button>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Student deleted successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Room</th>
                        <th>Mobile</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($student = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                            <td><?php echo $student['room_number'] ?? 'Not Assigned'; ?></td>
                            <td><?php echo htmlspecialchars($student['mobile']) ?: 'Not Provided'; ?></td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="editStudent('<?php echo $student['student_id']; ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteStudent('<?php echo $student['student_id']; ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="add-student.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Student ID</label>
                        <input type="text" class="form-control" name="student_id" required>
                    </div>
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label>Mobile</label>
                        <input type="text" class="form-control" name="mobile" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function deleteStudent(studentId) {
    if (confirm('Are you sure you want to delete this student?')) {
        window.location.href = `manage-students.php?delete=${studentId}`;
    }
}

function editStudent(studentId) {
    // Implement edit functionality
    window.location.href = `edit-student.php?id=${studentId}`;
}
</script>
</body>
</html>
