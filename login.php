<?php
session_start();
include('config/database.php');

// Redirect if already logged in
if (isset($_SESSION['employee_id'])) {
    header("Location: index.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($email == "" || $password == "") {
        $message = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM employees WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['employee_id'] = $user['id'];
                $_SESSION['employee_name'] = $user['name'];
                $_SESSION['role'] = $user['role'];

                header("Location: index.php");

                exit();
            } else {
                $message = "Invalid password.";
            }
        } else {
            $message = "No account found with that email.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Microfinance HR2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-body">
                        <h4 class="text-center mb-4">Microfinance HR2 Login</h4>

                        <?php if ($message): ?>
                            <div class="alert alert-danger py-2 text-center">
                                <?= htmlspecialchars($message) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>

                        <div class="text-center mt-3">
                            <small class="text-muted">dont have account?<a href="register.php">Register</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
