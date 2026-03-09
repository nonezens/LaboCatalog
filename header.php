<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<style>
    html, body { margin: 0 !important; padding: 0 !important; width: 100%; }

    .site-header { 
        background: #2c3e50; 
        color: white; 
        padding: 1rem 2rem; 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        width: 100%; 
        box-sizing: border-box; 
        position: sticky;
        top: 0;
        z-index: 9999;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2); 
    }
    
    .site-nav { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        max-width: 1200px; 
        margin: 0 auto; 
        flex-wrap: wrap; /* Allows the menu to drop down to the next row on mobile */
    }
    
    .site-logo a { color: white; text-decoration: none; display: flex; align-items: center; gap: 10px; }
    .logo-img { height: 40px; width: auto; object-fit: contain; }

    .logo-text-container {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }

    .logo-text {
        font-size: 1.5rem;
        font-weight: bold;
    }

    .baybayin-text {
        font-size: 0.8rem;
        font-weight: normal;
        letter-spacing: 0.5px;
        color: #bdc3c7;
    }

    /* --- THE HAMBURGER ICON (Hidden on Desktop) --- */
    .hamburger {
        display: none;
        flex-direction: column;
        cursor: pointer;
        gap: 6px;
    }
    .hamburger span {
        width: 30px;
        height: 3px;
        background-color: white;
        border-radius: 3px;
        transition: all 0.3s ease;
    }

    .site-nav-links { list-style: none; display: flex; gap: 20px; margin: 0; padding: 0; align-items: center; }
    .site-nav-links li a { color: white; text-decoration: none; font-size: 1rem; transition: color 0.3s; }
    .site-nav-links li a:hover { color: #c5a059; }
    .admin-link { border-left: 1px solid #7f8c8d; padding-left: 20px; }

    /* --- RESPONSIVE MOBILE VIEW --- */
    @media (max-width: 768px) {
        .site-header { padding: 1rem; }
        
        /* Show the hamburger icon */
        .hamburger { display: flex; }

        /* Hide the navigation links by default on mobile */
        .site-nav-links { 
            display: none; 
            width: 100%; 
            flex-direction: column; 
            text-align: center; 
            padding-top: 20px; 
            gap: 15px;
        }

        /* This class is added by JavaScript when the hamburger is tapped */
        .site-nav-links.active {
            display: flex;
        }

        .admin-link { border-left: none; padding-left: 0; }

        /* Fancy Animation: Turn the Hamburger into an "X" when open */
        .hamburger.open span:nth-child(1) { transform: rotate(45deg) translate(6px, 6px); }
        .hamburger.open span:nth-child(2) { opacity: 0; }
        .hamburger.open span:nth-child(3) { transform: rotate(-45deg) translate(7px, -7px); }
    }
</style>

<header class="site-header">
    <nav class="site-nav">
        <div class="site-logo">
            <a href="index.php">
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
            <li><a href="index.php">Home</a></li>
            <li><a href="categories.php">Departments</a></li>
            <li><a href="exhibits.php">All Artifacts</a></li>
            
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const hamburger = document.getElementById("hamburger-menu");
        const navLinks = document.getElementById("nav-links");

        hamburger.addEventListener("click", function() {
            // Toggle the menu visibility
            navLinks.classList.toggle("active");
            // Toggle the animation to turn the burger into an X
            hamburger.classList.toggle("open");
        });
    });
</script>