<?php
include 'db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Verify ID is provided
if (!isset($_GET['id']) && !isset($_POST['id'])) {
    header("Location: exhibits.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : (int)$_POST['id'];

// Fetch the exhibit to get image path
$stmt = $conn->prepare("SELECT image_path FROM exhibits WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: exhibits.php");
    exit();
}

$exhibit = $result->fetch_assoc();
$image_path = $exhibit['image_path'];

// Delete from database
$stmt = $conn->prepare("DELETE FROM exhibits WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    // Delete image file if it exists
    if ($image_path !== '' && file_exists(__DIR__ . '/uploads/' . $image_path)) {
        @unlink(__DIR__ . '/uploads/' . $image_path);
    }
    
    // Redirect with success message
    header("Location: exhibits.php?deleted=1");
    exit();
} else {
    // Redirect with error message
    header("Location: exhibits.php?error=1");
    exit();
}
?>
