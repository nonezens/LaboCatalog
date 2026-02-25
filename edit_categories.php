<?php
include 'db.php';
include 'header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    header("Location: categories.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : (int)$_POST['id'];

// Handle update
if (isset($_POST['submit'])) {
    $cat_name = $_POST['cat_name'];
    $oldImage = $_POST['old_image'] ?? '';

    $imageName = $oldImage;
    if (!empty($_FILES['cat_image']) && isset($_FILES['cat_image']['tmp_name']) && $_FILES['cat_image']['error'] === 0) {
        $imgName = basename($_FILES['cat_image']['name']);
        $tmpName = $_FILES['cat_image']['tmp_name'];
        $folder = "uploads/" . $imgName;
        if (move_uploaded_file($tmpName, $folder)) {
            // remove old file if different
            if ($oldImage !== '' && $oldImage !== $imgName && file_exists(__DIR__ . '/uploads/' . $oldImage)) {
                @unlink(__DIR__ . '/uploads/' . $oldImage);
            }
            $imageName = $imgName;
        }
    }

    $stmt = $conn->prepare("UPDATE categories SET name = ?, image_path = ? WHERE id = ?");
    $stmt->bind_param('ssi', $cat_name, $imageName, $id);
    if ($stmt->execute()) {
        $success = 'Category updated successfully.';
    } else {
        $error = 'Error: ' . htmlspecialchars($conn->error, ENT_QUOTES);
    }
}

// Load category
$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    header("Location: categories.php");
    exit();
}
$category = $res->fetch_assoc();
?>

<div class="form-container">
    <h2>Edit Category</h2>
    <?php if (!empty($success)): ?>
        <div class="message success"><?php echo htmlspecialchars($success, ENT_QUOTES); ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="message error"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></div>
    <?php endif; ?>

    <form action="edit_categories.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo (int)$category['id']; ?>">
        <input type="hidden" name="old_image" value="<?php echo htmlspecialchars($category['image_path'], ENT_QUOTES); ?>">

        <div class="form-group">
            <label class="form-label">Category Name</label>
            <input class="form-input" type="text" name="cat_name" required value="<?php echo htmlspecialchars($category['name'], ENT_QUOTES); ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Category Cover Image</label>
            <div style="margin-bottom: 15px;">
                <img src="uploads/<?php echo htmlspecialchars($category['image_path'], ENT_QUOTES); ?>" style="width: 200px; height: 150px; object-fit: cover; border-radius: 4px;">
            </div>
            <input class="form-file" type="file" name="cat_image" accept="image/*" id="catImage">
            <img id="catPreview" class="preview-img" src="#" alt="" style="display:none; width: 200px; height: 150px; object-fit: cover; margin-top: 10px; border-radius: 4px;" />
            <small style="display: block; margin-top: 8px; color: #666;">Leave empty to keep current image</small>
        </div>

        <button type="submit" name="submit" class="btn-primary">Update Category</button>
        <a href="categories.php" style="display: inline-block; margin-left: 10px; padding: 10px 20px; background: #666; color: white; text-decoration: none; border-radius: 4px;">Cancel</a>
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
