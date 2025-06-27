<?php
require_once(__DIR__ . '/../includes/dbconn.php');

// First, check if complaints table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'complaints'");
if ($tableCheck->num_rows == 0) {
    // Create complaints table if it doesn't exist
    $createTable = "CREATE TABLE IF NOT EXISTS complaints (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id VARCHAR(20) NOT NULL,
        category VARCHAR(50) NOT NULL,
        description TEXT NOT NULL,
        status ENUM('Pending', 'In Progress', 'Resolved', 'Rejected') DEFAULT 'Pending',
        warden_remarks TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES student(student_id)
    )";

    if ($conn->query($createTable)) {
        echo "Complaints table created successfully!<br>";
    } else {
        echo "Error creating complaints table: " . $conn->error . "<br>";
    }
} else {
    // Add missing columns if table exists
    $alterQueries = [
        "ALTER TABLE complaints MODIFY COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
        "ALTER TABLE complaints ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
    ];

    foreach ($alterQueries as $query) {
        if ($conn->query($query)) {
            echo "Successfully executed: $query<br>";
        } else {
            echo "Error executing: $query - " . $conn->error . "<br>";
        }
    }
}

// Add sample complaint if none exists
$checkComplaints = $conn->query("SELECT * FROM complaints WHERE student_id = '927623BIT001'");
if ($checkComplaints->num_rows == 0) {
    $sampleComplaint = "INSERT INTO complaints (student_id, category, description) 
                       VALUES ('927623BIT001', 'Maintenance', 'Fan not working properly')";
    
    if ($conn->query($sampleComplaint)) {
        echo "Sample complaint added successfully!<br>";
    } else {
        echo "Error adding sample complaint: " . $conn->error . "<br>";
    }
}

echo "<br>Complaints system update completed.<br>";
?> 