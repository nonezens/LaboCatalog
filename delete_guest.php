<?php
// 1. Security Check: Only Admins can delete guests
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php"); exit(); 
}

// 2. Database Connection
include 'db.php';

// 3. Check if an ID was passed in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 4. Delete the guest from the database
    $stmt = $conn->prepare("DELETE FROM guests WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// 5. Redirect back to the dashboard
header("Location: admin_dashboard.php");
exit();
?>