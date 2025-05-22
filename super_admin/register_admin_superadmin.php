<?php
session_start();
include '../connections.php';


// Only allow superadmins to create an account
// Ensure the user is logged in and has the correct role (superadmin)
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'superadmin') {
    header("Location: login.php");
    exit();
}

// Handle form submission for creating an admin or superadmin account
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role']; // Role will be selected by the user (admin or superadmin)

    if ($password !== $confirm_password) {
        $error = "Passwords don't match!";
    } else {
        // Check if username exists
        $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Username already taken!";
        } else {
            // Hash password before saving
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('$username', '$hash', '$role')");

            if ($insert) {
                $success = "Account created successfully!";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Admin or Superadmin Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5" style="max-width: 500px;">
        <h2 class="mb-4 text-center">Create Admin or Superadmin Account</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>

            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>

            <div class="mb-3">
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
            </div>

            <div class="mb-3">
                <!-- Dropdown to select role (admin or superadmin) -->
                <select name="role" class="form-select" required>
                    <option value="admin">Admin</option>
                    <option value="superadmin">Super Admin</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success w-100">Create Account</button>
        </form>

        <div class="text-center mt-3">
            <a href="../super_admin/super_admin_dashboard.php" class="btn btn-secondary btn-sm">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
