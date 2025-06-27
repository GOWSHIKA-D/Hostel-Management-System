<?php
require_once(__DIR__ . '/../includes/config.php');

try {
    // Check if system warden already exists
    $stmt = $pdo->prepare("SELECT id FROM wardens WHERE email = 'system@hostel.admin'");
    $stmt->execute();
    $system_warden = $stmt->fetch();

    if (!$system_warden) {
        // Create system warden
        $stmt = $pdo->prepare("
            INSERT INTO wardens (name, email, password, created_at) 
            VALUES ('System Admin', 'system@hostel.admin', 'NOT_FOR_LOGIN', NOW())
        ");
        $stmt->execute();
        echo "System warden account created successfully!";
    } else {
        echo "System warden account already exists.";
    }

    // Get the system warden ID
    $stmt = $pdo->prepare("SELECT id FROM wardens WHERE email = 'system@hostel.admin'");
    $stmt->execute();
    $system_warden = $stmt->fetch();
    
    if ($system_warden) {
        // Create a constant file to store the system warden ID
        $constant_content = "<?php\ndefine('SYSTEM_WARDEN_ID', " . $system_warden['id'] . ");\n?>";
        file_put_contents(__DIR__ . '/../includes/system_constants.php', $constant_content);
        echo "\nSystem constants file created successfully!";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 