<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php';
include 'functions.php';

// Initialize counters
$total_visitors = 0;
$total_artifacts = 0;
$total_categories = 0;
$total_comments = 0;
$total_likes = 0;
$total_users = 0;

// Safe query function
function safe_count($conn, $table) {
    $result = mysqli_query($conn, "SELECT id FROM " . mysqli_real_escape_string($conn, $table));
    return $result ? mysqli_num_rows($result) : 0;
}

$total_visitors = safe_count($conn, 'guests');
$total_artifacts = safe_count($conn, 'exhibits');
$total_categories = safe_count($conn, 'categories');

// Check if comments table exists
$comments_table = mysqli_query($conn, "SHOW TABLES LIKE 'comments'");
if ($comments_table && mysqli_num_rows($comments_table) > 0) {
    $total_comments = safe_count($conn, 'comments');
}

// Check if likes table exists
$likes_table = mysqli_query($conn, "SHOW TABLES LIKE 'likes'");
if ($likes_table && mysqli_num_rows($likes_table) > 0) {
    $total_likes = safe_count($conn, 'likes');
}

// Check if users table exists
$users_table = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
if ($users_table && mysqli_num_rows($users_table) > 0) {
    $total_users = safe_count($conn, 'users');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div class="statistics-container" style="padding: 20px;">
        <h1 style="color: #2c3e50; text-align: center;">Application Statistics</h1>
        <div class="statistics-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
            <div class="stat-card" style="background: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); padding: 20px; text-align: center;">
                <h3 style="margin-top: 0; color: #2c3e50;">Total Visitors</h3>
                <p style="font-size: 2rem; color: #3498db;"><?php echo $total_visitors; ?></p>
            </div>
            <div class="stat-card" style="background: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); padding: 20px; text-align: center;">
                <h3 style="margin-top: 0; color: #2c3e50;">Total Artifacts</h3>
                <p style="font-size: 2rem; color: #e74c3c;"><?php echo $total_artifacts; ?></p>
            </div>
            <div class="stat-card" style="background: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); padding: 20px; text-align: center;">
                <h3 style="margin-top: 0; color: #2c3e50;">Total Categories</h3>
                <p style="font-size: 2rem; color: #2ecc71;"><?php echo $total_categories; ?></p>
            </div>
            <div class="stat-card" style="background: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); padding: 20px; text-align: center;">
                <h3 style="margin-top: 0; color: #2c3e50;">Total Comments</h3>
                <p style="font-size: 2rem; color: #f1c40f;"><?php echo $total_comments; ?></p>
            </div>
            <div class="stat-card" style="background: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); padding: 20px; text-align: center;">
                <h3 style="margin-top: 0; color: #2c3e50;">Total Likes</h3>
                <p style="font-size: 2rem; color: #9b59b6;"><?php echo $total_likes; ?></p>
            </div>
            <div class="stat-card" style="background: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); padding: 20px; text-align: center;">
                <h3 style="margin-top: 0; color: #2c3e50;">Total Users</h3>
                <p style="font-size: 2rem; color: #e67e22;"><?php echo $total_users; ?></p>
            </div>
        </div>
    </div>

    </main>
</div>

</body>
</html>

