<?php
session_start();
require_once 'db_conn.php';

// Initialize variables
$errors = [];

// Handle promo code validation via AJAX
if (isset($_GET['action']) && $_GET['action'] == 'validate_promo') {
    $promo_code = isset($_POST['promo_code']) ? trim($_POST['promo_code']) : '';
    $subtotal = isset($_POST['subtotal']) ? floatval($_POST['subtotal']) : 0;

    // Validate promo code
    $response = validatePromoCode($promo_code, $subtotal, $conn);

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Handle the checkout form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user ID from session
    if (!isset($_SESSION['id'])) {
        $errors['system'] = 'User not authenticated. Please login.';
        $_SESSION['checkoutErrors'] = $errors;
        header('Location: Checkout.php');
        exit();
    }

    $user_id = $_SESSION['id'];
    $cart_id = isset($_POST['cart_id']) ? intval($_POST['cart_id']) : 0;

    // Validate cart ID
    if (!$cart_id) {
        $errors['system'] = 'Invalid cart. Please try again.';
        $_SESSION['checkoutErrors'] = $errors;
        header('Location: Checkout.php');
        exit();
    }

    // Validate required fields
    $required_fields = [
        'email' => 'Email address',
        'phone' => 'Phone number',
        'firstName' => 'First name',
        'lastName' => 'Last name',
        'address' => 'Address',
        'city' => 'City',
        'province' => 'Province'
    ];

    foreach ($required_fields as $field => $label) {
        if (empty($_POST[$field])) {
            $errors[$field] = "$label is required";
        }
    }

    // Validate email format
    if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    // If there are validation errors, redirect back to the checkout page
    if (!empty($errors)) {
        $_SESSION['checkoutErrors'] = $errors;
        header('Location: Checkout.php');
        exit();
    }

    // Get billing and shipping details
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $firstName = $_POST['firstName'];
    $middleName = isset($_POST['middleName']) ? $_POST['middleName'] : '';
    $lastName = $_POST['lastName'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $country = $_POST['country'];
    $delivery_type = isset($_POST['delivery']) ? $_POST['delivery'] : 'ship';
    $payment_method = isset($_POST['payment']) ? $_POST['payment'] : 'bank';

    // Get pricing details
    $subtotal = isset($_POST['subtotal']) ? floatval($_POST['subtotal']) : 0;
    $shipping = isset($_POST['shipping']) ? floatval($_POST['shipping']) : 0;
    $promo_code = isset($_POST['promo_code']) ? trim($_POST['promo_code']) : null;
    $discount_amount = isset($_POST['discount_amount']) ? floatval($_POST['discount_amount']) : 0;

    // Calculate total
    $total = $subtotal + $shipping - $discount_amount;

    // Get cart items
    $cart_query = "SELECT sci.shoe_id, sci.shoe_us_size, sci.quantity, sci.price_at_addition
                   FROM shopping_cart_items sci
                   WHERE sci.cart_id = ?";
    $stmt = $conn->prepare($cart_query);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $cart_result = $stmt->get_result();

    $cart_items = [];
    while ($item = $cart_result->fetch_assoc()) {
        $cart_items[] = $item;
    }

    // If cart is empty, redirect back with error
    if (empty($cart_items)) {
        $errors['system'] = 'Your cart is empty. Please add items before checkout.';
        $_SESSION['checkoutErrors'] = $errors;
        header('Location: Checkout.php');
        exit();
    }

    // Verify inventory before processing order
    foreach ($cart_items as $item) {
        $inventory_query = "SELECT stock FROM shoe_size_inventory 
                           WHERE shoe_id = ? AND shoe_us_size = ?";
        $stmt = $conn->prepare($inventory_query);
        $stmt->bind_param("id", $item['shoe_id'], $item['shoe_us_size']);
        $stmt->execute();
        $inventory_result = $stmt->get_result();
        $inventory = $inventory_result->fetch_assoc();

        if (!$inventory || $inventory['stock'] < $item['quantity']) {
            $errors['system'] = 'Some items in your cart are no longer available in the requested quantity.';
            $_SESSION['checkoutErrors'] = $errors;
            header('Location: Checkout.php');
            exit();
        }
    }

    // If a promo code was entered, validate it again
    if ($promo_code) {
        $promo_response = validatePromoCode($promo_code, $subtotal, $conn);
        if (!$promo_response['valid']) {
            $errors['system'] = 'Invalid promo code: ' . $promo_response['message'];
            $_SESSION['checkoutErrors'] = $errors;
            header('Location: Checkout.php');
            exit();
        }

        // Update discount amount and total
        $discount_amount = $promo_response['discount'];
        $total = $subtotal + $shipping - $discount_amount;
    }

    // Start transaction for order creation
    $conn->begin_transaction();

    try {
        // Create order record
        $order_datetime = date('Y-m-d H:i:s');
        $order_query = "INSERT INTO orders (user_id, order_datetime, total_price, promo_code, 
                      shipping_address, shipping_city, shipping_province, shipping_country, 
                      payment_method, shipping_fee, discount_amount) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($order_query);
        $stmt->bind_param(
            "isdssssssdds",
            $user_id,
            $order_datetime,
            $total,
            $promo_code,
            $address,
            $city,
            $province,
            $country,
            $payment_method,
            $shipping,
            $discount_amount
        );
        $stmt->execute();

        // Get the new order ID
        $order_id = $conn->insert_id;

        // Now create order items
        foreach ($cart_items as $item) {
            // Insert order item
            $item_query = "INSERT INTO order_items (order_id, shoe_id, shoe_size, quantity, price_at_purchase) 
                          VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($item_query);
            $stmt->bind_param(
                "iidid",
                $order_id,
                $item['shoe_id'],
                $item['shoe_us_size'],
                $item['quantity'],
                $item['price_at_addition']
            );
            $stmt->execute();

            // Update inventory
            $update_inventory = "UPDATE shoe_size_inventory 
                                SET stock = stock - ? 
                                WHERE shoe_id = ? AND shoe_us_size = ?";
            $stmt = $conn->prepare($update_inventory);
            $stmt->bind_param("iid", $item['quantity'], $item['shoe_id'], $item['shoe_us_size']);
            $stmt->execute();
        }

        // Clear shopping cart after successful order
        $clear_cart = "DELETE FROM shopping_cart_items WHERE cart_id = ?";
        $stmt = $conn->prepare($clear_cart);
        $stmt->bind_param("i", $cart_id);
        $stmt->execute();

        // Commit transaction
        $conn->commit();

        // Set success message
        $_SESSION['order_success'] = true;
        $_SESSION['order_id'] = $order_id;

        // Redirect to checkout page with success message
        header("Location: Checkout.php");
        exit();

    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        $errors['system'] = "Order processing error: " . $e->getMessage();
        $_SESSION['checkoutErrors'] = $errors;
        header("Location: Checkout.php");
        exit();
    }
}

