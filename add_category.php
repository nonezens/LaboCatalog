<?php 
include 'db.php'; 
include 'header.php'; 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit(); 
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
            echo "<div class=\"message success\">Category '".htmlspecialchars($cat_name, ENT_QUOTES)."' added!</div>";
        } else {
            echo "<div class=\"message error\">Error: ".htmlspecialchars($conn->error, ENT_QUOTES)."</div>";
        }
    }
}
?>
<div class="form-container">
    <h3>Add New Museum Category</h3>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label class="form-label">Category Name</label>
            <input class="form-input" type="text" name="cat_name" required>
        </div>

        <div class="form-group">
            <label class="form-label">Category Cover Image</label>
            <input class="form-file" type="file" name="cat_image" accept="image/*" id="catImage" required>
            <img id="catPreview" class="preview-img" src="#" alt="" style="display:none;" />
        </div>

        <button type="submit" name="add_cat" class="btn-primary">Create Category</button>
    </form>
</div>

<script>
document.getElementById('catImage').addEventListener('change', function(e){
    const [file] = this.files;
    const img = document.getElementById('catPreview');
    if(file){
        img.src = URL.createObjectURL(file);
        img.style.display = 'block';
    } else {
        img.style.display = 'none';
    }
});
</script>