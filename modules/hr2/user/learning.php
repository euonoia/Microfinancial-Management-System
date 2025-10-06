<?php
session_name('HR2_EMPLOYEE'); // Unique session for employee
session_start();
include('../../../config/database.php');

// --- Auth check ---
if (!isset($_SESSION['employee_id'])) {
    header("Location: ../../../login.php");
    exit();
}

// Get numeric employee ID from session
$employee_id = (int)$_SESSION['employee_id'];

// --- Get employee code ---
$stmt_emp = $conn->prepare("SELECT employee_id AS employee_code FROM employees WHERE id = ?");
$stmt_emp->bind_param("i", $employee_id);
$stmt_emp->execute();
$result_emp = $stmt_emp->get_result();
$employee = $result_emp->fetch_assoc();
$employee_code = $employee['employee_code'];
$stmt_emp->close();


// --- Handle enrollment ---
$message = "";
if (isset($_GET['enroll'])) {
    $course_id = $_GET['enroll'];

    // Check if already enrolled
    $stmt_check = $conn->prepare("SELECT id FROM course_enrolls WHERE employee_id = ? AND course_id = ?");
    $stmt_check->bind_param("ss", $employee_code, $course_id); // store code
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows === 0) {
        // Insert enrollment using employee code
        $stmt_insert = $conn->prepare("INSERT INTO course_enrolls (employee_id, course_id, status) VALUES (?, ?, 'enrolled')");
        $stmt_insert->bind_param("ss", $employee_code, $course_id);
        $stmt_insert->execute();
        $stmt_insert->close();
        $message = "Successfully enrolled!";
    } else {
        $message = "You are already enrolled in this course.";
    }
    $stmt_check->close();

    // Redirect to avoid GET sticking
    header("Location: learning.php?msg=" . urlencode($message));
    exit();
}

// --- Show message after redirect ---
if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
}

// --- Fetch all courses with enrollment status ---
$query = "
SELECT c.course_id, c.title, c.duration_minutes,
       CASE WHEN ce.id IS NULL THEN 0 ELSE 1 END AS enrolled,
       ce.employee_id AS enrolled_employee_code
FROM courses c
LEFT JOIN course_enrolls ce ON ce.course_id = c.course_id AND ce.employee_id = ?
ORDER BY c.created_at DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $employee_code); // use code
$stmt->execute();
$result = $stmt->get_result();
$courses = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
        <link rel="icon" href="../../../logo/deamns.png">

<title>Available Courses - HR2 Employee</title>
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
a.enroll-btn { background: #2563eb; color: #fff; padding: 6px 12px; border-radius: 5px; text-decoration: none; }
a.enroll-btn:hover { background: #1d4ed8; }
.message { padding: 10px; background: #22c55e; color: #fff; margin-bottom: 15px; border-radius: 5px; text-align: center; }
</style>
</head>
<body>
<div class="navbar">
    <div><strong>HR2 Employee </strong></div>
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
    <h2>Available Courses</h2>

    <?php if (!empty($message)) echo "<div class='message'>".htmlspecialchars($message)."</div>"; ?>

    <table>
        <thead>
            <tr>
                <th>Course Code</th>
                <th>Title</th>
                <th>Duration</th>
                <th>Status</th>
                <th>Employee Code</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($courses) > 0): ?>
                <?php foreach ($courses as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['course_id']) ?></td>
                        <td><?= htmlspecialchars($c['title']) ?></td>
                        <td><?= htmlspecialchars($c['duration_minutes']) ?> min</td>
                        <td><?= $c['enrolled'] ? 'Enrolled' : 'Not Enrolled' ?></td>
                        <td><?= $c['enrolled'] ? htmlspecialchars($c['enrolled_employee_code']) : '-' ?></td>
                        <td>
                            <?php if (!$c['enrolled']): ?>
                                <a href="?enroll=<?= urlencode($c['course_id']) ?>" class="enroll-btn">Enroll</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align:center;">No courses available.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
