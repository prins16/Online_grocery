<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "grocery_store";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}



?>