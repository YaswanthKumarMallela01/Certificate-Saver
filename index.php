<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yaswanth's AI Certificate Management Hub - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --primary-light: #e6e9ff;
            --success: #4cc9f0;
            --error: #f72585;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --border-radius: 8px;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7ff;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            line-height: 1.6;
        }

        :root {
            --page-width: 560px;
        }

        .topbar {
            width: 100%;
            max-width: var(--page-width);
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 18px 20px;
            margin-bottom: 18px;
        }

        .topbar-title {
            text-align: center;
            color: var(--primary);
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .topbar-datetime {
            display: flex;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
            font-size: 13px;
            color: var(--gray);
        }

        .auth-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            width: 100%;
            max-width: var(--page-width);
            padding: 40px;
            transition: var(--transition);
        }

        /* Slightly more modern feel (consistent with dashboard cards) */
        .topbar, .auth-container {
            border-radius: 12px;
        }

        h1 {
            color: var(--dark);
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
            font-size: 24px;
        }

        .form-container {
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #e0e0e0;
            border-radius: var(--border-radius);
            font-size: 15px;
            transition: var(--transition);
            background-color: #f9f9f9;
        }

        input:focus {
            border-color: var(--primary);
            outline: none;
            background-color: white;
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 10px;
        }

        button:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        button:active {
            transform: translateY(0);
        }

        .switch-text {
            text-align: center;
            margin-top: 20px;
            color: var(--gray);
            font-size: 14px;
        }

        .switch {
            color: var(--primary);
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
        }

        .switch:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .message {
            padding: 14px;
            border-radius: var(--border-radius);
            margin-top: 20px;
            font-size: 14px;
            display: none;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .success {
            background-color: rgba(76, 201, 240, 0.1);
            color: #1a936f;
            border-left: 3px solid #1a936f;
            display: block;
        }

        .error {
            background-color: rgba(247, 37, 133, 0.1);
            color: var(--error);
            border-left: 3px solid var(--error);
            display: block;
        }

        .hidden {
            display: none;
        }

        .password-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 12px;
            cursor: pointer;
            color: var(--gray);
            transition: var(--transition);
        }

        .password-toggle:hover {
            color: var(--primary);
        }

        .otp-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .otp-meta {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            margin: 10px 0 12px;
            font-size: 13px;
            color: var(--gray);
        }

        .otp-timer {
            font-weight: 600;
            color: var(--primary-dark);
        }

        .otp-status {
            font-weight: 500;
        }

        .otp-input-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 15px;
            align-items: stretch;
        }

        .otp-input-group input {
            flex: 1;
            width: 100%;
        }

        #otp-code {
            min-height: 52px;
            font-size: 18px;
            letter-spacing: 6px;
            text-align: center;
            padding: 14px 16px;
            background: linear-gradient(180deg, #ffffff 0%, #fbfbff 100%);
            border: 1px solid #e7e9f2;
            border-radius: 12px;
            outline: none;
            pointer-events: auto;
            z-index: 1;
        }

        #otp-code:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .otp-input-group button {
            flex: 0 0 auto;
            padding: 14px 20px;
            margin-top: 0;
            min-height: 52px;
            border-radius: 12px;
        }

        .resend-otp {
            text-align: center;
            margin-top: 10px;
        }

        .resend-otp a {
            color: var(--primary);
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .resend-otp a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="topbar-title">Yaswanth's AI Certificate Management Hub</div>
        <div class="topbar-datetime">
            <div id="day"></div>
            <div id="date"></div>
            <div id="time"></div>
        </div>
    </div>

    <div class="auth-container">
        <h1 id="form-title">Login</h1>
        
        <!-- Login Form -->
        <div id="login-form" class="form-container">
            <div class="form-group">
                <input type="text" id="login-rollno" placeholder="Roll Number/ Name" required>
            </div>
            <div class="form-group password-container">
                <input type="password" id="login-password" placeholder="Password" required>
                <span class="password-toggle" id="login-toggle">👁️</span>
            </div>
            <button id="login-btn">Login</button>
            <div class="switch-text">
                Don't have an account? <span class="switch" id="show-register">Register</span>
            </div>
        </div>
        
        <!-- Registration Form -->
        <div id="register-form" class="form-container hidden">
            <div class="form-group">
                <input type="text" id="register-rollno" placeholder="Roll Number" required>
            </div>
            <div class="form-group">
                <input type="email" id="register-email" placeholder="Email Address" required>
            </div>
            <div class="form-group password-container">
                <input type="password" id="register-password" placeholder="Password" required>
                <span class="password-toggle" id="register-toggle">👁️</span>
            </div>
            <div class="form-group password-container">
                <input type="password" id="register-confirm-password" placeholder="Confirm Password" required>
                <span class="password-toggle" id="confirm-toggle">👁️</span>
            </div>
            
            <!-- OTP Verification Section -->
            <div id="otp-section" class="otp-section hidden">
                <div class="form-group">
                    <label style="font-size: 14px; color: var(--gray); margin-bottom: 8px; display: block;">
                        Enter OTP sent to your email:
                    </label>
                    <div class="otp-meta">
                        <div class="otp-status" id="otpStatus">Waiting for OTP…</div>
                        <div class="otp-timer" id="otpTimer">10:00</div>
                    </div>
                    <div class="otp-input-group">
                        <input type="text" id="otp-code" inputmode="numeric" autocomplete="one-time-code" placeholder="Enter 6-digit OTP" maxlength="6" pattern="[0-9]{6}">
                        <button type="button" id="verify-otp-btn">Verify</button>
                    </div>
                    <div class="resend-otp">
                        <a href="#" id="resend-otp-link">Resend OTP</a>
                    </div>
                </div>
            </div>
            
            <button id="register-btn">Register</button>
            <div class="switch-text">
                Already have an account? <span class="switch" id="show-login">Login</span>
            </div>
        </div>
        
        <!-- Message Display -->
        <div id="message" class="message"></div>
    </div>

    <footer style="margin-top: 24px; text-align: center; font-size: 13px; color: var(--gray);">
        <div style="display: flex; justify-content: center; gap: 18px; flex-wrap: wrap; margin-bottom: 6px;">
            <a href="https://github.com/YaswanthKumarMallela01" target="_blank" rel="noopener noreferrer" style="color: var(--primary); text-decoration: none; font-weight: 500;">
                🔗 GitHub
            </a>
            <a href="https://www.linkedin.com/in/yaswanthkumar1/" target="_blank" rel="noopener noreferrer" style="color: var(--primary); text-decoration: none; font-weight: 500;">
                💼 LinkedIn
            </a>
            <a href="https://github.com/YaswanthKumarMallela01/Certificate-Saver" target="_blank" rel="noopener noreferrer" style="color: var(--primary); text-decoration: none; font-weight: 500;">
                📦 Repo
            </a>
        </div>
        <div>© 2026 Yaswanth's AI Certificate Management Hub</div>
    </footer>

    <script>
        // DOM Elements
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');
        const showRegister = document.getElementById('show-register');
        const showLogin = document.getElementById('show-login');
        const formTitle = document.getElementById('form-title');
        const messageDiv = document.getElementById('message');

        // Password toggle functionality
        function setupPasswordToggle(toggleId, inputId) {
            const toggle = document.getElementById(toggleId);
            const input = document.getElementById(inputId);
            
            toggle.addEventListener('click', () => {
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                toggle.textContent = isPassword ? '👁️‍🗨️' : '👁️';
            });
        }
        
        setupPasswordToggle('login-toggle', 'login-password');
        setupPasswordToggle('register-toggle', 'register-password');
        setupPasswordToggle('confirm-toggle', 'register-confirm-password');

        // Toggle between forms
        showRegister.addEventListener('click', () => {
            loginForm.classList.add('hidden');
            registerForm.classList.remove('hidden');
            formTitle.textContent = 'Register';
        });

        showLogin.addEventListener('click', () => {
            registerForm.classList.add('hidden');
            loginForm.classList.remove('hidden');
            formTitle.textContent = 'Login';
        });

        // Handle login
        document.getElementById('login-btn').addEventListener('click', async () => {
            const rollno = document.getElementById('login-rollno').value;
            const password = document.getElementById('login-password').value;
            
            if (!rollno || !password) {
                showMessage('Please fill in all fields', 'error');
                return;
            }

            try {
                const response = await fetch('auth.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'login', rollno, password })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showMessage('Login successful! Redirecting...', 'success');
                    // Redirect to dashboard or another page
                    setTimeout(() => { window.location.href = 'dashboard.php'; }, 1000);
                } else {
                    showMessage(data.message || 'Login failed', 'error');
                }
            } catch (error) {
                showMessage('An error occurred. Please try again.', 'error');
                console.error(error);
            }
        });

        // Safe JSON parsing (prevents generic errors)
        async function safeJson(response) {
            const text = await response.text();
            try {
                return JSON.parse(text);
            } catch {
                return { success: false, message: text || `HTTP ${response.status}` };
            }
        }

        let registrationData = {};
        let isOTPVerified = false;
        let otpRemainingSeconds = 0;
        let otpTimerInterval = null;

        function setOtpUi(statusText, timerSeconds) {
            const statusEl = document.getElementById('otpStatus');
            const timerEl = document.getElementById('otpTimer');
            if (statusEl) statusEl.textContent = statusText;
            if (timerEl) {
                const m = String(Math.floor(timerSeconds / 60)).padStart(2, '0');
                const s = String(timerSeconds % 60).padStart(2, '0');
                timerEl.textContent = `${m}:${s}`;
            }
        }

        function startOtpCountdown(seconds) {
            otpRemainingSeconds = seconds;
            const verifyBtn = document.getElementById('verify-otp-btn');
            const registerBtn = document.getElementById('register-btn');
            if (verifyBtn) verifyBtn.disabled = false;
            if (registerBtn) registerBtn.disabled = true;

            setOtpUi('OTP sent. Enter it below.', otpRemainingSeconds);

            if (otpTimerInterval) clearInterval(otpTimerInterval);
            otpTimerInterval = setInterval(() => {
                otpRemainingSeconds -= 1;
                if (otpRemainingSeconds <= 0) {
                    otpRemainingSeconds = 0;
                    clearInterval(otpTimerInterval);
                    otpTimerInterval = null;
                    if (verifyBtn) verifyBtn.disabled = true;
                    setOtpUi('OTP expired. Please resend OTP.', otpRemainingSeconds);
                    showMessage('OTP expired. Click "Resend OTP".', 'error');
                    return;
                }
                setOtpUi('OTP sent. Enter it below.', otpRemainingSeconds);
            }, 1000);
        }

        // Handle registration
        document.getElementById('register-btn').addEventListener('click', async () => {
            const rollno = document.getElementById('register-rollno').value;
            const email = document.getElementById('register-email').value;
            const password = document.getElementById('register-password').value;
            const confirmPassword = document.getElementById('register-confirm-password').value;
            const otpSection = document.getElementById('otp-section');
            
            if (!rollno || !email || !password || !confirmPassword) {
                showMessage('Please fill in all fields', 'error');
                return;
            }
            
            if (password !== confirmPassword) {
                showMessage('Passwords do not match', 'error');
                return;
            }

            // If OTP section is hidden, send OTP first
            if (otpSection.classList.contains('hidden')) {
                isOTPVerified = false;
                try {
                    const response = await fetch('send_otp.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'send_otp', rollno, email })
                    });
                    
                    const data = await safeJson(response);
                    
                    if (data.success) {
                        registrationData = { rollno, email, password };
                        otpSection.classList.remove('hidden');
                        document.getElementById('register-btn').textContent = 'Complete Registration';
                        document.getElementById('register-btn').disabled = true;
                        // Start 10-minute timer
                        startOtpCountdown(10 * 60);
                        showMessage(data.message || 'OTP sent to your email', 'success');
                        // Focus OTP input so user can type immediately
                        const otpInput = document.getElementById('otp-code');
                        if (otpInput) otpInput.focus();
                    } else {
                        showMessage(data.message || 'Failed to send OTP', 'error');
                    }
                } catch (error) {
                    showMessage('An error occurred. Please try again.', 'error');
                    console.error(error);
                }
            } else {
                // OTP verification already done, complete registration
                if (!isOTPVerified) {
                    showMessage('Please verify your email with OTP first', 'error');
                    return;
                }
                try {
                    const response = await fetch('auth.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'register', rollno: registrationData.rollno, password: registrationData.password, email: registrationData.email })
                    });
                    
                    const data = await safeJson(response);
                    
                    if (data.success) {
                        showMessage('Registration successful! You can now login.', 'success');
                        setTimeout(() => {
                            registerForm.classList.add('hidden');
                            loginForm.classList.remove('hidden');
                            formTitle.textContent = 'Login';
                            otpSection.classList.add('hidden');
                            document.getElementById('register-btn').textContent = 'Register';
                            document.getElementById('register-btn').disabled = false;
                            isOTPVerified = false;
                            // Clear form
                            document.getElementById('register-rollno').value = '';
                            document.getElementById('register-email').value = '';
                            document.getElementById('register-password').value = '';
                            document.getElementById('register-confirm-password').value = '';
                            document.getElementById('otp-code').value = '';
                        }, 2000);
                    } else {
                        showMessage(data.message || 'Registration failed', 'error');
                    }
                } catch (error) {
                    showMessage('An error occurred. Please try again.', 'error');
                    console.error(error);
                }
            }
        });

        // Handle OTP verification
        document.getElementById('verify-otp-btn').addEventListener('click', async () => {
            const otp = (document.getElementById('otp-code').value || '').replace(/\D+/g, '').slice(0, 6);
            
            if (!otp || otp.length !== 6) {
                showMessage('Please enter a valid 6-digit OTP', 'error');
                return;
            }

            try {
                const response = await fetch('send_otp.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        action: 'verify_otp', 
                        rollno: registrationData.rollno, 
                        email: registrationData.email,
                        otp: otp
                    })
                });
                
                const data = await safeJson(response);
                
                if (data.success) {
                    isOTPVerified = true;
                    document.getElementById('register-btn').disabled = false;
                    const verifyBtn = document.getElementById('verify-otp-btn');
                    if (verifyBtn) verifyBtn.disabled = true;
                    if (otpTimerInterval) {
                        clearInterval(otpTimerInterval);
                        otpTimerInterval = null;
                    }
                    setOtpUi('OTP verified successfully.', otpRemainingSeconds);
                    showMessage('Email verified! Click "Complete Registration" to finish.', 'success');
                } else {
                    showMessage(data.message || 'Invalid OTP', 'error');
                }
            } catch (error) {
                showMessage('An error occurred. Please try again.', 'error');
                console.error(error);
            }
        });

        // Keep OTP input digits-only (avoids copy/paste spaces)
        const otpInputEl = document.getElementById('otp-code');
        if (otpInputEl) {
            otpInputEl.addEventListener('input', () => {
                otpInputEl.value = (otpInputEl.value || '').replace(/\D+/g, '').slice(0, 6);
            });
        }

        // Handle resend OTP
        document.getElementById('resend-otp-link').addEventListener('click', async (e) => {
            e.preventDefault();
            if (!registrationData.rollno || !registrationData.email) {
                showMessage('Please fill in roll number and email first', 'error');
                return;
            }

            try {
                const response = await fetch('send_otp.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'send_otp', rollno: registrationData.rollno, email: registrationData.email })
                });
                
                const data = await safeJson(response);
                if (data.success) {
                    isOTPVerified = false;
                    startOtpCountdown(10 * 60);
                    const otpInput = document.getElementById('otp-code');
                    if (otpInput) otpInput.focus();
                }
                showMessage(data.message || 'OTP resent', data.success ? 'success' : 'error');
            } catch (error) {
                showMessage('An error occurred. Please try again.', 'error');
            }
        });

        // Allow Enter key to verify OTP
        document.getElementById('otp-code').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                document.getElementById('verify-otp-btn').click();
            }
        });

        // Helper function to show messages
        function showMessage(message, type) {
            messageDiv.textContent = message;
            messageDiv.className = `message ${type}`;
            
            // Hide message after 5 seconds
            setTimeout(() => {
                messageDiv.className = 'message';
            }, 5000);
        }

        // Topbar date/time/day (horizontal)
        function updateDateTime() {
            const now = new Date();
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

            const day = days[now.getDay()];
            const date = `${months[now.getMonth()]} ${now.getDate()}, ${now.getFullYear()}`;
            const time = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });

            document.getElementById('day').textContent = day;
            document.getElementById('date').textContent = date;
            document.getElementById('time').textContent = time;
        }

        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>
</body>
</html>