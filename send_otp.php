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
$rollno = trim((string)($data['rollno'] ?? ''));
$email = strtolower(trim((string)($data['email'] ?? '')));
$otp = preg_replace('/\D+/', '', (string)($data['otp'] ?? ''));

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
        
        // Store OTP in database (use MySQL time to avoid timezone mismatch)
        $stmt = $conn->prepare("INSERT INTO otp_verifications (rollno, email, otp, expires_at) VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))");
        if (!$stmt) {
            $respond(500, [
                'success' => false,
                'message' => 'OTP table missing. Please run updated setup.sql / migration.',
                'debug' => $conn->error
            ]);
        }
        $stmt->bind_param("sss", $rollno, $email, $otp_code);
        
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
        if (strlen($otp) !== 6) {
            $respond(400, ['success' => false, 'message' => 'OTP must be 6 digits']);
        }
        // Verify OTP (MySQL time)
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
            // Give a precise reason to the UI
            $debug_stmt = $conn->prepare("SELECT otp, is_used, expires_at, created_at FROM otp_verifications WHERE rollno = ? AND email = ? ORDER BY created_at DESC LIMIT 1");
            if ($debug_stmt) {
                $debug_stmt->bind_param("ss", $rollno, $email);
                $debug_stmt->execute();
                $debug_res = $debug_stmt->get_result();
                if ($debug_res->num_rows === 0) {
                    $respond(400, ['success' => false, 'message' => 'No OTP found for this email. Please resend OTP.']);
                }

                $row = $debug_res->fetch_assoc();

                // Normalize comparisons
                $dbOtp = preg_replace('/\D+/', '', (string)$row['otp']);
                $isUsed = (bool)$row['is_used'];

                // Check expiration using MySQL directly
                $expCheck = $conn->prepare("SELECT (NOW() > ?) AS expired");
                $expired = null;
                if ($expCheck) {
                    $expCheck->bind_param("s", $row['expires_at']);
                    $expCheck->execute();
                    $expRes = $expCheck->get_result();
                    $expired = (bool)($expRes->fetch_assoc()['expired'] ?? false);
                }

                if ($isUsed) {
                    $respond(400, ['success' => false, 'message' => 'This OTP was already used. Please resend OTP.']);
                }
                if ($expired) {
                    $respond(400, ['success' => false, 'message' => 'OTP expired. Please resend OTP.']);
                }
                if ($dbOtp !== $otp) {
                    $respond(400, ['success' => false, 'message' => 'Incorrect OTP. Please try again.']);
                }
            }

            $respond(400, ['success' => false, 'message' => 'Invalid OTP. Please resend OTP and try again.']);
        }
        break;
        
    default:
        $respond(400, ['success' => false, 'message' => 'Invalid action']);
}

$conn->close();
?>
