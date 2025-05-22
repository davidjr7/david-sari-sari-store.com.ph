<?php
session_start();

// If cart is empty, redirect to products page
if (empty($_SESSION['cart'])) {
    header("Location: frozenmeat.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "sari_sari_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle quantity changes and item removal
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle quantity updates
    if (isset($_POST['update_quantity'])) {
        foreach ($_POST['quantity'] as $id => $quantity) {
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $id) {
                    $item['quantity'] = max(1, $quantity); // Ensure at least 1 quantity
                    break;
                }
            }
        }
    }
    
    // Handle item removal
    if (isset($_POST['remove_item'])) {
        $remove_id = $_POST['remove_item'];
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $remove_id) {
                unset($_SESSION['cart'][$key]);
                break;
            }
        }
        // Reindex array after removal
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        
        // If cart is now empty, redirect
        if (empty($_SESSION['cart'])) {
            header("Location: frozenmeat.php");
            exit();
        }
    }
    
    // Handle checkout
    if (isset($_POST['checkout'])) {
        foreach ($_SESSION['cart'] as $cart_item) {
            // Update product quantity in database
            $sql = "UPDATE products SET quantity = quantity - " . $cart_item['quantity'] . " WHERE id = " . $cart_item['id'];
            $conn->query($sql);
        }

        // Clear the cart after successful checkout
        $_SESSION['cart'] = [];
        header("Location: checkout_success.php");
        exit();
    }
}

$conn->close();

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="tl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Sari-Sari Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }
        .quantity-input {
            width: 60px;
            text-align: center;
        }
        .total-price {
            font-size: 1.2rem;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-light">

<?php include 'header.php'; ?>

<div class="container py-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Your Shopping Cart</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                        <div class="row align-items-center mb-3 pb-3 border-bottom">
                            <div class="col-md-2">
                                <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="product-img">
                            </div>
                            <div class="col-md-4">
                                <h5 class="mb-1"><?php echo $item['name']; ?></h5>
                                <p class="text-muted mb-1">₱<?php echo number_format($item['price'], 2); ?></p>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input type="number" name="quantity[<?php echo $item['id']; ?>]" 
                                           value="<?php echo $item['quantity']; ?>" 
                                           min="1" class="form-control quantity-input">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <p class="mb-0">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" name="remove_item" value="<?php echo $item['id']; ?>" 
                                        class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="d-flex justify-content-between mt-3">
                            <button type="submit" name="update_quantity" class="btn btn-outline-primary">
                                <i class="fas fa-sync-alt"></i> Update Cart
                            </button>
                            <a href="frozenmeat.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Continue Shopping
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Order Summary</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>₱<?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Delivery Fee:</span>
                        <span>₱50.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between total-price">
                        <span>Total:</span>
                        <span>₱<?php echo number_format($total + 50, 2); ?></span>
                    </div>
                    
                    <form method="POST">
                        <button type="submit" name="checkout" class="btn btn-success w-100 mt-3 py-2">
                            <i class="fas fa-check-circle"></i> Proceed to Checkout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    // Prevent form resubmission on page refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
</body>
</html>