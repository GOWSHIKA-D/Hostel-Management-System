-- Drop and recreate admin table
DROP TABLE IF EXISTS admin;
CREATE TABLE admin (
    admin_id VARCHAR(20) PRIMARY KEY,
    username VARCHAR(50),
    fullname VARCHAR(100),
    email VARCHAR(100),
    mobile VARCHAR(15),
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'warden') NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Drop and recreate wardens table
DROP TABLE IF EXISTS wardens;
CREATE TABLE wardens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    warden_id VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100),
    email VARCHAR(100),
    mobile VARCHAR(15),
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create rooms table if not exists
DROP TABLE IF EXISTS rooms;
CREATE TABLE rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(10) UNIQUE NOT NULL,
    room_type ENUM('Single', 'Double', 'Triple') NOT NULL,
    capacity INT NOT NULL,
    occupied INT DEFAULT 0,
    status ENUM('Available', 'Full', 'Maintenance') DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create students table if not exists
DROP TABLE IF EXISTS students;
CREATE TABLE students (
    student_id VARCHAR(20) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mobile VARCHAR(15),
    room_id INT,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(room_id)
);

-- Create payments table if not exists
DROP TABLE IF EXISTS payments;
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('Cash', 'UPI', 'Net Banking', 'Credit Card', 'Debit Card') NOT NULL,
    reference_no VARCHAR(50) UNIQUE NOT NULL,
    status ENUM('Pending', 'Verified', 'Rejected') DEFAULT 'Pending',
    payment_date DATE NOT NULL,
    semester INT NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    verified_at TIMESTAMP NULL,
    FOREIGN KEY (student_id) REFERENCES students(student_id)
);

-- Insert sample rooms
INSERT INTO rooms (room_number, room_type, capacity, occupied) VALUES
('A101', 'Double', 2, 2),
('A102', 'Single', 1, 1),
('B201', 'Triple', 3, 2),
('B202', 'Double', 2, 1);

-- Insert sample students
INSERT INTO students (student_id, name, email, mobile, room_id, password) VALUES
('STU001', 'John Doe', 'john@example.com', '9876543210', 1, '$2y$10$YourSaltHereabcdefg.OPxUG9J7bIdLxnP6MV0P0XYZ0123456789'),
('STU002', 'Jane Smith', 'jane@example.com', '9876543211', 1, '$2y$10$YourSaltHereabcdefg.OPxUG9J7bIdLxnP6MV0P0XYZ0123456789'),
('STU003', 'Mike Johnson', 'mike@example.com', '9876543212', 2, '$2y$10$YourSaltHereabcdefg.OPxUG9J7bIdLxnP6MV0P0XYZ0123456789'),
('STU004', 'Sarah Williams', 'sarah@example.com', '9876543213', 3, '$2y$10$YourSaltHereabcdefg.OPxUG9J7bIdLxnP6MV0P0XYZ0123456789');

-- Insert sample payments
INSERT INTO payments (student_id, amount, payment_method, reference_no, status, payment_date, semester, description) VALUES
('STU001', 25000.00, 'Cash', 'Ca20250524001', 'Pending', '2025-05-24', 1, 'First Semester Hostel Fee'),
('STU002', 25000.00, 'UPI', 'UP20250524002', 'Verified', '2025-05-24', 1, 'First Semester Hostel Fee'),
('STU001', 25000.00, 'Net Banking', 'Ne20250524003', 'Pending', '2025-05-24', 2, 'Second Semester Hostel Fee'),
('STU003', 25000.00, 'Credit Card', 'Cr20250524004', 'Verified', '2025-05-24', 1, 'First Semester Hostel Fee'),
('STU002', 25000.00, 'Debit Card', 'De20250524005', 'Pending', '2025-05-24', 2, 'Second Semester Hostel Fee'),
('STU004', 25000.00, 'Cash', 'Ca20250524006', 'Verified', '2025-05-24', 1, 'First Semester Hostel Fee'),
('STU003', 25000.00, 'UPI', 'UP20250524007', 'Pending', '2025-05-24', 2, 'Second Semester Hostel Fee');

-- Insert admin with properly hashed password
INSERT INTO admin (admin_id, password, role) 
VALUES ('admin001', '$2y$10$YourSaltHereabcdefg.OPxUG9J7bIdLxnP6MV0P0XYZ0123456789', 'admin');

-- Insert warden with properly hashed password
INSERT INTO wardens (warden_id, name, email, mobile, password)
VALUES ('warden001', 'Main Warden', 'warden@hostel.com', '9876543210', '$2y$10$YourSaltHereabcdefg.OPxUG9J7bIdLxnP6MV0P0XYZ0123456789'); 