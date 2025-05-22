<?php
session_start(); // MUST be first line
include 'connections.php';

// Get all categories
$categories_query = "SELECT DISTINCT category FROM products ORDER BY category";
$categories_result = mysqli_query($conn, $categories_query);

// Get all products (or filtered by category)
$category_filter = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : null;
if ($category_filter) {
    $products_query = "SELECT p.*, u.username, u.profile_picture FROM products p 
                       JOIN users u ON p.user_id = u.id 
                       WHERE p.category = '$category_filter' 
                       ORDER BY p.created_at DESC";
} else {
    $products_query = "SELECT p.*, u.username, u.profile_picture FROM products p 
                      JOIN users u ON p.user_id = u.id 
                      ORDER BY p.created_at DESC";
}
$products_result = mysqli_query($conn, $products_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .product-card {
            transition: transform 0.3s;
            height: 100%;
            border-radius: 10px;
            overflow: hidden;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        .category-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 0.8rem;
        }
        .product-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 8px;
        }
        .add-product-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 100;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .filter-container {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .card-footer {
            background-color: rgba(0,0,0,0.03);
            border-top: none;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <h1 class="text-center mb-4">My Product Gallery</h1>
        
        <!-- Category Filter -->
        <div class="filter-container">
            <h5 class="text-center mb-3">Filter by Category</h5>
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <a href="product_gallery.php" class="btn btn-sm btn-outline-primary <?php echo !$category_filter ? 'active' : ''; ?>">
                    <i class="fas fa-th me-1"></i> All Products
                </a>
                <?php while($cat = mysqli_fetch_assoc($categories_result)): ?>
                    <a href="product_gallery.php?category=<?php echo urlencode($cat['category']); ?>" 
                       class="btn btn-sm btn-outline-primary <?php echo ($category_filter === $cat['category']) ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($cat['category']); ?>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="row">
            <?php if (mysqli_num_rows($products_result) > 0): ?>
                <?php while($product = mysqli_fetch_assoc($products_result)): ?>
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card product-card h-100">
                            <div class="position-relative">
                                <img src="<?php echo htmlspecialchars($product['image_path']); ?>" class="product-image" alt="<?php echo htmlspecialchars($product['title']); ?>">
                                <span class="badge bg-primary category-badge"><?php echo htmlspecialchars($product['category']); ?></span>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['title']); ?></h5>
                                <p class="card-text text-muted mb-3"><?php echo htmlspecialchars(substr($product['description'], 0, 80)); ?>...</p>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($product['profile_picture'])): ?>
                                                <img src="<?php echo htmlspecialchars($product['profile_picture']); ?>" class="user-avatar" alt="<?php echo htmlspecialchars($product['username']); ?>">
                                            <?php else: ?>
                                                <div class="user-avatar bg-secondary text-white d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            <?php endif; ?>
                                            <small><?php echo htmlspecialchars($product['username']); ?></small>
                                        </div>
                                        <span class="fw-bold text-primary">$<?php echo number_format($product['price'], 2); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary w-100">
                                    <i class="fas fa-eye me-1"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center py-4">
                        <i class="fas fa-info-circle me-2"></i>
                        No products found<?php echo $category_filter ? " in this category" : ""; ?>.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Floating Add Product Button -->
    <a href="user_product.php" class="btn btn-primary add-product-btn" title="Add Product">
        <i class="fas fa-plus"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>