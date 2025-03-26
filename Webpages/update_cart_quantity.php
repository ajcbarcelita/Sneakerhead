<?php
require 'db_conn.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php?error=Please log in to manage your cart.");
    exit();
}

$user_id = $_SESSION['id'];
$cart_item_id = $_POST['cart_item_id'];
$action = $_POST['action'];

// Fetch the current quantity and shoe size inventory
$stmt = $conn->prepare("
    SELECT sci.quantity, sci.shoe_id, sci.shoe_us_size, i.stock
    FROM shopping_cart_items sci
    JOIN shopping_cart sc ON sci.cart_id = sc.cart_id
    JOIN shoe_size_inventory i ON sci.shoe_id = i.shoe_id AND sci.shoe_us_size = i.shoe_us_size
    WHERE sci.cart_item_id = ? AND sc.user_id = ?
");
$stmt->bind_param("ii", $cart_item_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if ($item) {
    $quantity = $item['quantity'];
    $stock = $item['stock'];

    if ($action === 'increase') {
        if ($quantity < $stock) {
            $quantity++;
        } else {
            header("Location: cart.php?error=Cannot add more. Stock limit reached.");
            exit();
        }
    } elseif ($action === 'decrease' && $quantity > 1) {
        $quantity--;
    }

    // Update the quantity in the database
    $update_stmt = $conn->prepare("
        UPDATE shopping_cart_items
        SET quantity = ?
        WHERE cart_item_id = ?
    ");
    $update_stmt->bind_param("ii", $quantity, $cart_item_id);

    if ($update_stmt->execute()) {
        header("Location: cart.php?success=Cart updated successfully.");
    } else {
        header("Location: cart.php?error=Failed to update cart.");
    }
} else {
    header("Location: cart.php?error=Item not found in your cart.");
}
exit();
