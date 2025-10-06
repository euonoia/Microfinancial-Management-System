<?php
session_name('HR2_EMPLOYEE'); // <-- separate session for employees
session_start();
include('../../../config/database.php');

// --- Auth check (employee only) ---
if (!isset($_SESSION['employee_id'])) {
    header("Location: ../../../login.php");
    exit();
}

// --- Get employee code ---
$emp_id = (int)$_SESSION['employee_id'];
$stmt = $conn->prepare("SELECT employee_id AS employee_code FROM employees WHERE id = ?");
$stmt->bind_param("i", $emp_id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();
$employee_code = $employee['employee_code'];
$stmt->close();

// --- Handle enrollment ---
$message = "";
if (isset($_GET['enroll'])) {
    $training_id = $_GET['enroll'];

    // Check if already enrolled
    $stmt_check = $conn->prepare("SELECT id FROM training_enrolls WHERE employee_id = ? AND training_id = ?");
    $stmt_check->bind_param("ss", $employee_code, $training_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows === 0) {
        // Insert enrollment
        $stmt_insert = $conn->prepare("INSERT INTO training_enrolls (employee_id, training_id, status) VALUES (?, ?, 'enrolled')");
        $stmt_insert->bind_param("ss", $employee_code, $training_id);
        $stmt_insert->execute();
        $stmt_insert->close();
        $message = "Successfully enrolled!";
    } else {
        $message = "You are already enrolled in this training.";
    }
    $stmt_check->close();

    // Redirect to avoid duplicate GET
    header("Location: training.php?msg=" . urlencode($message));
    exit();
}

// --- Show message after redirect ---
if (isset($_GET['msg'])) $message = $_GET['msg'];

// --- Fetch training sessions with enrollment status ---
$query = "
SELECT ts.training_id, ts.title, ts.trainer, ts.start_datetime, ts.end_datetime, ts.location, ts.capacity,
       CASE WHEN te.id IS NULL THEN 0 ELSE 1 END AS enrolled
FROM training_sessions ts
LEFT JOIN training_enrolls te 
       ON te.training_id = ts.training_id AND te.employee_id = ?
ORDER BY ts.start_datetime ASC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $employee_code);
$stmt->execute();
$result = $stmt->get_result();
$sessions = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="icon" href="../../../logo/deamns.png">
<title>Training - HR2 Employee</title>

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
    <a href="../../../index.php">
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
    <a href="training.php" class="active">
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
            <h2>Available Training Sessions</h2>
            <p>Browse and enroll in available trainings below.</p>
        </div>

        <?php if (!empty($message)) echo "<div class='message'>".htmlspecialchars($message)."</div>"; ?>

        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Trainer</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Location</th>
                    <th>Capacity</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($sessions) > 0): ?>
                    <?php foreach ($sessions as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['title']) ?></td>
                            <td><?= htmlspecialchars($s['trainer']) ?></td>
                            <td><?= htmlspecialchars($s['start_datetime']) ?></td>
                            <td><?= htmlspecialchars($s['end_datetime']) ?></td>
                            <td><?= htmlspecialchars($s['location']) ?></td>
                            <td><?= htmlspecialchars($s['capacity']) ?></td>
                            <td><?= $s['enrolled'] ? 'Enrolled' : 'Not Enrolled' ?></td>
                            <td>
                                <?php if (!$s['enrolled']): ?>
                                    <a href="?enroll=<?= urlencode($s['training_id']) ?>" class="enroll-btn">Enroll</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" style="text-align:center;">No training sessions found.</td></tr>
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
