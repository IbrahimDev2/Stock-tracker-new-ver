<?php
if (!defined('APP_INIT')) {
    exit("No direct access allowed");
}

$servername = "localhost";
$username = "root"; 
$password = "dev";
$dbname = "stock-tracker"; 


$conn = mysqli_connect($servername, $username, $password, $dbname);


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>