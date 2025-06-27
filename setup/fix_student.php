<?php
require_once(__DIR__ . '/../includes/dbconn.php');

// Add missing columns if they don't exist
$alterQueries = [
    "ALTER TABLE student ADD COLUMN IF NOT EXISTS batch VARCHAR(10) DEFAULT '2023-24'",
    "ALTER TABLE student ADD COLUMN IF NOT EXISTS department VARCHAR(100) DEFAULT 'Computer Science'",
    "ALTER TABLE student ADD COLUMN IF NOT EXISTS mentor_name VARCHAR(100) DEFAULT 'Dr. Sarah Johnson'",
    "ALTER TABLE student ADD COLUMN IF NOT EXISTS family_members TEXT",
    "ALTER TABLE student ADD COLUMN IF NOT EXISTS dob DATE DEFAULT '2000-01-01'"
];

foreach ($alterQueries as $query) {
    if ($conn->query($query)) {
        echo "Successfully executed: $query<br>";
    } else {
        echo "Error executing: $query - " . $conn->error . "<br>";
    }
}

// Update student record with default values where fields are NULL
$updateQuery = "UPDATE student SET 
    batch = COALESCE(batch, '2023-24'),
    department = COALESCE(department, 'Computer Science'),
    mentor_name = COALESCE(mentor_name, 'Dr. Sarah Johnson'),
    family_members = COALESCE(family_members, 'Not Specified'),
    dob = COALESCE(dob, '2000-01-01')
WHERE student_id = '927623BIT001'";

if ($conn->query($updateQuery)) {
    echo "<br>Student record updated successfully!<br>";
} else {
    echo "<br>Error updating student record: " . $conn->error . "<br>";
}

echo "<br>Student table update completed.<br>";
?> 