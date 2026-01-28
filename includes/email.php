<?php
/**
 * IMPORTANT (XAMPP):
 * PHP's mail() will fail on Windows/XAMPP unless you configure a mail server.
 * So we send OTP via Gmail SMTP using an App Password.
 *
 * SECURITY:
 * Do NOT hardcode your app password in this repository.
 * Put credentials in `includes/secrets.php` (created locally) or environment variables.
 */

// Load secrets if present (DO NOT commit secrets.php)
// Expected variables:
//   $SMTP_USER = 'your@gmail.com';
//   $SMTP_PASS = 'your app password (16 chars with spaces is fine)';
// Optional:
//   $SMTP_FROM_NAME = '...';
if (file_exists(__DIR__ . '/secrets.php')) {
    require __DIR__ . '/secrets.php';
}

function _certHubGetSmtpConfig(): array {
    $smtp_user = $GLOBALS['SMTP_USER'] ?? getenv('CERT_HUB_SMTP_USER') ?? '';
    $smtp_pass = $GLOBALS['SMTP_PASS'] ?? getenv('CERT_HUB_SMTP_PASS') ?? '';
    $from_name = $GLOBALS['SMTP_FROM_NAME'] ?? "Yaswanth's AI Certificate Management Hub";

    // Gmail options
    return [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'user' => $smtp_user,
        'pass' => $smtp_pass,
        'from_email' => $smtp_user ?: 'noreply@example.com',
        'from_name' => $from_name,
    ];
}

function _certHubBuildOtpEmailHtml(string $otp, string $rollno): string {
    return "
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
}

function _certHubSmtpReadLine($fp): string {
    $data = '';
    while (!feof($fp)) {
        $line = fgets($fp, 515);
        if ($line === false) break;
        $data .= $line;
        // Response lines that continue have a dash after the status code: "250-"
        if (preg_match('/^\d{3}\s/', $line)) break;
    }
    return $data;
}

function _certHubSmtpExpect($fp, array $codes): void {
    $resp = _certHubSmtpReadLine($fp);
    $ok = false;
    foreach ($codes as $code) {
        if (str_starts_with($resp, (string)$code)) {
            $ok = true;
            break;
        }
    }
    if (!$ok) {
        throw new Exception("SMTP error: " . trim($resp));
    }
}

function _certHubSmtpWrite($fp, string $cmd): void {
    fwrite($fp, $cmd . "\r\n");
}

function sendOTPEmail($to_email, $otp, $rollno): bool {
    $cfg = _certHubGetSmtpConfig();
    if (!$cfg['user'] || !$cfg['pass']) {
        // Not configured
        return false;
    }

    $subject = "OTP Verification - Certificate Management Hub";
    $html = _certHubBuildOtpEmailHtml($otp, $rollno);

    $from_email = $cfg['from_email'];
    $from_name = $cfg['from_name'];

    // Connect
    $fp = stream_socket_client(
        "tcp://{$cfg['host']}:{$cfg['port']}",
        $errno,
        $errstr,
        15,
        STREAM_CLIENT_CONNECT
    );
    if (!$fp) {
        return false;
    }

    try {
        stream_set_timeout($fp, 15);
        _certHubSmtpExpect($fp, [220]);

        _certHubSmtpWrite($fp, "EHLO localhost");
        _certHubSmtpExpect($fp, [250]);

        // StartTLS
        _certHubSmtpWrite($fp, "STARTTLS");
        _certHubSmtpExpect($fp, [220]);
        if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            throw new Exception("Failed to enable TLS");
        }

        // EHLO again after TLS
        _certHubSmtpWrite($fp, "EHLO localhost");
        _certHubSmtpExpect($fp, [250]);

        // AUTH LOGIN
        _certHubSmtpWrite($fp, "AUTH LOGIN");
        _certHubSmtpExpect($fp, [334]);
        _certHubSmtpWrite($fp, base64_encode($cfg['user']));
        _certHubSmtpExpect($fp, [334]);
        _certHubSmtpWrite($fp, base64_encode($cfg['pass']));
        _certHubSmtpExpect($fp, [235]);

        // Mail headers
        $encoded_from = '=?UTF-8?B?' . base64_encode($from_name) . '?=';
        $headers = [];
        $headers[] = "From: {$encoded_from} <{$from_email}>";
        $headers[] = "To: <{$to_email}>";
        $headers[] = "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=";
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-Type: text/html; charset=UTF-8";
        $headers[] = "Content-Transfer-Encoding: 8bit";

        $data = implode("\r\n", $headers) . "\r\n\r\n" . $html;

        _certHubSmtpWrite($fp, "MAIL FROM:<{$from_email}>");
        _certHubSmtpExpect($fp, [250]);
        _certHubSmtpWrite($fp, "RCPT TO:<{$to_email}>");
        _certHubSmtpExpect($fp, [250, 251]);
        _certHubSmtpWrite($fp, "DATA");
        _certHubSmtpExpect($fp, [354]);

        // End DATA with <CRLF>.<CRLF>
        $data = str_replace("\n", "\r\n", $data);
        fwrite($fp, $data . "\r\n.\r\n");
        _certHubSmtpExpect($fp, [250]);

        _certHubSmtpWrite($fp, "QUIT");
        fclose($fp);
        return true;
    } catch (Exception $e) {
        // Best effort quit
        if (is_resource($fp)) {
            @fwrite($fp, "QUIT\r\n");
            @fclose($fp);
        }
        return false;
    }
}
?>
