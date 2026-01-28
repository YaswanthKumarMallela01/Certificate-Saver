<?php
session_start();
include 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['rollno'])) {
    header("Location: index.html");
    exit();
}

// Verify admin status
$stmt = $conn->prepare("SELECT is_admin FROM users WHERE rollno = ?");
$stmt->bind_param("s", $_SESSION['rollno']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || !$user['is_admin']) {
    header("Location: dashboard.php");
    exit();
}

if (!isset($_GET['rollno'])) {
    header("Location: admin.php");
    exit();
}

$student_rollno = $_GET['rollno'];

// Get student details
$studentStmt = $conn->prepare("SELECT rollno FROM users WHERE rollno = ?");
$studentStmt->bind_param("s", $student_rollno);
$studentStmt->execute();
$studentResult = $studentStmt->get_result();
$student = $studentResult->fetch_assoc();

// Get certificate count
$countStmt = $conn->prepare("SELECT COUNT(id) as cert_count FROM certificates WHERE rollno = ? AND is_deleted = FALSE");
$countStmt->bind_param("s", $student_rollno);
$countStmt->execute();
$countResult = $countStmt->get_result();
$certCount = $countResult->fetch_assoc()['cert_count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin View: <?php echo htmlspecialchars($student_rollno); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --error: #f72585;
            --success: #4cc9f0;
            --border-radius: 8px;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #f5f7ff;
            color: var(--dark);
            line-height: 1.6;
            padding: 0;
            margin: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background: white;
            box-shadow: var(--shadow);
            padding: 20px;
            margin-bottom: 30px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .student-id {
            color: var(--primary);
            font-weight: 600;
        }

        .student-name {
            color: var(--dark);
            font-size: 16px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: var(--border-radius);
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: var(--gray);
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 30px;
            margin-bottom: 30px;
        }

        .card-title {
            font-size: 20px;
            margin-bottom: 20px;
            color: var(--dark);
            font-weight: 600;
        }

        .student-info {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: var(--border-radius);
            min-width: 200px;
        }

        .info-label {
            font-size: 14px;
            color: var(--gray);
            margin-bottom: 5px;
        }

        .info-value {
            font-weight: 600;
            color: var(--primary);
        }

        .certificate-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .certificate-item {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: var(--transition);
        }

        .certificate-item:hover {
            background-color: rgba(67, 97, 238, 0.05);
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
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 14px;
            transition: var(--transition);
            text-decoration: none;
        }

        .view-btn {
            background: var(--primary);
        }

        .view-btn:hover {
            background: var(--primary-dark);
        }

        .download-btn {
            background: #4CAF50;
        }

        .download-btn:hover {
            background: #3e8e41;
        }

        .delete-btn {
            background: var(--error);
        }

        .delete-btn:hover {
            background: #d3166d;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--gray);
        }

        .message {
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            display: none;
        }

        .success {
            background: rgba(76, 201, 240, 0.1);
            color: #1a936f;
            border-left: 3px solid #1a936f;
            display: block;
        }

        .error {
            background: rgba(247, 37, 133, 0.1);
            color: var(--error);
            border-left: 3px solid var(--error);
            display: block;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .action-buttons {
                width: 100%;
                justify-content: flex-end;
            }
            
            .certificate-item {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .certificate-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div>
                <h1 class="page-title">Admin View</h1>
                <div class="student-id"><?php echo htmlspecialchars($student_rollno); ?></div>
            </div>
            <div class="action-buttons">
                <a href="admin.php" class="btn btn-secondary">Back to Admin</a>
                <a href="logout.php" class="btn btn-primary">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <a href="admin.php" class="back-link">← Back to All Students</a>
        
        <?php if (isset($_GET['delete_success'])): ?>
            <div class="message success">Certificate deleted successfully!</div>
        <?php elseif (isset($_GET['delete_error'])): ?>
            <div class="message error">Error deleting certificate. Please try again.</div>
        <?php endif; ?>

        <div class="card">
            <div class="student-info">
                <div class="info-item">
                    <div class="info-label">Roll Number</div>
                    <div class="info-value"><?php echo htmlspecialchars($student_rollno); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Total Certificates</div>
                    <div class="info-value"><?php echo $certCount; ?></div>
                </div>
            </div>

            <h2 class="card-title">Certificates</h2>
            <ul class="certificate-list">
                <?php
                $sql = "SELECT id, certificate_name, file_path, upload_date FROM certificates WHERE rollno = ? AND is_deleted = FALSE ORDER BY upload_date DESC";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $student_rollno);
                
                if (!$stmt->execute()) {
                    echo '<div class="error">Error loading certificates</div>';
                } else {
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $file_path = $row['file_path']; // Already stored as relative path
                            echo '<li class="certificate-item">';
                            echo '<div class="certificate-info">';
                            echo '<div class="certificate-name">' . htmlspecialchars($row['certificate_name']) . '</div>';
                            echo '<div class="certificate-date">Uploaded on ' . date('M d, Y', strtotime($row['upload_date'])) . '</div>';
                            echo '</div>';
                            echo '<div class="certificate-actions">';
                            echo '<a href="view.php?id=' . $row['id'] . '" target="_blank" class="action-btn view-btn">View</a>';
                            echo '<a href="' . htmlspecialchars($file_path) . '" download class="action-btn download-btn">Download</a>';
                            echo '<button class="action-btn delete-btn" onclick="confirmDelete(' . $row['id'] . ', true)">Delete</button>';
                            echo '</div>';
                            echo '</li>';
                        }
                    } else {
                        echo '<div class="empty-state">No certificates found for this student</div>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>

    <script>
        function confirmDelete(certId, isAdmin) {
            if (confirm('Are you sure you want to delete this certificate? This action cannot be undone.')) {
                deleteCertificate(certId, isAdmin);
            }
        }

        async function deleteCertificate(certId, isAdmin) {
            try {
                const response = await fetch('delete_certificate.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        id: certId,
                        is_admin: isAdmin 
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.location.href = 'admin_view.php?rollno=<?php echo urlencode($student_rollno); ?>&delete_success=1';
                } else {
                    window.location.href = 'admin_view.php?rollno=<?php echo urlencode($student_rollno); ?>&delete_error=1';
                }
            } catch (error) {
                console.error(error);
                alert('An error occurred. Please try again.');
            }
        }
    </script>
</body>
</html>