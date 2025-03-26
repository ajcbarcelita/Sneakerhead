<?php
session_start();
require "db_conn.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['id'])) {
        error_log("Session ID is not set. Redirecting to login.");
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['id'];
    $cart_id = $_SESSION['cart_id'] ?? null; // Use cart_id from session
    $total_amount = $_SESSION['total_amount'] ?? 0; // Use total_amount from session

    // Debugging: Log the total amount and cart_id
    error_log("User ID: $user_id");
    error_log("Cart ID from Session: " . ($cart_id ?? 'NULL'));
    error_log("Total Amount from Session: $total_amount");

    $promo_code = $_SESSION['promo_code'] ?? null; // Use promo_code from session

    // If cart_id is invalid, retrieve it using the user's email
    if (!$cart_id) {
        $user_email = $_SESSION['email'] ?? null; // Ensure email is stored in the session
        error_log("Cart ID is invalid. Attempting to retrieve using email: " . ($user_email ?? 'NULL'));
        if ($user_email) {
            $cart_query = "SELECT sc.cart_id FROM shopping_cart sc 
                           JOIN users u ON sc.user_id = u.id 
                           WHERE u.email = ?";
            $stmt = $conn->prepare($cart_query);
            if ($stmt) {
                $stmt->bind_param("s", $user_email);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $cart = $result->fetch_assoc();
                    $cart_id = $cart['cart_id'];
                    $_SESSION['cart_id'] = $cart_id; // Update session with valid cart_id
                    error_log("Cart ID retrieved successfully: $cart_id");
                } else {
                    error_log("No cart found for email: $user_email");
                }
            } else {
                error_log("Failed to prepare cart query: " . $conn->error);
            }
        }
        if (!$cart_id) {
            error_log("Cart ID is still invalid after attempting retrieval.");
            $_SESSION['checkoutErrors']['system'] = "Invalid cart.";
            header("Location: index.php"); // Redirect to index.php
            exit();
        }
    }

    if ($total_amount <= 0) {
        error_log("Total amount is invalid: $total_amount");
        $_SESSION['checkoutErrors']['system'] = "Invalid total amount.";
        header("Location: index.php"); // Redirect to index.php
        exit();
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert order details
        $order_query = "INSERT INTO orders (user_id, total_price, created_at, promo_code) VALUES (?, ?, NOW(), ?)"; // Matches SQL schema
        $stmt = $conn->prepare($order_query);
        if (!$stmt) {
            throw new Exception("Prepare failed for order_query: " . $conn->error);
        }
        $stmt->bind_param("ids", $user_id, $total_amount, $promo_code); // total_amount maps to total_price
        if (!$stmt->execute()) {
            throw new Exception("Execute failed for order_query: " . $stmt->error);
        }
        $order_id = $stmt->insert_id;
        error_log("Order created successfully. Order ID: $order_id");

        // Copy cart items to order items
        $order_items_query = "INSERT INTO order_items (order_id, shoe_id, shoe_size, quantity, price_at_purchase)
                              SELECT ?, shoe_id, shoe_us_size, quantity, price_at_addition
                              FROM shopping_cart_items
                              WHERE cart_id = ?"; // Matches SQL schema
        $stmt = $conn->prepare($order_items_query);
        if (!$stmt) {
            throw new Exception("Prepare failed for order_items_query: " . $conn->error);
        }
        $stmt->bind_param("ii", $order_id, $cart_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed for order_items_query: " . $stmt->error);
        }
        error_log("Order items copied successfully for Order ID: $order_id");

        // Clear shopping cart
        $clear_cart_query = "DELETE FROM shopping_cart_items WHERE cart_id = ?";
        $stmt = $conn->prepare($clear_cart_query);
        if (!$stmt) {
            throw new Exception("Prepare failed for clear_cart_query: " . $conn->error);
        }
        $stmt->bind_param("i", $cart_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed for clear_cart_query: " . $stmt->error);
        }
        error_log("Shopping cart cleared successfully for Cart ID: $cart_id");

        // Commit transaction
        $conn->commit();
        error_log("Transaction committed successfully.");

        // Set success message
        $_SESSION['order_success'] = true;
        $_SESSION['order_id'] = $order_id;

        header("Location: Checkout.php"); 
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        error_log("Transaction failed: " . $e->getMessage());
        $_SESSION['checkoutErrors']['system'] = "An error occurred while processing your order. Please try again.";
        header("Location: Checkout.php");
        exit();
    }
} else {
    error_log("Invalid request method. Redirecting to Checkout.");
    header("Location: Checkout.php");
    exit();
}
?>
