<?php
session_start();
include('../../config/database.php');

// --- Check login ---
if (!isset($_SESSION['employee_id'])) {
    header('Location: ../../login.php');
    exit();
}

// ========== ADD NEW TRAINING ==========
if (isset($_POST['add_training'])) {
    $title = trim($_POST['title']);
    $date = $_POST['date'];
    $location = trim($_POST['location']);
    $trainer = trim($_POST['trainer']);

    $stmt = $conn->prepare("INSERT INTO trainings (title, date, location, trainer) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $date, $location, $trainer);
    $stmt->execute();
    header("Location: training.php");
    exit();
}

// ========== ENROLL EMPLOYEE ==========
if (isset($_POST['enroll_employee'])) {
    $training_id = intval($_POST['training_id']);
    $employee_id = intval($_POST['employee_id']);

    $stmt = $conn->prepare("INSERT INTO training_attendance (training_id, employee_id, attended) VALUES (?, ?, 0)");
    $stmt->bind_param("ii", $training_id, $employee_id);
    $stmt->execute();
    header("Location: training.php");
    exit();
}

// ========== MARK ATTENDANCE ==========
if (isset($_GET['mark_attended'])) {
    $id = intval($_GET['mark_attended']);
    $conn->query("UPDATE training_attendance SET attended = 1 WHERE id = $id");
    header("Location: training.php");
    exit();
}
if (isset($_GET['mark_unattended'])) {
    $id = intval($_GET['mark_unattended']);
    $conn->query("UPDATE training_attendance SET attended = 0 WHERE id = $id");
    header("Location: training.php");
    exit();
}

// ========== DELETE ==========
if (isset($_GET['delete_training'])) {
    $id = intval($_GET['delete_training']);
    $conn->query("DELETE FROM trainings WHERE id = $id");
    $conn->query("DELETE FROM training_attendance WHERE training_id = $id");
    header("Location: training.php");
    exit();
}
if (isset($_GET['delete_attendance'])) {
    $id = intval($_GET['delete_attendance']);
    $conn->query("DELETE FROM training_attendance WHERE id = $id");
    header("Location: training.php");
    exit();
}

// ========== FETCH DATA ==========
$trainings = $conn->query("SELECT * FROM trainings ORDER BY date DESC");
$employees = $conn->query("SELECT id, name, position FROM employees ORDER BY name ASC");

$attendance = $conn->query("
    SELECT ta.id, ta.attended, t.title AS training_title, t.date, e.name AS employee_name, e.position
    FROM training_attendance ta
    LEFT JOIN trainings t ON ta.training_id = t.id
    LEFT JOIN employees e ON ta.employee_id = e.id
    ORDER BY t.date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Training Management | Microfinance HR2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Training Management</h3>
        <a href="../../logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    </div>

    <!-- ADD TRAINING -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Add New Training</h5>
            <form method="POST" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="title" class="form-control" placeholder="Training Title" required>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="location" class="form-control" placeholder="Location" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="trainer" class="form-control" placeholder="Trainer Name" required>
                </div>
                <div class="col-md-1 text-end">
                    <button type="submit" name="add_training" class="btn btn-primary w-100">Add</button>
                </div>
            </form>
        </div>
    </div>

    <!-- TRAINING LIST -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Training List</h5>
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Trainer</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($trainings && $trainings->num_rows > 0): ?>
                        <?php while ($t = $trainings->fetch_assoc()): ?>
                            <tr>
                                <td><?= $t['id'] ?></td>
                                <td><?= htmlspecialchars($t['title']) ?></td>
                                <td><?= htmlspecialchars($t['date']) ?></td>
                                <td><?= htmlspecialchars($t['location']) ?></td>
                                <td><?= htmlspecialchars($t['trainer']) ?></td>
                                <td class="text-center">
                                    <a href="?delete_training=<?= $t['id'] ?>" class="btn btn-sm btn-danger"
                                       onclick="return confirm('Delete this training and its attendance records?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted">No trainings available.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ENROLL EMPLOYEE -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Enroll Employee to Training</h5>
            <form method="POST" class="row g-3">
                <div class="col-md-5">
                    <select name="training_id" class="form-select" required>
                        <option value="">Select Training</option>
                        <?php
                        $trainings->data_seek(0);
                        while ($t = $trainings->fetch_assoc()): ?>
                            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['title']) ?> (<?= $t['date'] ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <select name="employee_id" class="form-select" required>
                        <option value="">Select Employee</option>
                        <?php
                        $employees->data_seek(0);
                        while ($e = $employees->fetch_assoc()): ?>
                            <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['name']) ?> (<?= htmlspecialchars($e['position']) ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2 text-end">
                    <button type="submit" name="enroll_employee" class="btn btn-primary w-100">Enroll</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ATTENDANCE TABLE -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Training Attendance</h5>
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Training</th>
                        <th>Date</th>
                        <th>Employee</th>
                        <th>Position</th>
                        <th>Attendance</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($attendance && $attendance->num_rows > 0): ?>
                        <?php while ($row = $attendance->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['training_title']) ?></td>
                                <td><?= htmlspecialchars($row['date']) ?></td>
                                <td><?= htmlspecialchars($row['employee_name']) ?></td>
                                <td><?= htmlspecialchars($row['position']) ?></td>
                                <td>
                                    <?php if ($row['attended']): ?>
                                        <span class="badge bg-success">Attended</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Absent</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['attended']): ?>
                                        <a href="?mark_unattended=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Mark Absent</a>
                                    <?php else: ?>
                                        <a href="?mark_attended=<?= $row['id'] ?>" class="btn btn-sm btn-success">Mark Attended</a>
                                    <?php endif; ?>
                                    <a href="?delete_attendance=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                       onclick="return confirm('Delete this attendance record?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted">No attendance records yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
