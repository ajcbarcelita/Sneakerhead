<?php
require "../db_conn.php";

if (isset($_GET['product_id']) && isset($_GET['shoe_size'])) {
    $productId = $_GET['product_id'];
    $shoeSize = $_GET['shoe_size'];

    $stmt = $conn->prepare("SELECT stock FROM shoe_size_inventory WHERE shoe_id = ? AND shoe_us_size = ?");
    $stmt->bind_param("id", $productId, $shoeSize);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode(['stock' => $row['stock']]);
    } else {
        echo json_encode(['stock' => 0]);
    }

    $stmt->close();
} else {
    echo json_encode(['stock' => 0]);
}

$conn->close();
