<?php
session_start();
require_once dirname(__DIR__, 2) . '/includes/db.php';
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }

$msg = "";
$msg_color = "red";

// --- ADD NEWS ---
if (isset($_POST['add_news'])) {
    $title = $_POST['title'];
    $type = $_POST['type'];
    $content = $_POST['content'];
    
    $stmt = $conn->prepare("INSERT INTO news_events (title, type, content) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sss", $title, $type, $content);
        if ($stmt->execute()) {
            header("Location: manage_news.php?success=1");
            exit();
        } else {
            $msg = "Database Error: " . $stmt->error;
        }
    } else {
        $msg = "SQL Error: " . $conn->error;
    }
}

if (isset($_GET['success'])) {
    $msg = "News/Event successfully posted or updated!";
    $msg_color = "green";
}

// --- DELETE NEWS ---
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $conn->query("DELETE FROM news_events WHERE id = $id");
    header("Location: manage_news.php");
    exit();
}

$news = $conn->query("SELECT * FROM news_events ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage News | Admin</title>
    <style>
        .form-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; border-top: 4px solid #e67e22; display: none; }
        .form-group { margin-bottom: 15px; }
        .form-label { display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px; font-size: 0.95rem; }
        .form-control { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px; font-family: inherit; font-size: 1rem; }
        .btn-toggle { background: #e67e22; color: white; padding: 12px 20px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 1.1rem; margin-bottom: 20px; display: inline-block; transition: 0.3s; }
        .btn-toggle:hover { background: #cf711f; }
        .btn-submit-form { width: 100%; padding: 12px; background: #e67e22; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        .btn-submit-form:hover { background: #cf711f; }
    </style>
</head>
<body style="margin: 0; background: #f4f7f6;">

    <?php include dirname(__DIR__, 2) . '/templates/components/header.php'; ?>
    <?php include dirname(__DIR__, 2) . '/templates/components/admin_sidebar.php'; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h2 class="table-title" style="margin: 0;">📰 Manage News & Events</h2>
        <button onclick="toggleForm()" class="btn-toggle">➕ Post News / Event</button>
    </div>

    <?php if ($msg): ?>
        <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 20px; color: <?php echo $msg_color; ?>; font-weight: bold;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div class="form-container" id="addNewsForm">
        <h3 style="margin-top: 0; color: #e67e22;">Post Details</h3>
        <form method="POST">
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <div class="form-group" style="flex: 2; min-width: 250px;">
                    <label class="form-label">Headline Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="form-group" style="flex: 1; min-width: 200px;">
                    <label class="form-label">Post Type</label>
                    <select name="type" class="form-control" required style="background: white;">
                        <option value="news">Museum News</option>
                        <option value="event">Upcoming Event</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Full Content</label>
                <textarea name="content" class="form-control" rows="5" required></textarea>
            </div>
            <button type="submit" name="add_news" class="btn-submit-form">Post Update</button>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr><th>ID</th><th>TYPE</th><th>TITLE</th><th>ACTIONS</th></tr>
            </thead>
            <tbody>
                <?php if($news && $news->num_rows > 0): while($row = $news->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td>
                        <span style="background: #eee; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; text-transform: uppercase; font-weight: bold; color: #555;">
                            <?php echo htmlspecialchars($row['type']); ?>
                        </span>
                    </td>
                    <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                    <td>
                        <div style="display: flex; gap: 5px;">
                            <a href="edit_news.php?id=<?php echo $row['id']; ?>" class="action-btn btn-edit">✏️ Edit</a>
                            <a href="manage_news.php?delete_id=<?php echo $row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete this post?');">🗑️ Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="4" style="text-align: center; padding: 20px;">No news posted yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    </main>
</div>

<script>
    function toggleForm() {
        var form = document.getElementById("addNewsForm");
        if (form.style.display === "none" || form.style.display === "") {
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    }
    <?php if ($msg && $msg_color == 'red'): ?>
        document.getElementById("addNewsForm").style.display = "block";
    <?php endif; ?>
</script>

</body>
</html>