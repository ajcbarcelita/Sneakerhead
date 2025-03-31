<?php
session_start();
require "../db_conn.php";

// Function to log messages to the PHP error log
function log_message($message) {
    $timestamp = date("Y-m-d H:i:s");
    error_log("[$timestamp] $message");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $shoe_size = $_POST['shoe_size'];
    $shoe_count = $_POST['shoe_count'];

    log_message("Received POST request with id: $id, shoe_size: $shoe_size, shoe_count: $shoe_count");

    if (!empty($id) && !empty($shoe_size) && is_numeric($shoe_count) && $shoe_count >= 0) {
        // Check if the product and size exist in the inventory
        $sql_check = "SELECT * FROM shoe_size_inventory WHERE shoe_id = ? AND shoe_us_size = ?";
        $stmt_check = $conn->prepare($sql_check);

        if (!$stmt_check) {
            log_message("Failed to prepare SQL check statement: " . $conn->error);
            $_SESSION['error'] = "Database error. Please try again.";
            header("Location: ../server_product.php");
            exit();
        }

        $stmt_check->bind_param("is", $id, $shoe_size);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            log_message("Product and size found in inventory. Proceeding to update stock.");

            // Update stock if the entry exists
            $stmt = $conn->prepare("UPDATE shoe_size_inventory SET stock = ? WHERE shoe_id = ? AND shoe_us_size = ?");
            if (!$stmt) {
                log_message("Failed to prepare SQL update statement: " . $conn->error);
                $_SESSION['error'] = "Database error. Please try again.";
                header("Location: ../server_product.php");
                exit();
            }

            $stmt->bind_param("iis", $shoe_count, $id, $shoe_size);
            if ($stmt->execute()) {
                log_message("Stock updated successfully for shoe_id: $id, shoe_size: $shoe_size, new stock: $shoe_count");
                $_SESSION['success'] = "Shoe stock updated successfully!";
            } else {
                log_message("Failed to execute stock update: " . $stmt->error);
                $_SESSION['error'] = "Failed to update shoe stock. Please try again.";
            }
            $stmt->close();
        } else {
            log_message("Product or size not found in inventory for shoe_id: $id, shoe_size: $shoe_size. Inserting new record.");

            // Insert new stock entry if it doesn't exist
            $stmt_insert = $conn->prepare("INSERT INTO shoe_size_inventory (shoe_id, shoe_us_size, stock) VALUES (?, ?, ?)");
            if (!$stmt_insert) {
                log_message("Failed to prepare SQL insert statement: " . $conn->error);
                $_SESSION['error'] = "Database error. Please try again.";
                header("Location: ../server_product.php");
                exit();
            }

            $stmt_insert->bind_param("isi", $id, $shoe_size, $shoe_count);
            if ($stmt_insert->execute()) {
                log_message("New stock inserted successfully for shoe_id: $id, shoe_size: $shoe_size, stock: $shoe_count");
                $_SESSION['success'] = "Shoe stock added successfully!";
            } else {
                log_message("Failed to execute stock insert: " . $stmt_insert->error);
                $_SESSION['error'] = "Failed to add shoe stock. Please try again.";
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    } else {
        log_message("Invalid input received. id: $id, shoe_size: $shoe_size, shoe_count: $shoe_count");
        $_SESSION['error'] = "Invalid input. Please provide valid data.";
    }
}

header("Location: ../server_product.php");
exit();
