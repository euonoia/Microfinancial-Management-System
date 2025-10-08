<?php
session_name('HR2_EMPLOYEE');
session_start();
include('../../../config/database.php');

// --- Auth check ---
if (empty($_SESSION['employee_id'])) {
    header("Location: ../../../login.php");
    exit();
}

// --- Get numeric employee ID ---
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
    $stmt_check->bind_param("ss", $employee_code, $course_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows === 0) {
        // Insert enrollment
        $stmt_insert = $conn->prepare("INSERT INTO course_enrolls (employee_id, course_id, status) VALUES (?, ?, 'enrolled')");
        $stmt_insert->bind_param("ss", $employee_code, $course_id);
        $stmt_insert->execute();
        $stmt_insert->close();
        $message = "Successfully enrolled!";
    } else {
        $message = "You are already enrolled in this course.";
    }
    $stmt_check->close();

    header("Location: learning.php?msg=" . urlencode($message));
    exit();
}

if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
}

// --- Fetch courses with enrollment status ---
$query = "
SELECT 
    c.course_id, 
    c.title, 
    c.duration_minutes,
    c.created_at,
    c.content_url,
    CASE WHEN ce.id IS NULL THEN 0 ELSE 1 END AS enrolled,
    ce.employee_id AS enrolled_employee_code
FROM courses c
LEFT JOIN course_enrolls ce ON ce.course_id = c.course_id AND ce.employee_id = ?
ORDER BY c.created_at DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $employee_code);
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
<title>Learning - HR2 Employee</title>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="style.css">
</head>
<body>
<!-- Mobile Topbar -->
<div class="topbar">
    <button class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('show')">☰</button>
    
</div>
<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="logo">
        <img src="../../../logo/deamns.png" alt="HR2 Logo">
    </div>
    <nav>
       <nav>
    <a href="../../../index.php" >
        <i class="bi bi-house-door"></i> <span>Dashboard</span>
        <div class="tooltip">Dashboard</div>
    </a>
    <a href="competency.php">
        <i class="bi bi-lightbulb"></i> <span>Competencies</span>
        <div class="tooltip">Competencies</div>
    </a>
    <a href="learning.php" class="active">
        <i class="bi bi-book"></i> <span>Learning</span>
        <div class="tooltip">Learning</div>
    </a>
    <a href="training.php">
        <i class="bi bi-mortarboard"></i> <span>Training</span>
        <div class="tooltip">Training</div>
    </a>
    <a href="succession.php">
        <i class="bi bi-tree"></i> <span>Succession</span>
        <div class="tooltip">Succession</div>
    </a>
    <a href="ess.php">
        <i class="bi bi-pencil-square"></i> <span>ESS</span>
        <div class="tooltip">ESS</div>
    </a>
    <a href="../../../logout.php">
        <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
        <div class="tooltip">Logout</div>
    </a>
</nav>

    </nav>
</div>

<!-- MAIN CONTENT -->
<div class="main">
    <div class="main-inner">
        <div class="header">
            <h2>Available Courses</h2>
            <p>Browse and enroll in available courses below.</p>
        </div>

        <?php if (!empty($message)) echo "<div class='message'>".htmlspecialchars($message)."</div>"; ?>

        <table>
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Title</th>
                    <th>Duration</th>
                    <th>Status</th>
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
                            <td>
                                <?php if (!$c['enrolled']): ?>
                                    <a href="?enroll=<?= urlencode($c['course_id']) ?>" class="enroll-btn">Enroll</a>
                                <?php else: ?>
                                    <?php if (!empty($c['content_url'])): ?>
                                        <a href="<?= htmlspecialchars($c['content_url']) ?>" target="_blank" class="view-btn">View Content</a>
                                    <?php else: ?>
                                        <em>No content available</em>
                                    <?php endif; ?>
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
</div>

<!-- SIDEBAR COLLAPSE SCRIPT -->
<script>
    const sidebar = document.getElementById('sidebar');
    sidebar.addEventListener('mouseenter', () => {
        if (window.innerWidth > 768 && sidebar.classList.contains('collapsed')) {
            sidebar.classList.remove('collapsed');
        }
    });
    sidebar.addEventListener('mouseleave', () => {
        if (window.innerWidth > 768) {
            sidebar.classList.add('collapsed');
        }
    });
    if (window.innerWidth > 768) {
        sidebar.classList.add('collapsed');
    }
    document.addEventListener('click', (e) => {
        const toggle = document.querySelector('.menu-toggle');
        if (!sidebar.contains(e.target) && (!toggle || !toggle.contains(e.target))) {
            sidebar.classList.remove('show');
        }
    });
</script>
</body>
</html>
