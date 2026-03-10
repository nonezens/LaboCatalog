<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }

include 'db.php'; 
include 'header.php';
include 'functions.php';

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
        log_activity($conn, $_SESSION['admin_id'], "Edited artifact with ID: " . $id);
        header("Location: admin_dashboard.php"); exit();
    }
}

$categories = $conn->query("SELECT * FROM categories");
?>

<style>
    body { background-color: #f4f7f6; }
    .edit-container {
        max-width: 900px;
        margin: 40px auto;
        padding: 30px;
        background: white;
        border-radius: 12px;
        font-family: 'Segoe UI', Tahoma, sans-serif;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .edit-container h2 {
        text-align: center;
        color: #2c3e50;
        margin-top: 0;
        margin-bottom: 30px;
        font-size: 2rem;
    }
    .edit-form {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 40px;
    }
    .image-column { text-align: center; }
    .image-preview-container {
        width: 100%;
        height: 250px;
        background: #f0f0f0;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 15px;
        border: 2px dashed #ddd;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .image-preview {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        display: block;
    }
    .file-input-label {
        display: block;
        padding: 10px 15px;
        background-color: #3498db;
        color: white;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
        font-weight: bold;
    }
    .file-input-label:hover { background-color: #2980b9; }
    #imageUpload { display: none; }
    .form-column label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: #555;
    }
    .form-column input[type="text"],
    .form-column select,
    .form-column textarea {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
        transition: border-color 0.3s;
    }
    .form-column input[type="text"]:focus,
    .form-column select:focus,
    .form-column textarea:focus {
        border-color: #f39c12;
        outline: none;
    }
    .grid-fields {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
    }
    .update-btn {
        width: 100%;
        padding: 15px;
        background: #f39c12;
        color: white;
        border: none;
        border-radius: 5px;
        font-weight: bold;
        font-size: 1.1rem;
        cursor: pointer;
        transition: background-color 0.3s;
        margin-top: 10px;
    }
    .update-btn:hover { background: #e67e22; }

    @media (max-width: 768px) {
        .edit-form {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        .image-preview-container { height: 200px; }
    }
</style>

<div class="edit-container">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <h2>Update Artifact Details</h2>
    <form method="POST" enctype="multipart/form-data" class="edit-form">
        
        <div class="image-column">
            <label>Artifact Image</label>
            <div class="image-preview-container">
                <img src="uploads/<?php echo htmlspecialchars($exhibit['image_path']); ?>" id="imagePreview" class="image-preview" alt="Current Image Preview">
            </div>
            <label for="imageUpload" class="file-input-label">Change Image</label>
            <input type="file" name="image" id="imageUpload" accept="image/*">
            <p style="font-size: 0.8rem; color: #777; margin-top: 10px;">Leave blank to keep current image.</p>
        </div>

        <div class="form-column">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($exhibit['title']); ?>" required>

            <label for="category_id">Department</label>
            <select id="category_id" name="category_id">
                <?php while($cat = $categories->fetch_assoc()): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php if($cat['id'] == $exhibit['category_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="6"><?php echo htmlspecialchars($exhibit['description']); ?></textarea>

            <div class="grid-fields">
                <div>
                    <label for="artifact_year">Year</label>
                    <input type="text" id="artifact_year" name="artifact_year" value="<?php echo htmlspecialchars($exhibit['artifact_year']); ?>">
                </div>
                <div>
                    <label for="origin">Origin</label>
                    <input type="text" id="origin" name="origin" value="<?php echo htmlspecialchars($exhibit['origin']); ?>">
                </div>
                <div>
                    <label for="donated_by">Donated By</label>
                    <input type="text" id="donated_by" name="donated_by" value="<?php echo htmlspecialchars($exhibit['donated_by']); ?>">
                </div>
            </div>

            <button type="submit" name="update" class="update-btn">Update Artifact</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('imageUpload').addEventListener('change', function(event) {
        const [file] = event.target.files;
        if (file) {
            const preview = document.getElementById('imagePreview');
            preview.src = URL.createObjectURL(file);
            preview.onload = () => URL.revokeObjectURL(preview.src); // Free memory
        }
    });
</script>