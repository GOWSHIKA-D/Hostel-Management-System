<?php
require_once(__DIR__ . '/../includes/dbconn.php');

// Create leave_requests table
$createTable = "CREATE TABLE IF NOT EXISTS leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL,
    leave_type VARCHAR(50) NOT NULL,
    from_date DATE NOT NULL,
    to_date DATE NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    warden_remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES student(student_id)
)";

if ($conn->query($createTable)) {
    echo "Leave requests table created successfully!<br>";
} else {
    echo "Error creating leave requests table: " . $conn->error . "<br>";
}

// Add sample leave request
$sampleLeave = "INSERT INTO leave_requests (student_id, leave_type, from_date, to_date, reason) 
                VALUES ('927623BIT001', 'Personal', '2024-03-20', '2024-03-22', 'Family function')";

if ($conn->query($sampleLeave)) {
    echo "Sample leave request added successfully!<br>";
} else {
    echo "Error adding sample leave request: " . $conn->error . "<br>";
}

echo "<br>Leave system setup completed.<br>";
?> 