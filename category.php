<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "sari_sari_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$category_name = $_GET['name'] ?? '';
$category = $conn->query("SELECT * FROM categories WHERE name = '" . $conn->real_escape_string($category_name) . "'")->fetch_assoc();

if (!$category) {
    header("Location: index.php");
    exit();
}

$products = $conn->query("SELECT * FROM products WHERE category = '" . $conn->real_escape_string($category_name) . "' ORDER BY name");
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo htmlspecialchars($category_name); ?> - Sari-Sari Store</title>
    <!-- Include your CSS and other head elements -->
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <h2><?php echo htmlspecialchars($category_name); ?></h2>
        
        <div class="products-grid">
            <?php while ($product = $products->fetch_assoc()): ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p>₱<?php echo number_format($product['price'], 2); ?></p>
                    <!-- Add to cart button, etc. -->
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>