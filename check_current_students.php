<?php
include('includes/dbconn.php');

$query = "SELECT name, student_id FROM student ORDER BY student_id";
$result = $conn->query($query);

echo "<h3>Current Student Records:</h3>";
echo "<pre>";
while ($row = $result->fetch_assoc()) {
    echo "Name: " . str_pad($row['name'], 30) . " ID: " . $row['student_id'] . "\n";
}
echo "</pre>";

$conn->close();
?> 