<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php"); exit(); 
}

include 'db.php';
include 'header.php';
$message = "";
if(isset($_POST['add_cat'])) {
    $cat_name = $_POST['cat_name'];
    $imgName = $_FILES['cat_image']['name'];
    $tmpName = $_FILES['cat_image']['tmp_name'];
    
    if(move_uploaded_file($tmpName, "uploads/" . $imgName)) {
        $stmt = $conn->prepare("INSERT INTO categories (name, image_path) VALUES (?, ?)");
        $stmt->bind_param("ss", $cat_name, $imgName);
        if($stmt->execute()) {
            $message = "<div class='alert success'>Category '$cat_name' created successfully!</div>";
        } else {
            $message = "<div class='alert error'>Error: " . $conn->error . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Category | Admin</title>
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .admin-card { max-width: 500px; margin: 60px auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .admin-card h2 { text-align: center; color: #2c3e50; margin-top: 0; margin-bottom: 30px; font-size: 1.8rem; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-size: 1rem; transition: border-color 0.3s; }
        .form-control:focus { border-color: #c5a059; outline: none; box-shadow: 0 0 5px rgba(197, 160, 89, 0.3); }
        
        .btn-submit { width: 100%; padding: 14px; background: #2c3e50; color: white; border: none; border-radius: 6px; font-size: 1.1rem; font-weight: bold; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        .btn-submit:hover { background: #1a252f; transform: translateY(-2px); }
        
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-weight: bold; }
        .success { background: #e8f8f5; color: #1abc9c; border: 1px solid #1abc9c; }
        .error { background: #fdedec; color: #e74c3c; border: 1px solid #e74c3c; }
    </style>
</head>
<body>

<div class="admin-card">
    <h2>🏛️ New Department</h2>
    
    <?php echo $message; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Department Name</label>
            <input type="text" name="cat_name" class="form-control" placeholder="e.g., Ancient Egypt" required>
        </div>

        <div class="form-group">
            <label>Cover Image</label>
            <input type="file" name="cat_image" class="form-control" required>
        </div>

        <button type="submit" name="add_cat" class="btn-submit">+ Create Category</button>
    </form>
</div>

</body>
</html>