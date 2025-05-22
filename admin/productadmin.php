<?php
session_start();
// Database connection
$conn = new mysqli("localhost", "root", "", "sari_sari_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle adding a product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_product"])) {
    $name = $_POST["name"];
    $price = $_POST["price"];
    $category = $_POST["category"]; 
    $status = "available"; 

    // Handle image upload
    $target_dir = "uploads/"; 
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true); 
    }

    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Allowed file formats
    $allowed_types = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowed_types)) {
        $_SESSION["message"] = "Invalid image format. Only JPG, JPEG, PNG & GIF allowed.";
        header("Location: productadmin.php");
        exit();
    }

    // Move uploaded file
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO products (name, price, image_url, category, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsss", $name, $price, $target_file, $category, $status);

        if ($stmt->execute()) {
            $_SESSION["message"] = "Product added successfully!";
        } else {
            $_SESSION["message"] = "Error adding product.";
        }
        $stmt->close();
    } else {
        $_SESSION["message"] = "Error uploading image.";
    }

    header("Location: productadmin.php");
    exit();
}

// Handle exporting products to XML
if (isset($_GET["export_xml"])) {
    $category = $_GET["category"] ?? "all";
    
    // Fetch products based on category
    $query = "SELECT * FROM products";
    if ($category !== "all") {
        $query .= " WHERE category = '$category'";
    }
    $query .= " ORDER BY id DESC";
    
    $result = $conn->query($query);
    
    // Create XML
    $xml = new SimpleXMLElement('<products/>');
    
    while ($row = $result->fetch_assoc()) {
        $product = $xml->addChild('product');
        $product->addChild('id', $row['id']);
        $product->addChild('name', htmlspecialchars($row['name']));
        $product->addChild('price', $row['price']);
        $product->addChild('image_url', htmlspecialchars($row['image_url']));
        $product->addChild('category', htmlspecialchars($row['category']));
        $product->addChild('status', htmlspecialchars($row['status']));
    }
    
    // Generate filename with timestamp
    $timestamp = date('Y-m-d_H-i-s');
    $filename = "products_" . ($category !== "all" ? $category . "_" : "") . $timestamp . ".xml";
    
    // Output XML
    header('Content-type: text/xml');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo $xml->asXML();
    exit();
}

// Handle deleting a product
if (isset($_GET["delete_id"])) {
    $id = $_GET["delete_id"];

    // Get the image path
    $result = $conn->query("SELECT image_url FROM products WHERE id = $id");
    $row = $result->fetch_assoc();
    $image_path = $row["image_url"];

    // Delete image file
    if (file_exists($image_path)) {
        unlink($image_path);
    }

    // Delete product from database
    $conn->query("DELETE FROM products WHERE id = $id");
    $_SESSION["message"] = "Product deleted!";
    header("Location: productadmin.php");
    exit();
}

// Fetch all products
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
$conn->close();
?>

<!DOCTYPE html>
<html lang="tl">
<head>
    <title>Admin - Manage Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/productadmin.css" rel="stylesheet">
</head>
<?php include 'adminheader.php'; ?>
<body>
<?php include 'adminsidebar.php'; ?>
<div class="container">
    <h2>Manage Products</h2>

    <?php if (isset($_SESSION["message"])): ?>
        <div class="alert alert-info"><?php echo $_SESSION["message"]; unset($_SESSION["message"]); ?></div>
    <?php endif; ?>

    <!-- Add Product Form -->
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" required class="form-control mb-2">
        <input type="number" step="0.01" name="price" placeholder="Price" required class="form-control mb-2">
        <select name="category" required class="form-control mb-2">
            <option value="">Select Category</option>
            <option value="Frozen Meat">Frozen Meat</option>
            <option value="Bigas">Bigas</option>
            <option value="Delata">De-lata</option>
            <option value="Kape">Kape</option>
            <option value="Soft Drinks">Soft Drinks</option>
            <option value="Alak">Alak</option>
        </select>
        <input type="file" name="image" accept="image/*" required class="form-control mb-2">
        <button type="submit" name="add_product" class="btn btn-success">Add Product</button>
    </form>

    <!-- Export to XML Form -->
    <div class="mt-4 mb-4">
        <h3>Export Products</h3>
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <select name="category" class="form-control">
                    <option value="all">All Categories</option>
                    <option value="Frozen Meat">Frozen Meat</option>
                    <option value="Bigas">Bigas</option>
                    <option value="Delata">De-lata</option>
                    <option value="Kape">Kape</option>
                    <option value="Soft Drinks">Soft Drinks</option>
                    <option value="Alak">Alak</option>
                </select>
            </div>
            <div class="col-md-6">
                <button type="submit" name="export_xml" value="1" class="btn btn-primary">Export as XML</button>
            </div>
        </form>
    </div>

    <hr>

    <!-- List of Products -->
    <h3>Existing Products</h3>
    <table class="table">
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Category</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><img src="<?php echo $row['image_url']; ?>" width="50"></td>
            <td><?php echo $row['name']; ?></td>
            <td>₱<?php echo number_format($row['price'], 2); ?></td>
            <td><?php echo $row['category']; ?></td>
            <td>
                <a href="productadmin.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-danger">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>