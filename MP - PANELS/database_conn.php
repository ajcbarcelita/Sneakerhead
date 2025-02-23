<?php

$username = "root";
$password = "";
$database = "tblstudent";
$conn = new mysqli("localhost", $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>