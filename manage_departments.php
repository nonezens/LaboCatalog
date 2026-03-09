<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php'; 

// --- Begin logic from add_category.php ---
$msg = "";
$msg_color = "red";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    
    $image_path = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $image_path = time() . '_' . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image_path);
    }

    if (!empty($name) && !empty($image_path)) {
        $stmt = $conn->prepare("INSERT INTO categories (name, image_path) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $image_path);
        
        if ($stmt->execute()) {
            $msg = "Department added successfully!";
            $msg_color = "green";
        } else {
            $msg = "Error adding department.";
        }
        $stmt->close();
    } else {
        $msg = "Please provide a department name and a cover image.";
    }
}
// --- End logic from add_category.php ---

// Fetch existing categories for the table
$cat_result = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Departments</title>
    <style>
        /* Styles from add_category.php */
        .form-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 600px; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px; font-size: 0.95rem; }
        .form-control { width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px; font-family: inherit; font-size: 1rem; transition: border-color 0.3s; }
        .form-control:focus { border-color: #c5a059; outline: none; }
        .btn-submit { width: 100%; padding: 15px; background: #27ae60; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer; transition: 0.3s; }
        .btn-submit:hover { background: #219653; transform: translateY(-2px); }

        /* New button for toggling form */
        #toggle-form-btn {
            background: #2980b9; color: white; border: none; padding: 12px 20px; font-size: 1rem; font-weight: bold; border-radius: 5px; cursor: pointer; transition: 0.3s;
        }
        #toggle-form-btn:hover { background: #1c5980; }
        /* New styles for animation */
        .form-container-wrapper {
            overflow: hidden;
            transition: max-height 0.7s ease-in-out, opacity 0.5s ease-in-out, margin-bottom 0.7s ease-in-out;
            max-height: 0;
            opacity: 0;
            margin-bottom: 0;
        }
        .form-container-wrapper.form-visible {
            max-height: 1000px; /* Adjust if form is taller */
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
            .form-container { padding: 20px; border-radius: 0; }
        }
    </style>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 class="table-title" style="margin-bottom: 0;">📁 Manage Departments</h3>
        <button id="toggle-form-btn">➕ Add New Department</button>
    </div>

    <div id="add-category-wrapper" class="form-container-wrapper <?php echo !empty($msg) ? 'form-visible' : ''; ?>">
        <div class="form-container">
            <?php if ($msg): ?>
                <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo ($msg_color === 'red' ? '#c0392b' : '#27ae60'); ?>; font-weight: bold;">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <form action="manage_departments.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Department Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g., Ancient History" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Cover Image</label>
                    <input type="file" name="image" id="image-input" class="form-control" accept="image/*" required style="padding: 9px; background: #f9f9f9;">
                    <img id="image-preview" src="#" alt="Image Preview" style="display: none; max-width: 200px; margin-top: 10px; border-radius: 5px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"/>
                </div>
                <button type="submit" name="add_category" class="btn-submit">Save Department</button>
            </form>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Cover Image</th><th>Department Name</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if($cat_result && $cat_result->num_rows > 0): while($cat_row = $cat_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $cat_row['id']; ?></td>
                    <td><img src="uploads/<?php echo $cat_row['image_path']; ?>" width="60" height="60" style="object-fit:cover; border-radius:4px;"></td>
                    <td><strong><?php echo htmlspecialchars($cat_row['name']); ?></strong></td>
                    <td>
                        <a href="edit_category.php?id=<?php echo $cat_row['id']; ?>" class="action-btn btn-edit">✎ Edit</a>
                        <a href="delete_category.php?id=<?php echo $cat_row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('WARNING: Deleting this department will permanently delete ALL artifacts inside it!');">🗑️ Delete</a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="4" style="text-align: center; padding: 20px;">No departments found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle form visibility
    const toggleBtn = document.getElementById('toggle-form-btn');
    const formWrapper = document.getElementById('add-category-wrapper');
    const tableContainer = document.querySelector('.table-container');

    // Set initial button text if form is already visible on page load
    if (formWrapper.classList.contains('form-visible')) {
        toggleBtn.textContent = '➖ Cancel';
        if(tableContainer) tableContainer.classList.add('faded');
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const isVisible = formWrapper.classList.toggle('form-visible');
            if(tableContainer) tableContainer.classList.toggle('faded');

            this.textContent = isVisible ? '➖ Cancel' : '➕ Add New Department';
            if (isVisible) {
                formWrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    // Image preview functionality
    const imageInput = document.getElementById('image-input');
    const imagePreview = document.getElementById('image-preview');

    if (imageInput) {
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
});
</script>

</body>
</html>
