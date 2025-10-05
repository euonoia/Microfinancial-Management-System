<?php
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
    header("Location: employee_training.php?msg=" . urlencode($message));
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
<title>Training - HR2 Employee</title>
<style>
body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; }
.navbar { background: #1f2937; color: #fff; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; }
.navbar a { color: #fff; text-decoration: none; margin-left: 15px; }
.navbar a:hover { text-decoration: underline; }
.container { max-width: 1000px; margin: 40px auto; background: #fff; border-radius: 10px; padding: 25px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
h2 { color: #111827; margin-bottom: 20px; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb; }
th { background: #f3f4f6; }
a.enroll-btn { background: #2563eb; color: #fff; padding: 6px 12px; border-radius: 5px; text-decoration: none; }
a.enroll-btn:hover { background: #1d4ed8; }
.message { padding: 10px; background: #22c55e; color: #fff; margin-bottom: 15px; border-radius: 5px; text-align: center; }
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
    <h2>Available Training Sessions</h2>

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
</body>
</html>
