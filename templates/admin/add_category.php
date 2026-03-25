<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
require_once dirname(__DIR__, 2) . '/includes/db.php'; 

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
    <style>
        .form-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 600px; margin: 0 auto; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px; font-size: 0.95rem; }
        .form-control { width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px; font-family: inherit; font-size: 1rem; transition: border-color 0.3s; }
        .form-control:focus { border-color: #c5a059; outline: none; }
        .btn-submit { width: 100%; padding: 15px; background: #27ae60; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer; transition: 0.3s; }
        .btn-submit:hover { background: #219653; transform: translateY(-2px); }
        
        /* Mobile Adjustments */
        @media (max-width: 768px) {
            .form-container { padding: 20px; border-radius: 0; box-shadow: none; border-top: 2px solid #c5a059; }
        }
    </style>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: 'Segoe UI', Tahoma, sans-serif;">

    <?php include dirname(__DIR__, 2) . '/templates/components/header.php'; ?>
    <?php include dirname(__DIR__, 2) . '/templates/components/admin_sidebar.php'; ?>

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