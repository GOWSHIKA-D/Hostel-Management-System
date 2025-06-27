-- Create database if not exists
DROP DATABASE IF EXISTS hostel_management;
CREATE DATABASE hostel_management;
USE hostel_management;

-- Create students table
CREATE TABLE IF NOT EXISTS students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    registration_number VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    parent_name VARCHAR(100),
    parent_phone VARCHAR(15),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create rooms table
CREATE TABLE IF NOT EXISTS rooms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_number VARCHAR(10) UNIQUE NOT NULL,
    capacity INT NOT NULL DEFAULT 2,
    occupied INT NOT NULL DEFAULT 0,
    floor INT NOT NULL,
    block VARCHAR(10) NOT NULL,
    type ENUM('AC', 'Non-AC') NOT NULL DEFAULT 'Non-AC',
    status ENUM('Available', 'Full', 'Maintenance') NOT NULL DEFAULT 'Available'
);

-- Create room_assignments table
CREATE TABLE IF NOT EXISTS room_assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    room_id INT NOT NULL,
    assigned_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (room_id) REFERENCES rooms(id)
);

-- Create wardens table
CREATE TABLE IF NOT EXISTS wardens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create payments table
CREATE TABLE IF NOT EXISTS payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_type ENUM('Hostel Fee', 'Mess Fee', 'Security Deposit', 'Other') NOT NULL,
    payment_method ENUM('Cash', 'Online Transfer', 'UPI', 'Cheque') NOT NULL,
    transaction_id VARCHAR(100),
    payment_date DATE NOT NULL,
    payment_status ENUM('Pending', 'Completed', 'Failed', 'Refunded') NOT NULL DEFAULT 'Pending',
    semester VARCHAR(20) NOT NULL,
    academic_year VARCHAR(10) NOT NULL,
    receipt_number VARCHAR(50) UNIQUE,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (created_by) REFERENCES wardens(id)
);

-- Insert sample warden
INSERT INTO wardens (name, email, phone, password) VALUES
('John Doe', 'warden@hostel.com', '1234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample rooms
INSERT INTO rooms (room_number, capacity, floor, block, type) VALUES
('A101', 2, 1, 'A', 'Non-AC'),
('A102', 2, 1, 'A', 'Non-AC'),
('A103', 2, 1, 'A', 'AC'),
('B101', 3, 1, 'B', 'Non-AC'),
('B102', 3, 1, 'B', 'AC');

-- Insert sample students with unique email addresses
INSERT INTO students (name, registration_number, email, phone, password, parent_name, parent_phone) VALUES
('John Smith', 'REG2024001', 'john.smith@example.com', '9876543210', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mr. Smith', '9876543211'),
('Emma Johnson', 'REG2024002', 'emma.j@example.com', '9876543212', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mr. Johnson', '9876543213'),
('Michael Brown', 'REG2024003', 'michael.b@example.com', '9876543214', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mrs. Brown', '9876543215'),
('Sarah Wilson', 'REG2024004', 'sarah.w@example.com', '9876543216', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mr. Wilson', '9876543217'),
('David Lee', 'REG2024005', 'david.l@example.com', '9876543218', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mr. Lee', '9876543219'); 