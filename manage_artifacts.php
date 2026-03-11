<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php'; 

// --- Begin logic from add_exhibit.php ---
$msg = "";
$msg_color = "red";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_exhibit'])) {
    $title = trim($_POST['title']);
    $category_id = $_POST['category_id'];
    $artifact_year = trim($_POST['artifact_year']);
    $origin = trim($_POST['origin']);
    $donated_by = trim($_POST['donated_by']);
    $description = trim($_POST['description']);
    $is_donated = isset($_POST['is_donated']) ? 1 : 0;
    
    // Image Upload Logic
    $image_path = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $image_path = time() . '_' . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image_path);
    }

    if (!empty($title) && !empty($description) && !empty($image_path)) {
        $stmt = $conn->prepare("INSERT INTO exhibits (title, category_id, description, image_path, artifact_year, origin, donated_by, is_donated) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisssssi", $title, $category_id, $description, $image_path, $artifact_year, $origin, $donated_by, $is_donated);
        
        if ($stmt->execute()) {
            $new_exhibit_id = $conn->insert_id;
            
            // If this is a newly donated artifact, auto-create a news entry
            if ($is_donated == 1) {
                $news_title = "New Artifact Donation: " . htmlspecialchars($title);
                $news_content = "We are thrilled to announce a new addition to our museum collection! ";
                
                if (!empty($donated_by)) {
                    $news_content .= "The artifact \"" . htmlspecialchars($title) . "\" was generously donated by " . htmlspecialchars($donated_by) . ". ";
                } else {
                    $news_content .= "The artifact \"" . htmlspecialchars($title) . "\" has been added to our collection. ";
                }
                
                if (!empty($artifact_year)) {
                    $news_content .= "This piece dates back to " . htmlspecialchars($artifact_year) . ". ";
                }
                
                if (!empty($origin)) {
                    $news_content .= "It originates from " . htmlspecialchars($origin) . ". ";
                }
                
                $news_content .= "Come visit us to see this wonderful new piece in person!";
                
                $news_stmt = $conn->prepare("INSERT INTO news_events (title, content, type, image_path, date_posted) VALUES (?, ?, 'news', ?, NOW())");
                $news_stmt->bind_param("sss", $news_title, $news_content, $image_path);
                $news_stmt->execute();
                $news_stmt->close();
            }
            
            $msg = "Artifact added successfully!";
            $msg_color = "green";
        } else {
            $msg = "Error adding artifact: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $msg = "Please fill in all required fields and upload an image.";
    }
}
// --- End logic from add_exhibit.php ---

// Fetch categories for filter and form dropdowns
$categories = [];
$cat_query = "SELECT * FROM categories ORDER BY name ASC";
$cat_result = $conn->query($cat_query);
if ($cat_result) {
    while ($cat_row = $cat_result->fetch_assoc()) {
        $categories[] = $cat_row;
    }
}

// Fetch exhibits for the main table
$query = "SELECT exhibits.*, categories.name AS cat_name FROM exhibits LEFT JOIN categories ON exhibits.category_id = categories.id ORDER BY exhibits.id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Artifacts</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/manage.css">
</head>
<body class="admin-body">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div class="page-header">
        <h3 class="table-title">🖼️ Manage Artifacts</h3>
        <button id="toggle-form-btn" class="toggle-form-btn">➕ Add New Artifact</button>
    </div>

    <div id="add-artifact-wrapper" class="form-container-wrapper <?php echo !empty($msg) ? 'form-visible' : ''; ?>">
        <div class="form-container">
             <?php if ($msg): ?>
                <div class="message-box <?php echo $msg_color === 'green' ? 'success' : ''; ?>" style="border-left-color: <?php echo $msg_color === 'green' ? '#27ae60' : '#c0392b'; ?>; color: <?php echo $msg_color === 'green' ? '#27ae60' : '#c0392b'; ?>;">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <form action="manage_artifacts.php" method="POST" enctype="multipart/form-data" class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">Artifact Title *</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g., Pre-Colonial Gold Necklace" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Department / Category</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">Select a Department</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">High-Res Image *</label>
                    <input type="file" name="image" id="image-input" class="form-control" accept="image/*" required style="padding: 9px;">
                    <img id="image-preview" src="#" alt="Image Preview">
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
                    <label class="form-label">
                        <input type="checkbox" name="is_donated" value="1" style="margin-right: 8px;">
                        This is a newly donated artifact
                    </label>
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
    </div>

    <div class="table-container">
        <div class="filter-container">
            <input type="text" id="search" placeholder="Search by title, description...">
            <select id="category">
                <option value="">All Departments</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" id="date" placeholder="Filter by year (e.g., 18th Century)">
        </div>

        <div id="artifacts-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Image</th><th>Title</th><th>Department</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if($result && $result->num_rows > 0): while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><img src="uploads/<?php echo $row['image_path']; ?>" width="60" height="60" style="object-fit:cover; border-radius:4px;"></td>
                    <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                    <td><?php echo $row['cat_name'] ? htmlspecialchars($row['cat_name']) : '<em style="color:#e74c3c;">Uncategorized</em>'; ?></td>
                    <td>
                        <a href="edit_exhibit.php?id=<?php echo $row['id']; ?>" class="action-btn btn-edit">✎ Edit</a>
                        <a href="delete_exhibit.php?id=<?php echo $row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete this artifact?');">🗑️ Delete</a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="5" style="text-align: center; padding: 20px;">No artifacts found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    </main>
</div>

<!-- JS -->
<script src="js/manage.js"></script>

</body>
</html>

