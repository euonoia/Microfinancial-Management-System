<?php
session_start();
include('../../../config/database.php');

// --- Auth check ---
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../../auth/admin_login.php");
    exit();
}

// --- ADD POSITION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_position'])) {
    $position_title = trim($_POST['position_title']);
    $branch_id = !empty($_POST['branch_id']) ? intval($_POST['branch_id']) : null;
    $criticality = $_POST['criticality'];

    if ($position_title !== '') {
        $stmt = $conn->prepare("
            INSERT INTO succession_positions (position_title, branch_id, criticality)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("sis", $position_title, $branch_id, $criticality);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: succession.php");
    exit();
}

// --- ADD CANDIDATE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_candidate'])) {
    $position_id = intval($_POST['position_id']);
    $employee_id = intval($_POST['employee_id']);
    $readiness = $_POST['readiness'];
    $development_plan = trim($_POST['development_plan']);

    $stmt = $conn->prepare("
        INSERT INTO succession_candidates (position_id, employee_id, readiness, development_plan)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("iiss", $position_id, $employee_id, $readiness, $development_plan);
    $stmt->execute();
    $stmt->close();

    header("Location: succession.php");
    exit();
}

// --- DELETE POSITION ---
if (isset($_GET['delete_position'])) {
    $id = intval($_GET['delete_position']);
    $conn->query("DELETE FROM succession_positions WHERE id = $id");
    header("Location: succession.php");
    exit();
}

// --- DELETE CANDIDATE ---
if (isset($_GET['delete_candidate'])) {
    $id = intval($_GET['delete_candidate']);
    $conn->query("DELETE FROM succession_candidates WHERE id = $id");
    header("Location: succession.php");
    exit();
}

// --- FETCH POSITIONS ---
$positionsQuery = "
SELECT p.*, COUNT(c.id) AS candidate_count
FROM succession_positions p
LEFT JOIN succession_candidates c ON c.position_id = p.id
GROUP BY p.id
ORDER BY p.position_title ASC
";
$positions = $conn->query($positionsQuery)->fetch_all(MYSQLI_ASSOC);

// --- FETCH CANDIDATES ---
$candidatesQuery = "
SELECT c.*, e.first_name, e.last_name, p.position_title
FROM succession_candidates c
JOIN employees e ON e.id = c.employee_id
JOIN succession_positions p ON p.id = c.position_id
ORDER BY p.position_title, e.first_name
";
$candidates = $conn->query($candidatesQuery)->fetch_all(MYSQLI_ASSOC);

// --- FETCH EMPLOYEES (for dropdown) ---
$employees = $conn->query("SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM employees ORDER BY first_name ASC")->fetch_all(MYSQLI_ASSOC);

// --- FETCH BRANCHES (for position form) ---
$branches = $conn->query("SELECT id, name FROM branches ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Succession Planning - HR2 Admin</title>
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
            max-width: 1100px;
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
        form {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            background: #2563eb;
            color: #fff;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover { background: #1d4ed8; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th { background: #f3f4f6; }
        a.btn-del {
            color: red;
            text-decoration: none;
        }
        a.btn-del:hover { text-decoration: underline; }
        .section-title {
            margin-top: 40px;
            color: #111827;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div><strong>HR2 Admin</strong></div>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="competency.php">Competency</a>
            <a href="learning.php">Learning</a>
            <a href="training.php">Training</a>
            <a href="succession.php">Succession</a>
            <a href="ess.php">ESS</a>
            <a href="../../logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>Succession Planning</h2>

        <!-- Add Position -->
        <form method="POST">
            <h3>Add New Position</h3>
            <input type="text" name="position_title" placeholder="Position Title" required>

            <label>Branch:</label>
            <select name="branch_id">
                <option value="">-- Select Branch --</option>
                <?php foreach ($branches as $b): ?>
                    <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Criticality:</label>
            <select name="criticality" required>
                <option value="low">Low</option>
                <option value="medium" selected>Medium</option>
                <option value="high">High</option>
            </select>
            <button type="submit" name="add_position">Add Position</button>
        </form>

        <!-- Position Table -->
        <h3 class="section-title">Succession Positions</h3>
        <table>
            <thead>
                <tr>
                    <th>Position Title</th>
                    <th>Branch</th>
                    <th>Criticality</th>
                    <th>Candidates</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($positions) > 0): ?>
                    <?php foreach ($positions as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['position_title']) ?></td>
                            <td><?= htmlspecialchars($p['branch_id']) ?></td>
                            <td><?= htmlspecialchars(ucfirst($p['criticality'])) ?></td>
                            <td><?= htmlspecialchars($p['candidate_count']) ?></td>
                            <td><a href="?delete_position=<?= $p['id'] ?>" class="btn-del" onclick="return confirm('Delete this position?')">Delete</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center;">No positions found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Add Candidate -->
        <form method="POST" style="margin-top:40px;">
            <h3>Add Candidate to Position</h3>
            <label>Position:</label>
            <select name="position_id" required>
                <option value="">-- Select Position --</option>
                <?php foreach ($positions as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['position_title']) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Employee:</label>
            <select name="employee_id" required>
                <option value="">-- Select Employee --</option>
                <?php foreach ($employees as $e): ?>
                    <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Readiness:</label>
            <select name="readiness" required>
                <option value="ready">Ready</option>
                <option value="1-2yrs">1-2 years</option>
                <option value="3-5yrs">3-5 years</option>
                <option value="not_ready">Not Ready</option>
            </select>

            <textarea name="development_plan" placeholder="Development Plan (optional)"></textarea>
            <button type="submit" name="add_candidate">Add Candidate</button>
        </form>

        <!-- Candidate List -->
        <h3 class="section-title">Succession Candidates</h3>
        <table>
            <thead>
                <tr>
                    <th>Position</th>
                    <th>Employee</th>
                    <th>Readiness</th>
                    <th>Development Plan</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($candidates) > 0): ?>
                    <?php foreach ($candidates as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['position_title']) ?></td>
                            <td><?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?></td>
                            <td><?= htmlspecialchars($c['readiness']) ?></td>
                            <td><?= htmlspecialchars($c['development_plan']) ?></td>
                            <td><a href="?delete_candidate=<?= $c['id'] ?>" class="btn-del" onclick="return confirm('Delete this candidate?')">Delete</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center;">No candidates found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
