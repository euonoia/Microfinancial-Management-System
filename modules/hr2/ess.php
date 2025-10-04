<?php
session_start();
include('../../config/database.php');

// --- Access control ---
if (!isset($_SESSION['employee_id'])) {
    header('Location: ../../login.php');
    exit();
}

$employee_id = $_SESSION['employee_id'];

// --- Get employee details ---
$stmt = $conn->prepare("SELECT name, email, position, branch, role FROM employees WHERE id = ?");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$employee = $stmt->get_result()->fetch_assoc();

// --- Fetch employee competencies (if table exists) ---
$competencies = $conn->query("
    SELECT c.title, c.description
    FROM employee_competencies ec
    JOIN competencies c ON ec.competency_id = c.id
    WHERE ec.employee_id = $employee_id
");

// --- Fetch employee trainings (if table exists) ---
$trainings = $conn->query("
    SELECT title, status, completion_date
    FROM trainings
    WHERE employee_id = $employee_id
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Self-Service (ESS)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Employee Self-Service (ESS)</h3>
        <a href="../../logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    </div>

    <!-- Profile Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">My Profile</h5>
            <p><strong>Name:</strong> <?= htmlspecialchars($employee['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($employee['email']) ?></p>
            <p><strong>Position:</strong> <?= htmlspecialchars($employee['position']) ?></p>
            <p><strong>Branch:</strong> <?= htmlspecialchars($employee['branch']) ?></p>
            <p><strong>Role:</strong> <?= htmlspecialchars($employee['role']) ?></p>
        </div>
    </div>

    <!-- Competencies Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">My Competencies</h5>
            <?php if ($competencies && $competencies->num_rows > 0): ?>
                <ul class="list-group">
                    <?php while ($c = $competencies->fetch_assoc()): ?>
                        <li class="list-group-item">
                            <strong><?= htmlspecialchars($c['title']) ?></strong><br>
                            <small class="text-muted"><?= htmlspecialchars($c['description']) ?></small>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted">No competencies assigned yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Trainings Section -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">My Trainings</h5>
            <?php if ($trainings && $trainings->num_rows > 0): ?>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Completion Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($t = $trainings->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($t['title']) ?></td>
                                <td><?= htmlspecialchars($t['status']) ?></td>
                                <td><?= htmlspecialchars($t['completion_date']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted">No trainings completed yet.</p>
            <?php endif; ?>
        </div>
    </div>

</div>

</body>
</html>
