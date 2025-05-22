<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "sari_sari_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination settings
$items_per_page = 10; // Number of items to show per page
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

// Calculate offset for pagination
$offset = ($current_page - 1) * $items_per_page;

// --- Product Adding, Editing, Exporting, and Deletion Logic ---

// Add or Update Product
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_product"])) {
        $name = $_POST["name"];
        $price = $_POST["price"];
        $category = $_POST["category"];
        $description = $_POST["description"];
        $quantity = (int)$_POST["quantity"];
        $stock = $quantity;
        $status = "available";

        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowed_types)) {
            $_SESSION["message"] = "Invalid image format.";
            header("Location: productadmin.php");
            exit();
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO products (name, price, image_url, category, status, description, quantity, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sdssssii", $name, $price, $target_file, $category, $status, $description, $quantity, $stock);

            $_SESSION["message"] = $stmt->execute() ? "Product added successfully!" : "Error adding product.";
            $stmt->close();
        } else {
            $_SESSION["message"] = "Image upload failed.";
        }
    } elseif (isset($_POST["update_product"])) {
        $id = $_POST["product_id"];
        $name = $_POST["name"];
        $price = $_POST["price"];
        $category = $_POST["category"];
        $description = $_POST["description"];
        $quantity = (int)$_POST["quantity"];
        
        // Check if a new image was uploaded
        if (!empty($_FILES["image"]["name"])) {
            $target_dir = "uploads/";
            $image_name = basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $image_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $allowed_types = ["jpg", "jpeg", "png", "gif"];
            if (!in_array($imageFileType, $allowed_types)) {
                $_SESSION["message"] = "Invalid image format.";
                header("Location: productadmin.php");
                exit();
            }

            // Delete old image if it exists
            $result = $conn->query("SELECT image_url FROM products WHERE id = $id");
            $row = $result->fetch_assoc();
            $old_image = $row["image_url"];
            if (file_exists($old_image)) {
                unlink($old_image);
            }

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $stmt = $conn->prepare("UPDATE products SET name=?, price=?, image_url=?, category=?, description=?, quantity=? WHERE id=?");
                $stmt->bind_param("sdsssii", $name, $price, $target_file, $category, $description, $quantity, $id);
            } else {
                $_SESSION["message"] = "Image upload failed.";
                header("Location: productadmin.php");
                exit();
            }
        } else {
            // Update without changing the image
            $stmt = $conn->prepare("UPDATE products SET name=?, price=?, category=?, description=?, quantity=? WHERE id=?");
            $stmt->bind_param("sdssii", $name, $price, $category, $description, $quantity, $id);
        }

        $_SESSION["message"] = $stmt->execute() ? "Product updated successfully!" : "Error updating product.";
        $stmt->close();
    }

    header("Location: productadmin.php");
    exit();
}

// Export Products to XML
if (isset($_GET["export_xml"])) {
    $category = $_GET["category"] ?? "all";

    $query = "SELECT * FROM products";
    if ($category !== "all") {
        $query .= " WHERE category = '$category'";
    }
    $query .= " ORDER BY id DESC";

    $result = $conn->query($query);

    $xml = new SimpleXMLElement('<catalog/>');
    $categories = [];

    while ($row = $result->fetch_assoc()) {
        $catName = $row['category'];
        $nodeName = strtolower(str_replace(' ', '_', $catName));
        if (!isset($categories[$catName])) {
            $categories[$catName] = $xml->addChild($nodeName);
        }

        $product = $categories[$catName]->addChild('product');
        $product->addChild('name', htmlspecialchars($row['name']));
        $product->addChild('price', $row['price']);
        $product->addChild('description', htmlspecialchars($row['description']));
        $product->addChild('quantity', $row['quantity']);
        $product->addChild('picture', htmlspecialchars(basename($row['image_url'])));
    }

    $timestamp = date('Y-m-d_H-i-s');
    $filename = "product_list_" . ($category !== "all" ? $category . "_" : "") . $timestamp . ".xml";

    header('Content-type: text/xml');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo $xml->asXML();
    exit();
}

// Delete Product
if (isset($_GET["delete_id"])) {
    $id = $_GET["delete_id"];
    $result = $conn->query("SELECT image_url FROM products WHERE id = $id");
    $row = $result->fetch_assoc();
    $image_path = $row["image_url"];

    if (file_exists($image_path)) {
        unlink($image_path);
    }

    $conn->query("DELETE FROM products WHERE id = $id");
    $_SESSION["message"] = "Product deleted!";
    header("Location: productadmin.php");
    exit();
}

