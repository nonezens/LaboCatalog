<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
require_once dirname(__DIR__, 2) . '/includes/db.php'; 

$msg = ""; $msg_color = "red";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_news'])) {
    $title = trim($_POST['title']);
    $type = $_POST['type'];
    $event_date = !empty($_POST['event_date']) ? $_POST['event_date'] : NULL;
    $content = trim($_POST['content']);
    
    // Image Upload
    $image_path = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $image_path = time() . '_' . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image_path);
    }

    if (!empty($title) && !empty($content)) {
        $stmt = $conn->prepare("INSERT INTO news_events (title, content, image_path, type, event_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $title, $content, $image_path, $type, $event_date);
        
        if ($stmt->execute()) {
            $msg = "Successfully posted!";
            $msg_color = "green";
        } else { $msg = "Database error."; }
    } else { $msg = "Please fill in the title and content."; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add News | Admin</title>
    <style>
        .form-container { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 800px; margin: 0 auto; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: 1 / -1; }
        .form-label { display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px; font-size: 0.95rem; }
        .form-control { width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px; font-family: inherit; font-size: 1rem; }
        textarea.form-control { resize: vertical; min-height: 150px; }
        .btn-submit { width: 100%; padding: 15px; background: #e67e22; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        .btn-submit:hover { background: #d35400; }
        @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } .form-container { padding: 20px; border-top: 2px solid #e67e22; } }
    </style>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: 'Segoe UI', Tahoma, sans-serif;">
    <?php include dirname(__DIR__, 2) . '/templates/components/header.php'; ?>
    <?php include dirname(__DIR__, 2) . '/templates/components/admin_sidebar.php'; ?>
        <div style="margin-bottom: 20px;">
            <h2 style="color: #2c3e50; margin-top: 0; font-size: 2rem;">📰 Add News or Event</h2>
        </div>
        <div class="form-container">
            <?php if ($msg) echo "<div style='background: #f8f9fa; border-left: 4px solid $msg_color; padding: 15px; margin-bottom: 25px; color: $msg_color; font-weight: bold;'>$msg</div>"; ?>
            <form method="POST" enctype="multipart/form-data" class="form-grid">
                <div class="full-width"><label class="form-label">Headline / Title *</label><input type="text" name="title" class="form-control" required></div>
                <div>
                    <label class="form-label">Type</label>
                    <select name="type" class="form-control" style="background: white;" required>
                        <option value="news">📰 General News</option>
                        <option value="event">📅 Upcoming Event</option>
                    </select>
                </div>
                <div><label class="form-label">Event Date (If applicable)</label><input type="date" name="event_date" class="form-control"></div>
                <div class="full-width"><label class="form-label">Cover Image</label><input type="file" name="image" class="form-control" accept="image/*" style="padding: 9px; background: #f9f9f9;"></div>
                <div class="full-width"><label class="form-label">Full Content *</label><textarea name="content" class="form-control" required></textarea></div>
                <div class="full-width"><button type="submit" name="add_news" class="btn-submit">Publish Post</button></div>
            </form>
        </div>
    </main>
</div>
</body>
</html>