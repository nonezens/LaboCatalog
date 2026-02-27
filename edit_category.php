<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }

include 'db.php'; 
include 'header.php';

// Fetch the existing category data
if (!isset($_GET['id'])) { header("Location: admin_dashboard.php"); exit(); }
$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();

// Handle the update form submission
if(isset($_POST['update_cat'])) {
    $name = $_POST['cat_name'];
    
    // Check if a new image was uploaded
    if (!empty($_FILES['cat_image']['name'])) {
        $imgName = $_FILES['cat_image']['name'];
        move_uploaded_file($_FILES['cat_image']['tmp_name'], "uploads/" . $imgName);
        
        // Update name AND image
        $update_stmt = $conn->prepare("UPDATE categories SET name=?, image_path=? WHERE id=?");
        $update_stmt->bind_param("ssi", $name, $imgName, $id);
    } else {
        // Update ONLY the name
        $update_stmt = $conn->prepare("UPDATE categories SET name=? WHERE id=?");
        $update_stmt->bind_param("si", $name, $id);
    }
    
    if($update_stmt->execute()) {
        header("Location: admin_dashboard.php"); 
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Category | Admin</title>
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; margin: 0; }
        .admin-card { max-width: 500px; margin: 60px auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .admin-card h2 { text-align: center; color: #2c3e50; margin-top: 0; margin-bottom: 30px; }
        .form-control { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        .btn-submit { width: 100%; padding: 14px; background: #f39c12; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-submit:hover { background: #d68910; }
    </style>
</head>
<body>

<div class="admin-card">
    <h2>Edit Department</h2>
    
    <form method="POST" enctype="multipart/form-data">
        <label style="font-weight: bold; color: #555;">Department Name</label>
        <input type="text" name="cat_name" class="form-control" value="<?php echo htmlspecialchars($category['name']); ?>" required>

        <label style="font-weight: bold; color: #555;">New Cover Image (Leave blank to keep current)</label>
        <input type="file" name="cat_image" class="form-control" accept="image/*">

        <button type="submit" name="update_cat" class="btn-submit">Update Category</button>
        <div style="text-align: center; margin-top: 15px;">
            <a href="admin_dashboard.php" style="color: #7f8c8d; text-decoration: none;">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>