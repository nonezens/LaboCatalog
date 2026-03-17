<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "museum_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    error_log("DB Connection failed: " . $conn->connect_error); // Log instead of die
    die("Connection failed. Please try again later.");
}
$conn->set_charset("utf8mb4");
?>

