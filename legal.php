<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php';
include 'functions.php';

$msg = "";
$msg_color = "red";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_legal_page'])) {
    $id = (int)$_POST['id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (!empty($title) && !empty($content)) {
        $stmt = $conn->prepare("UPDATE legal_pages SET title = ?, content = ? WHERE id = ?");
        $stmt->bind_param("ssi", $title, $content, $id);
        
        if ($stmt->execute()) {
            log_activity($conn, $_SESSION['admin_id'], "Updated legal page: " . $title);
            $msg = "Legal page updated successfully!";
            $msg_color = "green";
        } else {
            $msg = "Error updating legal page: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $msg = "Please fill in all fields.";
    }
}

$query = "SELECT * FROM legal_pages";
$result = $conn->query($query);
$legal_pages = [];
while ($row = $result->fetch_assoc()) {
    $legal_pages[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Legal Pages</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div class="form-container" style="max-width: 800px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #2c3e50;">Manage Legal Pages</h2>
        <?php if ($msg): ?>
            <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo ($msg_color === 'red' ? '#c0392b' : '#27ae60'); ?>; font-weight: bold;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <?php foreach ($legal_pages as $page): ?>
            <div style="margin-bottom: 30px;">
                <h3 style="color: #2c3e50;"><?php echo htmlspecialchars($page['title']); ?></h3>
                <form action="legal.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $page['id']; ?>">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="title_<?php echo $page['id']; ?>" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Title</label>
                        <input type="text" id="title_<?php echo $page['id']; ?>" name="title" class="form-control" value="<?php echo htmlspecialchars($page['title']); ?>" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="content_<?php echo $page['id']; ?>" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Content</label>
                        <textarea id="content_<?php echo $page['id']; ?>" name="content" class="form-control" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px; min-height: 300px;"><?php echo htmlspecialchars($page['content']); ?></textarea>
                    </div>
                    <button type="submit" name="update_legal_page" class="btn-submit" style="width: 100%; padding: 15px; background: #2980b9; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer;">Update <?php echo htmlspecialchars($page['title']); ?></button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    </main>
</div>

</body>
</html>