<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php"); exit(); 
}

include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. Find the image name so we can delete the actual file
    $stmt = $conn->prepare("SELECT image_path FROM exhibits WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $imagePath = "uploads/" . $row['image_path'];
        if (file_exists($imagePath)) {
            unlink($imagePath); // This deletes the physical image file
        }

        // 2. Delete the record from the database
        $del_stmt = $conn->prepare("DELETE FROM exhibits WHERE id = ?");
        $del_stmt->bind_param("i", $id);
        $del_stmt->execute();
    }
}

// Redirect back to the dashboard
header("Location: admin_dashboard.php");
exit();
?>