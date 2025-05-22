<?php
session_start();
include 'connections.php';

// Check if user is logged in
if (isset($_SESSION['user'])) {
    // Redirect based on role
    switch ($_SESSION['user']['role']) {
        case 'superadmin':
            header("Location: superadmin_dashboard.php");
            break;
        case 'admin':
            header("Location: admin_dashboard.php");
            break;
        default:
            header("Location: homepage.php");
    }
} else {
    // Not logged in - redirect to homepage (not login page)
    header("Location: homepage.php");
}
exit();
?>