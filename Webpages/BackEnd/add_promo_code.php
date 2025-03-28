<?php
    include "../db_conn.php";
    session_start();

    // Check if the user is logged in and is an admin
    if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
        // Redirect non-admin users to the login page
        header("Location: ../login.php");
        exit();
    }

    // Function to sanitize input
    function sanitizeInput($conn, $input) {
        return mysqli_real_escape_string($conn, trim($input));
    }

    function validatePromoCodeInputs($discount_type, $discount_value, $min_purchase) {
        if ($discount_type === "Percentage" && ($discount_value < 0 || $discount_value > 100)) {
            return "Discount value must be between 0 and 100 for Percentage type.";
        }

        if ($discount_type === "Fixed") {
            // If minimum value is greater than 0, check if the discount value is greater than the minimum purchase amount
            if ($min_purchase > 0 && $discount_value > $min_purchase) {
                return "Discount value cannot exceed the minimum purchase amount for Fixed type.";
            }

            // Case for when minimum purchase is 0
            if ($discount_value <= 0) {
                return "Discount value must be greater than 0 for Fixed type.";
            }
        }

        return null; // No validation errors
    }
    function redirectWithMessage($type, $message, $location = "../server_promo_codes.php") {
        $_SESSION[$type] = $message;
        header("Location: $location");
        exit();
    }

    function doesPromoCodeExist($conn, $promoCode) {
        $check_query = "SELECT * FROM promo_codes WHERE promo_code = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("s", $promoCode);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stmt->close();
            return true;
        }
        $stmt->close();
        return false;
    }    

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get data from submitted form
        $promo_code = sanitizeInput($conn, $_POST['promo_code']);
        $discount_type = sanitizeInput($conn, $_POST['discount_type']);
        $discount_value = sanitizeInput($conn, $_POST['discount_value']);
        $min_purchase = sanitizeInput($conn, $_POST['min_purchase']);
        $is_active = sanitizeInput($conn, $_POST['is_active']);

        if (empty($promo_code) || empty($discount_type) || empty($discount_value) || empty($min_purchase) || empty($is_active)) {
            redirectWithMessage('error', 'All fields are required.');
        }

        // Perform additional input validation for discount type, value, and min purchase
        $validation_error = validatePromoCodeInputs($discount_type, $discount_value, $min_purchase);
        if ($validation_error) {
            redirectWithMessage('error', $validation_error);
        }

        if (doesPromoCodeExist($conn, $promo_code)) {
            var_dump("Promo code exists: " . $promo_code);
            redirectWithMessage('error', 'Promo code ' . $promo_code . ' already exists.');
        }

        // Prepare and bind the SQL statement
        $insert_query = "INSERT INTO promo_codes (promo_code, discount_type, discount_value, min_purchase, is_active) 
                        VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ssdii", $promo_code, $discount_type, $discount_value, $min_purchase, $is_active);

        if ($stmt->execute() === false) {
            redirectWithMessage('error', 'Error adding promo code: ' . $stmt->error);
        } else {
            redirectWithMessage('success', 'Promo code added successfully!');
        }
        $stmt->close();
        $conn->close();
    }