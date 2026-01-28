<?php
session_start();
if (!isset($_SESSION['rollno'])) {
    header("Location: index.html");
    exit();
}

include 'includes/db.php';

$rollno = $_SESSION['rollno'];

// Create uploads directory if it doesn't exist
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
    chmod($upload_dir, 0755);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['certificate'])) {
    $original_name = basename($_FILES["certificate"]["name"]);
    $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
    
    // Generate unique filename
    $new_filename = uniqid() . '_' . $rollno . '.' . $file_extension;
    $target_file = $upload_dir . $new_filename;
    
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
        // Store relative path in database for portability
        $relative_path = 'uploads/' . $new_filename;
        
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO certificates (rollno, certificate_name, file_path) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $rollno, $original_name, $relative_path);
        
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