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

<style>
    :root {
        --primary: #1B3C53;       /* main sidebar/nav background */
        --primary-hover: #234C6A; /* hover or active background */
        --accent: #456882;        /* accents and card headings */
        --highlight: #D2C1B6;     /* soft beige highlight */
        --bg: #f7f8fa;            /* general background */
        --text-dark: #1B3C53;     /* main text */
        --text-light: #6b7280;    /* muted text */
    }

    * {
        box-sizing: border-box;
        transition: all 0.3s ease;
    }

    body {
        font-family: "Inter", Arial, sans-serif;
        background: var(--bg);
        margin: 0;
        display: flex;
        color: var(--text-dark);
        height: 100vh;
    }

    /* --- SIDEBAR --- */
    .sidebar {
        width: 240px;
        background: var(--primary);
        color: #fff;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 25px 0;
        box-shadow: 2px 0 6px rgba(0,0,0,0.1);
        position: fixed;
        height: 100vh;
        top: 0;
        left: 0;
        z-index: 100;
        transition: width 0.3s ease;
    }

    .sidebar.collapsed {
        width: 80px;
    }

    .sidebar .logo {
        text-align: center;
        margin-bottom: 35px;
        width: 100%;
    }

    .sidebar .logo img {
        width: 60px;
        height: auto;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.25);
        margin-bottom: 8px;
    }

    .logo-text {
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.5px;
        color: var(--highlight);
        opacity: 1;
    }

    .collapsed .logo-text {
        opacity: 0;
        visibility: hidden;
    }

    .sidebar nav {
        width: 100%;
        position: relative;
    }

    .sidebar nav a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 25px;
        color: #e5e7eb;
        text-decoration: none;
        font-size: 15px;
        transition: 0.2s;
        white-space: nowrap;
        position: relative;
    }

    .sidebar nav a:hover {
        background: var(--primary-hover);
        color: #fff;
    }

    .sidebar nav a.active {
        background: var(--accent);
        color: #fff;
        font-weight: 600;
    }

    .sidebar nav a i {
        font-size: 18px;
    }

    .collapsed nav a span {
        opacity: 0;
        visibility: hidden;
    }

    /* --- TOOLTIP --- */
    .tooltip {
        position: absolute;
        left: 80px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0,0,0,0.85);
        color: #fff;
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 13px;
        white-space: nowrap;
        pointer-events: none;
        opacity: 0;
        visibility: hidden;
        transition: all 0.2s ease;
    }

    .collapsed nav a:hover .tooltip {
        opacity: 1;
        visibility: visible;
        left: 90px;
    }

    /* --- MAIN CONTENT --- */
    .main {
        margin-left: 240px;
        padding: 40px;
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        overflow-y: auto;
        transition: margin-left 0.3s ease;
    }

    .collapsed ~ .main {
        margin-left: 80px;
    }

    .main-inner {
        max-width: 1000px;
        width: 100%;
    }

    .header {
        text-align: left;
        margin-bottom: 25px;
    }

    .header h2 {
        margin: 0;
        color: var(--text-dark);
        font-weight: 600;
        font-size: 24px;
    }

    .header p {
        color: var(--text-light);
        margin-top: 6px;
        font-size: 15px;
    }

    /* --- GRID CARDS --- */
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
    }

    .card {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        border-top: 4px solid var(--accent);
        transition: all 0.2s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-top-color: var(--primary-hover);
    }

    .card h3 {
        color: var(--accent);
        margin-bottom: 10px;
        font-size: 16px;
        font-weight: 500;
    }

    .card p {
        font-size: 28px;
        font-weight: 700;
        color: var(--primary);
        margin: 0;
    }

    /* --- TOPBAR FOR MOBILE --- */
    .topbar {
        display: none;
        background: #fff;
        padding: 15px 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 99;
    }

    .menu-toggle {
        background: var(--accent);
        color: #fff;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 18px;
    }

    .topbar .title {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 16px;
    }

    /* --- RESPONSIVE BEHAVIOR --- */
    @media (max-width: 1024px) {
        .main { padding: 25px; }
    }

    @media (max-width: 768px) {
        body { flex-direction: column; }

        .sidebar { transform: translateX(-100%); }

        .sidebar.show { transform: translateX(0); }

        .main {
            margin-left: 0 !important;
            padding: 20px;
        }

        .topbar { display: flex; }

        .tooltip { display: none; }
    }

    @media (max-width: 480px) {
        .header h2 { font-size: 20px; }
        .card p { font-size: 24px; }
        .card h3 { font-size: 14px; }
        .sidebar nav a { font-size: 13px; padding: 10px 20px; }
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<!-- Mobile Topbar -->
<div class="topbar">
    <button class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('show')">☰</button>
    <div class="title">HR2 Employee</div>
</div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="logo">
        <img src="logo/deamns.png" alt="HR2 Logo">
        <div class="logo-text">HR2 Employee</div>
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
