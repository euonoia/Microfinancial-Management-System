<?php
// Use a custom session name for admin to avoid conflicts with employee sessions
session_name('HR2_ADMIN');
session_start();

include('../../../config/database.php'); // adjust path as needed

// --- Check if there is at least one admin ---
$adminCheck = $conn->query("SELECT COUNT(*) AS total FROM admin WHERE employee_id IS NOT NULL");
if ($adminCheck) {
    $row = $adminCheck->fetch_assoc();
    if ($row['total'] == 0) {
        // No admin exists, block access
        die("Access denied. You must have at least one admin registered to access this page.");
    }
}
$adminCheck->free();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);

    // Check if username already exists
    $check = $conn->prepare("SELECT id FROM admin WHERE username = ? LIMIT 1");
    $check->bind_param("s", $username);
    $check->execute();
    $result = $check->get_result();

    if ($result && $result->num_rows > 0) {
        $error = "Username already taken.";
    } else {
        // Generate a unique random employee_id between 1000–9999
        do {
            $employee_id = rand(1000, 9999);
            $check_emp = $conn->prepare("SELECT id FROM admin WHERE employee_id = ? LIMIT 1");
            $check_emp->bind_param("i", $employee_id);
            $check_emp->execute();
            $emp_result = $check_emp->get_result();
        } while ($emp_result && $emp_result->num_rows > 0);
        $check_emp->close();

        // Insert new admin
        $stmt = $conn->prepare("INSERT INTO admin (employee_id, username, password, email, full_name) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $employee_id, $username, $password, $email, $full_name);

        if ($stmt->execute()) {
            $success = "Admin registered successfully! You can now <a href='admin_login.php'>login</a>.";
        } else {
            $error = "Error creating admin: " . $conn->error;
        }
        $stmt->close();
    }
    $check->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Admin - HR2</title>
    <style>
        body {
            font-family: Arial;
            background: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .register-box {
            background: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #111827;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            background: #16a34a;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover { background: #15803d; }
        .success { color: green; text-align: center; margin-bottom: 10px; }
        .error { color: red; text-align: center; margin-bottom: 10px; }
        .login-link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }
        .login-link a { color: #2563eb; text-decoration: none; }
    </style>
</head>
<body>
    <div class="register-box">
        <h2>Register Admin</h2>

        <?php if ($success): ?><div class="success"><?= $success ?></div><?php endif; ?>
        <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>

        <form method="POST">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>

        <div class="login-link">
            Back to <a href="dashboard.php">Dashboard</a>
        </div>
    </div>
</body>
</html>
