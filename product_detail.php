<?php
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Database connection
$conn = new mysqli("localhost", "root", "", "sari_sari_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// First check all static product arrays from different category files
$product = null;

// Define all possible static product arrays from your category files
$static_product_sources = [
    // Noodles
    [
        ["id" => 1, "name" => "Lucky Me! Pancit Canton Chilimansi", "price" => 15.00, "image" => "image/pancit-canton.jpg", "description" => "Chili lime flavor instant noodles", "quantity" => 50, "category" => "Noodles"],
        ["id" => 2, "name" => "Lucky Me! Beef La Paz Batchoy", "price" => 16.50, "image" => "image/beef-batchoy.jpg", "description" => "Rich beef noodle soup", "quantity" => 40, "category" => "Noodles"],
        ["id" => 3, "name" => "Mi Goreng Satay Flavor", "price" => 12.75, "image" => "image/mi-goreng.jpg", "description" => "Indonesian style fried noodles", "quantity" => 30, "category" => "Noodles"],
        ["id" => 4, "name" => "Nissin Cup Noodles Seafood", "price" => 28.00, "image" => "image/cup-seafood.jpg", "description" => "Ready-to-eat seafood cup noodles", "quantity" => 25, "category" => "Noodles"],
    ],
    // Add other categories here as needed
];

// Check all static product sources
foreach ($static_product_sources as $product_array) {
    foreach ($product_array as $item) {
        if ($item['id'] == $product_id) {
            $product = $item;
            break 2; // Break out of both loops
        }
    }
}

// If not found in static products, check database
if (!$product) {
    $sql = "SELECT * FROM products WHERE id = $product_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $product = [
            "id" => $row['id'],
            "name" => $row['name'],
            "price" => $row['price'],
            "image" => $row['image_url'],
            "description" => $row['description'],
            "quantity" => $row['quantity'],
            "category" => $row['category']
        ];
    }
}

$conn->close();

if (!$product) {
    header("Location: noodles.php");
    exit();
}

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    // Check if product already exists in cart
    $product_exists = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product_id) {
            $product_exists = true;
            $item['cart_quantity'] = isset($item['cart_quantity']) ? $item['cart_quantity'] + $quantity : $quantity;
            break;
        }
    }
    
    // If product doesn't exist in cart, add it
    if (!$product_exists) {
        $product_to_add = $product;
        $product_to_add['cart_quantity'] = $quantity;
        $_SESSION['cart'][] = $product_to_add;
    }
    
    header("Location: product_detail.php?id=" . $product_id);
    exit();
}

// Determine the back link based on the product category
$back_link = isset($product['category']) ? strtolower(str_replace(' ', '', $product['category'])) . '.php' : 'index.php';
?>
<!DOCTYPE html>
<html lang="tl">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['name']); ?> - Sari-Sari</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/alak.css">
    <style>
        .product-gallery {
            margin-bottom: 20px;
        }
        .main-image {
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
            max-height: 400px;
            width: auto;
            object-fit: contain;
        }
        .thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-right: 10px;
            cursor: pointer;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        .thumbnail:hover {
            border-color: #007bff;
        }
        .product-detail-section {
            background-color: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .product-title {
            font-size: 2rem;
            margin-bottom: 15px;
            color: #333;
        }
        .product-price {
            font-size: 1.8rem;
            color: #e63946;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .product-stock {
            font-size: 1.1rem;
            margin-bottom: 20px;
        }
        .in-stock {
            color: #2a9d8f;
        }
        .out-of-stock {
            color: #e63946;
        }
        .product-description {
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .back-to-products {
            margin-bottom: 30px;
        }
        .quantity-selector {
            margin-bottom: 20px;
        }
        .category-badge {
            font-size: 0.9rem;
            background-color: #f8f9fa;
            color: #6c757d;
            padding: 5px 10px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 15px;
        }
    </style>
</head>
<body class="product-detail-page">
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>
    
    <main class="container-fluid mt-4">
        <div class="back-to-products">
            <a href="<?php echo htmlspecialchars($back_link); ?>" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back to Products
            </a>
        </div>
        
        <div class="row">
            <div class="col-lg-6">
                <div class="product-gallery">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid main-image" id="mainImage">
                    <div class="thumbnails">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="thumbnail" onclick="changeImage(this)">
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="product-detail-section">
                    <span class="category-badge">
                        <i class="fas fa-tag"></i> <?php echo isset($product['category']) ? htmlspecialchars($product['category']) : 'General'; ?>
                    </span>
                    <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                    <div class="product-price">₱<?php echo number_format($product['price'], 2); ?></div>
                    
                    <div class="product-stock <?php echo ($product['quantity'] > 0) ? 'in-stock' : 'out-of-stock'; ?>">
                        <i class="fas fa-box-open"></i> 
                        <?php echo ($product['quantity'] > 0) ? 'In Stock (' . $product['quantity'] . ' available)' : 'Out of Stock'; ?>
                    </div>
                    
                    <div class="product-description">
                        <h5>Description</h5>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                    </div>
                    
                    <form method="POST" class="product-form">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        
                        <div class="quantity-selector">
                            <label for="quantity" class="form-label">Quantity:</label>
                            <input type="number" id="quantity" name="quantity" min="1" max="<?php echo $product['quantity']; ?>" 
                                   value="1" class="form-control" style="width: 100px;" 
                                   <?php echo ($product['quantity'] <= 0) ? 'disabled' : ''; ?>>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg btn-add-to-cart" 
                                <?php echo ($product['quantity'] <= 0) ? 'disabled' : ''; ?>>
                            <i class="fas fa-cart-plus"></i> 
                            <?php 
                            // Check if product is already in cart
                            $in_cart = false;
                            $cart_quantity = 0;
                            if (isset($_SESSION['cart'])) {
                                foreach ($_SESSION['cart'] as $cart_item) {
                                    if ($cart_item['id'] == $product['id']) {
                                        $in_cart = true;
                                        $cart_quantity = isset($cart_item['cart_quantity']) ? $cart_item['cart_quantity'] : 1;
                                        break;
                                    }
                                }
                            }
                            echo ($in_cart) ? 'In Cart ('.$cart_quantity.')' : (($product['quantity'] <= 0) ? 'Out of Stock' : 'Add to Cart'); 
                            ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        function changeImage(element) {
            document.getElementById('mainImage').src = element.src;
        }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>