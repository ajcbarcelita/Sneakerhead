<?php
    include "../db_conn.php";
    session_start();

    // Check if the user is logged in and is an admin
    if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
        // Redirect non-admin users to the login page
        header("Location: ../login.php");
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

    function redirectWithMessage($type, $message, $location = "../server_promo_codes.php") {
        $_SESSION[$type] = $message;
        header("Location: $location");
        exit();
    }

    // Check if the form is submitted and the promo code is set
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['promo_code'])) {
        // Sanitize the input to prevent SQL injection
        $promo_code = mysqli_real_escape_string($conn, $_POST['promo_code']);
        
        if (!doesPromoCodeExist($conn, $promo_code)) {
            redirectWithMessage('error', 'Promo code ' . $promo_code . ' does not exist in the database.');
        }

        // Perform the soft delete
        $query = "UPDATE promo_codes SET is_deleted = 1 WHERE promo_code = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $promo_code);
    
        if ($stmt->execute()) {
            redirectWithMessage('success', "Promo code '$promo_code' has been deleted successfully.");
        } else {
            redirectWithMessage('error', "Failed to delete promo code '$promo_code'.");
        }
    
        $stmt->close();
        $conn->close();

    } else {
        redirectWithMessage('error', "Invalid request.");
    }

?>