<?php
session_start();
require_once 'db_conn.php';
require_once 'promo_code_handler.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Start output buffering to prevent unexpected output
        ob_start();

        if (isset($_POST['apply_promo']) && $_POST['apply_promo'] == '1') {
            $promo_code = isset($_POST['promo_code']) ? trim($_POST['promo_code']) : '';
            $subtotal = isset($_POST['subtotal']) ? floatval($_POST['subtotal']) : 0;

            $response = validatePromoCode($promo_code, $subtotal, $conn);

            if ($response['valid']) {
                $_SESSION['promo_code'] = $promo_code;
                $_SESSION['discount'] = $response['discount'];
                $_SESSION['promo_message'] = $response['message'];
            } else {
                // Clear any previously applied promo code if the new one is invalid
                clearPromoCode();
                $_SESSION['promo_error'] = $response['message'];
            }

            error_log("Promo code response: " . json_encode($response)); // Debug log

            // Clear the output buffer and return JSON response
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    } catch (Exception $e) {
        error_log("Error applying promo code: " . $e->getMessage()); // Log the error

        // Clear the output buffer and return error JSON response
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['valid' => false, 'message' => 'An internal error occurred. Please try again later.']);
        exit();
    }
}
?>