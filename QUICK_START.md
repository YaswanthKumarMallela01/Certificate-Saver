# ⚡ Quick Start Guide - Certificate Saver

## 🎯 What Was Fixed

I've analyzed and fixed all issues in your Certificate Saver project:

### ✅ Fixed Issues:
1. **Database Connection** - Standardized all files to use `includes/db.php`
2. **Upload Directory** - Fixed undefined variable and path issues
3. **File Path Resolution** - Fixed relative/absolute path handling
4. **Database Schema** - Removed email field inconsistency
5. **Admin View** - Fixed certificate viewing for admins
6. **Code Duplication** - Removed duplicate queries in admin.php

### 📁 New Files Created:
- `setup.sql` - Complete database setup script
- `SETUP_INSTRUCTIONS.md` - Detailed setup guide
- `PROJECT_STRUCTURE.md` - Project architecture documentation
- `MYSQL_QUERIES.md` - Complete MySQL queries reference
- `QUICK_START.md` - This file

---

## 🚀 Quick Setup (5 Minutes)

### Step 1: Start XAMPP
1. Open XAMPP Control Panel
2. Start **Apache** and **MySQL**

### Step 2: Setup Database
1. Open browser: `http://localhost/phpmyadmin`
2. Click **SQL** tab
3. Copy and paste contents of `setup.sql`
4. Click **Go**

### Step 3: Configure Database
1. Open `includes/db.php`
2. Update if needed (default XAMPP settings should work):
   ```php
   $db_user = 'root';    // Default XAMPP
   $db_pass = '';        // Default XAMPP (empty)
   ```

### Step 4: Place Project
1. Copy project to: `C:\xampp\htdocs\Certificate-Saver\`
2. Create folder: `C:\xampp\htdocs\Certificate-Saver\uploads\`

### Step 5: Access Application
Open browser: `http://localhost/Certificate-Saver/`

---

## 🔐 Default Login Credentials

**Admin:**
- Roll Number: `admin`
- Password: `admin123`

**Test Student:**
- Roll Number: `student1`
- Password: `student123`

---

## 📋 MySQL Queries Summary

### Essential Queries (Already in setup.sql):

```sql
-- Create database
CREATE DATABASE IF NOT EXISTS user_auth;
USE user_auth;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rollno VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create certificates table
CREATE TABLE IF NOT EXISTS certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rollno VARCHAR(50) NOT NULL,
    certificate_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    is_deleted BOOLEAN DEFAULT FALSE,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rollno) REFERENCES users(rollno) ON DELETE CASCADE
);

-- Insert admin user
INSERT INTO users (rollno, password, is_admin) 
VALUES ('admin', 'admin123', TRUE);
```

**See `MYSQL_QUERIES.md` for complete query reference.**

---

## 🗂️ Project Structure

```
Certificate-Saver/
├── index.html              → Login/Register Page
├── dashboard.php           → User Dashboard
├── admin.php              → Admin Dashboard
├── admin_view.php         → Admin Certificate View
├── auth.php               → Authentication Handler
├── upload.php             → File Upload Handler
├── view.php               → Certificate Viewer
├── delete_certificate.php → Delete Handler
├── logout.php             → Logout Handler
├── includes/
│   └── db.php             → Database Connection
├── uploads/               → Certificate Storage
└── setup.sql              → Database Setup
```

---

## 🔧 How to Run the Code

### Method 1: Using XAMPP (Recommended)

1. **Install XAMPP**
   - Download from: https://www.apachefriends.org/
   - Install to default location

2. **Start Services**
   - Open XAMPP Control Panel
   - Click "Start" for Apache and MySQL

3. **Setup Database**
   ```bash
   # Option A: Using phpMyAdmin
   # 1. Go to http://localhost/phpmyadmin
   # 2. Click SQL tab
   # 3. Paste setup.sql contents
   # 4. Click Go
   
   # Option B: Using Command Line
   cd C:\xampp\mysql\bin
   mysql -u root -p < "D:\AI Gen Projects\Certificate Saver GitHub\Certificate-Saver\setup.sql"
   ```

4. **Place Project Files**
   ```
   Copy project to: C:\xampp\htdocs\Certificate-Saver\
   ```

5. **Create Uploads Folder**
   ```
   Create: C:\xampp\htdocs\Certificate-Saver\uploads\
   ```

6. **Access Application**
   ```
   Open: http://localhost/Certificate-Saver/
   ```

### Method 2: Using Other PHP Servers

If using WAMP, MAMP, or built-in PHP server:

1. **WAMP/MAMP**: Similar to XAMPP, adjust paths accordingly
2. **PHP Built-in Server**:
   ```bash
   cd "D:\AI Gen Projects\Certificate Saver GitHub\Certificate-Saver"
   php -S localhost:8000
   ```
   Then access: `http://localhost:8000`

---

## 📝 Configuration Checklist

- [ ] XAMPP installed
- [ ] Apache running
- [ ] MySQL running
- [ ] Database `user_auth` created
- [ ] Tables created (users, certificates)
- [ ] Admin user inserted
- [ ] Project in `htdocs` folder
- [ ] `uploads` folder created
- [ ] Database credentials in `includes/db.php` correct
- [ ] Can access `http://localhost/Certificate-Saver/`

---

## 🧪 Testing Checklist

- [ ] Can access login page
- [ ] Can register new user
- [ ] Can login with credentials
- [ ] Can upload certificate (PDF/JPG/PNG)
- [ ] Can view uploaded certificate
- [ ] Can delete own certificate
- [ ] Admin can login
- [ ] Admin can see all students
- [ ] Admin can view student certificates
- [ ] Admin can delete any certificate

---

## 🐛 Common Issues & Solutions

### Issue: "Database connection failed"
**Solution:**
- Check MySQL is running in XAMPP
- Verify credentials in `includes/db.php`
- Ensure database `user_auth` exists

### Issue: "File upload failed"
**Solution:**
- Create `uploads` folder manually
- Check folder permissions (write access)
- Verify PHP upload settings in `php.ini`

### Issue: "Cannot access website"
**Solution:**
- Ensure Apache is running
- Check project is in `htdocs` folder
- Verify URL: `http://localhost/Certificate-Saver/`

### Issue: "Permission denied"
**Solution:**
- Right-click `uploads` folder → Properties → Security
- Add write permissions for your user account

---

## 📚 Documentation Files

- **SETUP_INSTRUCTIONS.md** - Detailed setup guide
- **PROJECT_STRUCTURE.md** - Architecture and flow diagrams
- **MYSQL_QUERIES.md** - Complete MySQL query reference
- **README.md** - Project overview

---

## 🎉 You're Ready!

Your Certificate Saver application is now fully configured and ready to use!

**Next Steps:**
1. Follow the Quick Setup above
2. Test with default admin credentials
3. Register a new user
4. Upload a test certificate
5. Explore admin features

**Need Help?** Check `SETUP_INSTRUCTIONS.md` for detailed troubleshooting.

---

**Happy Coding! 🚀**
