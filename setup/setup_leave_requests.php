<?php
include('../includes/dbconn.php');

// Create leave_requests table
$create_table_sql = "CREATE TABLE IF NOT EXISTS leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL,
    leave_type ENUM('Medical', 'Personal', 'Family Emergency', 'Other') NOT NULL,
    from_date DATE NOT NULL,
    to_date DATE NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    warden_remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES student(student_id)
)";

if ($conn->query($create_table_sql)) {
    echo "Leave requests table created successfully!<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
    exit;
}

// Add sample leave requests
$sample_leaves = [
    [
        'student_id' => '927623BIT001',
        'leave_type' => 'Medical',
        'from_date' => '2024-05-25',
        'to_date' => '2024-05-28',
        'reason' => 'Medical appointment and treatment',
        'status' => 'Pending'
    ],
    [
        'student_id' => '927623BIT002',
        'leave_type' => 'Family Emergency',
        'from_date' => '2024-05-26',
        'to_date' => '2024-05-29',
        'reason' => 'Sister\'s wedding ceremony',
        'status' => 'Approved',
        'warden_remarks' => 'Approved based on valid reason and good conduct'
    ],
    [
        'student_id' => '927623BIT003',
        'leave_type' => 'Personal',
        'from_date' => '2024-05-27',
        'to_date' => '2024-05-28',
        'reason' => 'Attending a family function',
        'status' => 'Rejected',
        'warden_remarks' => 'Too many leaves taken this month'
    ],
    [
        'student_id' => '927623BIT004',
        'leave_type' => 'Medical',
        'from_date' => '2024-05-28',
        'to_date' => '2024-05-30',
        'reason' => 'Dental surgery and recovery',
        'status' => 'Approved',
        'warden_remarks' => 'Medical documents verified'
    ],
    [
        'student_id' => '927623BIT005',
        'leave_type' => 'Other',
        'from_date' => '2024-05-29',
        'to_date' => '2024-05-31',
        'reason' => 'Attending competitive exam',
        'status' => 'Pending'
    ]
];

// Clear existing data
$conn->query("DELETE FROM leave_requests");

$insert_query = "INSERT INTO leave_requests (student_id, leave_type, from_date, to_date, reason, status, warden_remarks) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insert_query);

foreach ($sample_leaves as $leave) {
    $remarks = isset($leave['warden_remarks']) ? $leave['warden_remarks'] : NULL;
    
    $stmt->bind_param("sssssss", 
        $leave['student_id'],
        $leave['leave_type'],
        $leave['from_date'],
        $leave['to_date'],
        $leave['reason'],
        $leave['status'],
        $remarks
    );
    
    if ($stmt->execute()) {
        echo "Added leave request for student {$leave['student_id']}<br>";
    } else {
        echo "Error adding leave request: " . $stmt->error . "<br>";
    }
}

$stmt->close();
$conn->close();
echo "Setup completed!";
?> 