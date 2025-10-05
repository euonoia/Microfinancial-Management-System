<?php
session_name('HR2_EMPLOYEE'); // <- Unique session for employees
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
    <link rel="icon" href="logo/deamns.png">
    <title>Login | Microfinance HR2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: linear-gradient(135deg, #f0f4f8, #d9e2ec);
        margin: 0;
        padding: 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        font-family: 'Segoe UI', sans-serif;
    }

    /* Header */
    .header {
        width: 100%;
        padding: 15px 20px;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        z-index: 1000;
    }

    .header img {
        width: 120px;
        height: 120px;
        object-fit: contain;
    }

    /* Main container */
    .main-container {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Card styling */
    .card {
        width: 100%;
        max-width: 400px;
        margin: 20px;
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        background: #ffffff;
        padding: 30px 25px;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }

    .card h4 {
        font-weight: 600;
        margin-bottom: 25px;
        color: #333;
    }

    .form-control:focus {
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        border-color: #007bff;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4a90e2, #357ab7);
        border: none;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #357ab7, #4a90e2);
    }

    .text-muted a {
        color: #357ab7;
        text-decoration: none;
        font-weight: 500;
    }

    .text-muted a:hover {
        text-decoration: underline;
    }
</style>

</head>
<body>
    <!-- Header with Logo -->
    <div class="header">
        <img src="logo/deamns.png" alt="Microfinance HR2 Logo">
    </div>

    <!-- Main content -->
    <div class="main-container">
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-body text-center">
                <h4 class="mb-4">Login</h4>

                <?php if ($message): ?>
                    <div class="alert alert-danger py-2 text-center">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3 text-start">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>

                <div class="text-center mt-3">
                    <small class="text-muted">Don't have an account? <a href="register.php">Register</a></small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
