<?php
session_name('HR2_EMPLOYEE'); 
session_start();
include('../../../config/database.php');

// --- Auth check ---
if (empty($_SESSION['employee_id'])) {
    header("Location: ../../../login.php");
    exit();
}

$numeric_id = (int)$_SESSION['employee_id'];

// --- Fetch employee code ---
$stmt_emp = $conn->prepare("SELECT employee_id AS employee_code FROM employees WHERE id = ?");
$stmt_emp->bind_param("i", $numeric_id);
$stmt_emp->execute();
$result_emp = $stmt_emp->get_result();
$employee = $result_emp->fetch_assoc();
$employee_code = $employee['employee_code'];
$stmt_emp->close();

// --- Fetch succession positions ---
$query = "
SELECT 
    p.position_title,
    p.branch_id,
    p.criticality,
    c.readiness,
    c.effective_at,
    c.development_plan
FROM successor_candidates c
JOIN succession_positions p ON p.branch_id = c.branch_id
WHERE c.employee_id = ?
ORDER BY p.position_title
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $employee_code);
$stmt->execute();
$result = $stmt->get_result();
$my_positions = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="icon" href="../../../logo/deamns.png">
<title>My Succession Plan - HR2 Employee</title>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Mobile Topbar -->
<div class="topbar">
    <button class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('show')">☰</button>
    
</div>
<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="logo">
        <img src="../../../logo/deamns.png" alt="HR2 Logo">
    </div>
    <nav>
        <nav>
    <a href="../../../index.php" >
        <i class="bi bi-house-door"></i> <span>Dashboard</span>
        <div class="tooltip">Dashboard</div>
    </a>
    <a href="competency.php">
        <i class="bi bi-lightbulb"></i> <span>Competencies</span>
        <div class="tooltip">Competencies</div>
    </a>
    <a href="learning.php">
        <i class="bi bi-book"></i> <span>Learning</span>
        <div class="tooltip">Learning</div>
    </a>
    <a href="training.php">
        <i class="bi bi-mortarboard"></i> <span>Training</span>
        <div class="tooltip">Training</div>
    </a>
    <a href="succession.php" class="active">
        <i class="bi bi-tree"></i> <span>Succession</span>
        <div class="tooltip">Succession</div>
    </a>
    <a href="ess.php">
        <i class="bi bi-pencil-square"></i> <span>ESS</span>
        <div class="tooltip">ESS</div>
    </a>
    <a href="../../../logout.php">
        <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
        <div class="tooltip">Logout</div>
    </a>
</nav>

    </nav>
</div>

<!-- MAIN CONTENT -->
<div class="main">
    <div class="main-inner">
        <div class="header">
            <h2>My Succession Plan</h2>
            <p>View positions you are assigned to and their readiness details.</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Position</th>
                    <th>Branch</th>
                    <th>Criticality</th>
                    <th>Readiness</th>
                    <th>Effective At</th>
                    <th>Development Plan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($my_positions) > 0): ?>
                    <?php foreach ($my_positions as $pos): ?>
                        <tr>
                            <td><?= htmlspecialchars($pos['position_title']) ?></td>
                            <td><?= htmlspecialchars($pos['branch_id']) ?></td>
                            <td><?= htmlspecialchars(ucfirst($pos['criticality'])) ?></td>
                            <td><?= htmlspecialchars(ucfirst($pos['readiness'])) ?></td>
                            <td><?= htmlspecialchars($pos['effective_at']) ?></td>
                            <td><?= htmlspecialchars($pos['development_plan']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center;">You are not assigned to any succession positions.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- SIDEBAR COLLAPSE SCRIPT -->
<script>
    const sidebar = document.getElementById('sidebar');
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
    if (window.innerWidth > 768) {
        sidebar.classList.add('collapsed');
    }
    document.addEventListener('click', (e) => {
        const toggle = document.querySelector('.menu-toggle');
        if (!sidebar.contains(e.target) && (!toggle || !toggle.contains(e.target))) {
            sidebar.classList.remove('show');
        }
    });
</script>
</body>
</html>
