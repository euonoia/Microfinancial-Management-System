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
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $position = trim($_POST['position']);
    $hire_date = trim($_POST['hire_date']);
    $branch = trim($_POST['branch']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($first_name == "" || $last_name == "" || $email == "" || $password == "" || $confirm_password == "") {
        $message = "Please fill in all required fields.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM employees WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Email is already registered.";
        } else {
            // --- Generate unique employee code ---
            // Example: EMP-2025-AB123
            $prefix = "EMP";
            $year = date("Y");
            $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5)); // 5 random alphanumeric
            $employee_id = "{$prefix}-{$year}-{$random}";

            // Hash password and prepare other data
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $role = "employee"; // default role
            $name = $first_name . " " . $last_name;
            $created_at = date('Y-m-d H:i:s');
            $updated_at = $created_at;

            // Insert with auto-generated code
            $stmt = $conn->prepare("INSERT INTO employees 
                (employee_id, first_name, last_name, name, position, hire_date, branch, email, phone, password, role, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssssssssssss",
                $employee_id,
                $first_name,
                $last_name,
                $name,
                $position,
                $hire_date,
                $branch,
                $email,
                $phone,
                $hashedPassword,
                $role,
                $created_at,
                $updated_at
            );

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Registration successful! You can now log in.";
                header("Location: login.php");
                exit();
            } else {
                $message = "Something went wrong. Please try again.";
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
        <link rel="icon" href="logo/deamns.png">
    <title>Register | Microfinance HR2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-body p-4">
                        <h4 class="text-center mb-4">Create Your Employee Account</h4>

                        <form method="POST" action="">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Position</label>
                                    <input type="text" name="position" class="form-control" placeholder="e.g. Loan Officer">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Hire Date</label>
                                    <input type="date" name="hire_date" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Branch</label>
                                    <input type="text" name="branch" class="form-control" placeholder="e.g. Main Branch">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" placeholder="e.g. 09123456789">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" name="confirm_password" class="form-control" required>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-success w-100 py-2">Register</button>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <small>Already have an account? <a href="login.php">Login here</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
