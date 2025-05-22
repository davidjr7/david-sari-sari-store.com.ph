<!DOCTYPE html>
<html>
<head>
    <title>Product List</title>
    <style>
        .product-box {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px;
            display: inline-block;
            width: 200px;
        }
    </style>
</head>
<body>

<?php
$xml_file = "product_list.xml";

if (file_exists($xml_file)) {
    $product_array = simplexml_load_file($xml_file);

    if ($product_array === false) {
        echo "<p>Error: Failed to load XML data.</p>";
    } else {
        foreach ($product_array->product as $product) {
            $category_class = strtolower($product->category);

            echo '<div class="product-box ' . $category_class . '">';
            echo 'Name: ' . $product->name . '<br>';
            echo 'Price: ₱' . $product->price . '<br>';
            echo 'Category: ' . $product->category . '<br>';
            echo 'Description: ' . $product->description . '<br>';
            echo '<img src="uploads/' . $product->image . '" width="100"><br>';
            echo '</div>';
        }
    }
} else {
    echo "<p>Error: XML file '$xml_file' not found.</p>";
}
?>

</body>
</html>
