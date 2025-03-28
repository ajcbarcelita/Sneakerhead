<?php
    include "../db_conn.php";
    session_start();
    
    // Check if the user is logged in and is an admin
    if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
        header("Location: ../login.php");
        exit();
    }
    
    // Function to remove whitespace and escape special characters to prevent SQL injection
    function sanitizeInput($conn, $input) {
        return mysqli_real_escape_string($conn, trim($input));
    }

     // Function to validate promo code inputs
    function validatePromoCodeInputs($discount_type, $discount_value, $min_purchase) {
        if ($discount_type === "Percentage" && ($discount_value < 0 || $discount_value > 100)) {
            return "Discount value must be between 0 and 100 for Percentage type.";
        }

        if ($discount_type === "Fixed" && $discount_value > $min_purchase) {
            return "Discount value cannot exceed the minimum purchase amount for Fixed type.";
        }

        return null; // No validation errors
    }

    // Function to redirect with a session message
    function redirectWithMessage($type, $message, $location = "../server_promo_codes.php") {
        $_SESSION[$type] = $message;
        header("Location: $location");
        exit();
    }

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize inputs
        $promo_code = sanitizeInput($conn, $_POST['promo_code']);
        $discount_type = sanitizeInput($conn, $_POST['discount_type']);
        $discount_value = sanitizeInput($conn, $_POST['discount_value']);
        $min_purchase = sanitizeInput($conn, $_POST['min_purchase']);
        $is_active = sanitizeInput($conn, $_POST['is_active']);

        // Validate required fields
        if (empty($promo_code) || empty($discount_type) || empty($discount_value) || empty($min_purchase) || empty($is_active)) {
            redirectWithMessage('error', 'All fields are required.');
        }

        // Perform additional input validation for discount type, value, and min purchase
        $validation_error = validatePromoCodeInputs($discount_type, $discount_value, $min_purchase);
        if ($validation_error) {
            redirectWithMessage('error', $validation_error);
        }

        // Update the promo code
        $query = "UPDATE promo_codes SET discount_type = ?, discount_value = ?, min_purchase = ?, is_active = ? WHERE promo_code = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sdiss", $discount_type, $discount_value, $min_purchase, $is_active, $promo_code);

        if ($stmt->execute()) {
            redirectWithMessage('success', "Promo code '$promo_code' has been updated successfully.");
        } else {
            redirectWithMessage('error', "Failed to update promo code '$promo_code'.");
        }

        $stmt->close();
        $conn->close();
    } else {
        redirectWithMessage('error', "Invalid request.");
    }
?>