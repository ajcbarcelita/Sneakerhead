<?php
require 'db_conn.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php?error=Please log in to manage your cart.");
    exit();
}

$user_id = $_SESSION['id'];

// Delete all items from the user's cart
$stmt = $conn->prepare("
    DELETE sci
    FROM shopping_cart_items sci
    JOIN shopping_cart sc ON sci.cart_id = sc.cart_id
    WHERE sc.user_id = ?
");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    header("Location: cart.php?success=All items removed from your cart.");
} else {
    header("Location: cart.php?error=Failed to remove items from your cart.");
}
exit();
