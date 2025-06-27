<?php
include('../includes/dbconn.php');

// Read and execute the SQL file
$sql = file_get_contents('admin_table.sql');

if ($conn->multi_query($sql)) {
    echo "✅ Admin table created and default admin account set up successfully!\n";
    echo "\nDefault Admin Credentials:\n";
    echo "Admin ID: admin\n";
    echo "Password: admin123\n";
} else {
    echo "❌ Error executing SQL: " . $conn->error;
}

$conn->close();
?> 