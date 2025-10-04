<?php
session_start();
include('../../config/database.php');

// --- Auth check ---
if (!isset($_SESSION['employee_id'])) {
    header('Location: ../../login.php');
    exit();
}

// --- Fetch competencies for dropdown ---
$competencies = $conn->query("SELECT id, title FROM competencies ORDER BY title ASC");

// --- Add Learning Module ---
if (isset($_POST['add'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $competency_id = intval($_POST['competency_id']);
    $type = $_POST['learning_type'];
    $duration = $_POST['duration'];

    if ($title !== '' && $competency_id > 0) {
        $stmt = $conn->prepare("INSERT INTO learning_modules (title, description, competency_id, learning_type, duration) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $title, $description, $competency_id, $type, $duration);
        $stmt->execute();
    }
    header("Location: learning.php");
    exit();
}

// --- Delete Learning Module ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM learning_modules WHERE id = $id");
    header("Location: learning.php");
    exit();
}

// --- Fetch all learning modules ---
$result = $conn->query("
    SELECT lm.*, c.title AS competency_title
    FROM learning_modules lm
    LEFT JOIN competencies c ON lm.competency_id = c.id
    ORDER BY lm.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Learning Management | Microfinance HR2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Learning Management</h3>
        <a href="../../logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    </div>

    <!-- Add Form -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Add New Learning Module</h5>
            <form method="POST" action="">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="title" class="form-control" placeholder="Learning Title" required>
                    </div>
                    <div class="col-md-4">
                        <select name="competency_id" class="form-select" required>
                            <option value="">Select Related Competency</option>
                            <?php while ($c = $competencies->fetch_assoc()): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['title']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="learning_type" class="form-select">
                            <option value="Online">Online</option>
                            <option value="Workshop">Workshop</option>
                            <option value="Seminar">Seminar</option>
                            <option value="Coaching">Coaching</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="duration" class="form-control" placeholder="Duration (e.g., 3 days)">
                    </div>
                    <div class="col-12">
                        <textarea name="description" rows="2" class="form-control" placeholder="Description (optional)"></textarea>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" name="add" class="btn btn-primary mt-2">Add Learning</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Learning List -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Learning Modules</h5>
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="20%">Title</th>
                        <th width="20%">Competency</th>
                        <th width="15%">Type</th>
                        <th width="15%">Duration</th>
                        <th>Description</th>
                        <th width="10%" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['title']) ?></td>
                                <td><?= htmlspecialchars($row['competency_title']) ?></td>
                                <td><?= htmlspecialchars($row['learning_type']) ?></td>
                                <td><?= htmlspecialchars($row['duration']) ?></td>
                                <td><?= htmlspecialchars($row['description']) ?></td>
                                <td class="text-center">
                                    <a href="?delete=<?= $row['id'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Delete this learning module?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted">No learning modules found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
