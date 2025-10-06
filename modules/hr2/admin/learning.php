<?php
// --- Set custom session name ---
session_name('HR2_ADMIN'); // <-- added session name
session_start();

include('../../../config/database.php');

// --- Auth check ---
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// --- ADD COURSE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $content_url = trim($_POST['content_url']);
    $duration = intval($_POST['duration_minutes']);
    $created_at = date('Y-m-d H:i:s');

    // Generate next course_id automatically
    $result = $conn->query("SELECT course_id FROM courses ORDER BY id DESC LIMIT 1");
    if ($result && $row = $result->fetch_assoc()) {
        $lastNumber = (int) preg_replace('/\D/', '', $row['course_id']); // extract numeric part
        $nextNumber = $lastNumber + 1;
    } else {
        $nextNumber = 1;
    }
    $course_id = 'COUR' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT); // e.g., COUR001

    // Insert the new course
    if ($title !== '') {
        $stmt = $conn->prepare("
            INSERT INTO courses (course_id, title, description, content_url, duration_minutes, created_at) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ssssis", $course_id, $title, $description, $content_url, $duration, $created_at);

        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }

        $stmt->close();
    }

    header("Location: learning.php");
    exit();
}

// --- DELETE COURSE ---
if (isset($_GET['delete'])) {
    $course_id = $_GET['delete'];

    // Step 1: Copy course to archive
    $archiveQuery = "
        INSERT INTO course_archive (course_id, title, description, content_url, duration_minutes, created_at)
        SELECT course_id, title, description, content_url, duration_minutes, created_at
        FROM courses
        WHERE course_id = ?
    ";
    $stmt = $conn->prepare($archiveQuery);
    $stmt->bind_param("s", $course_id);
    $stmt->execute();

    // Step 2: Delete the course from main table
    $deleteQuery = "DELETE FROM courses WHERE course_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("s", $course_id);
    $stmt->execute();

    // Optional: also remove enrollments if you want
    // $conn->query("DELETE FROM course_enrolls WHERE course_id = '$course_id'");

    echo "<script>alert('Course archived successfully!'); window.location.href='learning.php';</script>";
}

$query = "
SELECT 
    c.course_id,
    c.title,
    c.duration_minutes,
    c.created_at,
    COUNT(e.id) AS enrolled
FROM courses c
LEFT JOIN course_enrolls e 
    ON e.course_id = c.course_id
GROUP BY c.course_id, c.title, c.duration_minutes, c.created_at
ORDER BY c.created_at DESC
";


$result = $conn->query($query);
$courses = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Learning Management - HR2 Admin</title>
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
    </style>
    <link rel="icon" href="logo/logo.png" type="image/x-icon">
</head>
<body>
    <div class="navbar">
        <div><strong>HR2 Admin </strong></div>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="admin_register.php">add ADMIN</a>
            <a href="competency.php">Competency</a>
            <a href="learning.php">Learning</a>
            <a href="training.php">Training</a>
            <a href="succession.php">Succession</a>
            <a href="ess.php">ESS</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="container">
        <h2>Learning Management</h2>

        <!-- Add Course -->
        <form method="POST">
            <h3>Add New Course</h3>
            <input type="text" name="title" placeholder="Course Title" required>
            <textarea name="description" placeholder="Course Description"></textarea>
            <input type="text" name="content_url" placeholder="Content URL (optional)">
            <input type="number" name="duration_minutes" placeholder="Duration (minutes)" min="1">
            <button type="submit" name="add_course">Add Course</button>
        </form>

        <!-- Courses Table -->
<table>
    <thead>
        <tr>
            <th>Course Code</th>
            <th>Title</th>
            <th>Duration</th>
            <th>Enrollments</th>
            <th>Created</th>
            <th>Action</th>
        </tr>
    </thead>
        <tbody>
            <?php if (!empty($courses)): ?>
                <?php foreach ($courses as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['course_id']) ?></td>
                        <td><?= htmlspecialchars($c['title']) ?></td>
                        <td><?= htmlspecialchars($c['duration_minutes']) ?> min</td>
                        <td><?= htmlspecialchars($c['enrolled']) ?></td>
                        <td><?= htmlspecialchars($c['created_at']) ?></td>
                        <td>
                            <a href="?delete=<?= $c['course_id'] ?>" class="btn-del" onclick="return confirm('Archive this course?')">Archive</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center;">No courses available.</td>
                </tr>
            <?php endif; ?>
        </tbody>

</table>

    </div>
</body>
</html>
