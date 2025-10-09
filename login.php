<?php
session_name('HR2_EMPLOYEE'); 
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
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="bg-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="login-container">
        <div class="login-header">
            <div class="login-logo">
               <img src="logo/deamns.png" alt="">
            </div>
            <h1 class="login-title">Microfinance HR2</h1>
            <p class="login-subtitle">Please sign in to continue</p>
        </div>

        <?php if ($message): ?>
        <div class="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label"><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="Enter your email">
                <i class="fas fa-user input-icon"></i>
            </div>

            <div class="form-group">
                <label class="form-label"><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Enter your password">
                <i class="fas fa-lock input-icon"></i>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Login
                <div class="loading"></div>
            </button>
        </form>

        <div class="login-footer">
            <small>Don't have an account? <a href="register.php">Register</a></small>
        </div>
    </div>

<script>
        // 3D Interactive Effects
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.login-container');
            const form = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            const loading = document.querySelector('.loading');

            // Mouse movement 3D effect
            document.addEventListener('mousemove', function(e) {
                const x = (e.clientX / window.innerWidth) * 100;
                const y = (e.clientY / window.innerHeight) * 100;
                
                container.style.transform = `
                    translateY(-5px) 
                    rotateX(${(y - 50) * 0.1}deg) 
                    rotateY(${(x - 50) * 0.1}deg)
                `;
            });

            // Reset transform on mouse leave
            document.addEventListener('mouseleave', function() {
                container.style.transform = 'translateY(-5px) rotateX(5deg)';
            });

            // Form submission with loading animation
            form.addEventListener('submit', function(e) {
                loginBtn.style.opacity = '0.7';
                loading.style.display = 'block';
                loginBtn.disabled = true;
            });

            // Input focus effects
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });

            // Add floating animation to background shapes
            const shapes = document.querySelectorAll('.shape');
            shapes.forEach((shape, index) => {
                shape.style.animationDelay = `${index * 0.5}s`;
            });
        });
    </script>
</body>
</html>
