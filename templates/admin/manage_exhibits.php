<?php
session_start();
require_once dirname(__DIR__, 2) . '/includes/db.php';
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
    <style>
        .form-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; border-top: 4px solid #27ae60; display: none; /* Hidden by default */ }
        
        /* CSS Grid for form layout */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: 1 / -1; }
        
        .form-group { margin-bottom: 0; }
        .form-label { display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px; font-size: 0.95rem; }
        .form-control { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px; font-family: inherit; font-size: 1rem; }
        textarea.form-control { resize: vertical; min-height: 100px; }
        
        .btn-toggle { background: #27ae60; color: white; padding: 12px 20px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 1.1rem; margin-bottom: 20px; display: inline-block; transition: 0.3s; }
        .btn-toggle:hover { background: #219653; }
        .btn-submit-form { width: 100%; padding: 12px; background: #2c3e50; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer; transition: 0.3s; }
        .btn-submit-form:hover { background: #1a252f; }

        /* Hidden file input */
        input[type="file"] { display: none; }
        
        /* Stylized "Upload Image" button that triggers the hidden input */
        .custom-file-upload { display: inline-block; padding: 10px 20px; cursor: pointer; background: #fdfdfd; border: 2px dashed #ddd; border-radius: 6px; color: #555; text-align: center; font-weight: bold; transition: 0.3s; }
        .custom-file-upload:hover { border-color: #27ae60; color: #27ae60; }
        
        /* Hidden image preview container */
        #imagePreviewContainer { display: none; margin-top: 20px; max-width: 100%; height: auto; border-radius: 4px; border: 1px solid #ddd; overflow: hidden; padding: 10px; background: #f9f9f9; text-align: center; }
        #imagePreview { max-width: 100%; max-height: 300px; border-radius: 4px; }

        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; gap: 15px; }
        }
    </style>
</head>
<body style="margin: 0; background: #f4f7f6;">

    <?php include dirname(__DIR__, 2) . '/templates/components/header.php'; ?>
    <?php include dirname(__DIR__, 2) . '/templates/components/admin_sidebar.php'; ?>

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

<script>
    // 1. Logic to show the client-side image preview instantly after file upload
    const fileInput = document.getElementById('fileInput');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const imagePreview = document.getElementById('imagePreview');

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Check if it's an image
            if (!file.type.startsWith('image/')) {
                alert("Please select an image file.");
                return;
            }

            const reader = new FileReader();

            reader.addEventListener('load', function() {
                // Populate the image preview tag with the file's contents
                imagePreview.src = reader.result;
                // Instantly slide down the preview container
                imagePreviewContainer.style.display = 'block';
            });

            // Read the file as a data URL for client-side display
            reader.readAsDataURL(file);
        } else {
            // If the user clears the file, hide the preview
            imagePreviewContainer.style.display = 'none';
            imagePreview.src = '#';
        }
    });

    // 2. Logic to toggle the dropdown form open/close
    function toggleForm() {
        var form = document.getElementById("addArtifactForm");
        if (form.style.display === "none" || form.style.display === "") {
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    }

    // Smart Errors from previous logic: If there is an error message, keep the form open automatically so they don't have to click it again
    <?php if ($msg && $msg_color == 'red'): ?>
        document.getElementById("addArtifactForm").style.display = "block";
    <?php endif; ?>
</script>

</body>
</html>