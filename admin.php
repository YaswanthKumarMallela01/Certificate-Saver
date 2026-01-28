<?php
session_start();
if (!isset($_SESSION['rollno']) || $_SESSION['is_admin'] != true) {
    header("Location: index.html");
    exit();
}

require_once 'includes/db.php';

// Get all students with their certificate counts (only non-deleted certificates)
$sql = "SELECT u.rollno, COUNT(c.id) as cert_count 
        FROM users u 
        LEFT JOIN certificates c ON u.rollno = c.rollno AND c.is_deleted = FALSE
        WHERE u.is_admin = FALSE 
        GROUP BY u.rollno
        ORDER BY u.rollno";
$result = $conn->query($sql);

// Calculate total certificates by summing up all individual counts
$totalCerts = 0;
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $totalCerts += $row['cert_count'];
    }
    // Reset result pointer to use the same result set for the table display
    $result->data_seek(0);
}

// Get total student count
$totalStudentSql = "SELECT COUNT(rollno) as total FROM users WHERE is_admin = FALSE";
$totalStudentResult = $conn->query($totalStudentSql);
$totalStudents = $totalStudentResult->fetch_assoc()['total'];

// Calculate average certificates per student
$avgCerts = $totalStudents > 0 ? round($totalCerts / $totalStudents, 1) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7ff;
            color: var(--dark);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
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

        .button-group {
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

        .btn-danger {
            background-color: var(--error);
            color: white;
        }

        .btn-danger:hover {
            background-color: #d3166d;
        }

        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 30px;
            margin-bottom: 30px;
        }

        .card h2 {
            margin-bottom: 20px;
            color: var(--dark);
            font-size: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #f5f7ff;
            font-weight: 600;
            color: var(--primary);
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .view-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 14px;
            transition: var(--transition);
            text-decoration: none;
        }

        .view-btn:hover {
            background: var(--primary-dark);
        }

        .cert-count {
            font-weight: 600;
            color: var(--primary);
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

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 20px;
            text-align: center;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 600;
            color: var(--primary);
            margin: 10px 0;
        }

        .stat-label {
            color: var(--gray);
            font-size: 14px;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: var(--gray);
            font-style: italic;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="welcome">
                Welcome, Admin <span><?php echo htmlspecialchars($_SESSION['rollno']); ?></span>
            </div>
            <div class="button-group">
                <a href="dashboard.php" class="btn btn-primary">User Dashboard</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <?php if (isset($_GET['delete_success'])): ?>
            <div class="message success">Certificate deleted successfully!</div>
        <?php elseif (isset($_GET['delete_error'])): ?>
            <div class="message error">Error deleting certificate. Please try again.</div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-value"><?php echo $totalStudents; ?></div>
                <div class="stat-label">Total Students</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $totalCerts; ?></div>
                <div class="stat-label">Total Certificates</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $avgCerts; ?></div>
                <div class="stat-label">Avg. Certificates per Student</div>
            </div>
        </div>

        <div class="card">
            <h2>All Students</h2>
            <table>
                <thead>
                    <tr>
                        <th>Roll Number</th>
                        <th>Certificate Count</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['rollno']); ?></td>
                            <td><span class="cert-count"><?php echo $row['cert_count']; ?></span></td>
                            <td class="action-buttons">
                                <a href="admin_view.php?rollno=<?php echo urlencode($row['rollno']); ?>" class="view-btn">View Certificates</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="no-data">No student records found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>