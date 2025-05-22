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

// Get category name from URL or use default
$category_name = "add";
$page_title = "add";

// AJAX SEARCH HANDLER
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['txt_search'])) {
    $search_term = $_POST['txt_search'];
    $filtered_products = [];
    
    $sql = "SELECT * FROM products WHERE 
           category = '$category_name' AND 
           status = 'available' AND
           (name LIKE '%$search_term%' OR description LIKE '%$search_term%')";
    
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $filtered_products[] = [
                "id" => $row['id'],
                "name" => $row['name'],
                "price" => $row['price'],
                "image" => $row['image_url'],
                "description" => $row['description'],
                "quantity" => $row['quantity']
            ];
        }
    }
    
    // Return HTML
    if (!empty($filtered_products)) {
        foreach ($filtered_products as $product) {
            echo '
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-card">
                    <div class="product-badge '.($product['quantity'] <= 0 ? 'badge-soldout' : 'badge-stock').'">
                        '.($product['quantity'] <= 0 ? 'Sold Out' : 'In Stock').'
                    </div>
                    <div class="product-image">
                        <a href="product_detail.php?id='.$product['id'].'">
                            <img src="'.$product['image'].'" alt="'.$product['name'].'" class="img-fluid">
                        </a>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name">
                            <a href="product_detail.php?id='.$product['id'].'">'.$product['name'].'</a>
                        </h3>
                        <p class="product-desc">'.$product['description'].'</p>
                        <div class="product-meta">
                            <span class="product-stock"><i class="fas fa-box-open"></i> '.$product['quantity'].' left</span>
                            <span class="product-price">₱'.number_format($product['price'], 2).'</span>
                        </div>
                        <form method="POST" class="product-form">
                            <input type="hidden" name="product_id" value="'.$product['id'].'">
                            <button type="submit" class="btn-add-to-cart" '.($product['quantity'] <= 0 ? 'disabled' : '').'>
                                <i class="fas fa-cart-plus"></i>
                                '.($product['quantity'] <= 0 ? 'Out of Stock' : 'Add to Basket').'
                            </button>
                        </form>
                    </div>
                </div>
            </div>';
        }
    } else {
        echo '<div class="alert alert-info" role="alert">No products found.</div>';
    }
    exit();
}

// Normal page load - fetch all products in this category
$sql = "SELECT * FROM products WHERE category = '$category_name' AND status = 'available'";
$result = $conn->query($sql);
$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            "id" => $row['id'],
            "name" => $row['name'],
            "price" => $row['price'],
            "image" => $row['image_url'],
            "description" => $row['description'],
            "quantity" => $row['quantity']
        ];
    }
}
$conn->close();

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $product_exists = false;
    
    // Check if product already exists in cart
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product_id) {
            $product_exists = true;
            break;
        }
    }
    
    // If product doesn't exist in cart, add it
    if (!$product_exists) {
        foreach ($products as $product) {
            if ($product_id == $product['id']) {
                $_SESSION['cart'][] = $product;
                break;
            }
        }
    }
    
    header("Location: $category_name.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tl">
<head>
    <meta charset="UTF-8">
    <title>Sari-Sari - <?php echo $page_title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/alak.css">
       <style>
        .product-image a, .product-name a {
            text-decoration: none;
            color: inherit;
        }
        .product-image a:hover {
            opacity: 0.9;
        }
        .product-name a:hover {
            color: #0d6efd;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="category-page">

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<main class="container-fluid">
    <div class="hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1><?php echo $page_title; ?></h1>
            <p>Browse our selection of <?php echo strtolower($page_title); ?></p>
            <!-- Search -->
            <div class="search-section mt-4 mb-5">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="d-flex">
                                <input type="text" class="form-control me-2" id="txt_search" placeholder="Search <?php echo strtolower($page_title); ?>...">
                                <button type="button" id="btn_search" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="section-header">
            <h2 class="section-title"><?php echo $page_title; ?> Selection</h2>
            <div class="divider"></div>
            <p class="section-subtitle">Quality <?php echo strtolower($page_title); ?> for your needs</p>
        </div>

        <div id="product-container">
            <div class="row g-4 product-grid">
                <?php foreach ($products as $product): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="product-card">
                            <div class="product-badge <?php echo ($product['quantity'] <= 0) ? 'badge-soldout' : 'badge-stock'; ?>">
                                <?php echo ($product['quantity'] <= 0) ? 'Sold Out' : 'In Stock'; ?>
                            </div>
                            <div class="product-image">
                                <a href="product_detail.php?id=<?php echo $product['id']; ?>">
                                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="img-fluid">
                                </a>
                            </div>
                            <div class="product-info">
                                <h3 class="product-name">
                                    <a href="product_detail.php?id=<?php echo $product['id']; ?>"><?php echo $product['name']; ?></a>
                                </h3>
                                <p class="product-desc"><?php echo $product['description']; ?></p>
                                <div class="product-meta">
                                    <span class="product-stock"><i class="fas fa-box-open"></i> <?php echo $product['quantity']; ?> left</span>
                                    <span class="product-price">₱<?php echo number_format($product['price'], 2); ?></span>
                                </div>
                                <form method="POST" class="product-form">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" class="btn-add-to-cart" <?php echo ($product['quantity'] <= 0) ? 'disabled' : ''; ?>>
                                        <i class="fas fa-cart-plus"></i>
                                        <?php 
                                        // Check if product is already in cart
                                        $in_cart = false;
                                        foreach ($_SESSION['cart'] as $cart_item) {
                                            if ($cart_item['id'] == $product['id']) {
                                                $in_cart = true;
                                                break;
                                            }
                                        }
                                        echo ($in_cart) ? 'Already in Cart' : (($product['quantity'] <= 0) ? 'Out of Stock' : 'Add to Basket'); 
                                        ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>

<script>
$(document).ready(function() {
    // Real-time search with 300ms delay after typing stops
    var searchTimer;
    $('#txt_search').on('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(performSearch, 300);
    });

    // Search button click handler
    $('#btn_search').click(function() {
        performSearch();
    });
    
    // Enter key handler
    $('#txt_search').keypress(function(e) {
        if (e.which == 13) {
            performSearch();
            return false;
        }
    });
    
    function performSearch() {
        const searchTerm = $('#txt_search').val().trim();
        
        // Only search if at least 2 characters or empty (to reset)
        if (searchTerm.length >= 2 || searchTerm.length === 0) {
            $.ajax({
                url: '<?php echo $category_name; ?>.php',
                type: 'POST',
                data: { txt_search: searchTerm },
                success: function(response) {
                    $('.product-grid').html(response);
                },
                error: function() {
                    $('.product-grid').html('<div class="alert alert-danger">Error performing search. Please try again.</div>');
                }
            });
        }
    }
});
</script>

</body>
</html>