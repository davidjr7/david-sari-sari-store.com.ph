<?php
session_start();
require_once 'connections.php';

// Check if user is admin
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['role'] !== 'superadmin')) {
    header('Location: login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle logo removal
    if (isset($_POST['remove_logo']) && $_POST['remove_logo'] == '1') {
        $defaultLogo = 'uploads/logos/default_logo.png';
        $stmt = $conn->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_name = 'site_logo'");
        $stmt->bind_param("s", $defaultLogo);
        $stmt->execute();
    }
    // Handle file upload for logo
    else if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/logos/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = 'logo_' . time() . '.' . pathinfo($_FILES['site_logo']['name'], PATHINFO_EXTENSION);
        $uploadPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $uploadPath)) {
            $stmt = $conn->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_name = 'site_logo'");
            $stmt->bind_param("s", $uploadPath);
            $stmt->execute();
        }
    }
    
    // Handle other settings
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'setting_') === 0) {
            $setting_name = substr($key, 8);
            $stmt = $conn->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_name = ?");
            $stmt->bind_param("ss", $value, $setting_name);
            $stmt->execute();
        }
    }
    $success_message = "Settings updated successfully!";
}

// Get current settings
$settings = [];
$result = $conn->query("SELECT setting_name, setting_value FROM site_settings");
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_name']] = $row['setting_value'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4CAF50;
            --primary-dark: #3d8b40;
            --secondary: #6c757d;
            --light: #f8f9fa;
            --dark: #343a40;
            --border: #dee2e6;
            --success-bg: #d4edda;
            --success-text: #155724;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }
        
        .main-container {
            display: flex;
            min-height: 100vh;
        }
        
        .content {
            flex: 1;
            padding: 2rem;
        }
        
        .settings-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            padding: 2rem;
        }
        
        .page-header {
            border-bottom: 2px solid var(--primary);
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        
        .page-title {
            color: var(--dark);
            font-weight: 600;
        }
        
        .settings-section {
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border);
        }
        
        .section-title {
            color: var(--primary);
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--border);
            transition: all 0.2s;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .logo-upload {
            margin-top: 1rem;
        }
        
        .logo-preview {
            max-width: 150px;
            max-height: 150px;
            margin-top: 1rem;
            border-radius: 0.375rem;
            border: 1px solid var(--border);
            padding: 0.25rem;
            object-fit: contain;
        }
        
        .alert-success {
            background-color: var(--success-bg);
            color: var(--success-text);
            border-color: #c3e6cb;
        }
        
        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .form-check-label {
            margin-left: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .content {
                margin-left: 0;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <?php include 'admin_sidebar.php'; ?>
        <div class="content">
            <div class="settings-card">
                <div class="page-header">
                    <h1 class="page-title">Site Settings</h1>
                </div>
                
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success mb-4"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="settings-section">
                        <h2 class="section-title">Basic Information</h2>
                        <div class="mb-3">
                            <label for="setting_site_title" class="form-label">Site Title</label>
                            <input type="text" class="form-control" id="setting_site_title" name="setting_site_title" 
                                   value="<?php echo htmlspecialchars($settings['site_title'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="setting_site_tagline" class="form-label">Site Tagline</label>
                            <input type="text" class="form-control" id="setting_site_tagline" name="setting_site_tagline" 
                                   value="<?php echo htmlspecialchars($settings['site_tagline'] ?? ''); ?>">
                        </div>
                        <div class="mb-3 logo-upload">
                            <label for="site_logo" class="form-label">Site Logo</label>
                            <input type="file" class="form-control" id="site_logo" name="site_logo" accept="image/*">
                            <?php if (!empty($settings['site_logo'])): ?>
                                <div class="mt-3">
                                    <img src="<?php echo htmlspecialchars($settings['site_logo']); ?>" alt="Current Logo" class="logo-preview">
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="remove_logo" id="remove_logo" value="1">
                                    <label class="form-check-label" for="remove_logo">
                                        Remove current logo
                                    </label>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="settings-section">
                        <h2 class="section-title">Store Information</h2>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="setting_store_hours_weekdays" class="form-label">Weekday Hours</label>
                                <input type="text" class="form-control" id="setting_store_hours_weekdays" name="setting_store_hours_weekdays" 
                                       value="<?php echo htmlspecialchars($settings['store_hours_weekdays'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="setting_store_hours_weekend" class="form-label">Weekend Hours</label>
                                <input type="text" class="form-control" id="setting_store_hours_weekend" name="setting_store_hours_weekend" 
                                       value="<?php echo htmlspecialchars($settings['store_hours_weekend'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="setting_store_address" class="form-label">Store Address</label>
                            <input type="text" class="form-control" id="setting_store_address" name="setting_store_address" 
                                   value="<?php echo htmlspecialchars($settings['store_address'] ?? ''); ?>">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="setting_store_phone" class="form-label">Store Phone</label>
                                <input type="text" class="form-control" id="setting_store_phone" name="setting_store_phone" 
                                       value="<?php echo htmlspecialchars($settings['store_phone'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="setting_store_email" class="form-label">Store Email</label>
                                <input type="text" class="form-control" id="setting_store_email" name="setting_store_email" 
                                       value="<?php echo htmlspecialchars($settings['store_email'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="settings-section">
                        <h2 class="section-title">Social Media</h2>
                        <div class="mb-3">
                            <label for="setting_facebook_url" class="form-label">Facebook URL</label>
                            <input type="text" class="form-control" id="setting_facebook_url" name="setting_facebook_url" 
                                   value="<?php echo htmlspecialchars($settings['facebook_url'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="setting_instagram_url" class="form-label">Instagram URL</label>
                            <input type="text" class="form-control" id="setting_instagram_url" name="setting_instagram_url" 
                                   value="<?php echo htmlspecialchars($settings['instagram_url'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="setting_about_page_url" class="form-label">About Page URL</label>
                            <input type="text" class="form-control" id="setting_about_page_url" name="setting_about_page_url" 
                                   value="<?php echo htmlspecialchars($settings['about_page_url'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="settings-section">
                        <h2 class="section-title">Footer</h2>
                        <div class="mb-4">
                            <label for="setting_copyright_text" class="form-label">Copyright Text</label>
                            <input type="text" class="form-control" id="setting_copyright_text" name="setting_copyright_text" 
                                   value="<?php echo htmlspecialchars($settings['copyright_text'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>