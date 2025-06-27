<?php
include('../includes/dbconn.php');

// First, check if the student table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'student'");
if ($tableCheck->num_rows == 0) {
    // Create student table if it doesn't exist
    $createTable = "CREATE TABLE IF NOT EXISTS student (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id VARCHAR(20) UNIQUE NOT NULL,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100),
        phone VARCHAR(20),
        department VARCHAR(100),
        batch VARCHAR(10),
        mentor_name VARCHAR(100),
        family_members TEXT,
        dob DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($createTable)) {
        echo "Student table created successfully\n";
    } else {
        echo "Error creating student table: " . $conn->error . "\n";
        exit;
    }
}

// Array of columns to check and add if missing
$columns = [
    'batch' => 'VARCHAR(10)',
    'department' => 'VARCHAR(100)',
    'mentor_name' => 'VARCHAR(100)',
    'family_members' => 'TEXT',
    'dob' => 'DATE'
];

// Get existing columns
$result = $conn->query("SHOW COLUMNS FROM student");
$existingColumns = [];
while ($row = $result->fetch_assoc()) {
    $existingColumns[] = $row['Field'];
}

// Add missing columns
foreach ($columns as $column => $type) {
    if (!in_array($column, $existingColumns)) {
        $sql = "ALTER TABLE student ADD COLUMN $column $type";
        if ($conn->query($sql)) {
            echo "Added column $column successfully\n";
        } else {
            echo "Error adding column $column: " . $conn->error . "\n";
        }
    }
}

// Update student record if it exists
$checkStudent = $conn->query("SELECT * FROM student WHERE student_id = 'STU001'");
if ($checkStudent->num_rows == 0) {
    // Insert new student record
    $insertStudent = "INSERT INTO student (
        student_id, name, department, batch, mentor_name, 
        family_members, dob
    ) VALUES (
        'STU001',
        'John Doe',
        'Computer Science',
        '2023-24',
        'Dr. Sarah Johnson',
        'Father: John Doe Sr., Mother: Jane Doe',
        '2000-01-01'
    )";
    
    if ($conn->query($insertStudent)) {
        echo "Student record created successfully\n";
    } else {
        echo "Error creating student record: " . $conn->error . "\n";
    }
} else {
    // Update existing record only if fields are empty
    $updateStudent = "UPDATE student SET 
        department = COALESCE(NULLIF(department, ''), 'Computer Science'),
        batch = COALESCE(NULLIF(batch, ''), '2023-24'),
        mentor_name = COALESCE(NULLIF(mentor_name, ''), 'Dr. Sarah Johnson'),
        family_members = COALESCE(NULLIF(family_members, ''), 'Father: John Doe Sr., Mother: Jane Doe'),
        dob = COALESCE(dob, '2000-01-01')
        WHERE student_id = 'STU001'";
    
    if ($conn->query($updateStudent)) {
        echo "Student record updated successfully\n";
    } else {
        echo "Error updating student record: " . $conn->error . "\n";
    }
}

echo "\nUpdate completed successfully!\n";
?> 