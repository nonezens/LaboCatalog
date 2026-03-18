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
    
    <!-- CSS -->
<link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/manage_visitors.css">

</head>
<body class="admin-page admin-body">


    <?php include 'header.php'; ?>

    <div class="admin-layout">
        <?php include 'admin_sidebar.php'; ?>

        <main class="main-content">
    <div class="visitors-header">
                <div class="visitors-title">
                    <span>👥</span>
                    <span>Visitor Management (<?php echo $guest_result ? $guest_result->num_rows : 0 ?>)</span>
                </div>
                <div class="stats-bar">
                    <div class="stat-pill">All Time</div>
                    <div class="stat-pill" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">Today</div>
                    <div class="stat-pill" style="background: linear-gradient(135deg, #f39c12, #e67e22);">Export</div>
                </div>
            </div>

            <div class="filter-pills">
                <div class="filter-group">
                    <label class="filter-label">Month:</label>
                    <input type="month" name="filter_month" id="filterMonth" value="<?php echo htmlspecialchars($selected_month); ?>" class="filter-control" onchange="applyFilter()">
                </div>
                <div class="filter-group">
                    <label class="filter-label">Gender:</label>
                    <select name="filter_gender" id="filterGender" class="filter-control" onchange="applyFilter()">
                        <option value="">All</option>
                        <option value="Male" <?php echo $filter_gender == 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $filter_gender == 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo $filter_gender == 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Recent:</label>
                    <select name="filter_recent" id="filterRecent" class="filter-control" onchange="applyFilter()">
                        <option value="">All Time</option>
                        <option value="today" <?php echo $filter_recent == 'today' ? 'selected' : ''; ?>>Today</option>
                        <option value="week" <?php echo $filter_recent == 'week' ? 'selected' : ''; ?>>Week</option>
                        <option value="month" <?php echo $filter_recent == 'month' ? 'selected' : ''; ?>>Month</option>
                    </select>
                </div>
                <div class="filter-group">
                    <a href="manage_visitors.php" class="btn-clear action-btn">Clear All</a>
                </div>
                <div class="filter-group">
                    <a href="export_visitors.php?filter_month=<?php echo urlencode($selected_month); ?>&filter_gender=<?php echo urlencode($filter_gender); ?>&filter_recent=<?php echo urlencode($filter_recent); ?>" class="btn-export action-btn">
                        📥 Excel Export
                    </a>
                </div>
            </div>

            <script>
            function applyFilter() {
                var month = document.getElementById('filterMonth').value;
                var gender = document.getElementById('filterGender').value;
                var recent = document.getElementById('filterRecent').value;
                
                var url = 'manage_visitors.php?';
                var params = [];
                
                if (month) params.push('filter_month=' + encodeURIComponent(month));
                if (gender) params.push('filter_gender=' + encodeURIComponent(gender));
                if (recent) params.push('filter_recent=' + encodeURIComponent(recent));
                
                window.location.href = url + params.join('&');
            }
            </script>

            <div class="visitors-table-container">
                <table class="visitors-table">
                    <tr>
                        <th>Date & Time</th>
                        <th>Visitor Details</th>
                        <th>Demographics</th>
                        <th>Purpose</th>
                        <th>Actions</th>
                    </tr>
                    <?php if($guest_result && $guest_result->num_rows > 0): 
                        while($guest = $guest_result->fetch_assoc()): ?>
                    <tr class="visitor-row">
                        <td>
                            <div style="font-weight: 600; color: #2c3e50;"><?php echo date("M d", strtotime($guest['visit_date'])); ?></div>
                            <div style="font-size: 0.85rem; color: #7f8c8d;"><?php echo date("g:i A", strtotime($guest['visit_date'])); ?></div>
                        </td>
                        <td>
                            <div class="visitor-name"><?php echo htmlspecialchars($guest['guest_name']); ?></div>
                            <div class="visitor-contact">
                                📞 <span><?php echo htmlspecialchars($guest['contact_no']); ?></span>
                            </div>
                            <?php if (!empty($guest['access_id'])): ?>
                                <div style="margin-top: 8px; background: #e8f5e8; padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; color: #27ae60;">
                                    ID: <?php echo htmlspecialchars($guest['access_id']); ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="demographics">
                            <div><strong>Gender:</strong> <?php echo htmlspecialchars($guest['gender']); ?></div>
                            <div><strong>From:</strong> <?php echo htmlspecialchars($guest['residence']); ?></div>
                            <div style="font-size: 0.85rem;"><strong>Nat:</strong> <?php echo htmlspecialchars($guest['nationality']); ?></div>
                        </td>
                        <td class="purpose-cell">
                            <?php echo htmlspecialchars($guest['purpose']); ?>
                            <?php if (!empty($guest['num_days'])): ?>
                                <div style="font-size: 0.8rem; color: #7f8c8d;">Stay: <?php echo $guest['num_days']; ?> days</div>
                            <?php endif; ?>
                        </td>
                        <td class="action-cell">
                            <a href="delete_guest.php?id=<?php echo $guest['id']; ?>" class="btn-delete action-btn" onclick="return confirm('Delete <?php echo htmlspecialchars($guest['guest_name']); ?> record?');">
                                🗑️ Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; 
                    else: ?>
                        <tr>
                            <td colspan="5" class="empty-state">
                                <div class="empty-state-icon">👥</div>
                                <h3>No visitors yet</h3>
                                <p>Visitors will appear here once they sign the digital guestbook.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </main>
    </div>
    <script src="js/admin.js"></script>
</body>
</html>
