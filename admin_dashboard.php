<?php
session_start();
include 'db.php';
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }

// Fetch statistics
$visitor_count = $conn->query("SELECT COUNT(*) as count FROM guests")->fetch_assoc()['count'];
$artifact_count = $conn->query("SELECT COUNT(*) as count FROM exhibits")->fetch_assoc()['count'];
$news_count = $conn->query("SELECT COUNT(*) as count FROM news_events")->fetch_assoc()['count'];
$dept_count = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Admin</title>
    <link rel="stylesheet" href="css/admin-sidebar.css">
    <link rel="stylesheet" href="css/admin-dashboard.css">
</head>
<body style="margin: 0; background: #f4f7f6;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div class="welcome-banner">
        <h2>Welcome to your Control Panel</h2>
        <p>Here is a quick overview of the Museo de Labo digital catalog and visitor statistics.</p>
    </div>

    <div class="dashboard-grid">
        <div class="stat-card card-visitors">
            <div class="stat-info">
                <h3><?php echo $visitor_count; ?></h3>
                <p>Total Visitors</p>
            </div>
            <div class="stat-icon">👥</div>
        </div>

        <div class="stat-card card-artifacts">
            <div class="stat-info">
                <h3><?php echo $artifact_count; ?></h3>
                <p>Catalog Artifacts</p>
            </div>
            <div class="stat-icon">🏺</div>
        </div>

        <div class="stat-card card-departments">
            <div class="stat-info">
                <h3><?php echo $dept_count; ?></h3>
                <p>Departments</p>
            </div>
            <div class="stat-icon">📁</div>
        </div>

        <div class="stat-card card-news">
            <div class="stat-info">
                <h3><?php echo $news_count; ?></h3>
                <p>News & Events</p>
            </div>
            <div class="stat-icon">📰</div>
        </div>
    </div>

    <h3 style="color: #2c3e50; border-bottom: 2px solid #c5a059; display: inline-block; padding-bottom: 5px; margin-bottom: 20px;">⚡ Quick Actions</h3>
    
    <div class="quick-actions">
        <a href="manage_exhibits.php" class="action-card">
            <span>➕</span> Add New Artifact
        </a>
        <a href="manage_departments.php" class="action-card">
            <span>📂</span> Add Department
        </a>
        <a href="manage_news.php" class="action-card">
            <span>📢</span> Post News/Event
        </a>
        <a href="manage_visitors.php" class="action-card">
            <span>📥</span> Export Visitor Log
        </a>
    </div>

    </main>
</div>

</body>
</html>