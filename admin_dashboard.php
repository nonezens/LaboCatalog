<?php
include 'includes/auth.php';

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
    <style>
        /* Dashboard Specific Styles */
        .welcome-banner { background: white; padding: 25px 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; border-left: 5px solid #c5a059; }
        .welcome-banner h2 { margin: 0 0 5px 0; color: #2c3e50; font-size: 1.8rem; }
        .welcome-banner p { margin: 0; color: #7f8c8d; font-size: 1rem; }

        /* Stats Grid */
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 40px; }
        
        .stat-card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: space-between; transition: transform 0.3s; border: 1px solid #eee; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        
        /* Specific Card Accents */
        .card-visitors { border-bottom: 4px solid #3498db; }
        .card-artifacts { border-bottom: 4px solid #2ecc71; }
        .card-departments { border-bottom: 4px solid #9b59b6; }
        .card-news { border-bottom: 4px solid #e67e22; }

        .stat-info h3 { margin: 0; font-size: 2.2rem; color: #2c3e50; line-height: 1; }
        .stat-info p { margin: 8px 0 0 0; color: #7f8c8d; font-size: 0.9rem; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; }
        
        .stat-icon { font-size: 3rem; opacity: 0.2; }

        /* Quick Actions Grid */
        .quick-actions { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
        .action-card { background: white; padding: 20px; border-radius: 8px; text-align: center; text-decoration: none; color: #2c3e50; font-weight: bold; box-shadow: 0 2px 8px rgba(0,0,0,0.05); transition: 0.3s; border: 1px solid #eee; }
        .action-card:hover { background: #2c3e50; color: white; border-color: #2c3e50; }
        .action-card span { display: block; font-size: 2rem; margin-bottom: 10px; }

    </style>
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