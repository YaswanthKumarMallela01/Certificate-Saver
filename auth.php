<?php
header('Content-Type: application/json');

// Use centralized database connection
include 'includes/db.php';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';
$rollno = $data['rollno'] ?? '';
$password = $data['password'] ?? '';

// Process actions
switch ($action) {
    case 'login':
        // Check if user exists
        $stmt = $conn->prepare("SELECT password, is_admin FROM users WHERE rollno = ?");
        $stmt->bind_param("s", $rollno);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }
        
        $user = $result->fetch_assoc();
        
        // Verify password (use password_verify() if you hashed passwords)
        if ($password === $user['password']) {
            session_start();
            $_SESSION['rollno'] = $rollno;
            $_SESSION['is_admin'] = (bool)$user['is_admin'];
            echo json_encode([
                'success' => true, 
                'message' => 'Login successful',
                'is_admin' => $user['is_admin']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Incorrect password']);
        }
        break;
        
    case 'register':
        $email = $data['email'] ?? '';
        
        // Check if user already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE rollno = ?");
        $stmt->bind_param("s", $rollno);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'User already exists']);
            exit;
        }
        
        // Verify email is verified (check OTP table)
        if ($email) {
            $verify_stmt = $conn->prepare("SELECT * FROM otp_verifications WHERE rollno = ? AND email = ? AND is_used = TRUE ORDER BY created_at DESC LIMIT 1");
            $verify_stmt->bind_param("ss", $rollno, $email);
            $verify_stmt->execute();
            $verify_result = $verify_stmt->get_result();
            
            if ($verify_result->num_rows === 0) {
                echo json_encode(['success' => false, 'message' => 'Please verify your email first']);
                exit;
            }
        }
        
        // Insert new user (in production, hash the password!)
        $is_admin = ($rollno === 'admin') ? 1 : 0; // Auto-set admin flag if rollno is 'admin'
        $stmt = $conn->prepare("INSERT INTO users (rollno, email, password, is_admin, is_verified) VALUES (?, ?, ?, ?, ?)");
        $is_verified = $email ? 1 : 0;
        $stmt->bind_param("sssii", $rollno, $email, $password, $is_admin, $is_verified);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Registration successful']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration failed']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

$conn->close();
?>