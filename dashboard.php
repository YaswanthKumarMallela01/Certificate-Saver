<?php
session_start();
if (!isset($_SESSION['rollno'])) {
    header("Location: index.html");
    exit();
}

include 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yaswanth's AI Certificate Management Hub - Dashboard</title>
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
            min-height: 100vh;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            flex: 1;
        }

        header {
            background: white;
            box-shadow: var(--shadow);
            padding: 20px 0;
            margin-bottom: 30px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .welcome {
            font-size: 18px;
        }

        .welcome span {
            font-weight: 600;
            color: var(--primary);
        }

        .admin-link {
            margin-left: 15px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .admin-link:hover {
            text-decoration: underline;
        }

        .logout-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
        }

        .logout-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 25px;
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .card h2 {
            margin-bottom: 20px;
            color: var(--dark);
            font-size: 20px;
            font-weight: 600;
        }

        .upload-form input[type="file"] {
            display: none;
        }

        .file-picker {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px;
            border: 1px solid #e7e9f2;
            border-radius: 12px;
            background: linear-gradient(180deg, #ffffff 0%, #fbfbff 100%);
            margin-bottom: 15px;
            transition: var(--transition);
        }

        .file-picker:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .file-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid rgba(67, 97, 238, 0.25);
            background: rgba(67, 97, 238, 0.08);
            color: var(--primary-dark);
            font-weight: 600;
            cursor: pointer;
            user-select: none;
            transition: var(--transition);
            white-space: nowrap;
        }

        .file-btn:hover {
            background: rgba(67, 97, 238, 0.14);
            transform: translateY(-1px);
        }

        .file-name {
            flex: 1;
            color: var(--gray);
            font-size: 14px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .upload-form button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: var(--border-radius);
            cursor: pointer;
            width: 100%;
            font-weight: 500;
            transition: var(--transition);
        }

        .upload-form button:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .certificate-list {
            list-style: none;
            max-height: 500px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .certificate-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: var(--transition);
        }

        .certificate-item:hover {
            background-color: var(--primary-light);
        }

        .certificate-item:last-child {
            border-bottom: none;
        }

        .certificate-info {
            flex: 1;
        }

        .certificate-name {
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .certificate-date {
            color: var(--gray);
            font-size: 14px;
        }

        .certificate-actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            padding: 8px 16px;
            border-radius: var(--border-radius);
            border: none;
            cursor: pointer;
            font-size: 14px;
            transition: var(--transition);
            color: white;
            font-weight: 500;
        }

        .view-btn {
            background: var(--primary);
        }

        .view-btn:hover {
            background: var(--primary-dark);
        }

        .delete-btn {
            background: var(--error);
        }

        .delete-btn:hover {
            background: #d3166d;
        }

        .message {
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
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

        /* AI Assistant Styles */
        .ai-assistant {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 25px;
            margin-top: 30px;
        }

        .ai-assistant h2 {
            margin-bottom: 20px;
            color: var(--dark);
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ai-assistant h2::before {
            content: "✨";
        }

        .chat-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .chat-message {
            padding: 12px 16px;
            border-radius: var(--border-radius);
            max-width: 80%;
            line-height: 1.5;
            position: relative;
        }

        .user-message {
            background-color: #e3f2fd;
            align-self: flex-end;
            border-bottom-right-radius: 0;
        }

        .bot-message {
            background-color: #f1f1f1;
            align-self: flex-start;
            border-bottom-left-radius: 0;
        }

        .chat-input {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .chat-input input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-family: 'Inter', sans-serif;
        }

        .chat-input button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
        }

        .chat-input button:hover {
            background: var(--primary-dark);
        }

        /* Typing animation */
        .typing-animation {
            display: inline-block;
        }
        
        .typing-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--gray);
            margin-right: 4px;
            animation: typingAnimation 1.4s infinite both;
        }
        
        @keyframes typingAnimation {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-5px); }
        }

        .app-title {
            text-align: center;
            color: var(--primary);
            font-size: 24px;
            font-weight: 600;
            padding: 15px 0;
            margin-bottom: 10px;
            border-bottom: 2px solid var(--primary-light);
        }

        .datetime-display {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            text-align: center;
            color: var(--gray);
            font-size: 14px;
            padding: 10px 0;
            margin-bottom: 10px;
        }

        footer {
            background: white;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px 0;
            margin-top: 40px;
            text-align: center;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .social-links a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .social-links a:hover {
            color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .footer-text {
            color: var(--gray);
            font-size: 12px;
        }
    </style>
</head>
<body>
    <header>
        <div class="app-title">🎓 Yaswanth's AI Certificate Management Hub</div>
        <div class="datetime-display" id="datetime-display">
            <span id="day"></span>
            <span id="date"></span>
            <span id="time"></span>
        </div>
        <div class="header-content">
            <div class="welcome">
                Welcome, <span><?php echo htmlspecialchars($_SESSION['rollno']); ?></span>
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <a href="admin.php" class="admin-link">(Admin Panel)</a>
                <?php endif; ?>
            </div>
            <button class="logout-btn" onclick="location.href='logout.php'">Logout</button>
        </div>
    </header>

    <div class="container">
        <?php if (isset($_GET['upload_success'])): ?>
            <div class="message success">Certificate uploaded successfully!</div>
        <?php elseif (isset($_GET['upload_error'])): ?>
            <div class="message error">Error uploading certificate. Please try again.</div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="card">
                <h2>Upload New Certificate</h2>
                <form class="upload-form" action="upload.php" method="POST" enctype="multipart/form-data">
                    <div class="file-picker">
                        <input id="certificateFile" type="file" name="certificate" accept=".pdf,.jpg,.jpeg,.png" required>
                        <label class="file-btn" for="certificateFile">📎 Choose file</label>
                        <div id="chosenFileName" class="file-name">No file selected</div>
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label for="certificateDesc" style="display:block; font-size:14px; color: var(--gray); margin-bottom:8px;">Certificate description (optional)</label>
                        <textarea id="certificateDesc" name="description" rows="3" maxlength="2000" placeholder="Example: Completed Google AI Essentials (2026). Covered prompt engineering, model basics, and responsible AI." style="width:100%; padding:12px 14px; border:1px solid #e7e9f2; border-radius:12px; resize:vertical; font-family: 'Inter', sans-serif;"></textarea>
                    </div>
                    <button type="submit">Upload Certificate</button>
                </form>
            </div>

            <div class="card">
                <h2>Your Certificates</h2>
                <ul class="certificate-list">
                    <?php
                    $rollno = $_SESSION['rollno'];
                    $sql = "SELECT * FROM certificates WHERE rollno = ? AND is_deleted = FALSE ORDER BY upload_date DESC";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $rollno);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<li class="certificate-item">';
                            echo '<div class="certificate-info">';
                            echo '<div class="certificate-name">' . htmlspecialchars($row['certificate_name']) . '</div>';
                            if (!empty($row['description'])) {
                                echo '<div class="certificate-date" style="margin-top:6px;"><strong>Description:</strong> ' . htmlspecialchars($row['description']) . '</div>';
                            }
                            echo '<div class="certificate-date">Uploaded on ' . date('M d, Y', strtotime($row['upload_date'])) . '</div>';
                            echo '</div>';
                            echo '<div class="certificate-actions">';
                            echo '<button class="action-btn view-btn" onclick="location.href=\'view.php?id=' . $row['id'] . '\'">View</button>';
                            echo '<button class="action-btn delete-btn" onclick="confirmDelete(' . $row['id'] . ')">Delete</button>';
                            echo '</div>';
                            echo '</li>';
                        }
                    } else {
                        echo '<li class="certificate-item">No certificates uploaded yet.</li>';
                    }
                    ?>
                </ul>
            </div>
        </div>

        <!-- AI Career Assistant Section -->
        <div class="ai-assistant">
            <h2>AI Career Assistant</h2>
            <div class="chat-container" id="chatContainer">
                <div class="chat-message bot-message">
                    Hello! I'm your AI career assistant. I have access to your uploaded certificates and their descriptions. I can analyze your current skills and recommend FREE and PAID certifications to enhance your career in AI/ML. Ask me anything about certifications, career paths, or skill development!
                </div>
            </div>
            <div class="chat-input">
                <input type="text" id="userInput" placeholder="Type your message here..." autocomplete="off">
                <button onclick="sendMessage()">Send</button>
            </div>
        </div>
    </div>

    <script>
        // Store conversation history for context
        let conversationHistory = [];

        function sendMessage() {
            const userInput = document.getElementById('userInput');
            const chatContainer = document.getElementById('chatContainer');
            
            if (userInput.value.trim() === '') return;
            
            // Add user message to chat
            const userMessage = userInput.value;
            addMessageToChat(userMessage, 'user');
            
            // Add to local history for context
            conversationHistory.push({ role: 'user', text: userMessage });
            
            // Create typing indicator
            const typingIndicator = document.createElement('div');
            typingIndicator.className = 'chat-message bot-message';
            typingIndicator.id = 'typingIndicator';
            typingIndicator.innerHTML = `
                <div class="typing-animation">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            `;
            chatContainer.appendChild(typingIndicator);
            chatContainer.scrollTop = chatContainer.scrollHeight;
            
            // Clear input immediately for better UX
            userInput.value = '';
            
            // Call server-side AI endpoint (keeps API key secure, includes certificate descriptions)
            fetch('ai_chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    message: userMessage,
                    history: conversationHistory.slice(-10) // Send last 10 messages for context
                })
            })
            .then(response => response.json())
            .then(data => {
                // Remove typing indicator
                const typingElement = document.getElementById('typingIndicator');
                if (typingElement) chatContainer.removeChild(typingElement);
                
                // Process and display response
                if (data.success && data.reply) {
                    const botResponse = data.reply;
                    
                    // Add to conversation history
                    conversationHistory.push({ role: 'model', text: botResponse });
                    
                    // Display formatted response
                    addMessageToChat(botResponse, 'bot');
                } else {
                    addMessageToChat(data.message || 'Sorry, I encountered an error. Please try again.', 'bot');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const typingElement = document.getElementById('typingIndicator');
                if (typingElement) chatContainer.removeChild(typingElement);
                addMessageToChat('Sorry, I encountered an error. Please try again.', 'bot');
            });
        }
        
        function addMessageToChat(message, sender) {
            const chatContainer = document.getElementById('chatContainer');
            const messageElement = document.createElement('div');
            messageElement.className = `chat-message ${sender}-message`;
            
            // Format the message with proper line breaks and lists
            let formattedMessage = message
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') // Bold text
                .replace(/\*(.*?)\*/g, '<em>$1</em>') // Italic text
                .replace(/# (.*?)(<br>|$)/g, '<h3>$1</h3>') // Headings
                .replace(/## (.*?)(<br>|$)/g, '<h4>$1</h4>') // Subheadings
                .replace(/\n/g, '<br>') // Line breaks
                .replace(/- (.*?)(<br>|$)/g, '<li>$1</li>') // List items
                .replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" target="_blank">$1</a>'); // Links
            
            // Wrap lists in ul tags if we find list items
            if (formattedMessage.includes('<li>')) {
                formattedMessage = formattedMessage.replace(/(<li>.*?<\/li>)+/g, '<ul>$&</ul>');
            }
            
            messageElement.innerHTML = formattedMessage;
            chatContainer.appendChild(messageElement);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
        
        // Allow sending message with Enter key
        document.getElementById('userInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        // Update date and time display
        function updateDateTime() {
            const now = new Date();
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            
            const day = days[now.getDay()];
            const date = now.getDate();
            const month = months[now.getMonth()];
            const year = now.getFullYear();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            document.getElementById('day').textContent = day;
            document.getElementById('date').textContent = `${month} ${date}, ${year}`;
            document.getElementById('time').textContent = `${hours}:${minutes}:${seconds}`;
        }
        
        // Update every second
        updateDateTime();
        setInterval(updateDateTime, 1000);

        // Modern file picker label
        const certFile = document.getElementById('certificateFile');
        const chosenFileName = document.getElementById('chosenFileName');
        if (certFile && chosenFileName) {
            certFile.addEventListener('change', () => {
                chosenFileName.textContent = certFile.files?.[0]?.name || 'No file selected';
            });
        }

        // Certificate deletion functionality
        function confirmDelete(certId) {
            if (confirm('Are you sure you want to delete this certificate? This action cannot be undone.')) {
                deleteCertificate(certId);
            }
        }

        async function deleteCertificate(certId) {
            try {
                const response = await fetch('delete_certificate.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        id: certId,
                        is_admin: false 
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Certificate deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete certificate'));
                }
            } catch (error) {
                console.error(error);
                alert('An error occurred. Please try again.');
            }
        }
    </script>

    <footer>
        <div class="footer-content">
            <div class="social-links">
                <a href="https://github.com/YaswanthKumarMallela01" target="_blank" rel="noopener noreferrer">
                    <span>🔗</span> GitHub Profile
                </a>
                <a href="https://www.linkedin.com/in/yaswanthkumar1/" target="_blank" rel="noopener noreferrer">
                    <span>💼</span> LinkedIn
                </a>
                <a href="https://github.com/YaswanthKumarMallela01/Certificate-Saver" target="_blank" rel="noopener noreferrer">
                    <span>📦</span> Repository
                </a>
            </div>
            <div class="footer-text">
                © 2026 Yaswanth's AI Certificate Management Hub. All rights reserved.
            </div>
        </div>
    </footer>
</body>
</html>
