<?php
    // Database credentials
    $servername = "localhost"; 
    $username = "root"; 
    $db_pw = ""; 
    $db_name = "sneakerhead"; 

    $conn = new mysqli($servername, $username, $db_pw, $db_name);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>