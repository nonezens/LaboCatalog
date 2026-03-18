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
    <link rel="stylesheet" href="css/admin-sidebar.css">
    <link rel="stylesheet" href="css/manage-departments.css">
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

<script src="js/manage-departments.js"></script>

</body>
</html>