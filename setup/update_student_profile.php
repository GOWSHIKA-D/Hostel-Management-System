<?php
require_once(__DIR__ . '/../includes/dbconn.php');

// Add missing columns to student table
$alterQueries = [
    "ALTER TABLE student ADD COLUMN IF NOT EXISTS gender VARCHAR(10)",
    "ALTER TABLE student ADD COLUMN IF NOT EXISTS languages_known TEXT",
    "ALTER TABLE student ADD COLUMN IF NOT EXISTS blood_group VARCHAR(5)",
    "ALTER TABLE student ADD COLUMN IF NOT EXISTS aadhar_number VARCHAR(20)",
    "ALTER TABLE student ADD COLUMN IF NOT EXISTS communication_address TEXT",
    "ALTER TABLE student ADD COLUMN IF NOT EXISTS permanent_address TEXT",
    "ALTER TABLE student ADD COLUMN IF NOT EXISTS profile_picture VARCHAR(255)",
    "ALTER TABLE student ADD COLUMN IF NOT EXISTS room_number VARCHAR(10)"
];

foreach ($alterQueries as $query) {
    if ($conn->query($query)) {
        echo "Successfully executed: $query<br>";
    } else {
        echo "Error executing: $query - " . $conn->error . "<br>";
    }
}

// Update student record with sample data
$updateQuery = "UPDATE student SET 
    gender = COALESCE(gender, 'Male'),
    languages_known = COALESCE(languages_known, 'English, Hindi'),
    blood_group = COALESCE(blood_group, 'O+'),
    aadhar_number = COALESCE(aadhar_number, '1234-5678-9012'),
    communication_address = COALESCE(communication_address, 'Room 101, Boys Hostel'),
    permanent_address = COALESCE(permanent_address, '123 Main Street, City'),
    profile_picture = COALESCE(profile_picture, 'default.jpg'),
    room_number = COALESCE(room_number, 'A101')
WHERE student_id = '927623BIT001'";

if ($conn->query($updateQuery)) {
    echo "<br>Student profile updated successfully!<br>";
} else {
    echo "<br>Error updating student profile: " . $conn->error . "<br>";
}

echo "<br>Profile update completed.<br>";
?> 