<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "museum_db";

// Ensure mysqli extension is available and provide a friendly error if not.
if (!class_exists('mysqli')) {
    die("PHP mysqli extension is not enabled. Enable it in your php.ini (uncomment 'extension=mysqli' or 'extension=php_mysqli.dll') and restart Apache/XAMPP.");
}

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>