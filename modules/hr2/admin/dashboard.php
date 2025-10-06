<?php
session_name('HR2_ADMIN');
session_start();
include('../../../config/database.php');

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Fetch admin using employee_id
$employee_id = $_SESSION['admin_employee_id'];
$stmt = $conn->prepare("SELECT * FROM admin WHERE employee_id = ? LIMIT 1");
$stmt->bind_param("s", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    echo "<h2 style='color:red;text-align:center;'>Access Denied. Admins only.</h2>";
    exit();
}

$adminData = $result->fetch_assoc();
$stmt->close();

// --- Dashboard counts (for summary cards) ---
$tables = [
    'employees' => 'Employees',
    'competencies' => 'Competencies',
    'learning_modules' => 'Learning Modules',
    'training_sessions' => 'Trainings',
    'succession_positions' => 'Succession Positions',
    'ess_request' => 'ESS Requests'
];

$counts = [];
foreach ($tables as $table => $label) {
    $query = "SELECT COUNT(*) AS total FROM $table";
    $res = $conn->query($query);
    $row = $res ? $res->fetch_assoc() : ['total' => 0];
    $counts[$label] = $row['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - HR2</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            margin: 0;
        }
        .navbar {
            background: #1f2937;
            color: #fff;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: #fff;
            text-decoration: none;
            margin-left: 15px;
            font-size: 14px;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        .container {
            max-width: 1000px;
            margin: 40px auto;
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .header {
            margin-bottom: 25px;
        }
        .header h2 {
            margin: 0;
            color: #111827;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
        }
        .card {
            background: #f9fafb;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: 0.2s ease;
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 3px 6px rgba(0,0,0,0.15);
        }
        .card h3 {
            color: #111827;
            margin-bottom: 8px;
        }
        .card p {
            font-size: 22px;
            font-weight: bold;
            color: #2563eb;
        }
        footer {
            text-align: center;
            padding: 10px;
            font-size: 13px;
            color: #555;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div><strong>HR2 Admin </strong></div>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="admin_register.php">add ADMIN</a>
            <a href="competency.php">Competency</a>
            <a href="learning.php">Learning</a>
            <a href="training.php">Training</a>
            <a href="succession.php">Succession</a>
            <a href="ess.php">ESS</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <h2>Welcome, <?= htmlspecialchars($adminData['full_name']) ?> </h2>
            <p>Here’s a quick overview of the HR2 system modules:</p>
        </div>

        <div class="grid">
            <?php foreach ($counts as $label => $count): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($label) ?></h3>
                    <p><?= htmlspecialchars($count) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer>
        &copy; <?= date('Y') ?> Microfinancial Management System — HR2 Module
    </footer>
</body>
</html>
