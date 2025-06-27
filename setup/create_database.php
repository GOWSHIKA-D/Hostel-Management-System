// Create visitor_log table
$create_table_sql = "CREATE TABLE IF NOT EXISTS visitor_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL,
    visitor_name VARCHAR(100) NOT NULL,
    relation VARCHAR(50) NOT NULL,
    visit_date DATE NOT NULL,
    purpose TEXT NOT NULL,
    parent_name VARCHAR(100),
    parent_phone VARCHAR(20),
    parent_permission ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    parent_permission_date TIMESTAMP NULL,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    warden_remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES student(student_id)
)";

if ($conn->query($create_table_sql)) {
    echo "Visitor log table created successfully!<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
    exit;
}

// Add parent contact columns to student table if they don't exist
$alter_student_table_sql = "ALTER TABLE student 
    ADD COLUMN IF NOT EXISTS parent_name VARCHAR(100) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS parent_phone VARCHAR(20) DEFAULT NULL";

if ($conn->query($alter_student_table_sql)) {
    echo "Added parent contact columns to student table successfully!<br>";
} else {
    echo "Error adding parent contact columns: " . $conn->error . "<br>";
    exit;
}

// Update existing student records with sample parent data
$update_student_sql = "UPDATE student 
    SET parent_name = CONCAT('Parent of ', name),
        parent_phone = CONCAT('+91', FLOOR(RAND() * (9999999999 - 9000000000 + 1) + 9000000000))
    WHERE parent_name IS NULL OR parent_phone IS NULL";

if ($conn->query($update_student_sql)) {
    echo "Updated existing student records with sample parent data successfully!<br>";
} else {
    echo "Error updating student records: " . $conn->error . "<br>";
    exit;
} 