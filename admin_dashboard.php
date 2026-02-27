<?php
// 1. Security Check
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php"); exit(); 
}

include 'db.php'; 

// 2. Fetch all exhibits
$query = "SELECT exhibits.*, categories.name AS cat_name FROM exhibits LEFT JOIN categories ON exhibits.category_id = categories.id ORDER BY exhibits.id DESC";
$result = $conn->query($query);
$total_exhibits = $result->num_rows;

// 3. Fetch all categories
$cat_query = "SELECT * FROM categories ORDER BY id DESC";
$cat_result = $conn->query($cat_query);
$total_categories = $cat_result->num_rows;

// 4. Fetch all guest requests
$guest_query = "SELECT * FROM guests ORDER BY visit_date DESC";
$guest_result = $conn->query($guest_query);
$total_guests = $guest_result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Museum Labo Catalog</title>
    <style>
        body { background: #f4f7f6; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .dashboard { max-width: 1200px; margin: 40px auto; padding: 20px; }
        
        /* Action Bar & Stats */
        .dashboard-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .dashboard-header h2 { margin: 0; color: #2c3e50; font-size: 1.8rem; }
        .stats { display: flex; gap: 20px; font-size: 1rem; color: #555; font-weight: bold; margin-top: 10px; }
        .stats span { color: #e67e22; }
        
        .action-buttons { display: flex; gap: 15px; }
        .btn-add { padding: 10px 20px; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: 0.3s; display: inline-block; }
        .btn-add:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .bg-exhibit { background: #27ae60; }
        .bg-category { background: #2980b9; }

        /* Table Styles */
        .table-title { color: #2c3e50; margin-top: 40px; margin-bottom: 15px; border-bottom: 2px solid #c5a059; display: inline-block; padding-bottom: 5px; font-size: 1.5rem; }
        .table-container { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 40px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; vertical-align: middle; }
        th { background: #2c3e50; color: white; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; }
        tr:hover { background: #f9f9f9; }
        
        /* Action Buttons */
        .action-btn { padding: 8px 12px; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85rem; transition: 0.3s; display: inline-block; text-align: center; }
        .action-btn:hover { opacity: 0.8; }
        .btn-edit { background: #f39c12; margin-right: 5px; }
        .btn-delete { background: #e74c3c; }
        .btn-approve { background: #2ecc71; margin-bottom: 5px; display: block; }
        .btn-reject { background: #e74c3c; display: block; }
        
        /* Status Badges */
        .badge { padding: 4px 10px; border-radius: 12px; font-weight: bold; font-size: 0.8rem; color: white; display: inline-block; }
        .badge-pending { background: #f1c40f; color: #333; }
        .badge-approved { background: #2ecc71; }
        .badge-rejected { background: #e74c3c; }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="dashboard">
        
        <div class="dashboard-header">
            <div>
                <h2>⚙️ Admin Control Center</h2>
                <div class="stats">
                    <p>Total Departments: <span><?php echo $total_categories; ?></span></p>
                    <p>Total Artifacts: <span><?php echo $total_exhibits; ?></span></p>
                    <p>Total Visitors: <span><?php echo $total_guests; ?></span></p>
                </div>
            </div>
            <div class="action-buttons">
                <a href="add_exhibit.php" class="btn-add bg-exhibit">➕ Add Exhibit</a>
                <a href="add_category.php" class="btn-add bg-category">➕ Add Category</a>
            </div>
        </div>
        
        <h3 class="table-title">👥 Visitor Log & Access Requests</h3>
        <div class="table-container">
            <table>
                <tr>
                    <th>Date Registered</th>
                    <th>Guest Name & Contact</th>
                    <th>Demographics</th>
                    <th>Purpose & Duration</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php if($total_guests > 0): ?>
                    <?php while($guest = $guest_result->fetch_assoc()): ?>
                    <tr>
                        <td style="font-size: 0.9rem; color: #555;"><?php echo date("M d, Y g:i A", strtotime($guest['visit_date'])); ?></td>
                        <td>
                            <strong style="font-size: 1.1rem; color: #2c3e50;"><?php echo htmlspecialchars($guest['guest_name']); ?></strong><br>
                            <span style="color:#777; font-size: 0.9rem;">📞 <?php echo htmlspecialchars($guest['contact_no']); ?></span>
                        </td>
                        <td style="font-size: 0.9rem;">
                            <strong>Gender:</strong> <?php echo htmlspecialchars($guest['gender']); ?><br>
                            <strong>Nationality:</strong> <?php echo htmlspecialchars($guest['nationality']); ?><br>
                            <strong>From:</strong> <?php echo htmlspecialchars($guest['residence']); ?>
                        </td>
                        <td style="font-size: 0.9rem;">
                            <strong>Reason:</strong> <?php echo htmlspecialchars($guest['purpose']); ?><br>
                            <strong>Staying:</strong> <?php echo $guest['num_days']; ?> day(s)
                        </td>
                        <td>
                            <?php 
                                if($guest['status'] == 'pending') echo '<span class="badge badge-pending">Pending</span>';
                                elseif($guest['status'] == 'approved') echo '<span class="badge badge-approved">Approved</span>';
                                else echo '<span class="badge badge-rejected">Rejected</span>';
                            ?>
                        </td>
                        <td>
                            <?php if($guest['status'] == 'pending'): ?>
                                <a href="manage_guest.php?id=<?php echo $guest['id']; ?>&action=approve" class="action-btn btn-approve">✔️ Approve</a>
                                <a href="manage_guest.php?id=<?php echo $guest['id']; ?>&action=reject" class="action-btn btn-reject" style="margin-bottom: 5px;">❌ Reject</a>
                            <?php else: ?>
                                <span style="color: #95a5a6; font-style: italic; font-size: 0.9rem; display: block; margin-bottom: 5px;">Processed</span>
                            <?php endif; ?>
                            
                            <a href="delete_guest.php?id=<?php echo $guest['id']; ?>" class="action-btn btn-delete" style="display: block;" onclick="return confirm('Are you sure you want to permanently delete this visitor record?');">🗑️ Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align: center; padding: 20px; color: #7f8c8d;">No visitors have registered yet.</td></tr>
                <?php endif; ?>
            </table>
        </div>

        <h3 class="table-title">🖼️ Manage Artifacts</h3>
        <div class="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Department</th>
                    <th>Actions</th>
                </tr>
                <?php if($total_exhibits > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><img src="uploads/<?php echo $row['image_path']; ?>" width="60" height="60" style="object-fit:cover; border-radius:4px;"></td>
                        <td><strong style="color: #2c3e50;"><?php echo htmlspecialchars($row['title']); ?></strong></td>
                        <td><?php echo $row['cat_name'] ? htmlspecialchars($row['cat_name']) : '<em style="color:#e74c3c;">Uncategorized</em>'; ?></td>
                        <td>
                            <a href="edit_exhibit.php?id=<?php echo $row['id']; ?>" class="action-btn btn-edit">✎ Edit</a>
                            <a href="delete_exhibit.php?id=<?php echo $row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Are you sure you want to delete this artifact?');">🗑️ Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align: center; padding: 20px; color: #7f8c8d;">No artifacts found. Start adding some!</td></tr>
                <?php endif; ?>
            </table>
        </div>

        <h3 class="table-title">📁 Manage Departments (Categories)</h3>
        <div class="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Cover Image</th>
                    <th>Department Name</th>
                    <th>Actions</th>
                </tr>
                <?php if($total_categories > 0): ?>
                    <?php while($cat_row = $cat_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $cat_row['id']; ?></td>
                        <td><img src="uploads/<?php echo $cat_row['image_path']; ?>" width="60" height="60" style="object-fit:cover; border-radius:4px;"></td>
                        <td><strong style="color: #2c3e50;"><?php echo htmlspecialchars($cat_row['name']); ?></strong></td>
                        <td>
                            <a href="edit_category.php?id=<?php echo $cat_row['id']; ?>" class="action-btn btn-edit">✎ Edit</a>
                            <a href="delete_category.php?id=<?php echo $cat_row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('WARNING: Are you absolutely sure? Deleting this department will permanently delete ALL artifacts inside it!');">🗑️ Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align: center; padding: 20px; color: #7f8c8d;">No departments found. Create one first!</td></tr>
                <?php endif; ?>
            </table>
        </div>

    </div>

</body>
</html>