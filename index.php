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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="logo/deamns.png">
<title>Employee Dashboard - HR2</title>
<link rel="stylesheet" href="modules/hr2/user/style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<!-- Mobile Topbar -->
<div class="topbar">
    <button class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('show')">☰</button>
    
</div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="logo">
        <img src="logo/deamns.png" alt="HR2 Logo">
       
    </div>

    <nav>
        <a href="index.php" class="active">
            <i class="bi bi-house-door"></i> <span>Dashboard</span>
            <div class="tooltip">Dashboard</div>
        </a>
        <a href="modules/hr2/user/competency.php">
            <i class="bi bi-lightbulb"></i> <span>Competencies</span>
            <div class="tooltip">Competencies</div>
        </a>
        <a href="modules/hr2/user/learning.php">
            <i class="bi bi-book"></i> <span>Learning</span>
            <div class="tooltip">Learning</div>
        </a>
        <a href="modules/hr2/user/training.php">
            <i class="bi bi-mortarboard"></i> <span>Training</span>
            <div class="tooltip">Training</div>
        </a>
        <a href="modules/hr2/user/succession.php">
            <i class="bi bi-tree"></i> <span>Succession</span>
            <div class="tooltip">Succession</div>
        </a>
        <a href="modules/hr2/user/ess.php">
            <i class="bi bi-pencil-square"></i> <span>ESS</span>
            <div class="tooltip">ESS</div>
        </a>
        <a href="logout.php">
            <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
            <div class="tooltip">Logout</div>
        </a>
    </nav>
</div>

<!-- Main Content -->
<div class="main">
    <div class="main-inner">
        <div class="header">
            <h2>Welcome, <?= htmlspecialchars($employee['first_name']) ?> </h2>
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
</div>

<script>
    const sidebar = document.getElementById('sidebar');

    // Desktop hover collapse
    sidebar.addEventListener('mouseenter', () => {
        if (window.innerWidth > 768 && sidebar.classList.contains('collapsed')) {
            sidebar.classList.remove('collapsed');
        }
    });

    sidebar.addEventListener('mouseleave', () => {
        if (window.innerWidth > 768) {
            sidebar.classList.add('collapsed');
        }
    });

    // Start collapsed by default on desktop
    if (window.innerWidth > 768) {
        sidebar.classList.add('collapsed');
    }

    // Auto-close on mobile when clicking outside
    document.addEventListener('click', (e) => {
        const toggle = document.querySelector('.menu-toggle');
        if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
            sidebar.classList.remove('show');
        }
    });
</script>

</body>
</html>

</html>
