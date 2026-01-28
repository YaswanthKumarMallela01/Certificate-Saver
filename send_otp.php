<?php
header('Content-Type: application/json');
session_start();

include 'includes/db.php';
include 'includes/email.php';

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';
$rollno = $data['rollno'] ?? '';
$email = $data['email'] ?? '';
$otp = $data['otp'] ?? '';

switch ($action) {
    case 'send_otp':
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email address']);
            exit;
        }
        
        // Generate 6-digit OTP
        $otp_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Set expiration time (10 minutes)
        $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        
        // Store OTP in database
        $stmt = $conn->prepare("INSERT INTO otp_verifications (rollno, email, otp, expires_at) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $rollno, $email, $otp_code, $expires_at);
        
        if ($stmt->execute()) {
            // Send email
            if (sendOTPEmail($email, $otp_code, $rollno)) {
                echo json_encode(['success' => true, 'message' => 'OTP sent to your email']);
            } else {
                // Even if email fails, OTP is stored (for testing)
                echo json_encode(['success' => true, 'message' => 'OTP generated. Email may not have been sent. OTP: ' . $otp_code]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to generate OTP']);
        }
        break;
        
    case 'verify_otp':
        // Verify OTP
        $stmt = $conn->prepare("SELECT * FROM otp_verifications WHERE rollno = ? AND email = ? AND otp = ? AND is_used = FALSE AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1");
        $stmt->bind_param("sss", $rollno, $email, $otp);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Mark OTP as used
            $otp_data = $result->fetch_assoc();
            $update_stmt = $conn->prepare("UPDATE otp_verifications SET is_used = TRUE WHERE id = ?");
            $update_stmt->bind_param("i", $otp_data['id']);
            $update_stmt->execute();
            
            // Update user email and verification status
            $update_user = $conn->prepare("UPDATE users SET email = ?, is_verified = TRUE WHERE rollno = ?");
            $update_user->bind_param("ss", $email, $rollno);
            $update_user->execute();
            
            echo json_encode(['success' => true, 'message' => 'Email verified successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid or expired OTP']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

$conn->close();
?>
