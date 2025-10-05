    <?php
        session_name('HR2_EMPLOYEE'); // Unique session for employees
        session_start();
        include('config/database.php'); // Adjust if needed

        if (!isset($_SESSION['employee_id']) || empty($_SESSION['employee_id'])) {
            header("Location: login.php");
            exit();
        }


    // --- Get numeric employee primary key from session ---
    $numeric_id = (int)$_SESSION['employee_id'];

    // --- Fetch employee info including employee_id ---
    $stmt = $conn->prepare("
        SELECT employee_id, first_name, last_name, role
        FROM employees
        WHERE id = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $numeric_id);
    $stmt->execute();
    $employee = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $employee_id = $employee['employee_id']; // EMP-2025-0541F

    // --- Individual Employee Stats ---
    $counts = [
        'Competencies' => 0,
        'Courses' => 0,
        'Trainings' => 0,
        'ESS Requests' => 0
    ];

    // Competencies count
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM competencies");
    $stmt->execute();
    $counts['Competencies'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();


    // Enrolled courses
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM course_enrolls WHERE employee_id = ?");
    $stmt->bind_param("s", $employee_id);
    $stmt->execute();
    $counts['Courses'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    // Trainings attended or registered
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM training_enrolls WHERE employee_id = ?");
    $stmt->bind_param("s", $employee_id);
    $stmt->execute();
    $counts['Trainings'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    // ESS Requests
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM ess_request WHERE employee_id = ?");
    $stmt->bind_param("s", $employee_id);
    $stmt->execute();
    $counts['ESS Requests'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <link rel="icon" href="logo/deamns.png">
    <title>Employee Dashboard - HR2</title>
    <style>
    body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; }
    .navbar { background: #1f2937; color: #fff; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; }
    .navbar a { color: #fff; text-decoration: none; margin-left: 15px; font-size: 14px; }
    .navbar a:hover { text-decoration: underline; }
    .container { max-width: 1000px; margin: 40px auto; background: #fff; border-radius: 10px; padding: 25px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    .header { margin-bottom: 25px; }
    .header h2 { margin: 0; color: #111827; }
    .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 20px; }
    .card { background: #f9fafb; border-radius: 10px; padding: 20px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: 0.2s ease; }
    .card:hover { transform: translateY(-3px); box-shadow: 0 3px 6px rgba(0,0,0,0.15); }
    .card h3 { color: #111827; margin-bottom: 8px; }
    .card p { font-size: 22px; font-weight: bold; color: #2563eb; }
    footer { text-align: center; padding: 10px; font-size: 13px; color: #555; margin-top: 40px; }
    </style>
    </head>
    <body>
    <div class="navbar">
        <div><strong>HR2 Employee (<?= htmlspecialchars($employee_id) ?>)</strong></div>
        <div>
            <a href="index.php">Dashboard</a>
            <a href="modules/hr2/user/competency.php">Competencies</a>
            <a href="modules/hr2/user/learning.php">Learning</a>
            <a href="modules/hr2/user/training.php">Training</a>
            <a href="modules/hr2/user/succession.php">Succession</a>
            <a href="modules/hr2/user/ess.php">ESS</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <h2>Welcome, <?= htmlspecialchars($employee['first_name']) ?> 👋</h2>
            <p>Here’s your HR2 summary overview:</p>
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
        &copy; <?= date('Y') ?> Microfinancial Management System — HR2 Employee Portal
    </footer>
    </body>
    </html>
