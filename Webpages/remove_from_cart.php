<?php
require 'db_conn.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php?error=Please log in to manage your cart.");
    exit();
}

$user_id = $_SESSION['id'];
$cart_item_id = $_POST['cart_item_id'];

// Delete the specific item from the user's cart
$stmt = $conn->prepare("
    DELETE sci
    FROM shopping_cart_items sci
    JOIN shopping_cart sc ON sci.cart_id = sc.cart_id
    WHERE sci.cart_item_id = ? AND sc.user_id = ?
");
$stmt->bind_param("ii", $cart_item_id, $user_id);

if ($stmt->execute()) {
    header("Location: cart.php?success=Item removed from your cart.");
} else {
    header("Location: cart.php?error=Failed to remove item from your cart.");
}
exit();
