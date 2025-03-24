<?php
require "../db_conn.php";

$product_id = $_GET['id'] ?? '';

if ($product_id) {
    $sql = "SELECT s.name AS shoe_name, s.brand AS brand_name, s.price 
            FROM shoes s 
            WHERE s.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    echo json_encode($product);
}

$conn->close();
?>
