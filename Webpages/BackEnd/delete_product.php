<?php
session_start();
require '../db_conn.php'; // Updated file path

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    $sql = "UPDATE shoes SET is_deleted = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Product deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete product. Please try again.";
    }

    $stmt->close();
}

$conn->close();
header("Location: ../server_product.php"); // Updated file path
exit();
?>
