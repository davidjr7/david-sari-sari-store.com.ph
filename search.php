<?php
include('connections.php'); // Make sure this file sets $conn correctly

if (!isset($conn) || !$conn) {
    die("Database connection failed.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $txt_search = $_POST['txt_search'] ?? '';
    $category_search = $_POST['category_search'] ?? '';

    $sql = "SELECT * FROM `products` WHERE `status` = 'available'";

    if ($txt_search || $category_search) {
        if (strlen($txt_search) >= 1) {
            $txt_search = mysqli_real_escape_string($conn, $txt_search);
            $sql .= " AND (`name` LIKE '%$txt_search%' OR `description` LIKE '%$txt_search%')";
        }
        if ($category_search) {
            $category_search = mysqli_real_escape_string($conn, $category_search);
            $sql .= " AND LOWER(`category`) = LOWER('$category_search')";
        }
    }

    // Optional debug line — view source to inspect generated SQL
    // echo "<!-- DEBUG SQL: $sql -->";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $results = '';

        if ($products) {
            foreach ($products as $product) {
                $results .= '
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-card">
                        <div class="product-badge ' . ($product['quantity'] <= 0 ? 'badge-soldout' : 'badge-stock') . '">
                            ' . ($product['quantity'] <= 0 ? 'Sold Out' : 'In Stock') . '
                        </div>
                        <div class="product-image">
                            <img src="' . htmlspecialchars($product['image_url']) . '" alt="' . htmlspecialchars($product['name']) . '" class="img-fluid">
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">' . htmlspecialchars($product['name']) . '</h3>
                            <p class="product-desc">' . htmlspecialchars($product['description']) . '</p>
                            <div class="product-meta">
                                <span class="product-stock"><i class="fas fa-box-open"></i> ' . (int)$product['quantity'] . ' left</span>
                                <span class="product-price">₱' . number_format($product['price'], 2) . '</span>
                            </div>
                            <form method="POST" class="product-form">
                                <input type="hidden" name="product_id" value="' . (int)$product['id'] . '">
                                <button type="submit" class="btn-add-to-cart" ' . ($product['quantity'] <= 0 ? 'disabled' : '') . '>
                                    <i class="fas fa-cart-plus"></i>
                                    ' . ($product['quantity'] <= 0 ? 'Out of Stock' : 'Add to Basket') . '
                                </button>
                            </form>
                        </div>
                    </div>
                </div>';
            }
        } else {
            $results = '<div class="alert alert-info" role="alert">No products found.</div>';
        }

        echo $results;
    } else {
        echo '<div class="alert alert-danger">Database Error: ' . mysqli_error($conn) . '</div>';
    }
} else {
    echo '<div class="alert alert-warning">Invalid request</div>';
}
?>
