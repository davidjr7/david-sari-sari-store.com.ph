<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f7fa;
            display: flex;
            min-height: 100vh;
            color: #333;
        }
        .main-content {
            flex-grow: 1;
            padding: 2rem;
        }

        header {
            background-color: #fff;
            padding: 1rem 2rem;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 2rem;
            border-radius: 8px;
        }

        .card {
            background-color: #fff;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }

        .card h3 {
            margin-bottom: 1rem;
        }

        .stats {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .stat-box {
            flex: 1;
            min-width: 200px;
            background: #f9fafb;
            padding: 1rem;
            border-left: 5px solid #3b82f6;
            border-radius: 8px;
        }

        .logout {
            margin-top: 1rem;
            display: inline-block;
            background: #ef4444;
            color: white;
            padding: 0.5rem 1.25rem;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.95rem;
        }

        .logout:hover {
            background: #dc2626;
        }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main-content">
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user']['username']); ?>!</h1>
    </header>

    <div class="card">
        <h3>Dashboard Overview</h3>
        <div class="stats">
            <div class="stat-box">
                <strong>50</strong><br> Total Users
            </div>
            <div class="stat-box">
                <strong>12</strong><br> Pending Reports
            </div>
            <div class="stat-box">
                <strong>3</strong><br> Admin Accounts
            </div>
        </div>
    </div>

    <div class="card">
        <h3>Recent Activity</h3>
        <p>No recent activity found.</p>
    </div>

    <a class="logout" href="logout.php">Logout</a>
</div>

</body>
</html>
