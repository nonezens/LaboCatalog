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
    
    <!-- CSS -->
<link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/manage_departments.css">

</head>
<body class="admin-page admin-body">

    <?php include 'header.php'; ?>


    <div class="admin-layout">
        <?php include 'admin_sidebar.php'; ?>

        <main class="main-content">
            <div class="page-header">
                <h3 class="table-title">📁 Manage Departments</h3>
                <button id="toggle-form-btn" class="toggle-form-btn">➕ Add New Department</button>
            </div>

            <div id="add-category-wrapper" class="form-container-wrapper <?php echo !empty($msg) ? 'form-visible' : ''; ?>">
                <div class="form-container department-form">
                    <?php if ($msg): ?>
                        <div class="message-box <?php echo $msg_color === 'green' ? 'success' : ''; ?>" style="border-left-color: <?php echo $msg_color === 'green' ? '#27ae60' : '#c0392b'; ?>; color: <?php echo $msg_color === 'green' ? '#27ae60' : '#c0392b'; ?>;">
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
                            <img id="image-preview" src="#" alt="Image Preview">
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

    <!-- JS -->
    <script src="js/admin.js"></script>

</body>
</html>
</xai:function_call name="edit_file">

<xai:function_call name="edit_file">
<parameter name="path">manage_news.php
