<?php
$category = isset($_GET['category']) ? $_GET['category'] : 'All'; // Default to 'All'
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Create a connection to the database
$conn = new mysqli("localhost", "root", "", "sari_sari_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Build the SQL query
$sql = "SELECT * FROM products WHERE name LIKE ? AND status = 'available'";

// If a category is specified, include that in the query
if ($category !== 'All') {
    $sql .= " AND category = ?";
}

// Prepare the statement
$stmt = $conn->prepare($sql);

// Bind parameters based on category
if ($category === 'All') {
    $stmt->bind_param('s', $queryLike);
} else {
    $stmt->bind_param('ss', $queryLike, $category);
}

$queryLike = "%$query%";
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Output the results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Display each product
        echo "<div class='product-card'>";
        echo "<h3>{$row['name']}</h3>";
        echo "<p>{$row['description']}</p>";
        echo "<p>₱" . number_format($row['price'], 2) . "</p>";
        echo "</div>";
    }
} else {
    echo "No products found.";
}

$conn->close();
?>