// Fetch product for editing
$edit_product = null;
if (isset($_GET["edit_id"])) {
    $edit_id = $_GET["edit_id"];
    $result = $conn->query("SELECT * FROM products WHERE id = $edit_id");
    $edit_product = $result->fetch_assoc();
}

// Get filter and sort parameters
$category_filter = $_GET['category_filter'] ?? 'all';
$sort_by = $_GET['sort_by'] ?? 'id_desc';
$search_query = $_GET['search_query'] ?? '';

// Build the product query with filters and sorting
$query = "SELECT * FROM products";

// Apply category filter
if ($category_filter !== 'all') {
    $query .= " WHERE category = '$category_filter'";
}

// Apply search filter
if ($search_query !== '') {
    $search_query = $conn->real_escape_string($search_query);
    if (strpos($query, 'WHERE') !== false) {
        $query .= " AND (name LIKE '%$search_query%' OR description LIKE '%$search_query%')";
    } else {
        $query .= " WHERE (name LIKE '%$search_query%' OR description LIKE '%$search_query%')";
    }
}

// Apply sorting
switch ($sort_by) {
    case 'name_asc':
        $query .= " ORDER BY name ASC";
        break;
    case 'name_desc':
        $query .= " ORDER BY name DESC";
        break;
    case 'price_asc':
        $query .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY price DESC";
        break;
    case 'quantity_asc':
        $query .= " ORDER BY quantity ASC";
        break;
    case 'quantity_desc':
        $query .= " ORDER BY quantity DESC";
        break;
    case 'id_desc':
    default:
        $query .= " ORDER BY id DESC";
        break;
}

// Get total count of products (for pagination)
$count_query = str_replace("SELECT *", "SELECT COUNT(*) as total", $query);
$count_result = $conn->query($count_query);
$total_items = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_items / $items_per_page);

// Add pagination to the main query
$query .= " LIMIT $offset, $items_per_page";
$products_result = $conn->query($query);

// Fetch all categories (both static and from database)
$static_categories = ["Frozen Meat", "Bigas", "Delata", "Kape", "Soft Drinks", "Alak", "Snacks", "Noodles"];
$db_categories_result = $conn->query("SELECT name FROM categories ORDER BY name ASC");
$all_categories = $static_categories;

