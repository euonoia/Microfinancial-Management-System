<?php
// Use a custom session name for admin
session_name('HR2_ADMIN');
session_start();

include('../../../config/database.php');

// --- Auth check ---
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// --- Fetch admin info from session ---
$admin_employee_id = $_SESSION['admin_employee_id'] ?? null;
$admin_name = $_SESSION['admin_name'] ?? 'Admin';

// --- UPDATE REQUEST STATUS + ARCHIVE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $request_id = intval($_POST['request_id']);
    $status = $_POST['status']; // approved, rejected, closed

    // Update the request first
    $stmt = $conn->prepare("UPDATE ess_request SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param("si", $status, $request_id);
    $stmt->execute();
    $stmt->close();

    // If the request is approved or rejected → archive it
    if (in_array($status, ['approved', 'rejected'])) {
        // Get the full request data
        $select = $conn->prepare("SELECT * FROM ess_request WHERE id = ?");
        $select->bind_param("i", $request_id);
        $select->execute();
        $result = $select->get_result();
        $request = $result->fetch_assoc();
        $select->close();

        if ($request) {
            // Insert into archive with ess_id
            $insert = $conn->prepare("
                INSERT INTO ess_request_archive 
                (ess_id, employee_id, type, details, status, created_at, updated_at, archived_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $insert->bind_param(
                "issssss",
                $request['id'],
                $request['employee_id'],
                $request['type'],
                $request['details'],
                $request['status'],
                $request['created_at'],
                $request['updated_at']
            );
            $insert->execute();
            $insert->close();

            // Delete from main ESS table
            $delete = $conn->prepare("DELETE FROM ess_request WHERE id = ?");
            $delete->bind_param("i", $request_id);
            $delete->execute();
            $delete->close();
        }
    }

    header("Location: ess.php");
    exit();
}



// --- FETCH ALL ESS REQUESTS WITH EMPLOYEE NAMES ---
$query = "
SELECT e.*, CONCAT(u.first_name, ' ', u.last_name) AS employee_name
FROM ess_request e
LEFT JOIN employees u ON u.employee_id = e.employee_id
ORDER BY e.created_at DESC
";

$result = $conn->query($query);
$requests = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>ESS Requests - HR2 Admin</title>
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
form { display: inline; }
button { background: #2563eb; color: #fff; padding: 6px 10px; border: none; border-radius: 6px; cursor: pointer; margin-right: 5px; }
button.approve { background: #16a34a; }
button.reject { background: #dc2626; }
button.close { background: #f59e0b; }
button:hover { opacity: 0.9; }
button:disabled { background: #9ca3af; cursor: not-allowed; }

/* Style only the status text */
.status-approved { color: #16a34a; font-weight: bold; }
.status-rejected { color: #dc2626; font-weight: bold; }
.status-closed { color: #f59e0b; font-weight: bold; }
.status-pending { color: #111827; font-weight: bold; }

</style>
</head>
<body>
<div class="navbar">
    <div><strong>HR2 Admin</strong></div>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="admin_register.php">Add Admin</a>
        <a href="competency.php">Competency</a>
        <a href="learning.php">Learning</a>
        <a href="training.php">Training</a>
        <a href="succession.php">Succession</a>
        <a href="ess.php">ESS</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
<h2>Employee Self-Service Requests</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Employee</th>
            <th>Type</th>
            <th>Payload</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Updated At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php if (count($requests) > 0): ?>
        <?php foreach ($requests as $r): ?>
            <?php
            // Determine button states
            $disable_approve_reject = false;
            $disable_close = false;
            $status_class = 'status-pending';

            switch ($r['status']) {
                case 'approved':
                    $disable_approve_reject = true;
                    $disable_close = true;
                    $status_class = 'status-approved';
                    break;
                case 'rejected':
                    $disable_approve_reject = true;
                    $disable_close = true;
                    $status_class = 'status-rejected';
                    break;
                case 'closed':
                    $disable_approve_reject = false;
                    $disable_close = false;
                    $status_class = 'status-closed';
                    break;
                default: // pending
                    $disable_approve_reject = false;
                    $disable_close = false;
                    $status_class = 'status-pending';
                    break;
            }
            ?>
            <tr>
                <td><?= htmlspecialchars($r['id']) ?></td>
                <td><?= htmlspecialchars($r['employee_name']) ?></td>
                <td><?= htmlspecialchars($r['type']) ?></td>
                <td><?= htmlspecialchars($r['details']) ?></td>
                <td><strong class="<?= $status_class ?>"><?= htmlspecialchars(ucfirst($r['status'])) ?></strong></td>
                <td><?= htmlspecialchars($r['created_at']) ?></td>
                <td><?= htmlspecialchars($r['updated_at']) ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                        <button type="submit" name="update_status" value="approved" class="approve" <?= $disable_approve_reject ? 'disabled' : '' ?> onclick="this.form.status.value='approved'">Approve</button>
                        <button type="submit" name="update_status" value="rejected" class="reject" <?= $disable_approve_reject ? 'disabled' : '' ?> onclick="this.form.status.value='rejected'">Reject</button>
                        <button type="submit" name="update_status" value="closed" class="close" <?= $disable_close ? 'disabled' : '' ?> onclick="this.form.status.value='closed'">Close</button>
                        <input type="hidden" name="status" value="">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="8" style="text-align:center;">No requests found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>
</body>
</html>
