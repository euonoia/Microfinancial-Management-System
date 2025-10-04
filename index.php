<?php
session_start();
include('config/database.php');

// Check if user is logged in
if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    logged in as: <?php echo htmlspecialchars($_SESSION['employee_name']); ?> | <a href="logout.php">Logout</a>
    <h1>Welcome to Microfinance HR2 System</h1>
    <a href="modules/hr2/ess.php">Employee Self-Service</a>
    <a href="modules/hr2/learning.php">Learning Management</a>
    <a href="modules/hr2/succession.php">Succession Planning</a>
    <a href="modules/hr2/training.php">Training Management</a>
</body>
</html>