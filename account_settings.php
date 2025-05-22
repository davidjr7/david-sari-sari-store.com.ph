<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user = $_SESSION['user']; // Logged-in user data

// Database connection
$conn = new mysqli("localhost", "root", "", "sari_sari_store");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $target_dir = "uploads/profile_pics/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
    $new_filename = "user_" . $user['id'] . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Check if image file is a actual image
    $check = getimagesize($_FILES['profile_picture']['tmp_name']);
    if ($check === false) {
        $upload_error = "File is not an image.";
    } 
    // Check file size (max 2MB)
    elseif ($_FILES['profile_picture']['size'] > 2000000) {
        $upload_error = "Sorry, your file is too large (max 2MB).";
    } 
    // Allow certain file formats
    elseif (!in_array(strtolower($file_extension), ['jpg', 'jpeg', 'png', 'gif'])) {
        $upload_error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    } 
    // Try to upload file
    elseif (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
        // Update database with new profile picture path
        $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
        $stmt->bind_param("si", $new_filename, $user['id']);
        if ($stmt->execute()) {
            // Update session with new profile picture
            $_SESSION['user']['profile_picture'] = $new_filename;
            $user = $_SESSION['user'];
            $upload_success = "Profile picture updated successfully!";
        } else {
            $upload_error = "Error updating database: " . $conn->error;
        }
        $stmt->close();
    } else {
        $upload_error = "Sorry, there was an error uploading your file.";
    }
}

// Get cart count or initialize if not set
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="tl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings | Sari-Sari Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .profile-picture {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #0d6efd;
        }
        .upload-btn {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        .upload-btn input[type=file] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        .settings-card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="bi bi-shop"></i> Sari-Sari Store
        </a>
        <form class="d-flex ms-auto me-3" style="max-width: 500px;">
            <div class="input-group">
                <input class="form-control" type="search" placeholder="Maghanap ng produkto..." aria-label="Search">
                <button class="btn btn-light" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
        <div class="d-flex">
            <a href="checkout.php" class="btn btn-warning position-relative me-2">
                <i class="bi bi-cart"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <?php echo $cart_count; ?>
                </span>
            </a>
            <a href="account_settings.php" class="btn btn-light me-2">
                <i class="bi bi-person"></i> Profile
            </a>
            <a href="logout.php" class="btn btn-outline-light">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </div>
</nav>

<!-- Settings Content -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card settings-card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-gear"></i> Account Settings</h4>
                </div>
                <div class="card-body">
                    <!-- Profile Picture Section -->
                    <div class="text-center mb-4">
                        <?php if (!empty($user['profile_picture'])): ?>
                            <img src="uploads/profile_pics/<?php echo htmlspecialchars($user['profile_picture']); ?>" 
                                 class="profile-picture mb-3" 
                                 alt="Profile Picture">
                        <?php else: ?>
                            <div class="profile-picture mb-3 bg-light d-flex align-items-center justify-content-center mx-auto">
                                <i class="bi bi-person" style="font-size: 4rem; color: #6c757d;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data" class="d-inline-block">
                            <div class="upload-btn btn btn-primary">
                                <i class="bi bi-camera"></i> Change Photo
                                <input type="file" name="profile_picture" accept="image/*" onchange="this.form.submit()">
                            </div>
                        </form>
                        
                        <?php if (isset($upload_error)): ?>
                            <div class="alert alert-danger mt-3"><?php echo $upload_error; ?></div>
                        <?php elseif (isset($upload_success)): ?>
                            <div class="alert alert-success mt-3"><?php echo $upload_success; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Account Information Form -->
                    <form method="POST">
                        <h5 class="mb-3 border-bottom pb-2">Account Information</h5>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> To change your username or password, please contact the administrator.
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>