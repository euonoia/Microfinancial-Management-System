<?php
session_start();
include('../../../config/database.php');

// --- Strong Auth Check ---
if (empty($_SESSION['employee_id'])) {
    header("Location: ../../../login.php");
    exit();
}

// Optionally verify the employee still exists
$stmt = $conn->prepare("SELECT employee_id FROM employees WHERE employee_id = ? LIMIT 1");
$stmt->bind_param("i", $_SESSION['employee_id']);
$stmt->execute();
$stmt->store_result();


$stmt->close();


// --- Fetch all employee competencies ---
$query = "
SELECT 
    ec.id AS competency_id,
    c.code,
    c.title,
    c.description,
    c.competency_group,
    ec.level,
    ec.assessed_at,
    ec.notes
FROM employee_competencies ec
JOIN competencies c ON c.id = ec.competency_id
WHERE ec.employee_id = ?
ORDER BY c.title ASC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$competencies = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Competencies - HR2 Employee</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            margin: 0;
        }
        .navbar {
            background: #1f2937;
            color: #fff;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: #fff;
            text-decoration: none;
            margin-left: 15px;
        }
        .navbar a:hover { text-decoration: underline; }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        h2 {
            color: #111827;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        th { background: #f3f4f6; }
        .level {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9em;
            color: #fff;
        }
        .level-1 { background: #ef4444; }
        .level-2 { background: #f97316; }
        .level-3 { background: #eab308; }
        .level-4 { background: #22c55e; }
        .level-5 { background: #3b82f6; }
    </style>
</head>
<body>
    <div class="navbar">
        <div><strong>HR2 Employee</strong></div>
        <div>
            <a href="../../../index.php">Dashboard</a>
            <a href="competency.php">Competencies</a>
            <a href="learning.php">Learning</a>
            <a href="training.php">Training</a>
            <a href="ess.php">ESS</a>
            <a href="../../../logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>My Competencies</h2>

        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Group</th>
                    <th>Level</th>
                    <th>Assessed At</th>
                    <th>Notes</th>
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
                            <td><span class="level level-<?= $c['level'] ?>">Level <?= $c['level'] ?></span></td>
                            <td><?= $c['assessed_at'] ? htmlspecialchars($c['assessed_at']) : '-' ?></td>
                            <td><?= htmlspecialchars($c['notes'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" style="text-align:center;">No competencies recorded yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
