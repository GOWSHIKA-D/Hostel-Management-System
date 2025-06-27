<?php
include('../includes/dbconn.php');

// Room assignments
$assignments = [
    '927623BIT001' => 'A101',
    '927623BIT002' => 'A102',
    '927623BIT003' => 'B101',
    '927623BIT004' => 'B102',
    '927623BIT005' => 'C101'
];

foreach ($assignments as $student_id => $room_number) {
    // Get room_id
    $stmt = $conn->prepare("SELECT room_id FROM room WHERE room_number = ?");
    $stmt->bind_param("s", $room_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $room = $result->fetch_assoc();
    $stmt->close();

    if ($room) {
        // Update student's room assignment
        $stmt = $conn->prepare("UPDATE student SET room_id = ? WHERE student_id = ?");
        $stmt->bind_param("is", $room['room_id'], $student_id);
        
        if ($stmt->execute()) {
            echo "Assigned room $room_number to student $student_id<br>";
        } else {
            echo "Error assigning room: " . $stmt->error . "<br>";
        }
        $stmt->close();
    }
}

echo "<br>Room assignments completed!";
?> 