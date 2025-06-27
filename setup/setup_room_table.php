<?php
include('../includes/dbconn.php');

// Create room table
$create_table_sql = "CREATE TABLE IF NOT EXISTS room (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(10) UNIQUE NOT NULL,
    room_type ENUM('Single', 'Double', 'Triple') NOT NULL,
    floor INT NOT NULL,
    block CHAR(1) NOT NULL,
    availability ENUM('Available', 'Occupied', 'Maintenance') DEFAULT 'Available',
    fees DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($create_table_sql)) {
    echo "Room table created successfully!<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
    exit;
}

// Clear existing rooms first
$conn->query("TRUNCATE TABLE room");

// Add sample rooms with fees
$sample_rooms = [
    [
        'room_number' => 'A101',
        'room_type' => 'Double',
        'floor' => 1,
        'block' => 'A',
        'availability' => 'Occupied',
        'fees' => 25000.00
    ],
    [
        'room_number' => 'A102',
        'room_type' => 'Double',
        'floor' => 1,
        'block' => 'A',
        'availability' => 'Occupied',
        'fees' => 25000.00
    ],
    [
        'room_number' => 'B101',
        'room_type' => 'Single',
        'floor' => 1,
        'block' => 'B',
        'availability' => 'Occupied',
        'fees' => 35000.00
    ],
    [
        'room_number' => 'B102',
        'room_type' => 'Single',
        'floor' => 1,
        'block' => 'B',
        'availability' => 'Occupied',
        'fees' => 35000.00
    ],
    [
        'room_number' => 'C101',
        'room_type' => 'Triple',
        'floor' => 1,
        'block' => 'C',
        'availability' => 'Occupied',
        'fees' => 20000.00
    ]
];

$insert_query = "INSERT INTO room (room_number, room_type, floor, block, availability, fees) 
                VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insert_query);

foreach ($sample_rooms as $room) {
    $stmt->bind_param("ssissd", 
        $room['room_number'],
        $room['room_type'],
        $room['floor'],
        $room['block'],
        $room['availability'],
        $room['fees']
    );
    
    if ($stmt->execute()) {
        echo "Added room {$room['room_number']} - {$room['room_type']} - ₹{$room['fees']}<br>";
    } else {
        echo "Error adding room: " . $stmt->error . "<br>";
    }
}

$stmt->close();
$conn->close();

echo "<br>Room setup completed!";
?> 