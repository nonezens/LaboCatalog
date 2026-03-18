<?php
session_start();
include 'db.php';
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }

// Redirect back if no ID is selected
if (!isset($_GET['id'])) {
    header("Location: manage_news.php");
    exit();
}

$id = $_GET['id'];
$msg = "";
$msg_color = "red";

// Fetch the existing news post
$stmt = $conn->prepare("SELECT * FROM news_events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$news_item = $result->fetch_assoc();

if (!$news_item) {
    header("Location: manage_news.php");
    exit();
}

// --- HANDLE UPDATE ---
if (isset($_POST['update_news'])) {
    $title = $_POST['title'];
    $type = $_POST['type'];
    $content = $_POST['content'];
    
    $update_stmt = $conn->prepare("UPDATE news_events SET title = ?, type = ?, content = ? WHERE id = ?");
    if ($update_stmt) {
        $update_stmt->bind_param("sssi", $title, $type, $content, $id);
        if ($update_stmt->execute()) {
            // Success! Send them back to the manage page.
            header("Location: manage_news.php?success=1");
            exit();
        } else {
            $msg = "Error updating database: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit News | Admin</title>
    <link rel="stylesheet" href="css/admin-sidebar.css">
    <link rel="stylesheet" href="css/edit-news.css">
</head>
<body style="margin: 0; background: #f4f7f6;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <h2 class="table-title">✏️ Edit News & Events</h2>

    <?php if ($msg): ?>
        <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 20px; color: <?php echo $msg_color; ?>; font-weight: bold; max-width: 800px;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                
                <div class="form-group" style="flex: 2; min-width: 250px;">
                    <label class="form-label">Headline Title</label>
                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($news_item['title']); ?>" required>
                </div>
                
                <div class="form-group" style="flex: 1; min-width: 200px;">
                    <label class="form-label">Post Type</label>
                    <select name="type" class="form-control" required style="background: white;">
                        <option value="news" <?php echo ($news_item['type'] == 'news') ? 'selected' : ''; ?>>Museum News</option>
                        <option value="event" <?php echo ($news_item['type'] == 'event') ? 'selected' : ''; ?>>Upcoming Event</option>
                    </select>
                </div>
                
            </div>
            
            <div class="form-group">
                <label class="form-label">Full Content</label>
                <textarea name="content" class="form-control" rows="8" required><?php echo htmlspecialchars($news_item['content']); ?></textarea>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" name="update_news" class="btn-submit-form">Update Post</button>
                <a href="manage_news.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>

    </main>
</div>

</body>
</html>