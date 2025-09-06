<?php
// Database connection parameters
$servername = "localhost";
$username = "root"; // your database username
$password = ""; // your database password
$dbname = "stock-tracker"; // your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname,3307);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>