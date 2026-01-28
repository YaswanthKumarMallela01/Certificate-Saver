<?php
// Database configuration - Update these values for your XAMPP setup
$db_host = 'localhost';
$db_user = 'root'; // Default XAMPP MySQL username
$db_pass = ''; // Default XAMPP MySQL password (empty by default)
$db_name = 'user_auth';

// Upload directory configuration
$upload_dir = __DIR__ . '/../uploads/';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");
?>