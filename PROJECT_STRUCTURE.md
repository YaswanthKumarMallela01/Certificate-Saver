# 📊 Certificate Saver - Project Structure & Mind Map

## 🗂️ Project Architecture

```
Certificate-Saver/
│
├── 📄 Frontend Files
│   ├── index.html          → Login/Registration Page
│   ├── dashboard.php        → User Dashboard (Certificate Management)
│   ├── admin.php           → Admin Dashboard (View All Students)
│   └── admin_view.php      → Admin View (View Student's Certificates)
│
├── 🔧 Backend Files
│   ├── auth.php            → Authentication Handler (Login/Register)
│   ├── upload.php          → Certificate Upload Handler
│   ├── view.php            → Certificate View/Download Handler
│   ├── delete_certificate.php → Certificate Deletion Handler
│   └── logout.php          → Session Destroy & Logout
│
├── 📁 Configuration
│   ├── includes/
│   │   └── db.php          → Database Connection Configuration
│   ├── .htaccess           → Apache Configuration
│   └── setup.sql           → Database Setup Script
│
├── 📂 Data Storage
│   └── uploads/            → Certificate Files Storage (PDF, JPG, PNG)
│
└── 📚 Documentation
    ├── README.md           → Project Overview
    ├── SETUP_INSTRUCTIONS.md → Setup Guide
    └── PROJECT_STRUCTURE.md → This File
```

---

## 🔄 Application Flow

### User Authentication Flow
```
index.html
    ↓
[User enters credentials]
    ↓
auth.php (POST request)
    ↓
[Validate credentials]
    ↓
[Create session]
    ↓
dashboard.php (if user) OR admin.php (if admin)
```

### Certificate Upload Flow
```
dashboard.php
    ↓
[User selects file]
    ↓
upload.php (POST with file)
    ↓
[Validate file (type, size)]
    ↓
[Save to uploads/ folder]
    ↓
[Insert record to database]
    ↓
[Redirect to dashboard.php with success message]
```

### Certificate View Flow
```
dashboard.php OR admin_view.php
    ↓
[User clicks "View"]
    ↓
view.php?id={certificate_id}
    ↓
[Verify ownership/permissions]
    ↓
[Read file from disk]
    ↓
[Send file to browser with appropriate headers]
```

### Admin Flow
```
admin.php
    ↓
[Display all students with certificate counts]
    ↓
[Admin clicks "View Certificates"]
    ↓
admin_view.php?rollno={student_rollno}
    ↓
[Display all certificates for that student]
    ↓
[Admin can view/download/delete certificates]
```

---

## 🗄️ Database Schema

### users Table
```
┌─────────────┬──────────────┬─────────────┐
│ Field       │ Type         │ Description │
├─────────────┼──────────────┼─────────────┤
│ id          │ INT          │ Primary Key │
│ rollno      │ VARCHAR(50)  │ Unique ID   │
│ password    │ VARCHAR(255) │ Password    │
│ is_admin    │ BOOLEAN      │ Admin Flag  │
│ created_at  │ TIMESTAMP    │ Created At  │
└─────────────┴──────────────┴─────────────┘
```

### certificates Table
```
┌──────────────────┬──────────────┬──────────────────┐
│ Field            │ Type         │ Description       │
├──────────────────┼──────────────┼──────────────────┤
│ id               │ INT          │ Primary Key       │
│ rollno           │ VARCHAR(50)  │ Foreign Key       │
│ certificate_name │ VARCHAR(255) │ Original Filename │
│ file_path        │ VARCHAR(255) │ Relative Path     │
│ is_deleted       │ BOOLEAN      │ Soft Delete Flag  │
│ upload_date      │ TIMESTAMP    │ Upload Timestamp  │
└──────────────────┴──────────────┴──────────────────┘
```

---

## 🔐 Security Features

### Authentication
- ✅ Session-based authentication
- ✅ Role-based access control (Admin/User)
- ✅ Protected routes (session checks)
- ⚠️ Plain text passwords (needs hashing for production)

### File Upload Security
- ✅ File type validation (PDF, JPG, PNG only)
- ✅ File size limit (5MB max)
- ✅ Unique filename generation
- ✅ Directory traversal prevention

### Database Security
- ✅ Prepared statements (SQL injection prevention)
- ✅ Input sanitization with `htmlspecialchars()`
- ✅ Foreign key constraints

---

## 🎨 Frontend Components

### index.html
- Login form
- Registration form
- Password visibility toggle
- Form validation
- AJAX authentication

### dashboard.php
- Certificate upload form
- Certificate list display
- View/Delete actions
- AI Career Assistant (Gemini API integration)
- Admin panel link (if admin)

### admin.php
- Statistics cards (Total Students, Certificates, Average)
- Student list table
- Certificate count per student
- View certificates link

### admin_view.php
- Student information display
- Certificate list for specific student
- View/Download/Delete actions
- Back navigation

---

## 🔌 API Endpoints

### auth.php
- **POST** `/auth.php`
  - Action: `login` | `register`
  - Returns: JSON response with success status

### upload.php
- **POST** `/upload.php`
  - Form data with file
  - Redirects to dashboard with status

### view.php
- **GET** `/view.php?id={certificate_id}`
  - Returns: File content with appropriate headers

### delete_certificate.php
- **POST** `/delete_certificate.php`
  - JSON body with certificate ID
  - Returns: JSON response with success status

---

## 🛠️ Technology Stack

- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Server:** Apache (XAMPP)
- **External API:** Google Gemini API (for AI Assistant)

---

## 📝 Key Functions

### Authentication
- `auth.php`: Handles login and registration
- Session management across pages
- Admin role detection

### File Management
- `upload.php`: Validates and stores uploaded files
- `view.php`: Serves files with proper MIME types
- `delete_certificate.php`: Soft deletes certificates

### Admin Functions
- `admin.php`: Lists all students with statistics
- `admin_view.php`: Shows certificates for a specific student
- Admin can delete any certificate

---

## 🔄 Data Flow Diagram

```
User Input → Validation → Database → Response → UI Update
     ↓           ↓           ↓          ↓          ↓
  Form Data   PHP Checks   MySQL     JSON/File   JavaScript
```

---

## 🎯 User Roles

### Regular User
- Register/Login
- Upload certificates
- View own certificates
- Delete own certificates
- Access AI Career Assistant

### Admin User
- All regular user privileges
- View all students
- View any student's certificates
- Delete any certificate
- Access admin dashboard

---

## 📦 File Dependencies

```
index.html
    → auth.php

dashboard.php
    → includes/db.php
    → upload.php
    → view.php
    → delete_certificate.php
    → logout.php

admin.php
    → includes/db.php
    → admin_view.php
    → logout.php

admin_view.php
    → includes/db.php
    → view.php
    → delete_certificate.php
```

---

## 🚨 Important Notes

1. **Upload Directory:** Must exist and have write permissions
2. **Database:** Must be created before first use
3. **Session:** Requires PHP sessions to be enabled
4. **File Paths:** Uses relative paths for portability
5. **Soft Delete:** Certificates are marked as deleted, not physically removed

---

## 🔧 Configuration Points

1. **Database:** `includes/db.php`
2. **Upload Settings:** `upload.php` (file size, types)
3. **Session Timeout:** PHP configuration
4. **AI Assistant:** `dashboard.php` (Gemini API key)

---

**Last Updated:** January 2026
