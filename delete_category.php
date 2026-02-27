<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }

include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // --- PHASE 1: DELETE ALL EXHIBITS INSIDE THIS CATEGORY ---
    
    // 1A. Find all exhibits in this category to delete their physical images
    $exhibit_stmt = $conn->prepare("SELECT image_path FROM exhibits WHERE category_id = ?");
    $exhibit_stmt->bind_param("i", $id);
    $exhibit_stmt->execute();
    $exhibit_result = $exhibit_stmt->get_result();

    // Loop through every artifact and delete its image file from the uploads folder
    while ($exhibit_row = $exhibit_result->fetch_assoc()) {
        $ex_image_path = "uploads/" . $exhibit_row['image_path'];
        if (file_exists($ex_image_path) && !empty($exhibit_row['image_path'])) {
            unlink($ex_image_path); 
        }
    }

    // 1B. Delete all the exhibit records from the database
    $del_exhibits_stmt = $conn->prepare("DELETE FROM exhibits WHERE category_id = ?");
    $del_exhibits_stmt->bind_param("i", $id);
    $del_exhibits_stmt->execute();

    
    // --- PHASE 2: DELETE THE CATEGORY ITSELF ---
    
    // 2A. Find and delete the category's physical cover image file
    $cat_stmt = $conn->prepare("SELECT image_path FROM categories WHERE id = ?");
    $cat_stmt->bind_param("i", $id);
    $cat_stmt->execute();
    $cat_result = $cat_stmt->get_result();
    
    if ($cat_row = $cat_result->fetch_assoc()) {
        $cat_image_path = "uploads/" . $cat_row['image_path'];
        if (file_exists($cat_image_path) && !empty($cat_row['image_path'])) {
            unlink($cat_image_path); 
        }

        // 2B. Finally, delete the category record
        $del_cat_stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $del_cat_stmt->bind_param("i", $id);
        $del_cat_stmt->execute();
    }
}

// Redirect back to the dashboard
header("Location: admin_dashboard.php");
exit();
?>