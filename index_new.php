<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
include 'db.php'; 

// === PUBLIC DATA ===
$news_query = "SELECT * FROM news_events ORDER BY date_posted DESC LIMIT 5";
$news_result = $conn->query($news_query);

$categories_query = "SELECT * FROM categories ORDER BY name ASC";
$categories_result = $conn->query($categories_query);

$all_exhibits_query = "SELECT * FROM exhibits ORDER BY id DESC";
$all_exhibits_result = $conn->query($all_exhibits_query);

// === ADMIN DATA === (only query if admin)
$admin_data = [];
if ($is_admin) {
    // Dashboard stats
    $admin_data['total_exhibits'] = $conn->query("SELECT id FROM exhibits")->num_rows;
    $admin_data['total_categories'] = $conn->query("SELECT id FROM categories")->num_rows;
    $admin_data['total_guests'] = $conn->query("SELECT id FROM guests")->num_rows;
    $admin_data['today_visitors'] = $conn->query("SELECT id FROM guests WHERE DATE(visit_date) = CURDATE()")->num_rows;
    
    // Dashboard charts
    $monthly_data = [];
    for ($i = 5; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $month_name = date('M Y', strtotime("-$i months"));
        $count = $conn->query("SELECT id FROM guests WHERE DATE_FORMAT(visit_date, '%Y-%m') = '$month'")->num_rows;
        $monthly_data[] = ['month' => $month_name, 'count' => $count];
    }
    $daily_data = [];
    for ($i = 6; $i >= 0; $i--) {
        $day = date('Y-m-d', strtotime("-$i days"));
        $day_name = date('D', strtotime("-$i days"));
        $count = $conn->query("SELECT id FROM guests WHERE DATE(visit_date) = '$day'")->num_rows;
        $daily_data[] = ['day' => $day_name, 'count' => $count];
    }
    $max_monthly = max(array_column($monthly_data, 'count'), 1);
    $max_daily = max(array_column($daily_data, 'count'), 1);
    $admin_data['monthly_data'] = $monthly_data;
    $admin_data['daily_data'] = $daily_data;
    $admin_data['max_monthly'] = $max_monthly;
    $admin_data['max_daily'] = $max_daily;
    
    // Visitors table
    $selected_month = isset($_GET['filter_month']) ? $_GET['filter_month'] : '';
    $filter_gender = isset($_GET['filter_gender']) ? $_GET['filter_gender'] : '';
    $filter_recent = isset($_GET['filter_recent']) ? $_GET['filter_recent'] : '';
    $query = "SELECT * FROM guests";
    $conditions = [];
    if ($selected_month != '') $conditions[] = "DATE_FORMAT(visit_date, '%Y-%m') = '" . $conn->real_escape_string($selected_month) . "'";
    if ($filter_gender != '') $conditions[] = "gender = '" . $conn->real_escape_string($filter_gender) . "'";
    if ($filter_recent == 'today') $conditions[] = "DATE(visit_date) = CURDATE()";
    elseif ($filter_recent == 'week') $conditions[] = "visit_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    elseif ($filter_recent == 'month') $conditions[] = "visit_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    if (count($conditions) > 0) $query .= " WHERE " . implode(" AND ", $conditions);
    $query .= " ORDER BY visit_date DESC";
    $guest_result = $conn->query($query);
    
    // Artifacts
    $categories = [];
    $cat_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
    while ($cat_row = $cat_result->fetch_assoc()) $categories[] = $cat_row;
    $exhibits_query = "SELECT exhibits.*, categories.name AS cat_name FROM exhibits LEFT JOIN categories ON exhibits.category_id = categories.id ORDER BY exhibits.id DESC";
    $exhibits_result = $conn->query($exhibits_query);
    
    // Departments
    $cat_result_all = $conn->query("SELECT * FROM categories ORDER BY id DESC");
    
    // News
    $news_manage_result = $conn->query("SELECT * FROM news_events ORDER BY date_posted DESC");
    
    $admin_data['guest_result'] = $guest_result;
    $admin_data['categories'] = $categories;
    $admin_data['exhibits_result'] = $exhibits_result;
    $admin_data['cat_result_all'] = $cat_result_all;
    $admin_data['news_manage_result'] = $news_manage_result;
    $admin_data['selected_month'] = $selected_month;
    $admin_data['filter_gender'] = $filter_gender;
    $admin_data['filter_recent'] = $filter_recent;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device=device-width, initial-scale=1.0">
    <title><?php echo $is_admin ? 'Admin Dashboard | ' : 'Welcome | '; ?>Museo de Labo</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/index.css">
    <?php if ($is_admin): ?>
    <link rel="stylesheet" href="css/manage.css">
    <?php endif; ?>
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    
    <style>
        /* Existing styles + Admin layout */
        .admin-layout { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; flex-shrink: 0; }
        .main-content { flex: 1; padding: 20px; overflow-y: auto; }
        .admin-body { background: #f8f9fa; }
        .tab-nav-admin { justify-content: flex-start; padding: 20px; background: #2c3e50; color: white; }
        .tab-btn-admin { background: transparent; color: white; border: 1px solid #34495e; }
        .tab-btn-admin.active { background: #3498db; border-color: #2980b9; }
        /* Form animations etc from manage.css */
    </style>
</head>
<body class="<?php echo $is_admin ? 'admin-body' : ''; ?>">

    <?php include 'header.php'; ?>

    <?php if ($is_admin): ?>
    <div class="admin-layout">
        <?php include 'admin_sidebar.php'; ?>
    <?php endif; ?>

    <!-- PUBLIC TABS (hidden for admins) -->
    <?php if (!$is_admin): ?>
    <!-- HOME, DEPARTMENTS, ARTIFACTS, ABOUT tabs here - copy exact from original index.php -->
    <div id="home" class="tab-content active"> [EXISTING HOME CONTENT] </div>
    <div id="departments" class="tab-content"> [EXISTING DEPT CONTENT] </div>
    <!-- ... -->
    <?php else: ?>
    <!-- ADMIN TABS -->
    <main class="main-content">
        <div id="admin-dashboard" class="tab-content active">
            <div class="dashboard-header">
                <div>
                    <h2>⚙️ Control Center Overview</h2>
                    <div class="stats">
                        <p>Departments: <span><?php echo $admin_data['total_categories']; ?></span></p>
                        <p>Artifacts: <span><?php echo $admin_data['total_exhibits']; ?></span></p>
                        <p>Total Visitors: <span><?php echo $admin_data['total_guests']; ?></span></p>
                    </div>
                </div>
                <div class="action-buttons">
                    <button onclick="switchTab('manage-artifacts')" class="btn-add bg-exhibit">➕ Add Exhibit</button>
                    <button onclick="switchTab('manage-departments')" class="btn-add bg-category">➕ Add Category</button>
                </div>
            </div>
            <!-- Stats cards and charts -->
            <div style="display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px; border-radius: 10px; color: white; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 10px 0; font-size: 1rem; opacity: 0.9;">👥 Today's Visitors</h3>
                    <p style="margin: 0; font-size: 2.5rem; font-weight: bold;"><?php echo $admin_data['today_visitors']; ?></p>
                </div>
                <!-- more cards -->
            </div>
            <!-- Charts -->
            <!-- Welcome message -->
        </div>

        <div id="manage-visitors" class="tab-content">
            <!-- Visitors table with filters from manage_visitors.php -->
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;">
                <h3 class="table-title">👥 Visitor Log</h3>
                <!-- Filters -->
            </div>
            <!-- Table -->
        </div>

        <div id="manage-artifacts" class="tab-content">
            <!-- Full artifacts form + table from manage_artifacts.php -->
        </div>

        <div id="manage-departments" class="tab-content">
            <!-- Full departments form + table -->
        </div>

        <div id="manage-news" class="tab-content">
            <!-- Full news form + table -->
        </div>
    </main>
    </div>
    <?php endif; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="js/index.js"></script>
    <?php if ($is_admin): ?>
    <script src="js/manage.js"></script>
    <script src="js/admin.js"></script>
    <?php endif; ?>

</body>
</html>
