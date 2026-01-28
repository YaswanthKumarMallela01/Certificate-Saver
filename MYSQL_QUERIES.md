# 📊 MySQL Queries Reference Guide

## 🗄️ Database Setup Queries

### Create Database
```sql
CREATE DATABASE IF NOT EXISTS user_auth;
USE user_auth;
```

### Create Users Table
```sql
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rollno VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Create Certificates Table
```sql
CREATE TABLE IF NOT EXISTS certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rollno VARCHAR(50) NOT NULL,
    certificate_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    is_deleted BOOLEAN DEFAULT FALSE,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rollno) REFERENCES users(rollno) ON DELETE CASCADE
);
```

### Create Indexes for Performance
```sql
CREATE INDEX idx_rollno ON certificates(rollno);
CREATE INDEX idx_is_deleted ON certificates(is_deleted);
```

---

## 👤 User Management Queries

### Insert Admin User
```sql
INSERT INTO users (rollno, password, is_admin) 
VALUES ('admin', 'admin123', TRUE);
```

### Insert Regular User
```sql
INSERT INTO users (rollno, password, is_admin) 
VALUES ('student1', 'student123', FALSE);
```

### Get User by Roll Number
```sql
SELECT id, rollno, password, is_admin, created_at 
FROM users 
WHERE rollno = 'student1';
```

### Check if User Exists
```sql
SELECT id FROM users WHERE rollno = 'student1';
```

### Get All Users
```sql
SELECT * FROM users ORDER BY created_at DESC;
```

### Get All Non-Admin Users
```sql
SELECT * FROM users WHERE is_admin = FALSE ORDER BY rollno;
```

### Count Total Users
```sql
SELECT COUNT(*) as total FROM users;
```

### Count Total Students (Non-Admin)
```sql
SELECT COUNT(*) as total FROM users WHERE is_admin = FALSE;
```

---

## 📜 Certificate Management Queries

### Insert Certificate
```sql
INSERT INTO certificates (rollno, certificate_name, file_path) 
VALUES ('student1', 'My Certificate.pdf', 'uploads/abc123_student1.pdf');
```

### Get All Certificates for a User
```sql
SELECT * FROM certificates 
WHERE rollno = 'student1' AND is_deleted = FALSE 
ORDER BY upload_date DESC;
```

### Get Certificate by ID
```sql
SELECT * FROM certificates WHERE id = 1;
```

### Get Certificate with User Verification
```sql
SELECT file_path, certificate_name 
FROM certificates 
WHERE id = 1 AND rollno = 'student1';
```

### Count Certificates for a User
```sql
SELECT COUNT(*) as cert_count 
FROM certificates 
WHERE rollno = 'student1' AND is_deleted = FALSE;
```

### Get All Non-Deleted Certificates
```sql
SELECT * FROM certificates WHERE is_deleted = FALSE;
```

### Soft Delete Certificate
```sql
UPDATE certificates 
SET is_deleted = TRUE 
WHERE id = 1;
```

### Hard Delete Certificate (Permanent)
```sql
DELETE FROM certificates WHERE id = 1;
```

### Count Total Certificates
```sql
SELECT COUNT(*) as total FROM certificates WHERE is_deleted = FALSE;
```

---

## 📊 Admin Dashboard Queries

### Get All Students with Certificate Counts
```sql
SELECT 
    u.rollno, 
    COUNT(c.id) as cert_count 
FROM users u 
LEFT JOIN certificates c ON u.rollno = c.rollno AND c.is_deleted = FALSE
WHERE u.is_admin = FALSE 
GROUP BY u.rollno
ORDER BY u.rollno;
```

### Get Total Certificates Count
```sql
SELECT COUNT(id) as total 
FROM certificates 
WHERE is_deleted = FALSE;
```

### Get Average Certificates per Student
```sql
SELECT 
    COUNT(DISTINCT u.rollno) as total_students,
    COUNT(c.id) as total_certificates,
    ROUND(COUNT(c.id) / COUNT(DISTINCT u.rollno), 1) as avg_certs
FROM users u
LEFT JOIN certificates c ON u.rollno = c.rollno AND c.is_deleted = FALSE
WHERE u.is_admin = FALSE;
```

### Get All Certificates for Admin View
```sql
SELECT 
    id, 
    certificate_name, 
    file_path, 
    upload_date 
