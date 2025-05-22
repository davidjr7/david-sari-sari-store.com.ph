<?php
session_start();
include 'connections.php';

// Check if user is logged in and is editor
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'editor') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Editor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .welcome-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .welcome-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 2rem;
            position: relative;
        }
        .welcome-body {
            padding: 2rem;
            background-color: white;
        }
        .greeting {
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .welcome-text {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 2rem;
        }
        .action-btn {
            border-radius: 50px;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            margin-right: 1rem;
            margin-bottom: 1rem;
        }
        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: rgba(255,255,255,0.2);
            border: none;
            border-radius: 50px;
            padding: 0.5rem 1.2rem;
            color: white;
            transition: all 0.3s;
        }
        .logout-btn:hover {
            background-color: rgba(255,255,255,0.3);
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="welcome-card">
                    <div class="welcome-header text-center">
                        <!-- Logout Button -->
                        <a href="logout.php" class="logout-btn">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                        <h1>Welcome Back, <?php echo $_SESSION['user']['username']; ?>!</h1>
                        <p class="mb-0">Editor Dashboard</p>
                    </div>
                    
                    <div class="welcome-body">
                        <div class="greeting">
                            Hello <?php echo $_SESSION['user']['username']; ?>,
                        </div>
                        
                        <div class="welcome-text">
                            <p>We're glad to see you again. Here's what's happening today in your editor dashboard.</p>
                            <p>You have full access to manage content products and edit the product</p>
                        </div>
                        
                        <div class="d-flex flex-wrap">
                            <a href="editor_manage_products.php" class="btn btn-primary action-btn">
                                <i class="bi bi-box-seam"></i> Manage Products
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-muted">Last login: <?php echo date('F j, Y, g:i a'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>