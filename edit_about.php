<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php';
include 'functions.php';

$msg = "";
$msg_color = "red";

// Check if about_us table exists
$table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'about_us'");
if (!$table_exists || mysqli_num_rows($table_exists) == 0) {
    // Create the table if it doesn't exist
    $create_table = "CREATE TABLE IF NOT EXISTS about_us (
        id INT(11) PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL
    )";
    mysqli_query($conn, $create_table);
    
    // Insert default content if table is empty
    $check_empty = mysqli_query($conn, "SELECT * FROM about_us WHERE id = 1");
    if (mysqli_num_rows($check_empty) == 0) {
        mysqli_query($conn, "INSERT INTO about_us (id, title, content) VALUES (1, 'About Us', 'Welcome to our museum.')");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_about'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (!empty($title) && !empty($content)) {
        $stmt = $conn->prepare("UPDATE about_us SET title = ?, content = ? WHERE id = 1");
        $stmt->bind_param("ss", $title, $content);
        
        if ($stmt->execute()) {
            log_activity($conn, $_SESSION['admin_id'], "Updated the About Us page");
            $msg = "About Us page updated successfully!";
            $msg_color = "green";
        } else {
            $msg = "Error updating About Us page: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $msg = "Please fill in all fields.";
    }
}

$query = "SELECT * FROM about_us WHERE id = 1";
$result = mysqli_query($conn, $query);
$about_us = $result ? mysqli_fetch_assoc($result) : ['title' => 'About Us', 'content' => 'Welcome to our museum.'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit About Us</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div class="form-container" style="max-width: 800px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #2c3e50;">Edit About Us</h2>
        <?php if ($msg): ?>
            <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo ($msg_color === 'red' ? '#c0392b' : '#27ae60'); ?>; font-weight: bold;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>
        <form action="edit_about.php" method="POST">
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="title" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Title</label>
                <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($about_us['title']); ?>" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="content" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Content</label>
                <textarea id="content" name="content" class="form-control" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px; min-height: 300px;"><?php echo htmlspecialchars($about_us['content']); ?></textarea>
            </div>
            <button type="submit" name="update_about" class="btn-submit" style="width: 100%; padding: 15px; background: #2980b9; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer;">Update About Us</button>
        </form>
    </div>

    </main>
</div>

</body>
</html>

