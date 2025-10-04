<?php
// Start the session
session_start();

// Unset all session variables
$_SESSION = [];

// If there's a session cookie, delete it
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000, // Set expiration in the past
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Optional: remove any custom cookies you set for login
// Example: setcookie("user_id", "", time() - 3600, "/");

// Redirect to login page
header("Location: admin_login.php");
exit();
?>
