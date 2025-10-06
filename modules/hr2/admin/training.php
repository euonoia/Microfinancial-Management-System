<?php
// --- Set custom session name for admin ---
session_name('HR2_ADMIN');
session_start();

include('../../../config/database.php');

// --- Auth check ---
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// --- ADD TRAINING SESSION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_training'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $trainer = trim($_POST['trainer']);
    $start_datetime = $_POST['start_datetime'];
    $end_datetime = $_POST['end_datetime'];
    $location = trim($_POST['location']);
    $capacity = intval($_POST['capacity']);

    // --- Generate unique training ID ---
    $result = $conn->query("SELECT training_id FROM training_sessions ORDER BY id DESC LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $lastId = $result->fetch_assoc()['training_id'];
        $num = (int)filter_var($lastId, FILTER_SANITIZE_NUMBER_INT) + 1;
    } else {
        $num = 1;
    }
    $training_id = 'TRN-' . str_pad($num, 4, '0', STR_PAD_LEFT);

    // --- Insert new training ---
    if ($title !== '' && $start_datetime !== '') {
        $stmt = $conn->prepare("
            INSERT INTO training_sessions (training_id, title, description, start_datetime, end_datetime, location, trainer, capacity)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssssi", $training_id, $title, $description, $start_datetime, $end_datetime, $location, $trainer, $capacity);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: training.php");
    exit();
}

// --- ARCHIVE TRAINING SESSION (Soft Delete) ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Move the record to archive table before deleting
    $conn->query("
        INSERT INTO training_sessions_archive 
        (training_id, title, description, start_datetime, end_datetime, location, trainer, capacity)
        SELECT training_id, title, description, start_datetime, end_datetime, location, trainer, capacity
        FROM training_sessions
        WHERE id = $id
    ");

    // Delete the record from main table
    $conn->query("DELETE FROM training_sessions WHERE id = $id");

    header("Location: training.php");
    exit();
}


// --- FETCH TRAINING SESSIONS WITH ATTENDEES COUNT ---
$query = "
    SELECT 
        t.id,
        t.training_id,
        t.title,
        t.description,
        t.start_datetime,
        t.end_datetime,
        t.location,
        t.trainer,
        t.capacity,
        COUNT(e.id) AS attendees
    FROM training_sessions t
    LEFT JOIN training_enrolls e ON e.training_id = t.training_id
    GROUP BY t.id
    ORDER BY t.start_datetime DESC
";

$result = $conn->query($query);
$sessions = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// --- FETCH TRAINERS ---
$trainers = [];
$res = $conn->query("SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM employees ORDER BY first_name ASC");
if ($res) $trainers = $res->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Training Management - HR2 Admin</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; }
        .navbar { background: #1f2937; color: #fff; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: #fff; text-decoration: none; margin-left: 15px; }
        .navbar a:hover { text-decoration: underline; }
        .container { max-width: 1100px; margin: 40px auto; background: #fff; border-radius: 10px; padding: 25px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        h2 { color: #111827; margin-bottom: 20px; }
        form { background: #f9fafb; padding: 15px; border-radius: 8px; margin-bottom: 30px; }
        input, textarea, select { width: 100%; padding: 10px; margin: 8px 0; border-radius: 6px; border: 1px solid #ccc; }
        button { background: #2563eb; color: #fff; padding: 10px 16px; border: none; border-radius: 6px; cursor: pointer; }
        button:hover { background: #1d4ed8; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f3f4f6; }
        a.btn-del { color: red; text-decoration: none; }
        a.btn-del:hover { text-decoration: underline; }
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
        <h2>Training Management</h2>

        <!-- Add Training Session -->
        <form method="POST">
            <h3>Add New Training</h3>
            <input type="text" name="title" placeholder="Training Title" required>
            <textarea name="description" placeholder="Training Description"></textarea>
            <label>Start Date & Time:</label>
            <input type="datetime-local" name="start_datetime" required>
            <label>End Date & Time:</label>
            <input type="datetime-local" name="end_datetime">
            <input type="text" name="location" placeholder="Location">
            <label>Trainer:</label>
            <input type="text" name="trainer" placeholder="Trainer Name">
            <input type="number" name="capacity" placeholder="Capacity (optional)">
            <button type="submit" name="add_training">Add Training</button>
        </form>

        <!-- Training Sessions Table -->
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Trainer</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Location</th>
                    <th>Capacity</th>
                    <th>Attendees</th>
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
                            <td><?= htmlspecialchars($s['attendees']) ?></td>
                            <td>
                                <a href="?delete=<?= $s['id'] ?>" class="btn-del" onclick="return confirm('Archive this training session?')">Archive</a>
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
