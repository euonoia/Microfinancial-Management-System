<?php
session_start();
include('../../config/database.php');

// --- Check login ---
if (!isset($_SESSION['employee_id'])) {
    header('Location: ../../login.php');
    exit();
}

// --- Add candidate ---
if (isset($_POST['add_candidate'])) {
    $position_id = intval($_POST['position_id']);
    $employee_id = intval($_POST['employee_id']);
    $readiness = $_POST['readiness'];

    if ($position_id > 0 && $employee_id > 0) {
        $stmt = $conn->prepare("INSERT INTO succession_candidates (position_id, employee_id, readiness) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $position_id, $employee_id, $readiness);
        $stmt->execute();
    }
    header("Location: succession.php");
    exit();
}

// --- Delete candidate ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM succession_candidates WHERE id = $id");
    header("Location: succession.php");
    exit();
}

// --- Fetch positions ---
$positions = $conn->query("SELECT * FROM succession_positions ORDER BY position_title ASC");

// --- Fetch employees for dropdown ---
$employees = $conn->query("SELECT id, name, position FROM employees ORDER BY name ASC");

// --- Fetch all candidates with position and employee info ---
$result = $conn->query("
    SELECT sc.id, sc.readiness, 
           sp.position_title, sp.required_level,
           e.name AS employee_name, e.position AS employee_position
    FROM succession_candidates sc
    LEFT JOIN succession_positions sp ON sc.position_id = sp.id
    LEFT JOIN employees e ON sc.employee_id = e.id
    ORDER BY sp.position_title ASC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Succession Planning | Microfinance HR2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Succession Planning</h3>
        <a href="../../logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    </div>

    <!-- Add Candidate Form -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Add Successor Candidate</h5>
            <form method="POST" action="">
                <div class="row g-3">
                    <div class="col-md-4">
                        <select name="position_id" class="form-select" required>
                            <option value="">Select Position</option>
                            <?php while ($p = $positions->fetch_assoc()): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['position_title']) ?> 
                                    (Required Level: <?= htmlspecialchars($p['required_level']) ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <select name="employee_id" class="form-select" required>
                            <option value="">Select Employee</option>
                            <?php while ($e = $employees->fetch_assoc()): ?>
                                <option value="<?= $e['id'] ?>">
                                    <?= htmlspecialchars($e['name']) ?> (<?= htmlspecialchars($e['position']) ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="readiness" class="form-select">
                            <option value="ready">Ready</option>
                            <option value="not_ready">Not Ready</option>
                        </select>
                    </div>

                    <div class="col-md-2 text-end">
                        <button type="submit" name="add_candidate" class="btn btn-primary w-100">Add</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Candidate List -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Current Succession Candidates</h5>
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Position Title</th>
                        <th>Required Level</th>
                        <th>Employee</th>
                        <th>Current Role</th>
                        <th>Readiness</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['position_title']) ?></td>
                                <td><?= htmlspecialchars($row['required_level']) ?></td>
                                <td><?= htmlspecialchars($row['employee_name']) ?></td>
                                <td><?= htmlspecialchars($row['employee_position']) ?></td>
                                <td>
                                    <span class="badge <?= $row['readiness'] == 'ready' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= ucfirst($row['readiness']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="?delete=<?= $row['id'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Delete this candidate?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted">No candidates added yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
