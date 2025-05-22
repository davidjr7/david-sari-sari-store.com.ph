<?php
include 'connections.php';
include 'header.php';

// Fetch categories from database
$categories_query = "SELECT * FROM categories ORDER BY name ASC";
$categories_result = $conn->query($categories_query);
$db_categories = [];
if ($categories_result->num_rows > 0) {
    while ($row = $categories_result->fetch_assoc()) {
        $db_categories[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Munting Sari-Sari Store</title>
    <meta name="description" content="Your one-stop neighborhood sari-sari store offering rice, canned goods, snacks, beverages, and more at affordable prices.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="image/store-icon.png" type="image/png">
</head>
<body>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <?php if (isset($_SESSION['user'])): ?>
                <h1>Welcome, <?= htmlspecialchars($_SESSION['user']['username']) ?>!</h1>
                <p>What would you like to order today? We have fresh stocks just for you!</p>
                <?php if (isset($_SESSION['last_login'])): ?>
                    <small class="last-login">Last visited: <?= date('F j, Y g:i a', strtotime($_SESSION['last_login'])) ?></small>
                <?php endif; ?>
            <?php else: ?>
                <h1>Munting Sari-Sari Store ni David</h1>
                <p>Your neighborhood convenience store with affordable daily essentials</p>
                <a href="login.php" class="btn btn-primary">Login to Shop</a>
            <?php endif; ?>
        </div>
    </section>

    <main class="container">
        <!-- About Section -->
        <section class="about-section">
            <div class="about-container" style="display: flex; background: #fff; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); padding: 20px; margin: 40px;">

  <!-- Left: Storefront Image -->
  <div class="about-image" style="flex: 1; display: flex; justify-content: center; align-items: center;">
    <img src="image/d0f7b7ba-bbd2-45d8-860f-8a5b99643a3f.jpg" alt="Our Store Front" style="max-width: 100%; height: auto; border-radius: 10px;">
  </div>

  <!-- Right: Text Content Beside the Image -->
  <div class="about-description" style="flex: 2; padding-left: 30px; display: flex; flex-direction: column; justify-content: center;">
    <h2 style="font-size: 28px; color: #5c3b1e; margin-bottom: 15px;">The Man Behind the Tindahan</h2>
    <p style="font-size: 16px; color: #555;">
     Hi there! I’m David, Creator of  <strong>Munting Sari-Sari Store ni David</strong> I Created this sari-sari store to make everyday essentials easy to buy and online, affordable to buy, and always served with a smile. Tara, let’s make every day sulit!
  </div>

</div>
            <div class="about-content">
                <h2>About My Store</h2>
                <p>Established in 2025, Munting Sari-Sari Store ni David has been serving the community with quality products at affordable prices. We take pride in offering fresh goods and excellent customer service.</p>
                <div class="about-features">
                    <div class="feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Fresh Products Daily</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Affordable Prices</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Friendly Service</span>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Categories Section -->
        <section class="categories-section">
            <div class="section-header">
                <h2>Our Products</h2>
                <p>Find everything you need for your Del Monte Kitchennomics</p>
            </div>
            <div class="category-grid">
                <!-- Hardcoded categories -->
                <a href="bigas.php" class="category-card">
                    <div class="category-icon">🍚</div>
                    <h3>Bigas</h3>
                    <p>Quality rice varieties</p>
                </a>
                <a href="delata.php" class="category-card">
                    <div class="category-icon">🥫</div>
                    <h3>De Lata</h3>
                    <p>Canned goods & preserves</p>
                </a>
                <a href="noodles.php" class="category-card">
                    <div class="category-icon">🍜</div>
                    <h3>Noodles</h3>
                    <p>Instant & fresh noodles</p>
                </a>
                <a href="softdrinks.php" class="category-card">
                    <div class="category-icon">🥤</div>
                    <h3>Beverages</h3>
                    <p>Drinks & refreshments</p>
                </a>
                <a href="snacks.php" class="category-card">
                    <div class="category-icon">🍪</div>
                    <h3>Snacks</h3>
                    <p>Chips, cookies & more</p>
                </a>
                <a href="kape.php" class="category-card">
                    <div class="category-icon">☕</div>
                    <h3>Kape</h3>
                    <p>Coffee & creamers</p>
                </a>
                
                <!-- Dynamic categories from database -->
                <?php foreach ($db_categories as $category): 
                    $filename = strtolower(str_replace(' ', '', $category['name'])) . '.php';
                ?>
                    <a href="<?= $filename ?>" class="category-card">
                        <div class="category-icon"><i class="<?= $category['icon'] ?>"></i></div>
                        <h3><?= htmlspecialchars($category['name']) ?></h3>
                        <p><?= htmlspecialchars($category['description']) ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
        
        <!-- Promo Banner -->
        <section class="promo-banner">
            <div class="promo-content">
                <h3>Today's Special!</h3>
                <p>Buy 2 San Mig Light, Get 1 Free!</p>
                <small>Offer valid until supplies last</small>
                <a href="alak.php" class="btn btn-outline-light">Shop Now</a>
            </div>
        </section>
        
        <!-- Featured Products -->
        <?php
// Start session if using login checks
if (session_status() == PHP_SESSION_NONE) session_start();

// Featured product data
$featuredProducts = [
    [ 'id' => 1, 'name' => 'Jasmine Rice 1kg', 'price' => 55.00, 'image' => 'image/jasmine.jpg', 'category' => 'bigas.php', 'discount' => 0, 'description' => 'Premium quality jasmine rice' ],
    [ 'id' => 2, 'name' => 'Lucky Me Pancit Canton', 'price' => 15.00, 'image' => 'image/regularpancit.jpg', 'category' => 'noodles.php', 'discount' => 0, 'description' => 'Classic instant noodles' ],
    [ 'id' => 3, 'name' => 'Coke 1.5L', 'price' => 75.00, 'image' => 'image/Coca-Cola Regular 330ml.jpg', 'category' => 'softdrinks.php', 'discount' => 5.00, 'description' => 'Refreshing cola drink' ],
    [ 'id' => 4, 'name' => 'Nescafe 3-in-1', 'price' => 10.00, 'image' => 'image/nescafe.jpg', 'category' => 'kape.php', 'discount' => 0, 'description' => 'Instant coffee mix' ],
    [ 'id' => 5, 'name' => 'San Mig Light', 'price' => 45.00, 'image' => 'image/sanmig.jpg', 'category' => 'alak.php', 'discount' => 0, 'description' => 'Light beer beverage' ],
    // Add more if needed
];

// Pagination logic
$productsPerPage = 2; // How many per page
$totalProducts = count($featuredProducts);
$totalPages = ceil($totalProducts / $productsPerPage);

$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$startIndex = ($currentPage - 1) * $productsPerPage;

// Get only products for current page
$productsToShow = array_slice($featuredProducts, $startIndex, $productsPerPage);
?>

<section class="featured-products">
    <div class="section-header">
        <h2>Featured Products</h2>
        <p>Customer favorites this week</p>
    </div>
    <div class="product-grid">
        <?php foreach ($productsToShow as $product): ?>
        <div class="product-card">
            <a href="<?= $product['category'] ?>">
                <div class="product-image">
                    <img src="<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" loading="lazy">
                    <?php if ($product['discount'] > 0): ?>
                        <span class="discount-badge">Save ₱<?= number_format($product['discount'], 2) ?></span>
                    <?php endif; ?>
                </div>
            </a>
            <div class="product-info">
                <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                <p class="product-description"><?= htmlspecialchars($product['description']) ?></p>
                <div class="price-container">
                    <?php if ($product['discount'] > 0): ?>
                        <span class="original-price">₱<?= number_format($product['price'], 2) ?></span>
                        <span class="product-price">₱<?= number_format($product['price'] - $product['discount'], 2) ?></span>
                    <?php else: ?>
                        <span class="product-price">₱<?= number_format($product['price'], 2) ?></span>
                    <?php endif; ?>
                </div>
                <?php if (isset($_SESSION['user'])): ?>
                    <button class="add-to-cart" data-product-id="<?= $product['id'] ?>">
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                <?php else: ?>
                    <div class="login-notice">
                        <a href="login.php" class="login-link"><i class="fas fa-sign-in-alt"></i> Login to purchase</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <div class="pagination" style="text-align:center; margin-top: 20px;">
        <?php if ($currentPage > 1): ?>
            <a href="?page=<?= $currentPage - 1 ?>" style="margin: 0 5px;">&laquo; Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i == $currentPage): ?>
                <strong style="margin: 0 5px;"><?= $i ?></strong>
            <?php else: ?>
                <a href="?page=<?= $i ?>" style="margin: 0 5px;"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <a href="?page=<?= $currentPage + 1 ?>" style="margin: 0 5px;">Next &raquo;</a>
        <?php endif; ?>
    </div>
</section>

        
    <section class="testimonials-section">
    <div class="section-header">
        <h2>What Our Customers Say</h2>
        <p>Hear from our satisfied customers</p>
    </div>
    <div class="testimonial-grid">
        <div class="testimonial-card">
            <div class="testimonial-content">
                <div class="rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>"The best sari-sari store in our neighborhood! Always fresh yung owner."</p>
                <div class="customer-info">
                    <img src="image/liza.jpg" alt="Liza Soberano">
                    <div>
                        <h4>Liza Soberano</h4>
                        <small>Regular Customer</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="testimonial-card">
            <div class="testimonial-content">
                <div class="rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <p>"Nag bayad ako 1k dina binalik sukli. Dahil ba sa pananamit ko dahil ba ilongo ko"</p>
                <div class="customer-info">
                    <img src="image/willie.jpg" alt="Willie Revillame">
                    <div>
                        <h4>Willie Revillame</h4>
                        <small>Local Resident</small>
                    </div>
                </div>
            </div>
        </div>
        <!-- New testimonial added here -->
        <div class="testimonial-card">
            <div class="testimonial-content">
                <div class="rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>"Kaya lang ako nabili dito kase gwapo yung owner"</p>
                <div class="customer-info">
                    <img src="image/213094812_295082832396361_1326352468021634371_n.jpg" alt="Juan Dela Cruz">
                    <div>
                        <h4>Angel Locsin</h4>
                        <small>Frequent Shopper</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
        
        <!-- Newsletter -->
        <section class="newsletter">
            <div class="newsletter-content">
                <h3>Stay Updated</h3>
                <p>Subscribe to our newsletter for promotions and updates</p>
                <form class="newsletter-form">
                    <input type="email" placeholder="Your email address" required>
                    <button type="submit" class="btn btn-primary">Subscribe</button>
                </form>
            </div>
        </section>
    </main>
<?php include 'footer.php'; ?>
   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>

<style>
    :root {
        --primary-color: #6F4E37; /* Coffee brown */
        --primary-light: #8B6B4D;
        --primary-dark: #5A3A2C;
        --secondary-color:rgb(150, 146, 138); /* Cream */
        --accent-color:rgb(155, 133, 110); /* Light brown */
        --light-color: #F9F5F0; /* Off-white */
        --dark-color: #2A2118; /* Dark brown */
        --text-color: #333333;
        --text-light: #777777;
        --white: #ffffff;
        --black: #000000;
        --success-color: #28a745;
        --warning-color: #ffc107;
        --error-color: #dc3545;
    }
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }
    
    body {
        background-color: var(--light-color);
        color: var(--text-color);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        line-height: 1.6;
    }
    
    h1, h2, h3, h4 {
        font-family: 'Playfair Display', serif;
        font-weight: 600;
    }
    
    /* Hero Section */
    .hero {
        position: relative;
        height: 80vh;
        background: url('image/store-bg.jpg') no-repeat center center;
        background-size: cover;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: var(--white);
        margin-bottom: 60px;
    }
    
    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('image/4c39b5dd-f58a-48c7-830a-39cf7f9327d3.png');
        background-size: cover;
        background-repeat: no-repeat;
background-position: center;

    }
    
    .hero-content {
        position: relative;
        z-index: 1;
        max-width: 800px;
        padding: 0 20px;
    }
    
    .hero h1 {
        font-size: 3.5rem;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    
    .hero p {
        font-size: 1.2rem;
        margin-bottom: 30px;
    }
    
    .btn {
        display: inline-block;
        padding: 12px 30px;
        border-radius: 30px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        color: var(--white);
        border: 2px solid var(--primary-color);
    }
    
    .btn-primary:hover {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .btn-outline-light {
        background-color: transparent;
        color: var(--white);
        border: 2px solid var(--white);
    }
    
    .btn-outline-light:hover {
        background-color: var(--white);
        color: var(--primary-color);
    }
    
    main {
        flex: 1;
        padding: 0 20px 60px;
        max-width: 1200px;
        margin: 0 auto;
        width: 100%;
    }
    
    /* About Section */
    .about-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        margin-bottom: 80px;
        align-items: center;
    }
    
    .about-image {
        border-radius: 13px;
        overflow: hidden;
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    
    .about-image img {
        width: 200%;
        height: auto;
        display: block;
    }
    
    .about-content h2 {
        font-size: 2.5rem;
        margin-bottom: 20px;
        color: var(--primary-color);
    }
    
    .about-content p {
        margin-bottom: 25px;
        font-size: 1.1rem;
        color: var(--text-light);
    }
    
    .about-features {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }
    
    .feature {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .feature i {
        color: var(--primary-color);
        font-size: 1.2rem;
    }
    
    /* Section Header */
    .section-header {
        text-align: center;
        margin-bottom: 50px;
    }
    
    .section-header h2 {
        font-size: 2.5rem;
        color: var(--primary-color);
        margin-bottom: 15px;
        position: relative;
        display: inline-block;
    }
    
    .section-header h2:after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background-color: var(--accent-color);
    }
    
    .section-header p {
        color: var(--text-light);
        font-size: 1.1rem;
        max-width: 600px;
        margin: 0 auto;
    }
    
    /* Categories Section */
    .categories-section {
        margin-bottom: 80px;
    }
    
    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 30px;
    }
    
    .category-card {
        background-color: var(--white);
        border-radius: 10px;
        padding: 30px 20px;
        text-align: center;
        transition: all 0.3s ease;
        text-decoration: none;
        color: var(--text-color);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .category-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    
    .category-icon {
        font-size: 3rem;
        margin-bottom: 20px;
        color: var(--primary-color);
    }
    
    .category-card h3 {
        font-size: 1.5rem;
        margin-bottom: 10px;
        color: var(--primary-color);
    }
    
    .category-card p {
        color: var(--text-light);
        font-size: 0.9rem;
    }
    
    /* Promo Banner */
    .promo-banner {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: var(--white);
        padding: 60px 40px;
        border-radius: 10px;
        margin: 60px 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .promo-banner:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('image/coffee-beans-pattern.png') center/cover;
        opacity: 0.1;
    }
    
    .promo-content {
        position: relative;
        z-index: 1;
    }
    
    .promo-content h3 {
        font-size: 1.8rem;
        margin-bottom: 10px;
    }
    
    .promo-content p {
        font-size: 1.5rem;
        font-weight: 500;
        margin-bottom: 15px;
    }
    
    .promo-content small {
        display: block;
        margin-bottom: 20px;
        font-size: 0.9rem;
        opacity: 0.8;
    }
    
    /* Featured Products */
    .featured-products {
        margin-bottom: 80px;
    }
    
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 30px;
    }
    
    .product-card {
        background-color: var(--white);
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    
    .product-image {
        height: 200px;
        position: relative;
        overflow: hidden;
    }
    
    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }
    
    .product-card:hover .product-image img {
        transform: scale(1.1);
    }
    
    .discount-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background-color: var(--error-color);
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
    }
    
    .product-info {
        padding: 20px;
    }
    
    .product-name {
        font-size: 1.2rem;
        margin-bottom: 10px;
        color: var(--primary-color);
    }
    
    .product-description {
        color: var(--text-light);
        font-size: 0.9rem;
        margin-bottom: 15px;
        min-height: 40px;
    }
    
    .price-container {
        margin-bottom: 15px;
    }
    
    .product-price {
        color: var(--primary-color);
        font-weight: bold;
        font-size: 1.3rem;
    }
    
    .original-price {
        color: var(--text-light);
        font-size: 1rem;
        text-decoration: line-through;
        margin-right: 8px;
    }
    
    .add-to-cart {
        width: 100%;
        padding: 12px;
        background-color: var(--primary-color);
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .add-to-cart:hover {
        background-color: var(--primary-dark);
        transform: translateY(-3px);
    }
    
    .login-notice {
        text-align: center;
        margin-top: 15px;
    }
    
    .login-link {
        color: var(--primary-color);
        font-weight: 500;
        text-decoration: none;
        transition: color 0.3s;
    }
    
    .login-link:hover {
        color: var(--primary-dark);
        text-decoration: underline;
    }
    
    /* Testimonials */
    .testimonials-section {
        margin-bottom: 80px;
    }
    
    .testimonial-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 30px;
    }
    
    .testimonial-card {
        background-color: var(--white);
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .rating {
        color: var(--warning-color);
        margin-bottom: 15px;
    }
    
    .testimonial-content p {
        font-style: italic;
        margin-bottom: 20px;
        color: var(--text-light);
    }
    
    .customer-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .customer-info img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .customer-info h4 {
        font-size: 1.1rem;
        margin-bottom: 5px;
    }
    
    .customer-info small {
        font-size: 0.8rem;
        color: var(--text-light);
    }
    
    /* Newsletter */
    .newsletter {
        background-color: var(--secondary-color);
        padding: 60px;
        border-radius: 10px;
        text-align: center;
    }
    
    .newsletter h3 {
        font-size: 1.8rem;
        margin-bottom: 10px;
        color: var(--primary-color);
    }
    
    .newsletter p {
        color: var(--text-light);
        margin-bottom: 20px;
    }
    
    .newsletter-form {
        display: flex;
        max-width: 500px;
        margin: 0 auto;
    }
    
    .newsletter-form input {
        flex: 1;
        padding: 12px 20px;
        border: none;
        border-radius: 30px 0 0 30px;
        font-size: 1rem;
    }
    
    .newsletter-form button {
        border-radius: 0 30px 30px 0;
        padding: 12px 25px;
    }
    .categories-section {
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
}

/* Header */
.section-header {
    text-align: center;
    margin-bottom: 30px;
}

.section-header h2 {
    font-size: 2.5rem;
    margin-bottom: 10px;
    color: #2c3e50;
}

.section-header p {
    font-size: 1.1rem;
    color: #666;
}

/* Category Grid */
.category-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}

