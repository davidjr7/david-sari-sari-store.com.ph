<?php
$host = "localhost";  // Your database host (default: localhost)
$user = "root";       // Your database username (default: root in XAMPP)
$pass = "";           // Your database password (default: empty in XAMPP)
$db = "sari_sari_store"; // Your database name

$conn = mysqli_connect($host, $user, $pass, $db);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
