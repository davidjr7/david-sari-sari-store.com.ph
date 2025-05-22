<?php
require_once 'connections.php';

// Get site settings
$settings = [];
$result = $conn->query("SELECT setting_name, setting_value FROM site_settings");
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_name']] = $row['setting_value'];
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?php echo htmlspecialchars($settings['site_title']); ?></title>

<!-- Font Awesome & Bootstrap -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
  /* Light Navbar */
  .navbar-custom {
    background-color: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    border-bottom: 3px solid #D87F3A; /* warm orange border */
  }

  /* Brand styling */
  .navbar-brand img {
    height: 50px;
    margin-right: 12px;
    background: transparent;
    border-radius: 6px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  }
  .navbar-brand span {
    font-weight: 700;
    font-size: 1.6rem;
    color: #D87F3A; /* warm orange */
    letter-spacing: 1px;
  }

  /* Navbar text */
  .navbar-text {
    font-weight: 600;
    color: #5A3515; /* dark brown accent */
  }

  /* Buttons styling */
  .btn-light {
    background-color: #FFE9D1; /* very light orange */
    color: #D87F3A; /* warm orange */
    border-radius: 12px;
    font-weight: 600;
    border: 2px solid #D87F3A;
    transition: background-color 0.3s ease;
  }
  .btn-light:hover,
  .btn-light:focus {
    background-color: #D87F3A;
    color: #fff;
  }

  .btn-warning {
    background-color: #D87F3A;
    border-color: #B5642C;
    color: #fff;
    border-radius: 12px;
    font-weight: 700;
    box-shadow: 0 2px 5px rgba(216,127,58,0.5);
  }
  .btn-warning:hover,
  .btn-warning:focus {
    background-color: #B5642C;
    border-color: #8A3E12;
  }

  .btn-danger {
    background-color: #A03A2C;
    border-color: #7B2E23;
    color: #fff;
    border-radius: 12px;
    font-weight: 700;
  }
  .btn-danger:hover,
  .btn-danger:focus {
    background-color: #7B2E23;
    border-color: #591B17;
  }

  /* Cart badge */
  .cart-badge {
    font-size: 0.7rem;
    font-weight: 700;
    background-color: #D87F3A;
    color: white;
    box-shadow: 0 1px 3px rgba(0,0,0,0.15);
  }

  /* Sidebar toggle button */
  #sidebarToggle.navbar-toggler {
    border: none;
    background-color: transparent;
  }

  /* Mobile toggle icons */
  .navbar-toggler-icon {
    filter: drop-shadow(0 0 1px rgba(0,0,0,0.15));
  }
</style>
</head>
<body>
<header>
<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container-fluid">
        <!-- Sidebar Toggle Button -->
        <button id="sidebarToggle" class="navbar-toggler me-2" type="button" aria-label="Toggle sidebar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Brand Logo -->
        <a class="navbar-brand d-flex align-items-center" href="homepage.php">
            <?php if (!empty($settings['site_logo'])): ?>
                <img src="<?php echo htmlspecialchars($settings['site_logo']); ?>" alt="<?php echo htmlspecialchars($settings['site_title']); ?>" class="img-fluid" />
            <?php endif; ?>
            <span><?php echo htmlspecialchars($settings['site_title']); ?></span>
        </a>

        <?php if (isset($_SESSION['user'])): ?>
        <span class="navbar-text d-none d-lg-block me-3">
            Welcome, <strong><?php echo htmlspecialchars($_SESSION['user']['username']); ?></strong>!
        </span>
        <?php endif; ?>

        <!-- Navigation Links -->
        <div class="d-flex">
            <?php if (isset($_SESSION['user'])): ?>
            <!-- Mobile Search Toggle -->
            <button class="btn btn-light d-lg-none me-2" type="button" data-bs-toggle="collapse" data-bs-target="#mobileSearch" aria-label="Toggle search">
                <i class="fas fa-search"></i>
            </button>

            <a href="homepage.php" class="btn btn-light me-2" aria-label="Home">
                <i class="fas fa-home d-lg-none"></i>
                <span class="d-none d-lg-inline">Home</span>    
            </a>
            <a href="#" class="btn btn-light me-2" aria-label="Profile">
                <i class="fas fa-store d-lg-none"></i>
                <span class="d-none d-lg-inline">Profile</span>
            </a>
            <a href="checkout.php" class="btn btn-warning me-2 position-relative" aria-label="Cart">
                <i class="fas fa-shopping-cart"></i>
                <span class="d-none d-lg-inline">Cart</span>
                <?php $cartCount = count($_SESSION['cart'] ?? []); ?>
                <?php if ($cartCount > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill cart-badge">
                    <?php echo $cartCount; ?>
                </span>
                <?php endif; ?>
            </a>
            <a href="logout.php" class="btn btn-danger" aria-label="Logout">
                <i class="fas fa-sign-out-alt d-lg-none"></i>
                <span class="d-none d-lg-inline">Logout</span>
            </a>
            <?php else: ?>
            <a href="login.php" class="btn btn-light" aria-label="Login">
                <i class="fas fa-sign-in-alt d-lg-none"></i>
                <span class="d-none d-lg-inline">Login</span>
            </a>
            <?php endif; ?>
        </div>
    </div>
</nav>
</header>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
