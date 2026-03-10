<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php'; 

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];
    
    $new_status = 'pending';
    if ($action == 'approve') $new_status = 'approved';
    if ($action == 'reject') $new_status = 'rejected';

    $stmt = $conn->prepare("UPDATE comments SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $id);
    $stmt->execute();
    header("Location: manage_comments.php");
    exit();
}

$query = "SELECT comments.*, exhibits.title AS exhibit_title FROM comments LEFT JOIN exhibits ON comments.exhibit_id = exhibits.id ORDER BY comments.created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Comments</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Exhibit</th><th>Name</th><th>Comment</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if($result && $result->num_rows > 0): while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['exhibit_title']); ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['comment']); ?></td>
                <td>
                    <?php 
                        if($row['status'] == 'pending') echo '<span class="badge badge-pending">Pending</span>';
                        elseif($row['status'] == 'approved') echo '<span class="badge badge-approved">Approved</span>';
                        else echo '<span class="badge badge-rejected">Rejected</span>';
                    ?>
                </td>
                <td>
                    <?php if($row['status'] == 'pending'): ?>
                        <a href="manage_comments.php?id=<?php echo $row['id']; ?>&action=approve" class="action-btn btn-approve">✔️ Approve</a>
                        <a href="manage_comments.php?id=<?php echo $row['id']; ?>&action=reject" class="action-btn btn-reject">❌ Reject</a>
                    <?php endif; ?>
                    <a href="delete_comment.php?id=<?php echo $row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete this comment?');">🗑️ Delete</a>
                </td>
            </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="6" style="text-align: center; padding: 20px;">No comments found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    </main>
</div>

</body>
</html>