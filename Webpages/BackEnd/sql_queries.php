<?php
// Fetch products from the database
$sql = "SELECT s.id, s.name AS shoe_name, s.brand AS brand_name, si.file_path AS shoe_image, s.is_deleted, s.price 
        FROM shoes s 
        JOIN shoe_images si ON s.id = si.shoe_id";
$result = $conn->query($sql);

// Fetch shoe sizes from the database
$size_sql = "SELECT shoe_size FROM ref_us_sizes";
$size_result = $conn->query($size_sql);
$sizes = [];
while ($size_row = $size_result->fetch_assoc()) {
    $sizes[] = $size_row['shoe_size'];
}

// Fetch distinct brands from the database
$brand_sql = "SELECT DISTINCT brand FROM shoes";
$brand_result = $conn->query($brand_sql);
$brands = [];
while ($brand_row = $brand_result->fetch_assoc()) {
    $brands[] = $brand_row['brand'];
}

// Fetch product IDs and names from the database, sorted by ID
$product_sql = "SELECT id, name FROM shoes ORDER BY id";
$product_result = $conn->query($product_sql);
$products = [];
while ($product_row = $product_result->fetch_assoc()) {
    $products[] = $product_row;
}
?>
