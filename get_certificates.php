<?php
/**
 * Get Certificates API Endpoint
 * Returns user's certificate descriptions for client-side AI chat
 */

header('Content-Type: application/json');
session_start();

// Check if user is logged in
if (!isset($_SESSION['rollno'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

include 'includes/db.php';

$rollno = $_SESSION['rollno'];

// Fetch user's certificates with descriptions
$certificates = [];
$stmt = $conn->prepare("SELECT certificate_name, description, upload_date FROM certificates WHERE rollno = ? AND is_deleted = FALSE ORDER BY upload_date DESC");
if ($stmt) {
    $stmt->bind_param("s", $rollno);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $certificates[] = [
            'name' => $row['certificate_name'],
            'description' => $row['description'] ?? '',
            'date' => date('M d, Y', strtotime($row['upload_date']))
        ];
    }
    $stmt->close();
}

// Also return API key for client-side fallback (only if server-side fails)
if (file_exists(__DIR__ . '/includes/secrets.php')) {
    require __DIR__ . '/includes/secrets.php';
}
$api_key = $GLOBALS['GEMINI_API_KEY'] ?? '';

echo json_encode([
    'success' => true,
    'certificates' => $certificates,
    'api_key' => $api_key
]);

$conn->close();
?>
