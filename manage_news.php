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

$result = $conn->query("SELECT * FROM news_events ORDER BY date_posted DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage News | Admin</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">
    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <h3 class="table-title">📰 Manage News & Events</h3>
    <div class="table-container">
        <table>
            <tr><th>Date Posted</th><th>Type</th><th>Title</th><th>Event Date</th><th>Actions</th></tr>
            <?php if($result->num_rows > 0): while($row = $result->fetch_assoc()): ?>
            <tr>
                <td style="font-size: 0.9rem; color: #777;"><?php echo date("M d, Y", strtotime($row['date_posted'])); ?></td>
                <td>
                    <span class="badge" style="background: <?php echo $row['type'] == 'event' ? '#8e44ad' : '#3498db'; ?>">
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
</body>
</html>