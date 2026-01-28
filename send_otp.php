<?php
header('Content-Type: application/json');
session_start();

include 'includes/db.php';
include 'includes/email.php';

$respond = function(int $status, array $payload) {
    http_response_code($status);
    echo json_encode($payload);
    exit;
};

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';
$rollno = $data['rollno'] ?? '';
$email = $data['email'] ?? '';
$otp = $data['otp'] ?? '';

switch ($action) {
    case 'send_otp':
        if (!$rollno) {
            $respond(400, ['success' => false, 'message' => 'Roll number is required']);
        }
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $respond(400, ['success' => false, 'message' => 'Invalid email address']);
        }
        
        // Generate 6-digit OTP
        $otp_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Set expiration time (10 minutes)
        $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        
        // Store OTP in database
        $stmt = $conn->prepare("INSERT INTO otp_verifications (rollno, email, otp, expires_at) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            $respond(500, [
                'success' => false,
                'message' => 'OTP table missing. Please run updated setup.sql / migration.',
                'debug' => $conn->error
            ]);
        }
        $stmt->bind_param("ssss", $rollno, $email, $otp_code, $expires_at);
        
        if ($stmt->execute()) {
            // Send email
            if (sendOTPEmail($email, $otp_code, $rollno)) {
                $respond(200, ['success' => true, 'message' => 'OTP sent to your email']);
            } else {
                // Email failed (most commonly: SMTP not configured)
                $respond(200, [
                    'success' => true,
                    'message' => 'OTP generated but email delivery failed. Configure SMTP in includes/secrets.php.',
                    'otp' => $otp_code
                ]);
            }
        } else {
            $respond(500, ['success' => false, 'message' => 'Failed to generate OTP', 'debug' => $stmt->error]);
        }
        break;
        
    case 'verify_otp':
        if (!$rollno || !$email || !$otp) {
            $respond(400, ['success' => false, 'message' => 'Roll number, email, and OTP are required']);
        }
        // Verify OTP
        $stmt = $conn->prepare("SELECT * FROM otp_verifications WHERE rollno = ? AND email = ? AND otp = ? AND is_used = FALSE AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1");
        if (!$stmt) {
            $respond(500, [
                'success' => false,
                'message' => 'OTP table missing. Please run updated setup.sql / migration.',
                'debug' => $conn->error
            ]);
        }
        $stmt->bind_param("sss", $rollno, $email, $otp);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Mark OTP as used
            $otp_data = $result->fetch_assoc();
            $update_stmt = $conn->prepare("UPDATE otp_verifications SET is_used = TRUE WHERE id = ?");
            if (!$update_stmt) {
                $respond(500, ['success' => false, 'message' => 'Database error', 'debug' => $conn->error]);
            }
            $update_stmt->bind_param("i", $otp_data['id']);
            $update_stmt->execute();
            
            // Update user email and verification status
            $update_user = $conn->prepare("UPDATE users SET email = ?, is_verified = TRUE WHERE rollno = ?");
            if (!$update_user) {
                $respond(500, [
                    'success' => false,
                    'message' => 'Users table missing email/is_verified columns. Please run updated setup.sql / migration.',
                    'debug' => $conn->error
                ]);
            }
            $update_user->bind_param("ss", $email, $rollno);
            $update_user->execute();
            
            $respond(200, ['success' => true, 'message' => 'Email verified successfully']);
        } else {
            $respond(400, ['success' => false, 'message' => 'Invalid or expired OTP']);
        }
        break;
        
    default:
        $respond(400, ['success' => false, 'message' => 'Invalid action']);
}

$conn->close();
?>