while ($row = $db_categories_result->fetch_assoc()) {
    if (!in_array($row['name'], $all_categories)) {
        $all_categories[] = $row['name'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - Manage Products</title>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            display: flex;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 240px;
            background-color: #1f2937;
            color: white;
            min-height: 100vh;
        }

        .main-content {
            flex-grow: 1;
            padding: 2rem;
        }

        img { border-radius: 6px; }
        .product-form input, .product-form textarea, .product-form select {
            margin-bottom: 12px;
        }

        h2, h4 {
            color: #333;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #000;
        }

        .table th {
            background-color: #f1f1f1;
        }
        
        .current-image {
            max-width: 100px;
            margin-bottom: 10px;
        }
        
        .hero-section {
            position: relative;
            height: 250px;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            margin-bottom: 2rem;
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }
        
        .hero-content h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .hero-content p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .pagination .page-item.active .page-link {
            background-color: #6e8efb;
            border-color: #6e8efb;
        }
        .pagination .page-link {
            color: #6e8efb;
        }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main-content">
    <div class="hero-section">
        <div class="hero-content">
            <h1>Product Management</h1>
            <p>Manage your store's product inventory</p>
        </div>
    </div>

    <?php if (isset($_SESSION["message"])): ?>
        <div class="alert alert-info mt-2 mb-4"><?php echo $_SESSION["message"]; unset($_SESSION["message"]); ?></div>
    <?php endif; ?>

    <!-- Add/Edit Product Form -->
    <form method="POST" enctype="multipart/form-data" class="product-form mb-5 p-4 bg-white rounded shadow-sm">
        <?php if ($edit_product): ?>
            <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
            <h4>Edit Product</h4>
        <?php else: ?>
            <h4>Add New Product</h4>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6">
                <input type="text" name="name" placeholder="Product Name" required class="form-control" 
                       value="<?php echo $edit_product ? htmlspecialchars($edit_product['name']) : ''; ?>">
            </div>
            <div class="col-md-6">
                <input type="number" step="0.01" name="price" placeholder="Price" required class="form-control"
                       value="<?php echo $edit_product ? $edit_product['price'] : ''; ?>">
            </div>
        </div>
        
        <textarea name="description" placeholder="Product Description" required class="form-control" rows="3"><?php 
            echo $edit_product ? htmlspecialchars($edit_product['description']) : ''; 
        ?></textarea>
        
        <div class="row">
            <div class="col-md-6">
                <input type="number" name="quantity" placeholder="Quantity" min="1" required class="form-control"
                       value="<?php echo $edit_product ? $edit_product['quantity'] : ''; ?>">
            </div>
            <div class="col-md-6">
                <select name="category" required class="form-control">
                    <option value="">Select Category</option>
                    <?php foreach ($all_categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category); ?>" 
                            <?php echo ($edit_product && $edit_product['category'] == $category) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <?php if ($edit_product): ?>
            <div class="mt-3">
                <p>Current Image:</p>
                <img src="<?php echo $edit_product['image_url']; ?>" class="current-image">
            </div>
            <p>Upload new image (leave blank to keep current):</p>
            <input type="file" name="image" accept="image/*" class="form-control">
        <?php else: ?>
            <input type="file" name="image" accept="image/*" required class="form-control mt-3">
        <?php endif; ?>
        
        <div class="mt-3">
            <?php if ($edit_product): ?>
                <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
                <a href="productadmin.php" class="btn btn-secondary">Cancel</a>
            <?php else: ?>
                <button type="submit" name="add_product" class="btn btn-success">Add Product</button>
            <?php endif; ?>
        </div>
    </form>

    <!-- Export to XML -->
    <div class="mt-5 mb-3 p-4 bg-white rounded shadow-sm">
        <h4>Export Products</h4>
        <form method="GET" class="row g-2">
            <div class="col-md-6">
                <select name="category" class="form-control">
                    <option value="all">All Categories</option>
                    <?php foreach ($all_categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category); ?>">
                            <?php echo htmlspecialchars($category); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <button type="submit" name="export_xml" value="1" class="btn btn-primary">Export as XML</button>
            </div>
        </form>
    </div>

    <hr>
    <div class="filter-section mb-4">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="category_filter" class="form-label">Filter by Category:</label>
                <select name="category_filter" id="category_filter" class="form-select">
                    <option value="all" <?php echo $category_filter === 'all' ? 'selected' : ''; ?>>All Categories</option>
                    <?php foreach ($all_categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category); ?>" 
                            <?php echo $category_filter === $category ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="search_query" class="form-label">Search:</label>
                <input type="text" name="search_query" id="search_query" class="form-control" 
                       placeholder="Search by name or description" value="<?php echo htmlspecialchars($search_query); ?>">
            </div>
            <div class="col-md-4">
                <label for="sort_by" class="form-label">Sort By:</label>
                <select name="sort_by" id="sort_by" class="form-select">
                    <option value="id_desc" <?php echo $sort_by === 'id_desc' ? 'selected' : ''; ?>>Newest First</option>
                    <option value="name_asc" <?php echo $sort_by === 'name_asc' ? 'selected' : ''; ?>>Name (A-Z)</option>
                    <option value="name_desc" <?php echo $sort_by === 'name_desc' ? 'selected' : ''; ?>>Name (Z-A)</option>
                    <option value="price_asc" <?php echo $sort_by === 'price_asc' ? 'selected' : ''; ?>>Price (Low to High)</option>
                    <option value="price_desc" <?php echo $sort_by === 'price_desc' ? 'selected' : ''; ?>>Price (High to Low)</option>
                    <option value="quantity_asc" <?php echo $sort_by === 'quantity_asc' ? 'selected' : ''; ?>>Quantity (Low to High)</option>
                    <option value="quantity_desc" <?php echo $sort_by === 'quantity_desc' ? 'selected' : ''; ?>>Quantity (High to Low)</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                <a href="productadmin.php" class="btn btn-outline-secondary">Reset Filters</a>
            </div>
        </form>
    </div>

    <!-- Product List -->
    <h4>Existing Products</h4>
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Category</th>
            <th>Description</th>
            <th>Quantity</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $products_result->fetch_assoc()): ?>
            <tr>
                <td><img src="<?php echo $row['image_url']; ?>" width="50" height="50"></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td>₱<?php echo number_format($row['price'], 2); ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td>
                    <a href="?edit_id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="?delete_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" 
                       onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if ($current_page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>" aria-label="First">
                        <span aria-hidden="true">&laquo;&laquo;</span>
                    </a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php
            // Show page numbers
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            
            if ($start_page > 1) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            
            for ($i = $start_page; $i <= $end_page; $i++): ?>
                <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor;
            
            if ($end_page < $total_pages) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            ?>

            <?php if ($current_page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $total_pages])); ?>" aria-label="Last">
                        <span aria-hidden="true">&raquo;&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

</body>
</html>