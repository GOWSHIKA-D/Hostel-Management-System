<?php
require_once(__DIR__ . '/../includes/config.php');

try {
    // Read the SQL file
    $sql = file_get_contents(__DIR__ . '/database.sql');
    
    // Execute the SQL commands
    $pdo->exec($sql);
    
    echo "Database setup completed successfully!\n";
    echo "Default warden credentials:\n";
    echo "Email: warden@hostel.com\n";
    echo "Password: password\n";
} catch (PDOException $e) {
    die("Database setup failed: " . $e->getMessage() . "\n");
}
?> 