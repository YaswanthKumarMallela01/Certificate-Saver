<?php
// Email configuration
function sendOTPEmail($to_email, $otp, $rollno) {
    $from_email = "aicertificatemanagement@gmail.com";
    $from_name = "Yaswanth's AI Certificate Management Hub";
    $subject = "OTP Verification - Certificate Management Hub";
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #4361ee 0%, #3a56d4 100%); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
            .otp-box { background: white; border: 2px dashed #4361ee; padding: 20px; text-align: center; margin: 20px 0; border-radius: 8px; }
            .otp-code { font-size: 32px; font-weight: bold; color: #4361ee; letter-spacing: 5px; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🎓 Certificate Management Hub</h1>
            </div>
            <div class='content'>
                <h2>Hello!</h2>
                <p>Thank you for registering with <strong>Yaswanth's AI Certificate Management Hub</strong>.</p>
                <p>Your Roll Number: <strong>$rollno</strong></p>
                <p>Please use the following OTP to verify your email address:</p>
                <div class='otp-box'>
                    <div class='otp-code'>$otp</div>
                </div>
                <p><strong>This OTP will expire in 10 minutes.</strong></p>
                <p>If you didn't request this OTP, please ignore this email.</p>
            </div>
            <div class='footer'>
                <p>© 2026 Yaswanth's AI Certificate Management Hub. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: $from_name <$from_email>" . "\r\n";
    $headers .= "Reply-To: $from_email" . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Send email using mail() function
    return mail($to_email, $subject, $message, $headers);
}

// Alternative function using PHPMailer (if available)
function sendOTPEmailSMTP($to_email, $otp, $rollno) {
    // Email configuration
    $smtp_host = 'smtp.gmail.com';
    $smtp_port = 587;
    $smtp_username = 'aicertificatemanagement@gmail.com';
    $smtp_password = 'veyz vjld inqp dwvt';
    
    $from_email = "aicertificatemanagement@gmail.com";
    $from_name = "Yaswanth's AI Certificate Management Hub";
    $subject = "OTP Verification - Certificate Management Hub";
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #4361ee 0%, #3a56d4 100%); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
            .otp-box { background: white; border: 2px dashed #4361ee; padding: 20px; text-align: center; margin: 20px 0; border-radius: 8px; }
            .otp-code { font-size: 32px; font-weight: bold; color: #4361ee; letter-spacing: 5px; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🎓 Certificate Management Hub</h1>
            </div>
            <div class='content'>
                <h2>Hello!</h2>
                <p>Thank you for registering with <strong>Yaswanth's AI Certificate Management Hub</strong>.</p>
                <p>Your Roll Number: <strong>$rollno</strong></p>
                <p>Please use the following OTP to verify your email address:</p>
                <div class='otp-box'>
                    <div class='otp-code'>$otp</div>
                </div>
                <p><strong>This OTP will expire in 10 minutes.</strong></p>
                <p>If you didn't request this OTP, please ignore this email.</p>
            </div>
            <div class='footer'>
                <p>© 2026 Yaswanth's AI Certificate Management Hub. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Try using PHPMailer if available, otherwise fall back to mail()
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        require_once 'PHPMailer/PHPMailer.php';
        require_once 'PHPMailer/SMTP.php';
        require_once 'PHPMailer/Exception.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = $smtp_host;
            $mail->SMTPAuth = true;
            $mail->Username = $smtp_username;
            $mail->Password = $smtp_password;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $smtp_port;
            
            $mail->setFrom($from_email, $from_name);
            $mail->addAddress($to_email);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            
            return $mail->send();
        } catch (Exception $e) {
            return false;
        }
    } else {
        // Fallback to basic mail() function
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: $from_name <$from_email>" . "\r\n";
        return mail($to_email, $subject, $message, $headers);
    }
}
?>
