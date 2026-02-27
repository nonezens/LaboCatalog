<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }

include 'db.php'; include 'header.php';

// Fetch existing data
if (!isset($_GET['id'])) { header("Location: admin_dashboard.php"); exit(); }
$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM exhibits WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$exhibit = $stmt->get_result()->fetch_assoc();

// Handle Form Submission for Update
if(isset($_POST['update'])) {
    $title = $_POST['title']; $cat = $_POST['category_id']; $desc = $_POST['description'];
    $donor = $_POST['donated_by']; $year = $_POST['artifact_year']; $origin = $_POST['origin'];
    
    // Check if a new image was uploaded
    if (!empty($_FILES['image']['name'])) {
        $imgName = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $imgName);
        
        // Update WITH new image
        $update_stmt = $conn->prepare("UPDATE exhibits SET title=?, category_id=?, image_path=?, description=?, donated_by=?, artifact_year=?, origin=? WHERE id=?");
        $update_stmt->bind_param("sisssssi", $title, $cat, $imgName, $desc, $donor, $year, $origin, $id);
    } else {
        // Update WITHOUT changing the image
        $update_stmt = $conn->prepare("UPDATE exhibits SET title=?, category_id=?, description=?, donated_by=?, artifact_year=?, origin=? WHERE id=?");
        $update_stmt->bind_param("sissssi", $title, $cat, $desc, $donor, $year, $origin, $id);
    }
    
    if($update_stmt->execute()) {
        header("Location: admin_dashboard.php"); exit();
    }
}

$categories = $conn->query("SELECT * FROM categories");
?>

<div style="max-width: 800px; margin: 40px auto; padding: 20px; background: white; border-radius: 8px; font-family: sans-serif; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
    <h2>Update Artifact</h2>
    <form method="POST" enctype="multipart/form-data">
        
        <label>Title</label><br>
        <input type="text" name="title" value="<?php echo htmlspecialchars($exhibit['title']); ?>" style="width:100%; padding:10px; margin-bottom:15px;" required>

        <label>Category</label><br>
        <select name="category_id" style="width:100%; padding:10px; margin-bottom:15px;">
            <?php while($cat = $categories->fetch_assoc()): ?>
                <option value="<?php echo $cat['id']; ?>" <?php if($cat['id'] == $exhibit['category_id']) echo 'selected'; ?>>
                    <?php echo $cat['name']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Description</label><br>
        <textarea name="description" rows="5" style="width:100%; padding:10px; margin-bottom:15px;"><?php echo htmlspecialchars($exhibit['description']); ?></textarea>

        <div style="display:flex; gap:10px;">
            <div style="flex:1;">
                <label>Year</label>
                <input type="text" name="artifact_year" value="<?php echo htmlspecialchars($exhibit['artifact_year']); ?>" style="width:100%; padding:10px; margin-bottom:15px;">
            </div>
            <div style="flex:1;">
                <label>Origin</label>
                <input type="text" name="origin" value="<?php echo htmlspecialchars($exhibit['origin']); ?>" style="width:100%; padding:10px; margin-bottom:15px;">
            </div>
            <div style="flex:1;">
                <label>Donated By</label>
                <input type="text" name="donated_by" value="<?php echo htmlspecialchars($exhibit['donated_by']); ?>" style="width:100%; padding:10px; margin-bottom:15px;">
            </div>
        </div>

        <label>New Image (Leave blank to keep current image)</label><br>
        <input type="file" name="image" style="width:100%; padding:10px; margin-bottom:20px;">

        <button type="submit" name="update" style="width:100%; padding:15px; background:#f39c12; color:white; border:none; border-radius:5px; font-weight:bold; cursor:pointer;">Update Artifact</button>
    </form>
</div>