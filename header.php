<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Automatically detect which page the user is currently on
$current_page = basename($_SERVER['PHP_SELF']);
?>

<header class="site-header">
    <nav class="site-nav">
        <div class="site-logo">
            <a href="index.php#home">
                <img src="uploads/logo.png" alt="Museum Logo" class="logo-img" onerror="this.style.display='none';">
                <span>Museo De Labo<br><small>ᜋᜓᜐᜒᜂ ᜇᜒ ᜎᜊᜓ</small></span>
            </a>
        </div>
        
        <div class="hamburger" id="hamburger-menu">
            <span></span>
            <span></span>
            <span></span>
        </div>
        
        <ul class="site-nav-links" id="nav-links">
            <li><a href="index.php#home" class="nav-link" data-section="home">Home</a></li>
            <li><a href="index.php#about" class="nav-link" data-section="about">About</a></li>
            <li><a href="index.php#news" class="nav-link" data-section="news">News & Events</a></li>
            <li><a href="index.php#departments" class="nav-link" data-section="departments">Departments</a></li>
            <li><a href="index.php#allartifacts" class="nav-link" data-section="allartifacts">All Artifacts</a></li>
            
            <?php if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                <li class="separator"></li>
                <li><span class="admin-badge">👤 ADMIN</span></li>
                <li><a href="index.php#admin_dashboard" class="nav-link admin-btn" data-section="admin_dashboard">Dashboard</a></li>
                <li><a href="logout.php" class="logout-btn">Logout</a></li>
            <?php elseif(isset($_SESSION['guest_logged_in']) && $_SESSION['guest_logged_in'] === true): ?>
                <li class="separator"></li>
                <li><span class="guest-greeting">Welcome, <?php echo htmlspecialchars($_SESSION['guest_name']); ?>!</span></li>
                <li><a href="logout.php" class="logout-btn">Leave</a></li>
            <?php else: ?>
                <li class="separator"></li>
                <li><a href="index.php#login" class="nav-link login-btn" data-section="login">Login / Access</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<script src="js/header.js"></script>