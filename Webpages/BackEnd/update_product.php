<?php
require "../db_conn.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $shoe_name = $_POST['shoe_name'] ?? null;
    $brand_name = $_POST['brand_name'] ?? null;
    $shoe_size = $_POST['shoe_size'] ?? null;
    $shoe_count = $_POST['shoe_count'] ?? null;
    $price = $_POST['price'] ?? null;
    $shoe_image = $_FILES['shoe_image'] ?? null;

    // Validate inputs
    if (($shoe_count !== null && $shoe_count !== '' && $shoe_count < 0) || ($price !== null && $price !== '' && $price < 0)) {
        die("Shoe count and price must be non-negative.");
    }

    // Update product details
    $sql = "UPDATE shoes SET ";
    $fields = [];
    if ($shoe_name) $fields[] = "name='$shoe_name'";
    if ($brand_name) $fields[] = "brand='$brand_name'";
    if ($price !== null && $price !== '') $fields[] = "price='$price'";
    if ($fields) {
        $sql .= implode(", ", $fields) . " WHERE id='$id'";
        if ($conn->query($sql) === FALSE) {
            die("Error updating record: " . $conn->error);
        }
    }

    // Update shoe size inventory if provided
    if ($shoe_size !== null && $shoe_size !== '' && $shoe_count !== null && $shoe_count !== '') {
        $sql = "UPDATE shoe_size_inventory SET stock='$shoe_count' WHERE shoe_id='$id' AND shoe_us_size='$shoe_size'";
        if ($conn->query($sql) === FALSE) {
            die("Error updating shoe size inventory: " . $conn->error);
        }
    }

    // Update shoe image if provided
    if ($shoe_image && $shoe_image['tmp_name']) {
        $target_dir = "../images/";
        $target_file = $target_dir . basename($shoe_image["name"]);
        if (!move_uploaded_file($shoe_image["tmp_name"], $target_file)) {
            die("Error uploading file.");
        }

        $sql = "UPDATE shoe_images SET file_path='$target_file' WHERE shoe_id='$id'";
        if ($conn->query($sql) === FALSE) {
            die("Error updating image: " . $conn->error);
        }
    }

    // Redirect back to Server_Products.php
    header("Location: ../server_Products.php");
    exit();
}

$conn->close();
?>
