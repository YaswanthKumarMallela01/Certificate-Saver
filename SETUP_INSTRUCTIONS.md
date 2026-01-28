# 🚀 Certificate Saver - Setup Instructions

## Prerequisites

- **XAMPP** (or any PHP/MySQL server) installed on your system
- **Web Browser** (Chrome, Firefox, Edge, etc.)

---

## 📋 Step-by-Step Setup Guide

### Step 1: Install XAMPP

1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install XAMPP to your system (default location: `C:\xampp`)
3. Start **Apache** and **MySQL** services from XAMPP Control Panel

### Step 2: Configure Database Connection

1. Open `includes/db.php` in your code editor
2. Update the database credentials if needed:
   ```php
   $db_host = 'localhost';
   $db_user = 'root';        // Default XAMPP username
   $db_pass = '';            // Default XAMPP password (empty)
   $db_name = 'user_auth';
   ```

   **Note:** If you've set a MySQL root password, update `$db_pass` accordingly.

### Step 3: Setup Database

#### Option A: Using phpMyAdmin (Recommended)

1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click on **"SQL"** tab at the top
3. Open the file `setup.sql` from the project folder
4. Copy all the SQL commands
5. Paste them into the SQL query box in phpMyAdmin
6. Click **"Go"** to execute

#### Option B: Using MySQL Command Line

1. Open Command Prompt or Terminal
2. Navigate to XAMPP MySQL bin directory:
   ```bash
   cd C:\xampp\mysql\bin
   ```
3. Run MySQL:
   ```bash
   mysql -u root -p
   ```
   (Press Enter if no password is set)
4. Execute the SQL file:
   ```sql
   source D:/AI Gen Projects/Certificate Saver GitHub/Certificate-Saver/setup.sql
   ```
   (Adjust the path to your project location)

### Step 4: Place Project Files

1. Copy the entire project folder to XAMPP's `htdocs` directory:
   ```
   C:\xampp\htdocs\Certificate-Saver\
   ```

   Or if you prefer a different location, you can:
   - Create a virtual host (advanced)
   - Use the full path in your browser

### Step 5: Create Uploads Directory

1. Create a folder named `uploads` in the project root:
   ```
   C:\xampp\htdocs\Certificate-Saver\uploads\
   ```

2. **Important:** Set proper permissions:
   - Right-click on the `uploads` folder
   - Go to **Properties** → **Security**
   - Ensure the folder has **Write** permissions

   **Note:** The script will try to create this folder automatically, but it's better to create it manually.

### Step 6: Access the Application

1. Open your web browser
2. Navigate to:
   ```
   http://localhost/Certificate-Saver/
   ```
   or
   ```
   http://localhost/Certificate-Saver/index.html
   ```

---

## 🔐 Default Login Credentials

### Admin Account
- **Roll Number:** `admin`
- **Password:** `admin123`

### Test Student Account
- **Roll Number:** `student1`
- **Password:** `student123`

**⚠️ Important:** Change these passwords after first login in a production environment!

---

## 🧪 Testing the Application

1. **Test Registration:**
   - Click "Register" on the login page
   - Create a new account with a roll number and password
   - Login with the new credentials

2. **Test Certificate Upload:**
   - Login to your account
   - Upload a PDF, JPG, or PNG file (max 5MB)
   - Verify the certificate appears in your dashboard

3. **Test Admin Panel:**
   - Login with admin credentials
   - Click "Admin Panel" link
   - View all students and their certificates

4. **Test Certificate Viewing:**
   - Click "View" on any certificate
   - Verify the file opens correctly

---

## 🔧 Troubleshooting

### Issue: "Database connection failed"
**Solution:**
- Ensure MySQL is running in XAMPP Control Panel
- Check database credentials in `includes/db.php`
- Verify database `user_auth` exists

### Issue: "File upload failed"
**Solution:**
- Check if `uploads` folder exists and has write permissions
- Verify PHP `upload_max_filesize` in `php.ini` (should be at least 5M)
- Check PHP error logs in XAMPP

### Issue: "Cannot access the website"
**Solution:**
- Ensure Apache is running in XAMPP Control Panel
- Check if the project is in the correct `htdocs` folder
- Try accessing `http://localhost/` first to verify XAMPP is working

### Issue: "Permission denied" errors
**Solution:**
- Right-click on the project folder → Properties → Security
- Ensure your user account has full control
- For `uploads` folder, ensure write permissions are enabled

---

## 📝 Additional Configuration

### Increase Upload File Size Limit

1. Open `php.ini` file (usually in `C:\xampp\php\php.ini`)
2. Find and update these values:
   ```ini
   upload_max_filesize = 10M
   post_max_size = 12M
   ```
3. Restart Apache in XAMPP Control Panel

### Enable Error Display (Development Only)

In `php.ini`:
```ini
display_errors = On
error_reporting = E_ALL
```

**⚠️ Warning:** Disable this in production!

---

## 🗄️ Database Structure

### Users Table
- `id` - Primary key
- `rollno` - Unique roll number/username
- `password` - User password (stored in plain text - **not recommended for production**)
- `is_admin` - Admin flag (TRUE/FALSE)
- `created_at` - Registration timestamp

### Certificates Table
- `id` - Primary key
- `rollno` - Foreign key to users table
- `certificate_name` - Original filename
- `file_path` - Relative path to uploaded file
- `is_deleted` - Soft delete flag
- `upload_date` - Upload timestamp

---

## 🔒 Security Notes

1. **Password Storage:** Currently passwords are stored in plain text. For production, implement password hashing using `password_hash()` and `password_verify()`.

2. **File Upload Security:** The application validates file types and sizes, but consider additional security measures:
   - Virus scanning
   - File content validation
   - Rename uploaded files to prevent directory traversal

3. **SQL Injection:** The code uses prepared statements, which is good. Always maintain this practice.

4. **Session Security:** Consider implementing:
   - Session timeout
   - CSRF protection
   - Secure session cookies

---

## 📞 Support

If you encounter any issues:
1. Check the troubleshooting section above
2. Review PHP error logs in XAMPP
3. Check browser console for JavaScript errors
4. Verify all file paths are correct

---

## ✅ Setup Checklist

- [ ] XAMPP installed and running
- [ ] Apache and MySQL services started
- [ ] Database created using `setup.sql`
- [ ] Database credentials updated in `includes/db.php`
- [ ] Project files copied to `htdocs` folder
- [ ] `uploads` folder created with write permissions
- [ ] Application accessible at `http://localhost/Certificate-Saver/`
- [ ] Can login with admin credentials
- [ ] Can register a new user
- [ ] Can upload a certificate
- [ ] Can view uploaded certificates

---

**Happy Coding! 🎉**
