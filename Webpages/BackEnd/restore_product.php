<?php
require '../db_conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    // Update the is_deleted flag to 0 (restore the product)
    $sql = "UPDATE shoes SET is_deleted = 0 WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        // Redirect to Server_Products.php
        header("Location: ../Server_Products.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
