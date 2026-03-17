<?php
session_start();
include 'db.php';
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }

if (!isset($_GET['id'])) {
    header("Location: manage_departments.php");
    exit();
}

$id = $_GET['id'];
$msg = "";

// Fetch the existing category data
$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
    header("Location: manage_departments.php");
    exit();
}

// --- HANDLE UPDATE ---
if (isset($_POST['update_category'])) {
    $name = $_POST['name'];
    $image_path = $category['image_path']; // Keep old image by default
    
    // If a new image was uploaded, process it
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $image_path = time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image_path);
    }
    
    $update_stmt = $conn->prepare("UPDATE categories SET name = ?, image_path = ? WHERE id = ?");
    if ($update_stmt) {
        $update_stmt->bind_param("ssi", $name, $image_path, $id);
        if ($update_stmt->execute()) {
            header("Location: manage_departments.php?success=1");
            exit();
        } else {
$msg = "<div style='color: red; padding: 10px; border: 1px solid #e74c3c; border-radius: 4px; margin-bottom: 15px;'>Database error occurred.</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Department | Admin</title>
</head>
<body style="margin: 0; background: #f4f7f6;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <h2 class="table-title">✏️ Edit Department</h2>
    <?php echo $msg; ?>

    <div class="card" style="max-width: 600px;">
        <form method="POST" enctype="multipart/form-data">
            
            <?php if(!empty($category['image_path'])): ?>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; color: #2c3e50; font-weight: bold; margin-bottom: 5px;">Current Image:</label>
                    <img src="uploads/<?php echo htmlspecialchars($category['image_path']); ?>" style="max-width: 200px; border-radius: 8px; border: 1px solid #ddd;">
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label>Department Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($category['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Upload New Image (Leave blank to keep current image)</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" name="update_category" class="btn-submit bg-category">Update Department</button>
                <a href="manage_departments.php" class="action-btn" style="background: #95a5a6; padding: 10px 20px; font-weight: bold; line-height: 18px;">Cancel</a>
            </div>
        </form>
    </div>

    </main>
</div>

</body>
</html>