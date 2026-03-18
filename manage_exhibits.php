<?php
session_start();
include 'db.php';
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }

$msg = "";
$msg_color = "red";

// --- ADD ARTIFACT ---
if (isset($_POST['add_artifact'])) {
    $title = trim($_POST['title']);
    $category_id = $_POST['category_id'];
    $artifact_year = trim($_POST['artifact_year']);
    $origin = trim($_POST['origin']);
    $donated_by = trim($_POST['donated_by']);
    $description = trim($_POST['description']);
    
    // Image Upload Logic
    $image_path = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $image_path = time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image_path);
    }
    
    if (!empty($title) && !empty($description) && !empty($image_path)) {
        $stmt = $conn->prepare("INSERT INTO exhibits (title, category_id, description, image_path, artifact_year, origin, donated_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("sisssss", $title, $category_id, $description, $image_path, $artifact_year, $origin, $donated_by);
            if ($stmt->execute()) {
                header("Location: manage_exhibits.php?success=1");
                exit();
            } else {
                $msg = "Database Error: " . $stmt->error;
            }
        } else {
            $msg = "SQL Error: " . $conn->error;
        }
    } else {
        $msg = "Please fill in all required fields and upload an image.";
    }
}

if (isset($_GET['success'])) {
    $msg = "Artifact successfully added!";
    $msg_color = "green";
}

// --- DELETE ARTIFACT ---
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $conn->query("DELETE FROM exhibits WHERE id = $id");
    header("Location: manage_exhibits.php");
    exit();
}

// Get categories for the dropdown menu
$categories_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");

// Get exhibits AND their matching category name using a SQL JOIN
$exhibits = $conn->query("
    SELECT exhibits.*, categories.name AS category_name 
    FROM exhibits 
    LEFT JOIN categories ON exhibits.category_id = categories.id 
    ORDER BY exhibits.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Artifacts | Admin</title>
    <link rel="stylesheet" href="css/admin-sidebar.css">
    <link rel="stylesheet" href="css/manage-exhibits.css">
</head>
<body style="margin: 0; background: #f4f7f6;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h2 class="table-title" style="margin: 0;">🖼️ Manage Artifacts</h2>
        <button onclick="toggleForm()" class="btn-toggle">➕ Add New Artifact</button>
    </div>

    <?php if ($msg): ?>
        <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 20px; color: <?php echo $msg_color; ?>; font-weight: bold;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div class="form-container" id="addArtifactForm">
        <h3 style="margin-top: 0; color: #27ae60; margin-bottom: 20px;">Artifact Details</h3>
        <form method="POST" enctype="multipart/form-data" class="form-grid">
            
            <div class="form-group full-width">
                <label class="form-label">Artifact Title *</label>
                <input type="text" name="title" class="form-control" placeholder="e.g., Pre-Colonial Gold Necklace" required>
            </div>

            <div class="form-group">
                <label class="form-label">Department / Category *</label>
                <select name="category_id" class="form-control" style="background: white;" required>
                    <option value="">-- Select a Department --</option>
                    <?php 
                    if ($categories_result && $categories_result->num_rows > 0) {
                        // Reset pointer just in case
                        $categories_result->data_seek(0);
                        while($cat = $categories_result->fetch_assoc()) {
                            echo "<option value='" . $cat['id'] . "'>" . htmlspecialchars($cat['name']) . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <input type="file" name="image" id="fileInput" accept="image/*" required>
                <label for="fileInput" class="custom-file-upload"> Upload Image * </label>
                
                <div id="imagePreviewContainer">
                    <img id="imagePreview" src="#" alt="Artifact Preview">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Historical Period / Year</label>
                <input type="text" name="artifact_year" class="form-control" placeholder="e.g., 18th Century">
            </div>

            <div class="form-group">
                <label class="form-label">Origin / Discovery Location</label>
                <input type="text" name="origin" class="form-control" placeholder="e.g., Labo River">
            </div>

            <div class="form-group full-width">
                <label class="form-label">Donated / Contributed By</label>
                <input type="text" name="donated_by" class="form-control" placeholder="Leave blank if from the main museum collection">
            </div>

            <div class="form-group full-width">
                <label class="form-label">Full Historical Description *</label>
                <textarea name="description" class="form-control" placeholder="Write the history and significance of this artifact here..." required></textarea>
            </div>

            <div class="full-width">
                <button type="submit" name="add_artifact" class="btn-submit-form">Save Artifact to Database</button>
            </div>

        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr><th>ID</th><th>IMAGE</th><th>TITLE</th><th>DEPARTMENT</th><th>ACTIONS</th></tr>
            </thead>
            <tbody>
                <?php if($exhibits && $exhibits->num_rows > 0): while($row = $exhibits->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td>
                        <?php if(!empty($row['image_path'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($row['image_path']); ?>" width="60" style="border-radius:4px; height: 60px; object-fit: cover;">
                        <?php else: ?>
                            <span style="color:#999; font-size: 0.8rem;">No Image</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?php echo htmlspecialchars($row['title']); ?></strong>
                        <?php if(!empty($row['artifact_year'])): ?>
                            <br><span style="color:#7f8c8d; font-size:0.85rem;"><?php echo htmlspecialchars($row['artifact_year']); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo !empty($row['category_name']) ? htmlspecialchars($row['category_name']) : '<span style="color:#e74c3c;">None</span>'; ?>
                    </td>
                    <td>
                        <div style="display: flex; gap: 5px;">
                            <a href="edit_exhibit.php?id=<?php echo $row['id']; ?>" class="action-btn btn-edit">✏️ Edit</a>
                            <a href="manage_exhibits.php?delete_id=<?php echo $row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete this artifact?');">🗑️ Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="5" style="text-align: center; padding: 20px;">No artifacts found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    </main>
</div>

<script src="js/manage-exhibits.js"></script>

</body>
</html>