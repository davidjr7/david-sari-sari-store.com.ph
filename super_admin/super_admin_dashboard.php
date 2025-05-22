<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'superadmin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Super Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Welcome, Super Admin</h2>
        <a href="register_admin_superadmin.php" class="btn btn-success">Create Admin / Superadmin Account</a>
        <a href="../logout.php" class="btn btn-danger">Logout</a>
    </div>
</body>
</html>
