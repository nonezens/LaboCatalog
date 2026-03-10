<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php'; 

// Check filters
$selected_month = isset($_GET['filter_month']) ? $_GET['filter_month'] : '';
$filter_gender = isset($_GET['filter_gender']) ? $_GET['filter_gender'] : '';
$filter_recent = isset($_GET['filter_recent']) ? $_GET['filter_recent'] : '';

// Build the query with multiple filters
$query = "SELECT * FROM guests";
$conditions = [];

if ($selected_month != '') {
    $conditions[] = "DATE_FORMAT(visit_date, '%Y-%m') = '" . $conn->real_escape_string($selected_month) . "'";
}

if ($filter_gender != '') {
    $conditions[] = "gender = '" . $conn->real_escape_string($filter_gender) . "'";
}

if ($filter_recent == 'today') {
    $conditions[] = "DATE(visit_date) = CURDATE()";
} elseif ($filter_recent == 'week') {
    $conditions[] = "visit_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
} elseif ($filter_recent == 'month') {
    $conditions[] = "visit_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
}

if (count($conditions) > 0) {
    $query .= " WHERE " . implode(" AND ", $conditions);
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

    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <h3 class="table-title" style="margin: 0;">👥 Visitor Log & Access Requests</h3>
        
        <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
            <!-- Filters -->
            <form method="GET" style="display: flex; gap: 10px; align-items: center; background: white; padding: 10px; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); flex-wrap: wrap;">
                <!-- Month Filter -->
                <div style="display: flex; align-items: center; gap: 5px;">
                    <label style="font-size: 0.85rem; font-weight: bold; color: #2c3e50;">Month:</label>
                    <input type="month" name="filter_month" value="<?php echo htmlspecialchars($selected_month); ?>" style="padding: 6px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                
                <!-- Gender Filter -->
                <div style="display: flex; align-items: center; gap: 5px;">
                    <label style="font-size: 0.85rem; font-weight: bold; color: #2c3e50;">Gender:</label>
                    <select name="filter_gender" style="padding: 6px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">All</option>
                        <option value="Male" <?php echo $filter_gender == 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $filter_gender == 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo $filter_gender == 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                
                <!-- Recent Filter -->
                <div style="display: flex; align-items: center; gap: 5px;">
                    <label style="font-size: 0.85rem; font-weight: bold; color: #2c3e50;">Recent:</label>
                    <select name="filter_recent" style="padding: 6px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">All Time</option>
                        <option value="today" <?php echo $filter_recent == 'today' ? 'selected' : ''; ?>>Today</option>
                        <option value="week" <?php echo $filter_recent == 'week' ? 'selected' : ''; ?>>This Week</option>
                        <option value="month" <?php echo $filter_recent == 'month' ? 'selected' : ''; ?>>This Month</option>
                    </select>
                </div>
                
                <button type="submit" class="action-btn" style="background: #2980b9;">🔍 Filter</button>
                <?php if($selected_month != '' || $filter_gender != '' || $filter_recent != ''): ?>
                    <a href="manage_visitors.php" class="action-btn" style="background: #95a5a6;">Clear</a>
                <?php endif; ?>
            </form>
            
            <a href="export_visitors.php?filter_month=<?php echo urlencode($selected_month); ?>&filter_gender=<?php echo urlencode($filter_gender); ?>&filter_recent=<?php echo urlencode($filter_recent); ?>" class="action-btn" style="background: #27ae60; padding: 12px 15px; font-size: 0.95rem;">📥 Download Excel</a>
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