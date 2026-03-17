<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Automatically detect which page the user is currently on
$current_page = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="css/style.css">

<header class="site-header">
    <nav class="site-nav">
        <div class="site-logo">
            <a href="index.php">
                <img src="uploads/logo.png" alt="Museum Logo" class="logo-img" onerror="this.style.display='none';">
                Museo De Labo
            </a>
        </div>
        
        <div class="hamburger" id="hamburger-menu">
            <span></span>
            <span></span>
            <span></span>
        </div>
        
        <ul class="site-nav-links" id="nav-links">
            <li><a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active-page' : ''; ?>">Home</a></li>
            <li><a href="about.php" class="<?php echo ($current_page == 'about.php') ? 'active-page' : ''; ?>">About</a></li>
            <li><a href="news.php" class="<?php echo ($current_page == 'news.php') ? 'active-page' : ''; ?>">News & Events</a></li>
            <li><a href="categories.php" class="<?php echo ($current_page == 'categories.php') ? 'active-page' : ''; ?>">Departments</a></li>
            <li><a href="exhibits.php" class="<?php echo ($current_page == 'exhibits.php') ? 'active-page' : ''; ?>">All Artifacts</a></li>
            
            <?php if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                <li class="admin-link"><a href="admin_dashboard.php" style="color: #3498db; font-weight: bold;">⚙️ Dashboard</a></li>
                <li><a href="logout.php" style="color: #e74c3c; font-weight: bold;">Logout</a></li>
            <?php elseif(isset($_SESSION['guest_logged_in']) && $_SESSION['guest_logged_in'] === true): ?>
                <li class="admin-link" style="color: #bdc3c7; font-size: 0.95rem;">Welcome, <?php echo htmlspecialchars($_SESSION['guest_name']); ?>!</li>
                <li><a href="logout.php" style="color: #e74c3c; font-weight: bold;">Leave</a></li>
            <?php else: ?>
                <li class="admin-link"><a href="login.php" style="color: #95a5a6; font-size: 0.9em;">Login / Access</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
