<?php
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Get current page name
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<div class="sidebar">
    <div class="sidebar-header">
        <h3>🏢 Hostel IMS</h3>
        <p>Admin Panel</p>
    </div>
    
    <nav class="sidebar-nav">
        <a href="../admin/dashboard.php" class="nav-link <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        
        <a href="../admin/students.php" class="nav-link <?php echo $current_page === 'students' ? 'active' : ''; ?>">
            <i class="fas fa-user-graduate"></i> Students
        </a>
        
        <a href="../admin/rooms.php" class="nav-link <?php echo $current_page === 'rooms' ? 'active' : ''; ?>">
            <i class="fas fa-door-open"></i> Rooms
        </a>
        
        <a href="../admin/wardens.php" class="nav-link <?php echo $current_page === 'wardens' ? 'active' : ''; ?>">
            <i class="fas fa-user-shield"></i> Wardens
        </a>
        
        <a href="../admin/payments.php" class="nav-link <?php echo $current_page === 'payments' ? 'active' : ''; ?>">
            <i class="fas fa-money-bill-wave"></i> Payments
        </a>
        
        <a href="../admin/complaints.php" class="nav-link <?php echo $current_page === 'complaints' ? 'active' : ''; ?>">
            <i class="fas fa-exclamation-circle"></i> Complaints
        </a>
        
        <a href="../admin/notices.php" class="nav-link <?php echo $current_page === 'notices' ? 'active' : ''; ?>">
            <i class="fas fa-bullhorn"></i> Notices
        </a>
        
        <a href="../admin/settings.php" class="nav-link <?php echo $current_page === 'settings' ? 'active' : ''; ?>">
            <i class="fas fa-cog"></i> Settings
        </a>
        
        <a href="../auth/logout.php" class="nav-link text-danger">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</div>

<style>
.sidebar {
    width: 250px;
    height: 100vh;
    background: #2c3e50;
    color: #fff;
    position: fixed;
    left: 0;
    top: 0;
    padding: 20px 0;
}

.sidebar-header {
    padding: 0 20px 20px;
    border-bottom: 1px solid #34495e;
}

.sidebar-header h3 {
    margin: 0;
    font-size: 20px;
    color: #ecf0f1;
}

.sidebar-header p {
    margin: 5px 0 0;
    font-size: 14px;
    color: #95a5a6;
}

.sidebar-nav {
    padding: 20px 0;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #ecf0f1;
    text-decoration: none;
    transition: all 0.3s ease;
}

.nav-link:hover {
    background: #34495e;
    color: #3498db;
}

.nav-link.active {
    background: #34495e;
    color: #3498db;
    border-left: 4px solid #3498db;
}

.nav-link i {
    width: 20px;
    margin-right: 10px;
}

.text-danger {
    color: #e74c3c !important;
}

.text-danger:hover {
    background: #c0392b !important;
    color: #fff !important;
}
</style> 