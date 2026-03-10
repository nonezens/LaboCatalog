<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php'; 
include 'functions.php';

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
            $exhibit_id = $stmt->insert_id;
            log_activity($conn, $_SESSION['admin_id'], "Added artifact with ID: " . $exhibit_id);
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
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$query = "SELECT exhibits.*, categories.name AS cat_name FROM exhibits LEFT JOIN categories ON exhibits.category_id = categories.id";
if (!empty($search_query)) {
    $query .= " WHERE exhibits.title LIKE '%" . $conn->real_escape_string($search_query) . "%' OR exhibits.description LIKE '%" . $conn->real_escape_string($search_query) . "%'";
}
$query .= " ORDER BY exhibits.id DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Artifacts</title>
    <style>
        /* Styles from manage_artifacts.php */
        .filter-container {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: center;
        }
        .filter-container input, .filter-container select {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        /* Styles from add_exhibit.php */
        .form-container { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: 1 / -1; }
        .form-group { margin-bottom: 0; }
        .form-label { display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px; font-size: 0.95rem; }
        .form-control { width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px; font-family: inherit; font-size: 1rem; transition: border-color 0.3s; }
        .form-control:focus { border-color: #2980b9; outline: none; }
        textarea.form-control { resize: vertical; min-height: 120px; }
        .btn-submit { width: 100%; padding: 15px; background: #2980b9; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        .btn-submit:hover { background: #1c5980; transform: translateY(-2px); }

        /* New button for toggling form */
        #toggle-form-btn {
            background: #27ae60; color: white; border: none; padding: 12px 20px; font-size: 1rem; font-weight: bold; border-radius: 5px; cursor: pointer; transition: 0.3s;
        }
        #toggle-form-btn:hover { background: #219150; }
        
        /* New styles for animation */
        .form-container-wrapper {
            overflow: hidden;
            transition: max-height 0.7s ease-in-out, opacity 0.5s ease-in-out, margin-bottom 0.7s ease-in-out;
            max-height: 0;
            opacity: 0;
            margin-bottom: 0;
        }
        .form-container-wrapper.form-visible {
            max-height: 1500px; /* Adjust if form is taller */
            opacity: 1;
            margin-bottom: 30px; /* Original margin */
        }

        /* Styles for fading the table */
        .table-container {
            transition: opacity 0.5s ease-in-out;
        }
        .table-container.faded {
            opacity: 0.3;
            pointer-events: none; /* Prevents interaction with the faded table */
        }

        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; gap: 15px; }
            .form-container { padding: 20px; }
        }
    </style>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 class="table-title" style="margin-bottom: 0;">🖼️ Manage Artifacts</h3>
        <button id="toggle-form-btn">➕ Add New Artifact</button>
    </div>

    <div id="add-artifact-wrapper" class="form-container-wrapper <?php echo !empty($msg) ? 'form-visible' : ''; ?>">
        <div class="form-container">
             <?php if ($msg): ?>
                <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo ($msg_color === 'red' ? '#c0392b' : '#27ae60'); ?>; font-weight: bold;">
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
                    <img id="image-preview" src="#" alt="Image Preview" style="display: none; max-width: 200px; margin-top: 10px; border-radius: 5px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"/>
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
    </div>

    <div class="table-container">
        <div class="filter-container">
            <input type="text" id="search" placeholder="Search by title, description..." value="<?php echo htmlspecialchars($search_query); ?>">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle form visibility
    const toggleBtn = document.getElementById('toggle-form-btn');
    const formWrapper = document.getElementById('add-artifact-wrapper');
    const tableContainer = document.querySelector('.table-container');

    // Set initial button text if form is already visible on page load (e.g., due to a PHP error message)
    if (formWrapper.classList.contains('form-visible')) {
        toggleBtn.textContent = '➖ Cancel';
        if(tableContainer) tableContainer.classList.add('faded');
    }

    if(toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const isVisible = formWrapper.classList.toggle('form-visible');
            if(tableContainer) tableContainer.classList.toggle('faded');
            
            this.textContent = isVisible ? '➖ Cancel' : '➕ Add New Artifact';
            if (isVisible) {
                // Smooth scroll to the form when it opens
                formWrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    // Image preview functionality
    const imageInput = document.getElementById('image-input');
    const imagePreview = document.getElementById('image-preview');

    if(imageInput) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                }
                reader.readAsDataURL(this.files[0]);
            } else {
                imagePreview.style.display = 'none';
                imagePreview.src = '#';
            }
        });
    }

    // Existing filter functionality
    const searchInput = document.getElementById('search');
    const categoryInput = document.getElementById('category');
    const dateInput = document.getElementById('date');
    const artifactsTable = document.getElementById('artifacts-table');
    let debounceTimer;

    function fetchArtifacts() {
        artifactsTable.innerHTML = '<table><tbody><tr><td colspan="5" style="text-align: center; padding: 20px;">Loading...</td></tr></tbody></table>';
        
        let formData = new FormData();
        formData.append('search', searchInput.value);
        formData.append('category_id', categoryInput.value);
        formData.append('artifact_year', dateInput.value);

        fetch('fetch_artifacts.php', { method: 'POST', body: formData })
        .then(response => response.text())
        .then(data => { artifactsTable.innerHTML = data; })
        .catch(error => {
            console.error('Error:', error);
            artifactsTable.innerHTML = '<table><tbody><tr><td colspan="5" style="text-align: center; padding: 20px; color: red;">An error occurred.</td></tr></tbody></table>';
        });
    }

    function handleInput() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchArtifacts, 400);
    }

    searchInput.addEventListener('keyup', handleInput);
    categoryInput.addEventListener('change', fetchArtifacts);
    dateInput.addEventListener('keyup', handleInput);
});
</script>

</body>
</html>
