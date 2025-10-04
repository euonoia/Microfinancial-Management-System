<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "microfinance_hr2";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>
