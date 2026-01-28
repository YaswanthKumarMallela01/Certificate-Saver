# 📄 Certificate Saver & AI Hub

![Status](https://img.shields.io/badge/Status-Active-success) ![AI Integration](https://img.shields.io/badge/AI-In%20Development-orange) ![Docker](https://img.shields.io/badge/Docker-Coming%20Soon-blue)

A secure, web-based platform designed to upload, manage, and organize student certificates. Built with **PHP**, **HTML/CSS/JS**, and enhanced by **Prompt Engineering**, this system acts as a centralized digital locker for your academic achievements.

> **🌟 Note:** The core certificate management features are fully functional. The **AI Career Assistant Chatbot** is currently under active development and scheduled for release by year-end.

🔗 **Live Demo**: [https://yaswanthaicertificatemanagementhub.free.nf/](https://yaswanthaicertificatemanagementhub.free.nf/)

---

## 🚀 Key Features

* **🔐 OTP Authentication & Verification** – Secure user registration and login
* **📤 Certificate Upload** (PDF/JPG/PNG)
* **🧾 Certificate Viewing** & Listing with roll number filtering
* **🗑️ Certificate Deletion** (Admin Only)
* **🧠 UI/UX** developed using Prompt Engineering
* **🛠️ Admin Dashboard** with Full Control
* **📁 Clean, Responsive Frontend**
*  👁️ **Password** Show/Hide Toggle
* **🤖 AI** Career Assistant (Coming Soon)
* **📬 Email** Integration for OTP Delivery

---

## ⚠️ Admin Access (For Testing Only)

These credentials are provided strictly for **testing and demonstration purposes**. Please use them responsibly.

| Role | Username | Password |
| :--- | :--- | :--- |
| **Admin** | `admin` | `admin123` |

> **Warning:** Do not use these credentials to store personal or sensitive real-world data during testing.

---

## 🔮 Roadmap & Future Updates

We are taking a forward-thinking view to evolve this project into a complete Career Assistant:

* **🤖 AI Chatbot (NLP):** A Natural Language Processing bot is currently in development (Target: End of Year). It will analyze your certificates and suggest career paths/jobs.
* **🐳 Docker Support:** We are actively working on containerizing the application. A verified image will be available on **Docker Hub** shortly for one-click deployment.

---

## 🛠️ Local Setup Instructions

Follow these steps to run the project securely on your local machine using **XAMPP**.

### 1. Prerequisites
* Install **XAMPP** (or WAMP/MAMP) for a local PHP/MySQL server.
* Git installed on your machine.

### 2. Clone the Repository
```bash
git clone [https://github.com/Yash864512/Certificate-Saver.git](https://github.com/Yash864512/Certificate-Saver.git)
cd Certificate-Saver
```

### 3. Database Configuration
1.  Open **XAMPP Control Panel** and start **Apache** and **MySQL**.
2.  Go to `http://localhost/phpmyadmin`.
3.  Create a new database named `certificate_hub` (or as preferred).
4.  Import the provided SQL file:
    * Click **Import** tab.
    * Select `setup.sql` from the project folder.
    * Click **Go** to generate the necessary tables.

### 4. Configure Secrets
To ensure security, sensitive credentials are isolated.
1.  Navigate to the `includes/` folder.
2.  Rename `secrets.example.php` to `secrets.php` (if the file doesn't exist, create it).
3.  Open `secrets.php` and fill in your local configurations:
    ```php
    <?php
    // Database Config
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'certificate_hub';

    // API Keys (For Future AI Features)
    $gemini_api_key = 'YOUR_API_KEY_HERE';
    
    // Email Config (For OTP)
    $smtp_email = 'your_email@gmail.com';
    $smtp_pass = 'your_app_password';
    ?>
    ```

### 5. Run the Application
1. Move the project folder to your XAMPP htdocs directory (e.g., C:\xampp\htdocs\Certificate-Saver).
2. Open your browser and navigate to: http://localhost/Certificate-Saver/index.html 

## 📁 Folder Structure

```text
CERTIFICATE-SAVER/
├── includes/
│   ├── db.php              # Database connection
│   ├── email.php           # Email (OTP) functionality
│   ├── secrets.php         # Configuration file
│
├── admin_view.php          # Admin certificate panel
├── admin.php               # Admin login logic
├── ai_chat.php             # AI chatbot (under development)
├── auth.php                # Login/register logic
├── dashboard.php           # User dashboard
├── delete_certificate.php
├── get_certificates.php
├── index.php               # Landing + auth page
├── logout.php
├── send_otp.php            # OTP sending logic
├── setup.sql               # Database setup queries
├── upload.php
├── view.php
│
├── CHANGES_SUMMARY.md
├── MYSQL_QUERIES.md
├── PROJECT_STRUCTURE.md
├── QUICK_START.md
├── SETUP_INSTRUCTIONS.md
├── UPDATE_SUMMARY.md
└── .htaccess               # Apache configuration
```

## 🔮 Upcoming Features
* **🤖 AI Career Assistant Chatbot** – NLP-powered chatbot for certificate queries and career guidance (Coming by year-end)

* **🐳 Docker Support** – Containerized setup for easy deployment (Docker Hub availability soon)

* **📱 Progressive Web App (PWA) capabilities**

* **📈 Certificate analytics dashboard**


## 🎯 Why Use Certificate Saver?
```text
This platform is designed for students, professionals, and institutions to securely store, organize, and access certificates with military-grade OTP authentication. The intuitive interface and upcoming AI features make it a future-proof solution for certificate management.
```

## 📄 License
This project is open-source and available for personal and educational use.

## Enjoy storing your certificates securely! 🎓🔐

💬 Feedback Welcome: The project is under active development. Your feedback and contributions are welcome via GitHub issues or pull requests.