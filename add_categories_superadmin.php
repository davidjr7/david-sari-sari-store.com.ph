<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'superadmin') {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "sari_sari_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle category operations
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_category"])) {
        $name = trim($_POST["name"]);
        $description = trim($_POST["description"]);
        $icon = $_POST["icon"];
        
        // Check if category already exists
        $check_stmt = $conn->prepare("SELECT id FROM categories WHERE name = ?");
        $check_stmt->bind_param("s", $name);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $_SESSION["message"] = "Category already exists!";
        } else {
            $stmt = $conn->prepare("INSERT INTO categories (name, description, icon) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $description, $icon);
            
            if ($stmt->execute()) {
                $_SESSION["message"] = "Category added successfully!";
                
                // Create the category page file
                $filename = strtolower(str_replace(' ', '', $name)) . '.php';
                $template = file_get_contents('category_template.php');
                $template = str_replace('{{CATEGORY_NAME}}', $name, $template);
                $template = str_replace('{{CATEGORY_TITLE}}', $name, $template);
                file_put_contents($filename, $template);
            } else {
                $_SESSION["message"] = "Error adding category: " . $conn->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
        
        header("Location: add_categories_superadmin.php");
        exit();
    } elseif (isset($_POST["update_category"])) {
        $id = $_POST["category_id"];
        $name = trim($_POST["name"]);
        $description = trim($_POST["description"]);
        $icon = $_POST["icon"];
        
        $stmt = $conn->prepare("UPDATE categories SET name=?, description=?, icon=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $description, $icon, $id);
        
        $_SESSION["message"] = $stmt->execute() ? "Category updated successfully!" : "Error updating category.";
        $stmt->close();
        
        header("Location: add_categories_superadmin.php");
        exit();
    }
}

// Delete category
if (isset($_GET["delete_id"])) {
    $id = $_GET["delete_id"];
    
    // Get category name before deleting
    $result = $conn->query("SELECT name FROM categories WHERE id = $id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $category_name = $row['name'];
        $filename = strtolower(str_replace(' ', '', $category_name)) . '.php';
        
        // Delete the category file if it exists
        if (file_exists($filename)) {
            unlink($filename);
        }
    }
    
    $conn->query("DELETE FROM categories WHERE id = $id");
    $_SESSION["message"] = "Category deleted!";
    header("Location: add_categories_superadmin.php");
    exit();
}

// Fetch category for editing
$edit_category = null;
if (isset($_GET["edit_id"])) {
    $edit_id = $_GET["edit_id"];
    $result = $conn->query("SELECT * FROM categories WHERE id = $edit_id");
    $edit_category = $result->fetch_assoc();
}

// Fetch all categories
$result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperAdmin - Manage Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .icon-preview {
            font-size: 1.5rem;
            margin-left: 10px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
<?php include 'sidebar_for_superadmin.php'; ?>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content p-4">
                <h2>Manage Categories</h2>
                <hr>
                
                <?php if (isset($_SESSION["message"])): ?>
                    <div class="alert alert-info"><?php echo $_SESSION["message"]; unset($_SESSION["message"]); ?></div>
                <?php endif; ?>

                <!-- Add/Edit Category Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><?php echo $edit_category ? 'Edit Category' : 'Add New Category'; ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <?php if ($edit_category): ?>
                                <input type="hidden" name="category_id" value="<?php echo $edit_category['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Category Name</label>
                                    <input type="text" name="name" class="form-control" required 
                                           value="<?php echo $edit_category ? htmlspecialchars($edit_category['name']) : ''; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Icon</label>
                                    <div class="input-group">
                                        <select name="icon" id="icon-select" required class="form-select">
                                            <option value="">Select Icon</option>
                                            <option value="fas fa-bowl-rice" <?php echo ($edit_category && $edit_category['icon'] == 'fas fa-bowl-rice') ? 'selected' : ''; ?>>Rice Bowl</option>
                                            <option value="fas fa-box" <?php echo ($edit_category && $edit_category['icon'] == 'fas fa-box') ? 'selected' : ''; ?>>Box</option>
                                            <option value="fas fa-utensils" <?php echo ($edit_category && $edit_category['icon'] == 'fas fa-utensils') ? 'selected' : ''; ?>>Utensils</option>
                                            <option value="fas fa-glass-martini-alt" <?php echo ($edit_category && $edit_category['icon'] == 'fas fa-glass-martini-alt') ? 'selected' : ''; ?>>Drink</option>
                                            <option value="fas fa-cookie-bite" <?php echo ($edit_category && $edit_category['icon'] == 'fas fa-cookie-bite') ? 'selected' : ''; ?>>Cookie</option>
                                            <option value="fas fa-mug-hot" <?php echo ($edit_category && $edit_category['icon'] == 'fas fa-mug-hot') ? 'selected' : ''; ?>>Coffee Mug</option>
                                            <option value="fas fa-wine-bottle" <?php echo ($edit_category && $edit_category['icon'] == 'fas fa-wine-bottle') ? 'selected' : ''; ?>>Wine Bottle</option>
                                            <option value="fas fa-snowflake" <?php echo ($edit_category && $edit_category['icon'] == 'fas fa-snowflake') ? 'selected' : ''; ?>>Snowflake</option>
                                            <option value="fas fa-bread-slice" <?php echo ($edit_category && $edit_category['icon'] == 'fas fa-bread-slice') ? 'selected' : ''; ?>>Bread</option>
                                            <option value="fas fa-ice-cream" <?php echo ($edit_category && $edit_category['icon'] == 'fas fa-ice-cream') ? 'selected' : ''; ?>>Ice Cream</option>
                                        </select>
                                        <span class="input-group-text"><i id="icon-preview" class="<?php echo $edit_category ? $edit_category['icon'] : 'fas fa-question'; ?>"></i></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3" required><?php 
                                    echo $edit_category ? htmlspecialchars($edit_category['description']) : ''; 
                                ?></textarea>
                            </div>
                            
                            <?php if ($edit_category): ?>
                                <button type="submit" name="update_category" class="btn btn-primary">Update Category</button>
                                <a href="add_categories_superadmin.php" class="btn btn-secondary">Cancel</a>
                            <?php else: ?>
                                <button type="submit" name="add_category" class="btn btn-success">Add Category</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Category List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Existing Categories</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Icon</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td class="text-center"><i class="<?php echo $row['icon']; ?>"></i></td>
                                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                                            <td>
                                                <a href="?edit_id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                                <a href="?delete_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" 
                                                   onclick="return confirm('Are you sure you want to delete this category? All products in this category will become uncategorized.')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No categories found.</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Update icon preview when selection changes
    document.getElementById('icon-select').addEventListener('change', function() {
        const iconPreview = document.getElementById('icon-preview');
        iconPreview.className = this.value;
    });
    </script>
</body>
</html>