/* Category Card */
.category-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    background-color: #fafafa;
    padding: 20px;
    border-radius: 12px;
    text-decoration: none;
    color: #2c3e50;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.03);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}

.category-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
}

.category-card h3 {
    font-size: 1.3rem;
    margin-bottom: 8px;
}

.category-card p {
    font-size: 0.95rem;
    color: #555;
}

/* Pagination */
.pagination {
    margin-top: 40px;
    text-align: center;
}

.pagination a {
    display: inline-block;
    padding: 10px 16px;
    margin: 0 5px;
    background: #e0e0e0;
    color: #333;
    font-weight: 500;
    text-decoration: none;
    border-radius: 8px;
    transition: background 0.3s ease;
}

.pagination a:hover {
    background-color: #007bff;
    color: white;
}

.pagination a.active {
    background-color: #007bff;
    color: white;
    pointer-events: none;
}
    /* Responsive Styles */
    @media (max-width: 992px) {
        .about-section {
            grid-template-columns: 1fr;
        }
        
        .about-image {
            order: -1;
        }
        
        .hero h1 {
            font-size: 2.8rem;
        }
    }
    
    @media (max-width: 768px) {
        .hero {
            height: 60vh;
        }
        
        .hero h1 {
            font-size: 2.2rem;
        }
        
        .hero p {
            font-size: 1rem;
        }
        
        .section-header h2 {
            font-size: 2rem;
        }
        
        .category-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }
        
        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }
        
        .testimonial-grid {
            grid-template-columns: 1fr;
        }
        
        .newsletter {
            padding: 40px 20px;
        }
        
        .newsletter-form {
            flex-direction: column;
        }
        
        .newsletter-form input {
            border-radius: 30px;
            margin-bottom: 10px;
        }
        
        .newsletter-form button {
            border-radius: 30px;
        }
    }
    
    @media (max-width: 576px) {
        .hero {
            height: 50vh;
            margin-bottom: 40px;
        }
        
        .hero h1 {
            font-size: 1.8rem;
        }
        
        .about-features {
            grid-template-columns: 1fr;
        }
        
        .promo-banner {
            padding: 40px 20px;
        }
        
        .promo-content h3 {
            font-size: 1.5rem;
        }
        
        .promo-content p {
            font-size: 1.2rem;
        }
    }
    /* ========================
   MEDIA QUERIES
   ======================== */

