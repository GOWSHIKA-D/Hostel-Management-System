<?php
require_once('../includes/config.php');

try {
    // First, drop the existing foreign key constraint
    $pdo->exec("ALTER TABLE payments DROP FOREIGN KEY payments_ibfk_2");
    
    // Add creator_type column
    $pdo->exec("ALTER TABLE payments ADD COLUMN creator_type ENUM('admin', 'warden') NOT NULL AFTER created_by");
    
    // Update existing records to set warden as creator_type
    $pdo->exec("UPDATE payments SET creator_type = 'warden' WHERE created_by IS NOT NULL");
    
    echo "Successfully updated payments table structure!";
} catch (PDOException $e) {
    echo "Error updating payments table: " . $e->getMessage();
}
?> 