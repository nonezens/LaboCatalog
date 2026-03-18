<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Automatically detect which page the admin is currently on
$current_page = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="css/admin-sidebar.css">

<div class="admin-layout">
    <aside class="sidebar">
        <h3>Admin Menu</h3>
        <ul class="sidebar-menu">
            <li><a href="admin_dashboard.php" class="<?php echo ($current_page == 'admin_dashboard.php') ? 'active-sidebar' : ''; ?>">📊 Overview</a></li>
            
            <li><a href="manage_visitors.php" class="<?php echo ($current_page == 'manage_visitors.php') ? 'active-sidebar' : ''; ?>">👥 Visitor Log</a></li>
            
            <li><a href="manage_exhibits.php" class="<?php echo ($current_page == 'manage_exhibits.php' || $current_page == 'edit_exhibit.php') ? 'active-sidebar' : ''; ?>">🖼️ Manage Artifacts</a></li>
            
            <li><a href="manage_departments.php" class="<?php echo ($current_page == 'manage_departments.php' || $current_page == 'edit_category.php') ? 'active-sidebar' : ''; ?>">📁 Manage Departments</a></li>
            
            <li><a href="manage_news.php" class="<?php echo ($current_page == 'manage_news.php' || $current_page == 'edit_news.php') ? 'active-sidebar' : ''; ?>">📰 Manage News</a></li>
        </ul>
    </aside>

    <main class="main-content">