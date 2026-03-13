<?php
$servername = "localhost";
$username = "root";       // Change this from "root"
$password = "";    // Change this from "" (blank)
$dbname = "museum_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>