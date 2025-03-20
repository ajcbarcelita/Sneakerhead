<?php
require 'database_conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shoe_name = $_POST['shoe_name'];
    $brand_name = $_POST['brand_name'];
    $shoe_size = $_POST['shoe_size'];
    $shoe_count = $_POST['shoe_count'];
    $shoe_image = $_FILES['shoe_image'];
    $price = $_POST['price']; // Assuming price is also posted

    // Check if the file is a valid JPEG
    $allowed_types = ['image/jpeg'];
    if (!in_array($shoe_image['type'], $allowed_types)) {
        die("Only JPEG files are allowed.");
    }

    // Move the uploaded file to the desired directory
    $target_dir = "images/";
    $target_file = $target_dir . basename($shoe_image['name']);
    if (!move_uploaded_file($shoe_image['tmp_name'], $target_file)) {
        die("Failed to upload file.");
    }

    // Insert product into the database
    $sql = "INSERT INTO shoes (name, brand, price) VALUES ('$shoe_name', '$brand_name', $price)";
    if ($conn->query($sql) === TRUE) {
        $shoe_id = $conn->insert_id;
        $sql_image = "INSERT INTO shoe_images (shoe_id, image_name, file_path) VALUES ($shoe_id, '{$shoe_image['name']}', '$target_file')";
        $conn->query($sql_image);

        // Insert shoe size and stock into the inventory
        $sql_inventory = "INSERT INTO shoe_size_inventory (shoe_id, shoe_us_size, stock) VALUES ($shoe_id, $shoe_size, $shoe_count)";
        $conn->query($sql_inventory);

        echo "Product added successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
