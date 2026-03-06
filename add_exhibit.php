<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php'; 

$msg = "";
$msg_color = "red";

// Fetch categories for the dropdown menu
$cat_query = "SELECT * FROM categories ORDER BY name ASC";
$cat_result = $conn->query($cat_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_exhibit'])) {
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
        $image_path = time() . '_' . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image_path);
    }

    if (!empty($title) && !empty($description) && !empty($image_path)) {
        $stmt = $conn->prepare("INSERT INTO exhibits (title, category_id, description, image_path, artifact_year, origin, donated_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisssss", $title, $category_id, $description, $image_path, $artifact_year, $origin, $donated_by);
        
        if ($stmt->execute()) {
            $msg = "Artifact added successfully!";
            $msg_color = "green";
        } else {
            $msg = "Error adding artifact.";
        }
    } else {
        $msg = "Please fill in all required fields and upload an image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Add Artifact | Admin</title>
    <style>
        .form-container { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 800px; margin: 0 auto; }
        
        /* CSS Grid for form layout */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: 1 / -1; }
        
        .form-group { margin-bottom: 0; } /* Gap handled by grid */
        .form-label { display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px; font-size: 0.95rem; }
        .form-control { width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px; font-family: inherit; font-size: 1rem; transition: border-color 0.3s; }
        .form-control:focus { border-color: #2980b9; outline: none; }
        textarea.form-control { resize: vertical; min-height: 120px; }
        
        .btn-submit { width: 100%; padding: 15px; background: #2980b9; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        .btn-submit:hover { background: #1c5980; transform: translateY(-2px); }
        
        /* Mobile Adjustments */
        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; /* Stacks everything on cellphones! */ gap: 15px; }
            .form-container { padding: 20px; border-radius: 0; box-shadow: none; border-top: 2px solid #2980b9; }
        }
    </style>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: 'Segoe UI', Tahoma, sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

        <div style="margin-bottom: 20px;">
            <h2 style="color: #2c3e50; margin-top: 0; font-size: 2rem;">➕ Add New Artifact</h2>
            <p style="color: #7f8c8d;">Document a new piece of history for the museum catalog.</p>
        </div>

        <div class="form-container">
            <?php if ($msg): ?>
                <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo $msg_color; ?>; font-weight: bold;">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">Artifact Title *</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g., Pre-Colonial Gold Necklace" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Department / Category</label>
                    <select name="category_id" class="form-control" style="background: white;" required>
                        <option value="">Select a Department</option>
                        <?php while($cat = $cat_result->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">High-Res Image *</label>
                    <input type="file" name="image" class="form-control" accept="image/*" required style="padding: 9px; background: #f9f9f9;">
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
                    <button type="submit" name="add_exhibit" class="btn-submit">Save Artifact to Database</button>
                </div>

            </form>
        </div>

    </main>
</div>
</body>
</html>