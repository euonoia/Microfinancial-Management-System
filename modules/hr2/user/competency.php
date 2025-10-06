<?php
session_name('HR2_EMPLOYEE');
session_start();
include('../../../config/database.php');

// --- Strong Auth Check ---
if (empty($_SESSION['employee_id'])) {
    header("Location: ../../../login.php");
    exit();
}

// --- Fetch competencies ---
$query = "
SELECT 
    id,
    code,
    title,
    description,
    competency_group,
    created_at
FROM competencies
ORDER BY created_at DESC
";
$result = $conn->query($query);
$competencies = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="icon" href="../../../logo/deamns.png">
<title>Competencies - HR2 Employee</title>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>

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
    <a href="competency.php" class="active"> 
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
    <a href="succession.php">
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
            <h2>All Competencies</h2>
            <p>Here’s the list of available competencies.</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Group</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($competencies) > 0): ?>
                    <?php foreach ($competencies as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['code']) ?></td>
                            <td><?= htmlspecialchars($c['title']) ?></td>
                            <td><?= nl2br(htmlspecialchars($c['description'])) ?></td>
                            <td><?= htmlspecialchars($c['competency_group']) ?></td>
                            <td><?= htmlspecialchars($c['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center;">No competencies recorded yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- SIDEBAR COLLAPSE SCRIPT -->
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
        if (!sidebar.contains(e.target) && (!toggle || !toggle.contains(e.target))) {
            sidebar.classList.remove('show');
        }
    });
</script>

</body>
</html>
