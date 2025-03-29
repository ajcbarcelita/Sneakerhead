<?php
    require 'db_conn.php';
    session_start();
    $user = $_SESSION['id'];

    // POST variables
    $cart = $_POST['cart'];
    $shoe = $_POST['shoe'];
    $size = $_POST['size'];
    $qty = $_POST['qty'];
    $price = $_POST['price'];

    // Do not proceed if the user is not logged in!
    if ($user) {
        $add = $conn->query("
            INSERT INTO shopping_cart_items (cart_id, shoe_id, shoe_us_size, quantity, price_at_addition, added_at)
            VALUES ('".$cart."','".$shoe."','".$size."','".$qty."','".$price."', current_timestamp())
        ");

        if ($add)
            echo "OK";
    }
?>