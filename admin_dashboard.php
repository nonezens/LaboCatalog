<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php'; 

// Fetch basic stats
$total_exhibits = $conn->query("SELECT id FROM exhibits")->num_rows;
$total_categories = $conn->query("SELECT id FROM categories")->num_rows;
$total_guests = $conn->query("SELECT id FROM guests")->num_rows;

// Today's visitors
$today_visitors = $conn->query("SELECT id FROM guests WHERE DATE(visit_date) = CURDATE()")->num_rows;

// Get visitors for the last 6 months for bar chart
$monthly_data = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $month_name = date('M Y', strtotime("-$i months"));
    $count = $conn->query("SELECT id FROM guests WHERE DATE_FORMAT(visit_date, '%Y-%m') = '$month'")->num_rows;
    $monthly_data[] = ['month' => $month_name, 'count' => $count];
}

// Get last 7 days data for bar chart
$daily_data = [];
for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $day_name = date('D', strtotime("-$i days"));
    $count = $conn->query("SELECT id FROM guests WHERE DATE(visit_date) = '$day'")->num_rows;
    $daily_data[] = ['day' => $day_name, 'count' => $count];
}

// Find max values for chart scaling
$max_monthly = max(array_column($monthly_data, 'count'), 1);
$max_daily = max(array_column($daily_data, 'count'), 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/manage.css">
</head>
<body class="admin-body">

    <?php include 'header.php'; ?>
    
    <div class="admin-layout">
        <?php include 'admin_sidebar.php'; ?>

        <main class="main-content">
            <div class="dashboard-header">
                <div>
                    <h2>⚙️ Control Center Overview</h2>
                    <div class="stats">
                        <p>Departments: <span><?php echo $total_categories; ?></span></p>
                        <p>Artifacts: <span><?php echo $total_exhibits; ?></span></p>
                        <p>Total Visitors: <span><?php echo $total_guests; ?></span></p>
                    </div>
                </div>
                <div class="action-buttons">
                    <a href="manage_artifacts.php" class="btn-add bg-exhibit">➕ Add Exhibit</a>
                    <a href="manage_departments.php" class="btn-add bg-category">➕ Add Category</a>
                </div>
            </div>

            <!-- Visitor Statistics Cards -->
            <div style="display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px; border-radius: 10px; color: white; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 10px 0; font-size: 1rem; opacity: 0.9;">👥 Today's Visitors</h3>
                    <p style="margin: 0; font-size: 2.5rem; font-weight: bold;"><?php echo $today_visitors; ?></p>
                </div>
                <div style="flex: 1; min-width: 200px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 25px; border-radius: 10px; color: white; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 10px 0; font-size: 1rem; opacity: 0.9;">📊 Total Visitors</h3>
                    <p style="margin: 0; font-size: 2.5rem; font-weight: bold;"><?php echo $total_guests; ?></p>
                </div>
                <div style="flex: 1; min-width: 200px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 25px; border-radius: 10px; color: white; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 10px 0; font-size: 1rem; opacity: 0.9;">🏛️ Total Exhibits</h3>
                    <p style="margin: 0; font-size: 2.5rem; font-weight: bold;"><?php echo $total_exhibits; ?></p>
                </div>
            </div>

            <!-- Bar Charts -->
            <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 30px;">
                <!-- Monthly Visitors Chart -->
                <div style="flex: 1; min-width: 350px; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <h3 style="margin: 0 0 20px 0; color: #2c3e50; font-size: 1.2rem;">📈 Monthly Visitors (Last 6 Months)</h3>
                    <div style="display: flex; align-items: flex-end; justify-content: space-around; height: 200px; border-bottom: 2px solid #eee; padding-bottom: 10px;">
                        <?php foreach ($monthly_data as $data): ?>
                            <div style="display: flex; flex-direction: column; align-items: center; flex: 1;">
                                <span style="font-size: 0.85rem; color: #7f8c8d; margin-bottom: 5px;"><?php echo $data['count']; ?></span>
                                <div style="width: 40px; background: linear-gradient(to top, #667eea, #764ba2); border-radius: 4px 4px 0 0; height: <?php echo ($max_monthly > 0) ? ($data['count'] / $max_monthly * 150) : 5; ?>px; min-height: 5px;"></div>
                                <span style="font-size: 0.75rem; color: #2c3e50; margin-top: 8px;"><?php echo $data['month']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Daily Visitors Chart -->
                <div style="flex: 1; min-width: 350px; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <h3 style="margin: 0 0 20px 0; color: #2c3e50; font-size: 1.2rem;">📅 Daily Visitors (Last 7 Days)</h3>
                    <div style="display: flex; align-items: flex-end; justify-content: space-around; height: 200px; border-bottom: 2px solid #eee; padding-bottom: 10px;">
                        <?php foreach ($daily_data as $data): ?>
                            <div style="display: flex; flex-direction: column; align-items: center; flex: 1;">
                                <span style="font-size: 0.85rem; color: #7f8c8d; margin-bottom: 5px;"><?php echo $data['count']; ?></span>
                                <div style="width: 40px; background: linear-gradient(to top, #4facfe, #00f2fe); border-radius: 4px 4px 0 0; height: <?php echo ($max_daily > 0) ? ($data['count'] / $max_daily * 150) : 5; ?>px; min-height: 5px;"></div>
                                <span style="font-size: 0.75rem; color: #2c3e50; margin-top: 8px;"><?php echo $data['day']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
    
            <div style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center;">
                <h1 style="color: #2c3e50;">Welcome back, Admin!</h1>
                <p style="color: #7f8c8d; font-size: 1.1rem;">Use the left menu to manage visitors, artifacts, and departments.</p>
            </div>
        </main>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
