<?php
session_start();
if (!isset($_SESSION['rollno'])) {
    header("Location: index.html");
    exit();
}

include 'includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$certificate_id = $_GET['id'];
$rollno = $_SESSION['rollno'];
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];

// Verify the certificate belongs to the logged-in user (or user is admin)
if ($is_admin) {
    // Admin can view any certificate
    $stmt = $conn->prepare("SELECT file_path, certificate_name FROM certificates WHERE id = ?");
    $stmt->bind_param("i", $certificate_id);
} else {
    // Regular user can only view their own certificates
    $stmt = $conn->prepare("SELECT file_path, certificate_name FROM certificates WHERE id = ? AND rollno = ?");
    $stmt->bind_param("is", $certificate_id, $rollno);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard.php");
    exit();
}

$certificate = $result->fetch_assoc();
$relative_path = $certificate['file_path'];
$file_name = $certificate['certificate_name'];

// Convert relative path to absolute path (handles user-specific folders)
$file_path = __DIR__ . '/' . $relative_path;
$file_extension = strtolower(pathinfo($relative_path, PATHINFO_EXTENSION));

// Check if file exists
if (!file_exists($file_path)) {
    header("Location: dashboard.php?error=file_not_found");
    exit();
}

// Set appropriate headers based on file type
switch ($file_extension) {
    case 'pdf':
        header('Content-Type: application/pdf');
        break;
    case 'jpg':
    case 'jpeg':
        header('Content-Type: image/jpeg');
        break;
    case 'png':
        header('Content-Type: image/png');
        break;
    default:
        header('Content-Type: application/octet-stream');
}

header('Content-Disposition: inline; filename="' . $file_name . '"');
header('Content-Length: ' . filesize($file_path));

readfile($file_path);
exit();
?>