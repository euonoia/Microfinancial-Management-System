<?php
session_start();
include('../../../config/database.php');

// --- Strong Auth Check ---
if (empty($_SESSION['employee_id'])) {
    header("Location: ../../../login.php");
    exit();
}



// --- Fetch competencies table (without employee-specific fields) ---
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
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; }
        .navbar { background: #1f2937; color: #fff; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: #fff; text-decoration: none; margin-left: 15px; }
        .navbar a:hover { text-decoration: underline; }
        .container { max-width: 1000px; margin: 40px auto; background: #fff; border-radius: 10px; padding: 25px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        h2 { color: #111827; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        th { background: #f3f4f6; }
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
            <a href="succession.php">Succession</a>
            <a href="ess.php">ESS</a>
            <a href="../../../logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>All Competencies</h2>
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
</body>
</html>
