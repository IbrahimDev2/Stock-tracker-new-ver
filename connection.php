<?php
if (!defined('APP_INIT')) {
    exit("No direct access allowed");
}

$servername = "server";
$username = "username"; 
$password = "password";
$dbname = "database_name"; 


$conn = mysqli_connect($servername, $username, $password, $dbname);


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>