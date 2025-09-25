<?php
if (!defined('APP_INIT')) {
    exit("No direct access allowed");
}
// Database connection parameters
$servername = "localhost";
$username = "root"; // your database username
$password = "dev"; // your database password
$dbname = "stock-tracker"; // your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>