<?php
require '../db_conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']); // Ensure $id is an integer

    // Update the is_deleted flag to 0 (restore the product)
    $sql = "UPDATE shoes SET is_deleted = 0 WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        // Redirect to server_product.php
        header("Location: ../server_product.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
