<!-- warden-sidebar.php -->
<link rel="stylesheet" href="../assets/css/style.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 240px;
    height: 100vh;
    background: #1e1e2f;
    color: #fff;
    padding-top: 60px;
    overflow-y: auto;
    z-index: 1000;
}

.sidebar a {
    display: block;
    color: #ddd;
    padding: 12px 20px;
    text-decoration: none;
    font-size: 16px;
    transition: all 0.3s;
    border-radius: 4px;
    margin: 4px 8px;
}

.sidebar a:hover, .sidebar a.active {
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
}

.sidebar h4 {
    text-align: center;
    margin-bottom: 20px;
    color: #fff;
    font-size: 24px;
    padding: 0 20px;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 10px;
}

.nav-link i {
    width: 20px;
    text-align: center;
    font-size: 18px;
}

.nav-link.text-danger {
    color: #ff5b5b !important;
}

.nav-link.text-danger:hover {
    background: rgba(255, 91, 91, 0.1);
}
</style>

<div class="sidebar">
    <h4>Warden Panel</h4>
    <a href="../warden/dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
        <i class="fas fa-chart-line"></i>
        <span>Dashboard</span>
    </a>
    <a href="../warden/manage-rooms.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage-rooms.php' ? 'active' : ''; ?>">
        <i class="fas fa-bed"></i>
        <span>Manage Rooms</span>
    </a>
    <a href="../warden/complaints.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'complaints.php' ? 'active' : ''; ?>">
        <i class="fas fa-exclamation-circle"></i>
        <span>Complaints</span>
    </a>
    <a href="../warden/visitor-requests.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'visitor-requests.php' ? 'active' : ''; ?>">
        <i class="fas fa-users"></i>
        <span>Visitor Requests</span>
    </a>
    <a href="../warden/leave_approval.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'leave_approval.php' ? 'active' : ''; ?>">
        <i class="fas fa-calendar-check"></i>
        <span>Leave Requests</span>
    </a>
    <a href="../warden/notice-board.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'notice-board.php' ? 'active' : ''; ?>">
        <i class="fas fa-bullhorn"></i>
        <span>Notice Board</span>
    </a>
    <a href="../auth/logout.php" class="nav-link text-danger" style="margin-top: 20px;">
        <i class="fas fa-power-off"></i>
        <span>Logout</span>
    </a>
</div>