// Function to validate promo code
function validatePromoCode($promo_code, $subtotal, $conn)
{
    if (empty($promo_code)) {
        return [
            'valid' => false,
            'message' => 'Please enter a promo code'
        ];
    }

    // Query to get promo code details
    $promo_query = "SELECT * FROM promo_codes WHERE promo_code = ? AND is_active = 1 AND is_deleted = 0";
    $stmt = $conn->prepare($promo_query);
    $stmt->bind_param("s", $promo_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return [
            'valid' => false,
            'message' => 'Invalid promo code'
        ];
    }

    $promo = $result->fetch_assoc();

    // Check minimum purchase requirement
    if ($subtotal < $promo['min_purchase']) {
        return [
            'valid' => false,
            'message' => 'This promo code requires a minimum purchase of ₱' . number_format($promo['min_purchase'], 2)
        ];
    }

    // Calculate discount
    $discount = 0;
    if ($promo['discount_type'] === 'Percentage') {
        $discount = $subtotal * ($promo['discount_value'] / 100);
    } else { // Fixed amount
        $discount = $promo['discount_value'];
    }

    // Standard shipping fee
    $shipping = 100.00;

    // Calculate new total
    $new_total = $subtotal + $shipping - $discount;

    return [
        'valid' => true,
        'message' => 'Promo code applied successfully!',
        'discount' => $discount,
        'new_total' => $new_total
    ];
}
?>