<?php
// admin_login.php

// Use a custom session name for admin to avoid colliding with employee sessions
session_name('HR2_ADMIN');
session_start();

include('../../../config/database.php'); // adjust path if required

// If already logged in as admin, go to dashboard
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header('Location: dashboard.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $message = 'Please provide both email and password.';
    } else {
        // Fetch admin record by email
        $stmt = $conn->prepare("SELECT id, employee_id, full_name, password FROM admin WHERE email = ? LIMIT 1");
        if (!$stmt) {
            // For debugging; remove or log in production
            die('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        $stmt->close();

        if ($admin && password_verify($password, $admin['password'])) {
            // Successful login: store admin session (by employee_id)
            $_SESSION['is_admin'] = true;
            $_SESSION['admin_employee_id'] = $admin['employee_id']; // e.g., 7749 or numeric as stored
            $_SESSION['admin_name'] = $admin['full_name'];

            // regenerate session id to mitigate fixation
            session_regenerate_id(true);

            header('Location: dashboard.php');
            exit();
        } else {
            // Failed login
            $message = 'Invalid email or password.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin Login — HR2</title>
<style>
    body { font-family: Arial, sans-serif; background:#f3f4f6; margin:0; display:flex; align-items:center; justify-content:center; min-height:100vh;}
    .card { background:#fff; padding:28px; border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,0.08); width:360px; }
    h2 { margin:0 0 12px; font-size:20px; }
    input { width:100%; padding:10px; margin:8px 0; border-radius:6px; border:1px solid #ccc; box-sizing:border-box; }
    button { width:100%; padding:10px; border-radius:6px; border:0; background:#2563eb; color:#fff; font-weight:600; cursor:pointer; }
    .msg { margin:12px 0; padding:10px; border-radius:6px; background:#fee2e2; color:#b91c1c; }
    label { font-size:13px; color:#333; display:block; margin-top:6px; }
</style>
</head>
<body>
<div class="card">
    <h2>Admin Login</h2>

    <?php if ($message): ?>
        <div class="msg"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">

        <label for="password">Password</label>
        <input id="password" name="password" type="password" required>

        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>
