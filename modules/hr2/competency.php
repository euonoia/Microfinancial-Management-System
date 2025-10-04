<?php
session_start();
include('../../config/database.php');

// --- Access Control ---
if (!isset($_SESSION['employee_id'])) {
    header('Location: ../../login.php');
    exit();
}

// --- Add Competency ---
if (isset($_POST['add'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if ($title !== '') {
        $stmt = $conn->prepare("INSERT INTO competencies (title, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $description);
        $stmt->execute();
    }
    header("Location: competency.php");
    exit();
}

// --- Delete Competency ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM competencies WHERE id = $id");
    header("Location: competency.php");
    exit();
}

// --- Fetch all competencies ---
$result = $conn->query("SELECT * FROM competencies ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Competency Management | Microfinance HR2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Competency Management</h3>
            <a href="../../logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
        </div>

        <!-- Add Form -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Add New Competency</h5>
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <input type="text" name="title" class="form-control" placeholder="Competency Title" required>
                        </div>
                        <div class="col-md-5 mb-3">
                            <input type="text" name="description" class="form-control" placeholder="Description (optional)">
                        </div>
                        <div class="col-md-2 mb-3">
                            <button type="submit" name="add" class="btn btn-primary w-100">Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Display -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Competency List</h5>
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="25%">Title</th>
                            <th>Description</th>
                            <th width="15%" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['title']) ?></td>
                                    <td><?= htmlspecialchars($row['description']) ?></td>
                                    <td class="text-center">
                                        <a href="?delete=<?= $row['id'] ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Are you sure you want to delete this competency?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center text-muted">No competencies found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>
