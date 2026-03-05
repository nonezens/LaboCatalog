<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php'; 

$cat_result = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Departments</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <h3 class="table-title">📁 Manage Departments</h3>
    <div class="table-container">
        <table>
            <tr>
                <th>ID</th><th>Cover Image</th><th>Department Name</th><th>Actions</th>
            </tr>
            <?php if($cat_result->num_rows > 0): while($cat_row = $cat_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $cat_row['id']; ?></td>
                <td><img src="uploads/<?php echo $cat_row['image_path']; ?>" width="60" height="60" style="object-fit:cover; border-radius:4px;"></td>
                <td><strong><?php echo htmlspecialchars($cat_row['name']); ?></strong></td>
                <td>
                    <a href="edit_category.php?id=<?php echo $cat_row['id']; ?>" class="action-btn btn-edit">✎ Edit</a>
                    <a href="delete_category.php?id=<?php echo $cat_row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('WARNING: Deleting this department will permanently delete ALL artifacts inside it!');">🗑️ Delete</a>
                </td>
            </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="4" style="text-align: center; padding: 20px;">No departments found.</td></tr>
            <?php endif; ?>
        </table>
    </div>

    </main>
</div>
</body>
</html>