FROM certificates 
WHERE rollno = 'student1' AND is_deleted = FALSE 
ORDER BY upload_date DESC;
```

---

## 🔍 Search and Filter Queries

### Search Users by Roll Number
```sql
SELECT * FROM users 
WHERE rollno LIKE '%student%' 
ORDER BY rollno;
```

### Get Certificates Uploaded in Last 30 Days
```sql
SELECT * FROM certificates 
WHERE upload_date >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
AND is_deleted = FALSE
ORDER BY upload_date DESC;
```

### Get Certificates by File Type
```sql
SELECT * FROM certificates 
WHERE file_path LIKE '%.pdf' 
AND is_deleted = FALSE;
```

### Get Users with Most Certificates
```sql
SELECT 
    u.rollno,
    COUNT(c.id) as cert_count
FROM users u
LEFT JOIN certificates c ON u.rollno = c.rollno AND c.is_deleted = FALSE
WHERE u.is_admin = FALSE
GROUP BY u.rollno
ORDER BY cert_count DESC
LIMIT 10;
```

---

## 🧹 Maintenance Queries

### Get All Deleted Certificates
```sql
SELECT * FROM certificates WHERE is_deleted = TRUE;
```

### Permanently Delete All Soft-Deleted Certificates
```sql
DELETE FROM certificates WHERE is_deleted = TRUE;
```

### Get Orphaned Certificates (User doesn't exist)
```sql
SELECT c.* 
FROM certificates c
LEFT JOIN users u ON c.rollno = u.rollno
WHERE u.rollno IS NULL;
```

### Update User Password
```sql
UPDATE users 
SET password = 'newpassword123' 
WHERE rollno = 'student1';
```

### Make User Admin
```sql
UPDATE users 
SET is_admin = TRUE 
WHERE rollno = 'student1';
```

### Remove Admin Status
```sql
UPDATE users 
SET is_admin = FALSE 
WHERE rollno = 'student1';
```

---

## 📈 Statistics Queries

### Get Registration Statistics
```sql
SELECT 
    DATE(created_at) as date,
    COUNT(*) as registrations
FROM users
WHERE is_admin = FALSE
GROUP BY DATE(created_at)
ORDER BY date DESC;
```

### Get Upload Statistics
```sql
SELECT 
    DATE(upload_date) as date,
    COUNT(*) as uploads
FROM certificates
WHERE is_deleted = FALSE
GROUP BY DATE(upload_date)
ORDER BY date DESC;
```

### Get File Type Distribution
```sql
SELECT 
    CASE 
        WHEN file_path LIKE '%.pdf' THEN 'PDF'
        WHEN file_path LIKE '%.jpg' OR file_path LIKE '%.jpeg' THEN 'JPG'
        WHEN file_path LIKE '%.png' THEN 'PNG'
        ELSE 'Other'
    END as file_type,
    COUNT(*) as count
FROM certificates
WHERE is_deleted = FALSE
GROUP BY file_type;
```

---

## 🔐 Authentication Queries

### Verify Login Credentials
```sql
SELECT password, is_admin 
FROM users 
WHERE rollno = 'student1';
```

### Check if Roll Number Exists (for Registration)
```sql
SELECT id FROM users WHERE rollno = 'newstudent';
```

---

## ⚠️ Important Notes

1. **Always use prepared statements** in PHP to prevent SQL injection
2. **Use soft deletes** (`is_deleted = TRUE`) instead of hard deletes for data recovery
3. **Foreign key constraints** ensure data integrity
4. **Indexes** improve query performance on large datasets
5. **Always verify user permissions** before executing admin queries

---

## 🚀 Quick Setup Query

Run this single query to set up everything (after creating database):

```sql
-- Create tables
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rollno VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rollno VARCHAR(50) NOT NULL,
    certificate_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    is_deleted BOOLEAN DEFAULT FALSE,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rollno) REFERENCES users(rollno) ON DELETE CASCADE
);

-- Create indexes
CREATE INDEX idx_rollno ON certificates(rollno);
CREATE INDEX idx_is_deleted ON certificates(is_deleted);

-- Insert default admin
INSERT INTO users (rollno, password, is_admin) 
VALUES ('admin', 'admin123', TRUE);

-- Verify
SELECT 'Setup Complete!' AS status;
SELECT COUNT(*) AS users FROM users;
SELECT COUNT(*) AS certificates FROM certificates;
```

---

**Last Updated:** January 2026
