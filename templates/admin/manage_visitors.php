<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
require_once dirname(__DIR__, 2) . '/includes/db.php'; 

$selected_month = isset($_GET['filter_month']) ? $_GET['filter_month'] : '';
$query = "SELECT * FROM guests";
if ($selected_month != '') {
    $query .= " WHERE DATE_FORMAT(visit_date, '%Y-%m') = '" . $conn->real_escape_string($selected_month) . "'";
}
$query .= " ORDER BY visit_date DESC";
$guest_result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Visitors | Admin</title>
</head>
<body style="background: #f4f7f6; margin: 0;">

    <?php include dirname(__DIR__, 2) . '/templates/components/header.php'; ?>
    <?php include dirname(__DIR__, 2) . '/templates/components/admin_sidebar.php'; ?>

    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <h3 class="table-title" style="margin: 0;">👥 Visitor Log</h3>
        
        <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
            <form method="GET" style="display: flex; gap: 10px; align-items: center; background: white; padding: 10px; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <label style="font-size: 0.9rem; font-weight: bold; color: #2c3e50;">Filter by Month:</label>
                <input type="month" name="filter_month" value="<?php echo htmlspecialchars($selected_month); ?>" style="padding: 6px; border: 1px solid #ddd; border-radius: 4px;">
                <button type="submit" class="action-btn" style="background: #2980b9; color: white; border: none; padding: 7px 12px; border-radius: 4px; cursor: pointer;">🔍 Filter</button>
                <?php if($selected_month != ''): ?>
                    <a href="manage_visitors.php" class="action-btn" style="background: #95a5a6; color: white; text-decoration: none; padding: 7px 12px; border-radius: 4px;">Clear</a>
                <?php endif; ?>
            </form>
            <a href="export_visitors.php?filter_month=<?php echo urlencode($selected_month); ?>" class="action-btn" style="background: #27ae60; color: white; text-decoration: none; padding: 12px 15px; font-size: 0.95rem; border-radius: 4px;">📥 Download Excel</a>
        </div>
    </div>

    <div class="table-container">
        <table style="width: 100%; border-collapse: collapse; background: white; text-align: left;">
            <tr style="background: #34495e; color: white;">
                <th style="padding: 15px;">Date</th>
                <th style="padding: 15px;">Guest / Rep Name</th>
                <th style="padding: 15px;">Type / Org</th>
                <th style="padding: 15px; text-align: center;">Total Pax</th>
                <th style="padding: 15px;">Demographics</th>
                <th style="padding: 15px;">Actions</th>
            </tr>
            <?php if($guest_result->num_rows > 0): while($guest = $guest_result->fetch_assoc()): ?>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 15px; font-size: 0.85rem; color: #555;">
                    <?php echo date("M d, Y", strtotime($guest['visit_date'])); ?><br>
                    <?php echo date("g:i A", strtotime($guest['visit_date'])); ?>
                </td>
                <td style="padding: 15px;">
                    <strong style="color: #2c3e50;"><?php echo htmlspecialchars($guest['guest_name']); ?></strong><br>
                    <span style="color:#777; font-size:0.85rem;">📞 <?php echo htmlspecialchars($guest['contact_no']); ?></span>
                </td>
                <td style="padding: 15px;">
                    <?php if(isset($guest['visitor_type']) && $guest['visitor_type'] == 'Group'): ?>
                        <span style="background: #e8f8f5; color: #27ae60; padding: 3px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: bold;">Group</span><br>
                        <span style="font-size: 0.9rem; font-weight: bold; color: #333;"><?php echo htmlspecialchars($guest['organization']); ?></span>
                    <?php else: ?>
                        <span style="background: #ebf5fb; color: #2980b9; padding: 3px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: bold;">Individual</span>
                    <?php endif; ?>
                </td>
                <td style="padding: 15px; text-align: center;">
                    <strong style="font-size: 1.1rem; color: #c5a059;"><?php echo isset($guest['headcount']) ? $guest['headcount'] : 1; ?> Total</strong><br>
                    <?php if(isset($guest['visitor_type']) && $guest['visitor_type'] == 'Group'): ?>
                        <span style="font-size: 0.8rem; color: #7f8c8d; font-weight: bold;">
                            (<?php echo isset($guest['male_count']) ? $guest['male_count'] : 0; ?> M / <?php echo isset($guest['female_count']) ? $guest['female_count'] : 0; ?> F)
                        </span>
                    <?php endif; ?>
                </td>
                <td style="padding: 15px; font-size: 0.85rem; color: #444; line-height: 1.5;">
                    <strong>Rep Gen:</strong> <?php echo htmlspecialchars($guest['gender']); ?><br>
                    <strong>Nat:</strong> <?php echo htmlspecialchars($guest['nationality']); ?><br>
                    <strong>Loc:</strong> <?php echo htmlspecialchars($guest['residence']); ?>
                </td>
                <td style="padding: 15px;">
                    <a href="delete_guest.php?id=<?php echo $guest['id']; ?>" class="action-btn btn-delete" style="background: #e74c3c; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 0.85rem;" onclick="return confirm('Delete this record?');">🗑️ Delete</a>
                </td>
            </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="6" style="text-align: center; padding: 30px; color: #7f8c8d;">No visitors found.</td></tr>
            <?php endif; ?>
        </table>
    </div>

    </main>
</div>
</body>
</html>