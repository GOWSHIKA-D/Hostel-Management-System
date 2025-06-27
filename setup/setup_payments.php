<?php
include('../includes/dbconn.php');

// First, check if payments table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'payments'");
if ($tableCheck->num_rows == 0) {
    // Create payments table if it doesn't exist
    $createTable = "CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id VARCHAR(20) NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        payment_method ENUM('Cash', 'Cheque', 'DD', 'UPI', 'Net Banking', 'Credit Card', 'Debit Card') NOT NULL,
        reference_no VARCHAR(50) NOT NULL,
        status ENUM('Pending', 'Verified', 'Rejected') DEFAULT 'Pending',
        semester VARCHAR(20) NOT NULL,
        description TEXT,
        payment_date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        verified_at TIMESTAMP NULL,
        payment_details JSON,
        FOREIGN KEY (student_id) REFERENCES student(student_id)
    )";

    if ($conn->query($createTable)) {
        echo "Payments table created successfully!<br>";
    } else {
        echo "Error creating payments table: " . $conn->error . "<br>";
        exit;
    }
} else {
    // If table exists, add payment_details column if it doesn't exist
    $columnCheck = $conn->query("SHOW COLUMNS FROM payments LIKE 'payment_details'");
    if ($columnCheck->num_rows == 0) {
        $alterTable = "ALTER TABLE payments ADD COLUMN payment_details JSON AFTER verified_at";
        if ($conn->query($alterTable)) {
            echo "Added payment_details column to payments table<br>";
        } else {
            echo "Error adding payment_details column: " . $conn->error . "<br>";
        }
    }
}

// Sample payment data
$sample_payments = [
    [
        'student_id' => '927623BIT001',
        'amount' => 25000.00,
        'payment_method' => 'UPI',
        'reference_no' => 'UPI123456789',
        'status' => 'Verified',
        'semester' => 'Fall 2023',
        'description' => 'Hostel Fee Payment for Fall 2023',
        'payment_date' => '2023-12-01',
        'created_at' => '2023-12-01 10:30:00',
        'verified_at' => '2023-12-01 14:15:00',
        'payment_details' => json_encode([
            'convenience_fee' => 250.00,
            'gst' => 45.00,
            'total_amount' => 25295.00,
            'mobile' => '9876543210',
            'email' => 'student1@example.com',
            'payer_name' => 'John Doe',
            'cheque_details' => []
        ])
    ],
    [
        'student_id' => '927623BIT002',
        'amount' => 25000.00,
        'payment_method' => 'Cheque',
        'reference_no' => 'CHQ987654321',
        'status' => 'Pending',
        'semester' => 'Fall 2023',
        'description' => 'Hostel Fee Payment for Fall 2023',
        'payment_date' => '2023-12-02',
        'created_at' => '2023-12-02 11:45:00',
        'verified_at' => NULL,
        'payment_details' => json_encode([
            'convenience_fee' => 0,
            'gst' => 0,
            'total_amount' => 25000.00,
            'mobile' => '9876543211',
            'email' => 'student2@example.com',
            'payer_name' => 'Jane Smith',
            'cheque_details' => [
                'drawer_name' => 'Jane Smith',
                'drawee_name' => 'INSTITUTE OF INFORMATION AND COMMUNICATION TECHNOLOGY',
                'cheque_date' => '2023-12-02',
                'cheque_number' => 'CHQ123456'
            ]
        ])
    ],
    [
        'student_id' => '927623BIT003',
        'amount' => 25000.00,
        'payment_method' => 'Credit Card',
        'reference_no' => 'CC456789123',
        'status' => 'Verified',
        'semester' => 'Fall 2023',
        'created_at' => '2023-12-03 09:15:00',
        'verified_at' => '2023-12-03 13:20:00'
    ],
    [
        'student_id' => '927623BIT001',
        'amount' => 12500.00,
        'payment_method' => 'UPI',
        'reference_no' => 'UPI987123456',
        'status' => 'Pending',
        'semester' => 'Spring 2024',
        'created_at' => '2024-01-05 15:30:00',
        'verified_at' => NULL
    ],
    [
        'student_id' => '927623BIT004',
        'amount' => 25000.00,
        'payment_method' => 'Debit Card',
        'reference_no' => 'DC789123456',
        'status' => 'Rejected',
        'semester' => 'Fall 2023',
        'created_at' => '2023-12-04 14:20:00',
        'verified_at' => '2023-12-04 16:45:00'
    ],
    [
        'student_id' => '927623BIT005',
        'amount' => 25000.00,
        'payment_method' => 'Cash',
        'reference_no' => 'CASH001',
        'status' => 'Verified',
        'semester' => 'Fall 2023',
        'created_at' => '2023-12-05 10:00:00',
        'verified_at' => '2023-12-05 10:05:00'
    ]
];

// Insert sample payments
$insert_query = "INSERT INTO payments (
    student_id, amount, payment_method, reference_no, 
    status, semester, description, payment_date, 
    created_at, verified_at, payment_details
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($insert_query);

foreach ($sample_payments as $payment) {
    $stmt->bind_param("sdssssssss", 
        $payment['student_id'],
        $payment['amount'],
        $payment['payment_method'],
        $payment['reference_no'],
        $payment['status'],
        $payment['semester'],
        $payment['description'],
        $payment['payment_date'],
        $payment['created_at'],
        $payment['verified_at'],
        $payment['payment_details']
    );
    
    if ($stmt->execute()) {
        echo "Added payment for student {$payment['student_id']} - ₹{$payment['amount']} ({$payment['status']})<br>";
    } else {
        echo "Error adding payment: " . $stmt->error . "<br>";
    }
}

$stmt->close();
$conn->close();

echo "<br>Sample payments setup completed!";
?> 