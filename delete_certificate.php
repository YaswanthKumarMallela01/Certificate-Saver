<?php
session_start();
include 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['rollno'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$certId = $data['id'] ?? null;
$isAdmin = $data['is_admin'] ?? false;

if (!$certId) {
    echo json_encode(['success' => false, 'message' => 'Invalid certificate ID']);
    exit;
}

// Get certificate info
$stmt = $conn->prepare("SELECT rollno, file_path FROM certificates WHERE id = ?");
$stmt->bind_param("i", $certId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Certificate not found']);
    exit;
}

$cert = $result->fetch_assoc();

// Verify permissions
if (!$isAdmin && $cert['rollno'] !== $_SESSION['rollno']) {
    echo json_encode(['success' => false, 'message' => 'Not authorized to delete this certificate']);
    exit;
}

// Soft delete (mark as deleted)
$stmt = $conn->prepare("UPDATE certificates SET is_deleted = TRUE WHERE id = ?");
$stmt->bind_param("i", $certId);

if ($stmt->execute()) {
    // Optionally: Actually delete the file (uncomment if you want to delete files)
    // $file_path = __DIR__ . '/' . $cert['file_path'];
    // if (file_exists($file_path)) {
    //     unlink($file_path);
    // }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$conn->close();
?>