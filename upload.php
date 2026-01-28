<?php
session_start();
if (!isset($_SESSION['rollno'])) {
    header("Location: index.html");
    exit();
}

include 'includes/db.php';

$rollno = $_SESSION['rollno'];

// Create user-specific upload directory
$user_upload_dir = $upload_dir . $rollno . '/';

// Create uploads directory if it doesn't exist
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
    chmod($upload_dir, 0755);
}

// Create user-specific directory if it doesn't exist
if (!file_exists($user_upload_dir)) {
    mkdir($user_upload_dir, 0755, true);
    chmod($user_upload_dir, 0755);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['certificate'])) {
    $original_name = basename($_FILES["certificate"]["name"]);
    $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
    $description = trim((string)($_POST['description'] ?? ''));
    if (strlen($description) > 2000) {
        $description = substr($description, 0, 2000);
    }
    
    // Generate unique filename
    $new_filename = uniqid() . '_' . $rollno . '.' . $file_extension;
    $target_file = $user_upload_dir . $new_filename;
    
    // Check file size (max 5MB)
    if ($_FILES["certificate"]["size"] > 5000000) {
        header("Location: dashboard.php?upload_error=file_too_large");
        exit();
    }
    
    // Allow certain file formats
    $allowed_extensions = array("pdf", "jpg", "jpeg", "png");
    if (!in_array(strtolower($file_extension), $allowed_extensions)) {
        header("Location: dashboard.php?upload_error=invalid_file_type");
        exit();
    }
    
    // Try to upload file
    if (move_uploaded_file($_FILES["certificate"]["tmp_name"], $target_file)) {
        // Store relative path in database for portability (user-specific folder)
        $relative_path = 'uploads/' . $rollno . '/' . $new_filename;
        
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO certificates (rollno, certificate_name, description, file_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $rollno, $original_name, $description, $relative_path);
        
        if ($stmt->execute()) {
            header("Location: dashboard.php?upload_success=1");
        } else {
            // Delete the uploaded file if database insert failed
            unlink($target_file);
            header("Location: dashboard.php?upload_error=db_error");
        }
    } else {
        header("Location: dashboard.php?upload_error=upload_failed");
    }
} else {
    header("Location: dashboard.php");
}
?>