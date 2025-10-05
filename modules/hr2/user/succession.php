<?php
session_name('HR2_EMPLOYEE'); // <-- separate session for employees
session_start();
include('../../../config/database.php');

// --- Auth check ---
if (empty($_SESSION['employee_id'])) {
    header("Location: ../../../login.php");
    exit();
}

// Get numeric employee ID from session
$numeric_id = (int)$_SESSION['employee_id'];

// --- Fetch employee code ---
$stmt_emp = $conn->prepare("SELECT employee_id AS employee_code FROM employees WHERE id = ?");
$stmt_emp->bind_param("i", $numeric_id);
$stmt_emp->execute();
$result_emp = $stmt_emp->get_result();
$employee = $result_emp->fetch_assoc();
$employee_code = $employee['employee_code'];
$stmt_emp->close();


// --- Fetch succession positions assigned to this employee ---
$query = "
SELECT 
    p.position_title,
    p.branch_id,
    p.criticality,
    c.readiness,
    c.development_plan
FROM successor_candidates c
JOIN succession_positions p ON p.branch_id = c.branch_id
WHERE c.employee_id = ?
ORDER BY p.position_title
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $employee_code); // Use employee_code instead of numeric ID
$stmt->execute();
$result = $stmt->get_result();
$my_positions = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="../../../logo/deamns.png">
    <meta charset="UTF-8">
    <title>My Succession Plan</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; }
        .navbar { background: #1f2937; color: #fff; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: #fff; text-decoration: none; margin-left: 15px; }
        .navbar a:hover { text-decoration: underline; }
        .container { max-width: 1000px; margin: 40px auto; background: #fff; border-radius: 10px; padding: 25px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        h2 { color: #111827; margin-bottom: 10px; text-align:center; }
        p.logged-in { text-align: center; font-weight: bold; color: #1f2937; }
        table { width: 100%; border-collapse: collapse; margin-top: 25px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f3f4f6; }
        .logout-btn { padding: 8px 16px; background:#f00; color:#fff; border:none; border-radius:5px; cursor:pointer; text-decoration:none; }
        .logout-btn:hover { background:#c00; }
        .section-title { margin-top: 20px; color: #111827; text-align:center; }
    </style>
</head>
<body>
<div class="navbar">
   <div><strong>HR2 Employee (<?= htmlspecialchars($employee_code) ?>)</strong></div>
   <div>
       <a href="../../../index.php">Dashboard</a>
       <a href="competency.php">Competencies</a>
       <a href="learning.php">Learning</a>
       <a href="training.php">Training</a>
       <a href="succession.php">Succession</a>
       <a href="ess.php">ESS</a>
       <a href="../../../logout.php">Logout</a>
   </div>
</div>

<div class="container">

    <h3 class="section-title">Assigned Positions</h3>
    <table>
        <thead>
        <tr>
            <th>Position</th>
            <th>Branch</th>
            <th>Criticality</th>
            <th>Readiness</th>
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
                    <td><?= htmlspecialchars($pos['development_plan']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" style="text-align:center;">You are not assigned to any succession positions.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
