<?php
include('../includes/dbconn.php');

// Add missing columns to student table if they don't exist
$alterQueries = [
    "ALTER TABLE student ADD COLUMN IF NOT EXISTS batch VARCHAR(10)",
    "ALTER TABLE student ADD COLUMN IF NOT EXISTS department VARCHAR(100)",
    "ALTER TABLE student ADD COLUMN IF NOT EXISTS mentor_name VARCHAR(100)",
    "ALTER TABLE student ADD COLUMN IF NOT EXISTS family_members TEXT",
    "ALTER TABLE student ADD COLUMN IF NOT EXISTS dob DATE"
];

foreach ($alterQueries as $query) {
    if ($conn->query($query)) {
        echo "Successfully executed: $query\n";
    } else {
        echo "Error executing: $query\n" . $conn->error . "\n";
    }
}

// Update the existing student record with complete information
$updateQuery = "UPDATE student SET 
    batch = '2023-24',
    department = 'Computer Science',
    mentor_name = 'Dr. Sarah Johnson',
    family_members = 'Father: John Doe Sr., Mother: Jane Doe',
    dob = '2000-01-01'
WHERE student_id = 'STU001'";

if ($conn->query($updateQuery)) {
    echo "\nStudent record updated successfully!\n";
} else {
    echo "\nError updating student record: " . $conn->error . "\n";
}

echo "\nStudent table update completed.\n";
?> 