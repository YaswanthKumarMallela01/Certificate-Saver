-- ============================================
-- Certificate Saver Database Setup Script
-- For XAMPP / Local MySQL
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS user_auth;
USE user_auth;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rollno VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Certificates table
CREATE TABLE IF NOT EXISTS certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rollno VARCHAR(50) NOT NULL,
    certificate_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    is_deleted BOOLEAN DEFAULT FALSE,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rollno) REFERENCES users(rollno) ON DELETE CASCADE
);

-- Create index for better performance
CREATE INDEX idx_rollno ON certificates(rollno);
CREATE INDEX idx_is_deleted ON certificates(is_deleted);

-- Insert default admin user
-- Roll Number: admin
-- Password: admin123
INSERT INTO users (rollno, password, is_admin) 
VALUES ('admin', 'admin123', TRUE)
ON DUPLICATE KEY UPDATE rollno=rollno;

-- Insert sample student for testing (optional)
-- Roll Number: student1
-- Password: student123
INSERT INTO users (rollno, password, is_admin) 
VALUES ('student1', 'student123', FALSE)
ON DUPLICATE KEY UPDATE rollno=rollno;

-- Show tables
SHOW TABLES;

-- Verify setup
SELECT 'Database setup completed successfully!' AS status;
SELECT COUNT(*) AS total_users FROM users;
SELECT COUNT(*) AS total_certificates FROM certificates;
