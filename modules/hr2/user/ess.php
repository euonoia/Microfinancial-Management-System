<?php
session_start();
include('../../../config/database.php');

// --- Auth check ---
if (!isset($_SESSION['employee_id'])) {
    header("Location: ../../../login.php");
    exit();
}

// --- Get numeric employee ID from session ---
$numeric_id = (int)$_SESSION['employee_id'];

// --- Fetch employee code ---
$stmt_emp = $conn->prepare("SELECT employee_id AS employee_code FROM employees WHERE id = ?");
if (!$stmt_emp) {
    die("Prepare failed: " . $conn->error);
}
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

    // Generate next ESS ID automatically
    $result = $conn->query("SELECT ess_id FROM ess_request ORDER BY id DESC LIMIT 1");
    if ($result && $row = $result->fetch_assoc()) {
        $lastNumber = (int) preg_replace('/\D/', '', $row['ess_id']); // extract numeric part
        $nextNumber = $lastNumber + 1;
    } else {
        $nextNumber = 1;
    }
    $ess_id = 'ESS' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT); // e.g., ESS0001

    // Insert ESS request using employee code
    $stmt = $conn->prepare("
        INSERT INTO ess_request (ess_id, employee_id, type, details, status, created_at)
        VALUES (?, ?, ?, ?, 'pending', ?)
    ");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("sssss", $ess_id, $employee_code, $type, $details, $created_at);
    $stmt->execute();
    $stmt->close();

    header("Location: ess.php");
    exit();
}

// --- Fetch ESS requests for this employee using employee code ---
$query = "
SELECT *
FROM ess_request
WHERE employee_id = ?
ORDER BY created_at DESC
";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $employee_code); // use employee code
$stmt->execute();
$result = $stmt->get_result();
$requests = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>ESS Requests - Employee</title>
<style>
body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; }
.navbar { background: #1f2937; color: #fff; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; }
.navbar a { color: #fff; text-decoration: none; margin-left: 15px; }
.navbar a:hover { text-decoration: underline; }
.container { max-width: 900px; margin: 40px auto; background: #fff; border-radius: 10px; padding: 25px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
h2 { color: #111827; margin-bottom: 20px; }
form { background: #f9fafb; padding: 15px; border-radius: 8px; margin-bottom: 30px; }
input, textarea, select { width: 100%; padding: 10px; margin: 8px 0; border-radius: 6px; border: 1px solid #ccc; }
button { background: #2563eb; color: #fff; padding: 10px 16px; border: none; border-radius: 6px; cursor: pointer; }
button:hover { background: #1d4ed8; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb; }
th { background: #f3f4f6; }
.status-pending { color: #f59e0b; font-weight: bold; }
.status-approved { color: #16a34a; font-weight: bold; }
.status-rejected { color: #dc2626; font-weight: bold; }
.status-closed { color: #6b7280; font-weight: bold; }
</style>
</head>
<body>
<div class="navbar">
    <div><strong>ESS Portal (<?= htmlspecialchars($employee_code) ?>)</strong></div>
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
<h2>Submit New ESS Request</h2>
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

<h2>Your ESS Requests</h2>
<table>
<thead>
<tr>
    <th>ESS ID</th>
    <th>Employee Code</th>
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
            <td><?= htmlspecialchars($r['employee_id']) ?></td>
            <td><?= htmlspecialchars($r['type']) ?></td>
            <td><?= htmlspecialchars($r['details']) ?></td>
            <td class="status-<?= htmlspecialchars($r['status']) ?>">
                <?= ucfirst(htmlspecialchars($r['status'])) ?>
            </td>
            <td><?= htmlspecialchars($r['created_at']) ?></td>
            <td><?= htmlspecialchars($r['updated_at']) ?></td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="7" style="text-align:center;">No requests found.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</body>
</html>
