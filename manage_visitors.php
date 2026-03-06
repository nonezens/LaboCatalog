<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php'; 

// Check if a specific month was selected in the filter
$selected_month = isset($_GET['filter_month']) ? $_GET['filter_month'] : '';

// Build the query
$query = "SELECT * FROM guests";

// If a month is selected (Format: YYYY-MM), filter the results!
if ($selected_month != '') {
    // We use DATE_FORMAT to match the YYYY-MM string
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
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;">
        <h3 class="table-title" style="margin: 0;">👥 Visitor Log & Access Requests</h3>
        
        <div style="display: flex; gap: 15px; align-items: center;">
            <form method="GET" style="display: flex; gap: 10px; align-items: center; background: white; padding: 10px; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <label style="font-size: 0.9rem; font-weight: bold; color: #2c3e50;">Filter by Month:</label>
                <input type="month" name="filter_month" value="<?php echo htmlspecialchars($selected_month); ?>" style="padding: 6px; border: 1px solid #ddd; border-radius: 4px;">
                <button type="submit" class="action-btn" style="background: #2980b9;">🔍 Filter</button>
                <?php if($selected_month != ''): ?>
                    <a href="manage_visitors.php" class="action-btn" style="background: #95a5a6;">Clear</a>
                <?php endif; ?>
            </form>
            
            <a href="export_visitors.php?filter_month=<?php echo urlencode($selected_month); ?>" class="action-btn" style="background: #27ae60; padding: 12px 15px; font-size: 0.95rem;">📥 Download Excel</a>
        </div>
    </div>

    <div class="table-container">
        <table>
            <tr>
                <th>Date</th><th>Guest Name & Contact</th><th>Demographics</th><th>Purpose</th><th>Status</th><th>Actions</th>
            </tr>
            <?php if($guest_result->num_rows > 0): while($guest = $guest_result->fetch_assoc()): ?>
            <tr>
                <td style="font-size: 0.9rem;"><?php echo date("M d, Y g:i A", strtotime($guest['visit_date'])); ?></td>
                <td>
                    <strong><?php echo htmlspecialchars($guest['guest_name']); ?></strong><br>
                    <span style="color:#777; font-size:0.9rem;">📞 <?php echo htmlspecialchars($guest['contact_no']); ?></span>
                </td>
                <td style="font-size: 0.9rem;">
                    Gender: <?php echo htmlspecialchars($guest['gender']); ?><br>
                    Nat: <?php echo htmlspecialchars($guest['nationality']); ?><br>
                    From: <?php echo htmlspecialchars($guest['residence']); ?>
                </td>
                <td style="font-size: 0.9rem;"><?php echo htmlspecialchars($guest['purpose']); ?></td>
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
                    <a href="delete_guest.php?id=<?php echo $guest['id']; ?>" class="action-btn btn-delete" style="display: block;" onclick="return confirm('Delete this record?');">🗑️ Delete</a>
                </td>
            </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="6" style="text-align: center; padding: 20px;">No visitors found for this period.</td></tr>
            <?php endif; ?>
        </table>
    </div>

    </main>
</div>
</body>
</html>