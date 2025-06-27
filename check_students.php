<?php
include('includes/dbconn.php');

$query = "SELECT student_id, name FROM student";
$result = $conn->query($query);

echo "<h3>Available Students:</h3>";
echo "<pre>";
while ($row = $result->fetch_assoc()) {
    echo "Student ID: " . $row['student_id'] . " - Name: " . $row['name'] . "\n";
}
echo "</pre>";

$conn->close();
?> 