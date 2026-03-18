<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php'; 

$msg = "";
$msg_color = "red";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    
    // Image Upload Logic
    $image_path = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        // Create the directory if it doesn't exist
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        
        // Create a unique filename to prevent overwriting
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
    } else {
        $msg = "Please provide a department name and a cover image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Add Department | Admin</title>
    <link rel="stylesheet" href="css/admin-sidebar.css">
    <link rel="stylesheet" href="css/add-category.css">
</head>
<body style="background: #f4f7f6; margin: 0; font-family: 'Segoe UI', Tahoma, sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

        <div style="margin-bottom: 20px;">
            <h2 style="color: #2c3e50; margin-top: 0; font-size: 2rem;">➕ Add New Department</h2>
            <p style="color: #7f8c8d;">Create a new category wing for your museum catalog.</p>
        </div>

        <div class="form-container">
            <?php if ($msg): ?>
                <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo $msg_color; ?>; font-weight: bold;">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label class="form-label">Department Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g., Ancient History" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Cover Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*" required style="padding: 9px; background: #f9f9f9;">
                </div>

                <button type="submit" name="add_category" class="btn-submit">Save Department</button>
            </form>
        </div>

    </main>
</div>
</body>
</html>