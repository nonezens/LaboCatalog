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
    header("Location: categories.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : (int)$_POST['id'];

// Fetch the category to get image path
$stmt = $conn->prepare("SELECT image_path FROM categories WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: categories.php");
    exit();
}

$category = $result->fetch_assoc();
$image_path = $category['image_path'];

// Check if there are exhibits in this category
$stmt_check = $conn->prepare("SELECT COUNT(*) as count FROM exhibits WHERE category_id = ?");
$stmt_check->bind_param('i', $id);
$stmt_check->execute();
$check_result = $stmt_check->get_result();
$check_data = $check_result->fetch_assoc();

if ($check_data['count'] > 0) {
    // Cannot delete category with exhibits
    header("Location: categories.php?error=has_exhibits");
    exit();
}

// Delete from database
$stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    // Delete image file if it exists
    if ($image_path !== '' && file_exists(__DIR__ . '/uploads/' . $image_path)) {
        @unlink(__DIR__ . '/uploads/' . $image_path);
    }
    
    // Redirect with success message
    header("Location: categories.php?deleted=1");
    exit();
} else {
    // Redirect with error message
    header("Location: categories.php?error=1");
    exit();
}
?>
