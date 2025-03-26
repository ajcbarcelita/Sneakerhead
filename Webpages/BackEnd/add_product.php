<?php
require '../db_conn.php'; // Updated file path

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shoe_name = $_POST['shoe_name'];
    $brand_name = $_POST['brand_name'];
    $shoe_size = $_POST['shoe_size'];
    $shoe_count = $_POST['shoe_count'];
    $shoe_image = $_FILES['shoe_image'];
    $price = $_POST['price']; // Assuming price is also posted

    // Validate price and shoe count
    if ($price < 0 || $shoe_count < 0) {
        die("Price and Shoe Count cannot be negative.");
    }

    // Check if the file is a valid JPEG or JPG
    $allowed_types = ['image/jpeg', 'image/jpg'];
    if (!in_array($shoe_image['type'], $allowed_types)) {
        die("Only JPEG and JPG files are allowed.");
    }

    // Ensure the target directory exists
    $target_dir = "../images/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Format the file name to be all lowercase and replace spaces with underscores
    $formatted_name = strtolower(str_replace(' ', '_', $shoe_name));
    $target_file = $target_dir . $formatted_name . '.jpg';
    if (!move_uploaded_file($shoe_image['tmp_name'], $target_file)) {
        die("Failed to upload file.");
    }

    // Insert product into the database
    $sql = "INSERT INTO shoes (name, brand, price) VALUES ('$shoe_name', '$brand_name', $price)";
    if ($conn->query($sql) === TRUE) {
        $shoe_id = $conn->insert_id;
        $sql_image = "INSERT INTO shoe_images (shoe_id, image_name, file_path) VALUES ($shoe_id, '{$formatted_name}.jpg', 'images/{$formatted_name}.jpg')";
        $conn->query($sql_image);

        // Insert shoe size and stock into the inventory
        $sql_inventory = "INSERT INTO shoe_size_inventory (shoe_id, shoe_us_size, stock) VALUES ($shoe_id, $shoe_size, $shoe_count)";
        $conn->query($sql_inventory);

        // Redirect to Server_Products.php
        header("Location: ../server_Products.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
