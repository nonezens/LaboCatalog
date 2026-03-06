<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php'; 

// Fetch basic stats
$total_exhibits = $conn->query("SELECT id FROM exhibits")->num_rows;
$total_categories = $conn->query("SELECT id FROM categories")->num_rows;
$total_guests = $conn->query("SELECT id FROM guests")->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

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
            <a href="add_exhibit.php" class="btn-add bg-exhibit">➕ Add Exhibit</a>
            <a href="add_category.php" class="btn-add bg-category">➕ Add Category</a>
        </div>
    </div>
    
    <div style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center;">
        <h1 style="color: #2c3e50;">Welcome back, Admin!</h1>
        <p style="color: #7f8c8d; font-size: 1.1rem;">Use the left menu to manage visitors, artifacts, and departments.</p>
    </div>

    </main>
</div>

</body>
</html>