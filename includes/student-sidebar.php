<!-- student-sidebar.php -->
<link rel="stylesheet" href="../assets/css/style.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    height: 100vh;
    background: #1e1e2f;
    padding-top: 60px;
    color: #fff;
    overflow-y: auto;
}

.sidebar a {
    display: block;
    color: #ddd;
    padding: 12px 20px;
    text-decoration: none;
    font-size: 16px;
    transition: background 0.2s;
}

.sidebar a:hover {
    background: #343454;
    color: #fff;
}

.dropdown-item a {
    padding-left: 30px;
    font-size: 15px;
    color: #bbb;
}

.dropdown-toggle {
    cursor: pointer;
}

.dropdown-menu {
    padding-left: 10px;
}
</style>

<div class="sidebar p-3">
    <h4 class="text-center mb-4">📚 Student Panel</h4>
    <a href="../student/dashboard.php">🏠 Dashboard</a>

    <!-- My Profile Dropdown -->
    <div class="dropdown-item">
        <a class="dropdown-toggle" onclick="toggleDropdown('profileMenu')">👤 My Profile ⏷</a>
        <div id="profileMenu" class="dropdown-menu" style="display: none;">
            <a href="../modules/profile.php">📄 View Profile</a>
            <a href="../modules/edit-profile.php">✏️ Edit Profile</a>
            <a href="../modules/change-password.php">🔑 Change Password</a>
        </div>
    </div>

    <!-- Other Functionalities -->
    <a href="../modules/room-details.php">🛏️ Room Details</a>
    <a href="../modules/attendance.php">📅 Attendance</a>
    <a href="../student/leave_request.php">📝 Apply Leave</a>
    <a href="../modules/complaint-box.php">📢 Complaint Box</a>
    <a href="../modules/visitor-log.php">🧾 Visitor Log</a>
    <a href="../student/payments.php">💳 Payments</a>
    <a href="../modules/mess-menu.php">🍽️ Mess Menu</a>
    <a href="../modules/notice-board.php">📌 Notice Board</a>

    <a href="../auth/logout.php">🚪 Logout</a>
</div>

<script>
function toggleDropdown(id) {
    const menu = document.getElementById(id);
    menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
}
</script>
