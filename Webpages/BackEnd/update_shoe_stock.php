<?php
session_start();
require "../db_conn.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $shoe_size = $_POST['shoe_size'];
    $shoe_count = $_POST['shoe_count'];

    if (!empty($id) && !empty($shoe_size) && is_numeric($shoe_count) && $shoe_count >= 0) {
        $stmt = $conn->prepare("UPDATE shoe_size_inventory SET stock = ? WHERE shoe_id = ? AND shoe_us_size = ?");
        if (!$stmt) {
            $_SESSION['error'] = "Failed to prepare the statement: " . $conn->error;
            header("Location: ../server_product.php");
            exit();
        }

        $stmt->bind_param("iis", $shoe_count, $id, $shoe_size);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Shoe stock updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update shoe stock. Please try again.";
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = "Invalid input. Please provide valid data.";
    }
}

header("Location: ../server_product.php");
exit();
