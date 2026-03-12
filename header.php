<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<header class="site-header">
    <nav class="site-nav">
        <div class="site-logo">
<a href="index.php" class="nav-link">
                <img src="uploads/logo.png" alt="Museum Logo" class="logo-img" onerror="this.style.display='none';">
                <div class="logo-text-container">
                    <span class="logo-text">Museo De Labo</span>
                    <span class="baybayin-text">ᜋᜓᜐᜒᜂ ᜇᜒ ᜎᜊᜓ</span>
                </div>
            </a>
        </div>
        
        <div class="hamburger" id="hamburger-menu">
            <span></span>
            <span></span>
            <span></span>
        </div>
        
<ul class="site-nav-links" id="nav-links">
            <li><a href="#home" data-tab="home" class="nav-link">Home</a></li>
            <li><a href="#departments" data-tab="departments" class="nav-link">Departments</a></li>
            <li><a href="#artifacts" data-tab="artifacts" class="nav-link">All Artifacts</a></li>
            <li><a href="#about" data-tab="about" class="nav-link">About</a></li>
            
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

<script src="js/header.js"></script>