/* Large devices (desktops, less than 1200px) */
@media (max-width: 1199.98px) {
    .hero-content {
        padding: 0 40px;
    }
    
    .about-section {
        gap: 30px;
    }
}

/* Medium devices (tablets, less than 992px) */
@media (max-width: 991.98px) {
    /* General adjustments */
    .hero {
        height: 70vh;
    }
    
    .hero h1 {
        font-size: 2.8rem;
    }
    
    /* About section */
    .about-section {
        grid-template-columns: 1fr;
    }
    
    .about-image {
        order: -1;
        max-width: 500px;
        margin: 0 auto;
    }
    
    /* Categories */
    .category-grid {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    }
    
    /* Testimonials */
    .testimonial-grid {
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    }
}

/* Small devices (landscape phones, less than 768px) */
@media (max-width: 767.98px) {
    /* Hero section */
    .hero {
        height: 60vh;
        margin-bottom: 40px;
    }
    
    .hero h1 {
        font-size: 2.2rem;
    }
    
    .hero p {
        font-size: 1rem;
    }
    
    /* Section headers */
    .section-header h2 {
        font-size: 2rem;
    }
    
    /* Products */
    .product-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
    }
    
    /* Promo banner */
    .promo-banner {
        padding: 40px 20px;
    }
    
    .promo-content h3 {
        font-size: 1.5rem;
    }
    
    .promo-content p {
        font-size: 1.2rem;
    }
    
    /* Testimonials */
    .testimonial-card {
        padding: 20px;
    }
    
    /* Newsletter */
    .newsletter {
        padding: 40px 20px;
    }
    
    .newsletter-form {
        flex-direction: column;
    }
    
    .newsletter-form input {
        border-radius: 30px;
        margin-bottom: 10px;
    }
    
    .newsletter-form button {
        border-radius: 30px;
        width: 100%;
    }
}

