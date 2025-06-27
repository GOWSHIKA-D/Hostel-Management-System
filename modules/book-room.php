<?php
include('../includes/dbconn.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['room_number'])) {
    $roomNumber = $_POST['room_number'];
    $studentId = $_SESSION['student_id']; // assuming student_id is stored in session

    // Update room availability
    $updateQuery = "UPDATE room SET availability = 'Occupied' WHERE room_number = '$roomNumber'";
    $updateResult = mysqli_query($conn, $updateQuery);

    if ($updateResult) {
        // Insert booking record
        $insertQuery = "INSERT INTO room_bookings (student_id, room_number, booked_on) VALUES ('$studentId', '$roomNumber', NOW())";
        mysqli_query($conn, $insertQuery);

        // ✅ Redirect to roomdetails.php with success message
        header("Location: ../student/roomdetails.php?booked=true");
        exit();
    } else {
        echo "Error booking the room.";
    }
} else {
    echo "Invalid request.";
}
?>
