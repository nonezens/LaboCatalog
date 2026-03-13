<?php
session_start();
include 'db.php';
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }

$msg = "";
$msg_color = "red";

// --- ADD DEPARTMENT ---
if (isset($_POST['add_department'])) {
    $name = $_POST['name'];
    
    // Image Upload Logic
    $image_path = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $image_path = time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image_path);
    }
    
    // Insert into categories table including the image_path
    $stmt = $conn->prepare("INSERT INTO categories (name, image_path) VALUES (?, ?)");
    if ($stmt) {
        $stmt->bind_param("ss", $name, $image_path);
        if ($stmt->execute()) {
            header("Location: manage_departments.php?success=1");
            exit();
        } else {
            $msg = "Database Error: " . $stmt->error;
        }
    } else {
        $msg = "SQL Error: " . $conn->error;
    }
}

if (isset($_GET['success'])) {
    $msg = "Department successfully updated/added!";
    $msg_color = "green";
}

// --- DELETE DEPARTMENT ---
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $conn->query("DELETE FROM categories WHERE id = $id");
    header("Location: manage_departments.php");
    exit();
}

// Fetch from 'categories' table
$departments = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Departments | Admin</title>
    <style>
        .form-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; border-top: 4px solid #2980b9; display: none; /* Hidden by default! */ }
        
        .form-group { margin-bottom: 15px; }
        .form-label { display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px; font-size: 0.95rem; }
        .form-control { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px; font-family: inherit; font-size: 1rem; }
        
        /* The Toggle Button Styles */
        .btn-toggle { background: #2980b9; color: white; padding: 12px 20px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 1.1rem; margin-bottom: 20px; display: inline-block; transition: 0.3s; }
        .btn-toggle:hover { background: #1f6391; }
        
        /* Hidden file input & Stylized "Upload Image" button */
        input[type="file"] { display: none; }
        .custom-file-upload { display: block; width: 100%; padding: 10px; box-sizing: border-box; cursor: pointer; background: #fdfdfd; border: 2px dashed #ddd; border-radius: 4px; color: #555; text-align: center; font-weight: bold; transition: 0.3s; margin-top: 5px; }
        .custom-file-upload:hover { border-color: #2980b9; color: #2980b9; background: #f4f9fc; }
        
        /* Image preview container */
        #imagePreviewContainer { display: none; margin-top: 15px; text-align: center; background: #f9f9f9; border: 1px solid #ddd; padding: 10px; border-radius: 4px; }
        #imagePreview { max-width: 100%; max-height: 200px; border-radius: 4px; }
    </style>
</head>
<body style="margin: 0; background: #f4f7f6;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h2 class="table-title" style="margin: 0;">📁 Manage Departments</h2>
        <button onclick="toggleForm()" class="btn-toggle">➕ Add New Department</button>
    </div>

    <?php if ($msg): ?>
        <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 20px; color: <?php echo $msg_color; ?>; font-weight: bold;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div class="form-container" id="addDepartmentForm">
        <h3 style="margin-top: 0; color: #2980b9;">Department Details</h3>
        <form method="POST" enctype="multipart/form-data">
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                
                <div class="form-group" style="flex: 1; min-width: 250px;">
                    <label class="form-label">Department Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g., Spanish Colonial Era" required>
                </div>
                
                <div class="form-group" style="flex: 1; min-width: 250px;">
                    <label class="form-label">Department Image</label>
                    
                    <input type="file" name="image" id="fileInput" accept="image/*">
                    <label for="fileInput" class="custom-file-upload">Upload Image</label>
                    
                    <div id="imagePreviewContainer">
                        <img id="imagePreview" src="#" alt="Department Preview">
                    </div>
                </div>
                
            </div>
            
            <button type="submit" name="add_department" class="btn-submit bg-category" style="margin-top: 20px; width: 100%; padding: 12px; font-size: 1.1rem; border-radius: 4px; border: none; color: white; cursor: pointer;">Save Department</button>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr><th>ID</th><th>IMAGE</th><th>NAME</th><th>ACTIONS</th></tr>
            </thead>
            <tbody>
                <?php if($departments && $departments->num_rows > 0): while($row = $departments->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td>
                        <?php if(!empty($row['image_path'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($row['image_path']); ?>" width="60" style="border-radius:4px; height: 60px; object-fit: cover;">
                        <?php else: ?>
                            <span style="color:#999; font-size: 0.8rem;">No Image</span>
                        <?php endif; ?>
                    </td>
                    <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                    <td>
                        <div style="display: flex; gap: 5px;">
                            <a href="edit_category.php?id=<?php echo $row['id']; ?>" class="action-btn btn-edit">✏️ Edit</a>
                            <a href="manage_departments.php?delete_id=<?php echo $row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete this department? Note: Ensure no artifacts are linked to it first.');">🗑️ Delete</a>
                        </div>
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
    // 1. Logic for the Dropdown Toggle Button
    function toggleForm() {
        var form = document.getElementById("addDepartmentForm");
        if (form.style.display === "none" || form.style.display === "") {
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    }

    // Keep form open if there is an error
    <?php if ($msg && $msg_color == 'red'): ?>
        document.getElementById("addDepartmentForm").style.display = "block";
    <?php endif; ?>

    // 2. Logic for the Image Preview
    const fileInput = document.getElementById('fileInput');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const imagePreview = document.getElementById('imagePreview');

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (!file.type.startsWith('image/')) {
                alert("Please select an image file.");
                return;
            }

            const reader = new FileReader();

            reader.addEventListener('load', function() {
                imagePreview.src = reader.result;
                imagePreviewContainer.style.display = 'block';
            });

            reader.readAsDataURL(file);
        } else {
            imagePreviewContainer.style.display = 'none';
            imagePreview.src = '#';
        }
    });
</script>

</body>
</html>