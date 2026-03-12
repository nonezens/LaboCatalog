<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php'; 

// Handle Deletion
if (isset($_GET['delete_id'])) {
    $del_stmt = $conn->prepare("DELETE FROM news_events WHERE id = ?");
    $del_stmt->bind_param("i", $_GET['delete_id']);
    $del_stmt->execute();
    header("Location: manage_news.php"); exit();
}

// Handle Add New News/Event
if (isset($_POST['add_news'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $type = $_POST['type'];
    $event_date = !empty($_POST['event_date']) ? $_POST['event_date'] : NULL;
    
    // Handle image upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $file_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $file_name;
        }
    }
    
    $stmt = $conn->prepare("INSERT INTO news_events (title, content, type, event_date, image_path, date_posted) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssss", $title, $content, $type, $event_date, $image_path);
    $stmt->execute();
    header("Location: manage_news.php"); exit();
}

$result = $conn->query("SELECT * FROM news_events ORDER BY date_posted DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage News | Admin</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="admin-page admin-body">
    <?php include 'header.php'; ?>


    <div class="admin-layout">
        <?php include 'admin_sidebar.php'; ?>

        <main class="main-content">
            <h3 class="table-title">📰 Manage News & Events</h3>
            
            <!-- Add New News Button -->
            <div style="margin-bottom: 20px;">
                <button onclick="toggleAddForm()" id="toggle-add-news-btn" class="btn-add bg-exhibit" style="cursor: pointer; border: none;">➕ Add New News/Event</button>
            </div>
            
            <!-- Add New News Form (Hidden by default) -->
            <div id="addForm" class="form-toggle" style="display: none; opacity: 0; background: white; padding: 25px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h4 style="margin-top: 0; color: #2c3e50;">Add New News/Event</h4>
                <form method="POST" enctype="multipart/form-data" style="display: grid; gap: 15px; max-width: 600px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">Title:</label>
                        <input type="text" name="title" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">Type:</label>
                        <select name="type" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
                            <option value="news">📰 News</option>
                            <option value="event">📅 Event</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">Event Date (for events):</label>
                        <input type="date" name="event_date" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">Image:</label>
                        <input type="file" name="image" accept="image/*" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">Content:</label>
                        <textarea name="content" rows="5" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; resize: vertical;"></textarea>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" name="add_news" class="btn-add bg-exhibit" style="cursor: pointer; border: none;">💾 Save</button>
                        <button type="button" onclick="toggleAddForm()" class="btn-add" style="background: #95a5a6; cursor: pointer; border: none;">❌ Cancel</button>
                    </div>
                </form>
            </div>

            <script>
            function toggleAddForm() {
                var form = document.getElementById('addForm');
                if (form.style.display === 'none' || form.style.display === '') {
                    form.style.display = 'block';
                    // Small delay to allow display:block to apply before opacity transition
                    setTimeout(function() {
                        form.style.opacity = '1';
                        form.style.transform = 'translateY(0)';
                    }, 10);
                } else {
                    form.style.opacity = '0';
                    form.style.transform = 'translateY(-10px)';
                    setTimeout(function() {
                        form.style.display = 'none';
                    }, 300);
                }
            }
            </script>
            
            <div class="table-container">
                <table>
                    <tr><th>Date Posted</th><th>Type</th><th>Title</th><th>Event Date</th><th>Actions</th></tr>
                    <?php if($result->num_rows > 0): while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td style="font-size: 0.9rem; color: #777;"><?php echo date("M d, Y", strtotime($row['date_posted'])); ?></td>
                        <td>
                            <span class="badge" style="background: <?php echo $row['type'] == 'event' ? '#8e44ad' : '#3498db'; ?>;">
                                <?php echo strtoupper($row['type']); ?>
                            </span>
                        </td>
                        <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                        <td><?php echo $row['event_date'] ? date("M d, Y", strtotime($row['event_date'])) : '-'; ?></td>
                        <td>
                            <a href="manage_news.php?delete_id=<?php echo $row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete this post?');">🗑️ Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="5" style="text-align: center; padding: 20px;">No news or events posted yet.</td></tr>
                    <?php endif; ?>
                </table>
            </div>
        </main>
    </div>
    <script src="js/admin.js"></script>
</body>
</html>
