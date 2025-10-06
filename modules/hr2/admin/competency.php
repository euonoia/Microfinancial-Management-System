<?php
// Use a custom session name for admin
session_name('HR2_ADMIN');
session_start();

include('../../../config/database.php'); // Adjust path as needed

// --- Auth check ---
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// --- Fetch admin info from session ---
$admin_employee_id = $_SESSION['admin_employee_id'] ?? null;
$admin_name = $_SESSION['admin_name'] ?? 'Admin';

// --- Handle Add Competency ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_competency'])) {
    // Generate next code automatically
    $result = $conn->query("SELECT code FROM competencies ORDER BY id DESC LIMIT 1");
    if ($result && $row = $result->fetch_assoc()) {
        $lastNumber = (int) preg_replace('/\D/', '', $row['code']);
        $nextNumber = $lastNumber + 1;
    } else {
        $nextNumber = 1;
    }

    $code = 'COMP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $group = trim($_POST['competency_group']);

    if ($title !== '') {
        $stmt = $conn->prepare("INSERT INTO competencies (code, title, description, competency_group) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $code, $title, $description, $group);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: competency.php");
    exit();
}

// --- Handle Archive Competency (Soft Delete) ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Move the record to archive table
    $conn->query("
        INSERT INTO competencies_archive (code, title, description, competency_group, created_at)
        SELECT code, title, description, competency_group, created_at
        FROM competencies
        WHERE id = $id
    ");

    // Delete from main table
    $conn->query("DELETE FROM competencies WHERE id = $id");

    header("Location: competency.php");
    exit();
}


// --- Fetch Competencies ---
$result = $conn->query("SELECT * FROM competencies ORDER BY created_at DESC");
$competencies = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Competency Management - HR2 Admin</title>
<style>
body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; }
.navbar { background: #1f2937; color: #fff; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; }
.navbar a { color: #fff; text-decoration: none; margin-left: 15px; }
.navbar a:hover { text-decoration: underline; }
.container { max-width: 1000px; margin: 40px auto; background: #fff; border-radius: 10px; padding: 25px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
h2 { color: #111827; margin-bottom: 20px; }
form { background: #f9fafb; padding: 15px; border-radius: 8px; margin-bottom: 30px; }
input, textarea { width: 100%; padding: 10px; margin: 8px 0; border-radius: 6px; border: 1px solid #ccc; }
button { background: #2563eb; color: #fff; padding: 10px 16px; border: none; border-radius: 6px; cursor: pointer; }
button:hover { background: #1d4ed8; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb; }
th { background: #f3f4f6; }
a.btn-del { color: red; text-decoration: none; }
a.btn-del:hover { text-decoration: underline; }
.admin-info { text-align: right; margin-bottom: 10px; color: #111827; font-weight: bold; }
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
    

    <h2>Competency Management</h2>

    <!-- Add Competency Form -->
    <form method="POST">
        <h3>Add New Competency</h3> 
        <input type="text" name="title" placeholder="Title" required>
        <textarea name="description" placeholder="Description (optional)"></textarea>
        <input type="text" name="competency_group" placeholder="Group (e.g. Technical, Leadership)">
        <button type="submit" name="add_competency">Add Competency</button>
    </form>

    <!-- Competency Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Title</th>
                <th>Group</th>
                <th>Created</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($competencies) > 0): ?>
                <?php foreach ($competencies as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['id']) ?></td>
                        <td><?= htmlspecialchars($c['code']) ?></td>
                        <td><?= htmlspecialchars($c['title']) ?></td>
                        <td><?= htmlspecialchars($c['competency_group']) ?></td>
                        <td><?= htmlspecialchars($c['created_at']) ?></td>
                        <td><a href="?delete=<?= $c['id'] ?>" class="btn-del" onclick="return confirm('Archive this competency?')">Archive</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align:center;">No competencies found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
