<?php 
include 'db.php'; 
include 'header.php'; 
session_start();

// Check if the admin session is NOT set
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect them to the login page
    header("Location: login.php");
    exit(); // Stop running the rest of the code
}
if(isset($_POST['add_cat'])) {
    $cat_name = $_POST['cat_name'];
    
    // Image Handling
    $imgName = $_FILES['cat_image']['name'];
    $tmpName = $_FILES['cat_image']['tmp_name'];
    $folder = "uploads/" . $imgName;

    if(move_uploaded_file($tmpName, $folder)) {
        $stmt = $conn->prepare("INSERT INTO categories (name, image_path) VALUES (?, ?)");
        $stmt->bind_param("ss", $cat_name, $imgName);
        
        if($stmt->execute()) {
            echo "<p style='color:green; text-align:center;'>Category '$cat_name' added!</p>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>

<div style="max-width: 400px; margin: 40px auto; padding: 20px; border: 1px solid #ddd; font-family: sans-serif;">
    <h3>Add New Museum Category</h3>
    <form method="POST" enctype="multipart/form-data">
        <label>Category Name:</label>
        <input type="text" name="cat_name" style="width:100%; margin-bottom:15px; padding:8px;" required>

        <label>Category Cover Image:</label>
        <input type="file" name="cat_image" style="width:100%; margin-bottom:15px;" required>

        <button type="submit" name="add_cat" style="width:100%; padding:10px; background:#2c3e50; color:white; border:none; cursor:pointer;">
            Create Category
        </button>
    </form>
</div>