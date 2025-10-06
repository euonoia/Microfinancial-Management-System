<?php
session_name('HR2_EMPLOYEE');
session_start();
include('../../../config/database.php');

// --- Auth check ---
if (!isset($_SESSION['employee_id'])) {
    header("Location: ../../../login.php");
    exit();
}

// --- Get numeric employee ID ---
$numeric_id = (int)$_SESSION['employee_id'];

// --- Fetch employee code ---
$stmt_emp = $conn->prepare("SELECT employee_id AS employee_code FROM employees WHERE id = ?");
$stmt_emp->bind_param("i", $numeric_id);
$stmt_emp->execute();
$result_emp = $stmt_emp->get_result();
$employee = $result_emp->fetch_assoc();
$employee_code = $employee['employee_code'];
$stmt_emp->close();

// --- Handle new ESS request ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_request'])) {
    $type = trim($_POST['type']);
    $details = trim($_POST['details']);
    $created_at = date('Y-m-d H:i:s');

    $result = $conn->query("SELECT ess_id FROM ess_request ORDER BY id DESC LIMIT 1");
    if ($result && $row = $result->fetch_assoc()) {
        $lastNumber = (int) preg_replace('/\D/', '', $row['ess_id']);
        $nextNumber = $lastNumber + 1;
    } else {
        $nextNumber = 1;
    }
    $ess_id = 'ESS' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

    $stmt = $conn->prepare("
        INSERT INTO ess_request (ess_id, employee_id, type, details, status, created_at)
        VALUES (?, ?, ?, ?, 'pending', ?)
    ");
    $stmt->bind_param("sssss", $ess_id, $employee_code, $type, $details, $created_at);
    $stmt->execute();
    $stmt->close();

    header("Location: ess.php");
    exit();
}

// --- Fetch requests (active + archived) ---
$query = "
SELECT ess_id, employee_id, type, details, status, created_at, updated_at 
FROM ess_request
WHERE employee_id = ?

UNION ALL

SELECT ess_id, employee_id, type, details, status, created_at, updated_at
FROM ess_request_archive
WHERE employee_id = ?

ORDER BY created_at DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $employee_code, $employee_code);
$stmt->execute();
$result = $stmt->get_result();
$requests = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="icon" href="../../../logo/deamns.png">
<title>ESS - HR2 Employee</title>
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
    <a href="succession.php">
        <i class="bi bi-tree"></i> <span>Succession</span>
        <div class="tooltip">Succession</div>
    </a>
    <a href="ess.php" class="active">
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
            <h2>Employee Self Service (ESS)</h2>
            <p>Submit and track your service requests.</p>
        </div>

        <form method="POST">
            <select name="type" required>
                <option value="">Select Request Type</option>
                <option value="Leave">Leave</option>
                <option value="Overtime">Overtime</option>
                <option value="Payroll Issue">Payroll Issue</option>
                <option value="Other">Other</option>
            </select>
            <textarea name="details" placeholder="Request details..." required></textarea>
            <button type="submit" name="add_request">Submit Request</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ESS ID</th>
                    <th>Type</th>
                    <th>Details</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($requests) > 0): ?>
                    <?php foreach ($requests as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['ess_id']) ?></td>
                            <td><?= htmlspecialchars($r['type']) ?></td>
                            <td><?= htmlspecialchars($r['details']) ?></td>
                            <td class="status-<?= htmlspecialchars($r['status']) ?>"><?= ucfirst(htmlspecialchars($r['status'])) ?></td>
                            <td><?= htmlspecialchars($r['created_at']) ?></td>
                            <td><?= htmlspecialchars($r['updated_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center;">No requests found.</td></tr>
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
