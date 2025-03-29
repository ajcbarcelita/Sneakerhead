<?php
    require 'db_conn.php';
    session_start();
    $user = $_SESSION['id'];

    // POST variables
    $shoe = $_POST['shoe'];
    $rating = $_POST['rating'];
    $text = $_POST['text'];

    // Do not proceed if the user is not logged in!
    if ($user) {
        if ($conn->query("SELECT COUNT(*) FROM shoe_reviews WHERE user_id='".$user."' AND shoe_id='".$shoe."'")->fetch_array()[0]) {
            $id = $conn->query("SELECT review_id FROM shoe_reviews WHERE user_id='".$user."' AND shoe_id='".$shoe."'")->fetch_array()[0];
            $add = $conn->query("UPDATE shoe_reviews SET rating = '".$rating."', review_text='".$text."', updated_at=current_timestamp() WHERE review_id='".$id."'");
        }
        else
            $add = $conn->query("INSERT INTO shoe_reviews (user_id, shoe_id, rating, review_text, created_at, updated_at) VALUES ('".$user."','".$shoe."','".$rating."','".$text."', current_timestamp(), current_timestamp())");

        if ($add)
            echo "OK";
    }
?>