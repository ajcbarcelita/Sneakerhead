<?php
require "../db_conn.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $shoe_name = $_POST['shoe_name'];
    $brand_name = $_POST['brand_name'];
    $shoe_size = $_POST['shoe_size'];
    $shoe_count = $_POST['shoe_count'];
    $price = $_POST['price'];
    $shoe_image = $_FILES['shoe_image'];

    // Validate inputs
    if ($shoe_count < 0 || $price < 0) {
        die("Shoe count and price must be non-negative.");
    }

    // Update product details
    $sql = "UPDATE shoes SET ";
    $fields = [];
    if ($shoe_name) $fields[] = "name='$shoe_name'";
    if ($brand_name) $fields[] = "brand='$brand_name'";
    if ($shoe_size) $fields[] = "size='$shoe_size'";
    if ($shoe_count !== null) $fields[] = "count='$shoe_count'";
    if ($price !== null) $fields[] = "price='$price'";
    if ($fields) {
        $sql .= implode(", ", $fields) . " WHERE id='$id'";
        $conn->query($sql);
    }

    // Update shoe image if provided
    if ($shoe_image && $shoe_image['tmp_name']) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($shoe_image["name"]);
        move_uploaded_file($shoe_image["tmp_name"], $target_file);

        $sql = "UPDATE shoe_images SET file_path='$target_file' WHERE shoe_id='$id'";
        $conn->query($sql);
    }

    header("Location: ../Server_Products.php");
    exit();
}

$conn->close();
?>
