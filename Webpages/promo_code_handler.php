<?php
require_once 'db_conn.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function validatePromoCode($promo_code, $subtotal, $conn) {
    $response = ['valid' => false, 'discount' => 0, 'message' => ''];

    if (empty($promo_code)) {
        $response['message'] = 'Promo code cannot be empty.';
        return $response;
    }

    $promo_code = strtoupper(trim($promo_code)); // Normalize promo code input
    error_log("Validating promo code: $promo_code with subtotal: $subtotal"); // Debug log

    $query = "SELECT * FROM promo_codes WHERE promo_code = ? AND is_active = 1 AND is_deleted = 0";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("Failed to prepare statement: " . $conn->error); // Debug log
        $response['message'] = 'Database error.';
        return $response;
    }

    $stmt->bind_param("s", $promo_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        error_log("Promo code not found or inactive: $promo_code"); // Debug log
        $response['message'] = 'Invalid or inactive promo code.';
        return $response;
    }

    $promo = $result->fetch_assoc();
    error_log("Promo code details: " . json_encode($promo)); // Debug log

    if ($subtotal < $promo['min_purchase']) {
        $response['message'] = 'Minimum purchase amount not met for this promo code.';
        return $response;
    }

    if ($promo['discount_type'] === 'Fixed') {
        $discount = $promo['discount_value'];
    } elseif ($promo['discount_type'] === 'Percentage') {
        $discount = ($promo['discount_value'] / 100) * $subtotal;
        if (!is_null($promo['max_discount']) && $discount > $promo['max_discount']) {
            $discount = $promo['max_discount'];
        }
    } else {
        $response['message'] = 'Invalid discount type.';
        return $response;
    }

    // Store the promo code and discount in the session
    $_SESSION['promo_code'] = $promo_code;
    $_SESSION['discount'] = $discount;

    $response['valid'] = true;
    $response['discount'] = $discount;
    $response['message'] = 'Promo code applied successfully.';
    return $response;
}

function clearPromoCode() {
    unset($_SESSION['promo_code'], $_SESSION['discount']);
}
?>
