<?php
// Use the same session name as the employee side
session_name('HR2_EMPLOYEE');
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Optionally clear session cookie (extra security)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect back to employee login page
header("Location: login.php");
exit();
?>