/* Extra small devices (portrait phones, less than 576px) */
@media (max-width: 575.98px) {
    /* Hero section */
    .hero {
        height: 50vh;
    }
    
    .hero h1 {
        font-size: 1.8rem;
        line-height: 1.3;
    }
    
    /* About section */
    .about-features {
        grid-template-columns: 1fr;
    }
    
    /* Categories */
    .category-grid {
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }
    
    .category-card {
        padding: 20px 15px;
    }
    
    .category-icon {
        font-size: 2.5rem;
    }
    
    /* Products */
    .product-grid {
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }
    
    .product-image {
        height: 150px;
    }
    
    /* Testimonials */
    .testimonial-grid {
        grid-template-columns: 1fr;
    }
    
    /* Footer adjustments */
    footer .footer-content {
        flex-direction: column;
        text-align: center;
        gap: 20px;
    }
    
    footer .footer-links {
        justify-content: center;
    }
}

/* Very small devices (phones under 400px) */
@media (max-width: 399.98px) {
    /* Hero section */
    .hero h1 {
        font-size: 1.5rem;
    }
    
    /* Categories */
    .category-grid {
        grid-template-columns: 1fr;
    }
    
    /* Products */
    .product-grid {
        grid-template-columns: 1fr;
    }
    
    /* Testimonials */
    .testimonial-card {
        padding: 15px;
    }
    
    .customer-info {
        flex-direction: column;
        text-align: center;
    }
    
    /* Navigation */
    nav .nav-links {
        flex-direction: column;
        gap: 10px;
    }
}

/* Orientation-specific adjustments */
@media (max-height: 500px) and (orientation: landscape) {
    .hero {
        height: 100vh;
    }
    
    .hero-content {
        transform: scale(0.9);
    }
}

/* High-resolution displays */
@media 
(-webkit-min-device-pixel-ratio: 2), 
(min-resolution: 192dpi) { 
    /* Use higher resolution images if available */
    .hero {
        background-image: url('image/store-bg@2x.jpg');
    }
}

/* Print styles */
@media print {
    .hero, nav, footer {
        display: none;
    }
    
    body {
        background: white;
        color: black;
        font-size: 12pt;
    }
    
    a::after {
        content: " (" attr(href) ")";
        font-size: 0.8em;
        font-weight: normal;
    }
}
</style>