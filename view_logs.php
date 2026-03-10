<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php'; 

$query = "SELECT activity_logs.*, users.username FROM activity_logs LEFT JOIN users ON activity_logs.user_id = users.id ORDER BY activity_logs.created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>User</th><th>Action</th><th>Date</th>
                </tr>
            </thead>
            <tbody>
            <?php if($result && $result->num_rows > 0): while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['action']); ?></td>
                <td><?php echo date("M d, Y g:i A", strtotime($row['created_at'])); ?></td>
            </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="4" style="text-align: center; padding: 20px;">No activity logs found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    </main>
</div>

</body>
</html>
