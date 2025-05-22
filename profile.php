<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user = $_SESSION['user']; // Logged-in user data
?>

<!DOCTYPE html>
<html lang="tl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Customer Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Sari-Sari Store</a>
        <form class="d-flex ms-auto">
            <input class="form-control me-2" type="search" placeholder="Maghanap ng produkto..." aria-label="Search">
            <button class="btn btn-light" type="submit">Search</button>
        </form>
        <a href="checkout.php" class="btn btn-warning ms-2">Cart (<?php echo count($_SESSION['cart']); ?>)</a>
        <a href="profile.php" class="btn btn-light ms-2">Profile</a>
        <a href="logout.php" class="btn btn-light ms-2">Logout</a>
    </div>
</nav>

<!-- Profile Content -->
<div class="container mt-5">
    <h2>Your Profile</h2>
    <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
    <p><strong>Role:</strong> <?php echo ucfirst($user['role']); ?></p>

    <!-- Optionally, you can add a form to allow users to update their profile -->
    <h3>Update Profile</h3>
    <form method="POST" action="update_profile.php">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" name="username" value="<?php echo $user['username']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

</body>
</html>
