<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Automatically detect which page the user is currently on
$current_page = isset($_GET['page']) ? $_GET['page'] . '.php' : 'index.php';
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
        flex-wrap: wrap; 
    }
    
    .site-logo a { color: white; text-decoration: none; font-size: 1.5rem; font-weight: bold; display: flex; align-items: center; gap: 10px; }
    .logo-img { height: 40px; width: auto; object-fit: contain; }

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
    
    /* Navigation Links Base Style */
    .site-nav-links li a { 
        color: white; 
        text-decoration: none; 
        font-size: 1rem; 
        transition: all 0.3s; 
        padding-bottom: 4px;
        border-bottom: 2px solid transparent; /* Invisible border prevents jumping */
    }
    
    .site-nav-links li a:hover { 
        color: #c5a059; 
    }
    
    /* --- ACTIVE PAGE INDICATOR STYLE --- */
    .site-nav-links li a.active-page {
        color: #c5a059;
        font-weight: bold;
        border-bottom: 2px solid #c5a059; /* Adds the gold underline */
    }

    .admin-link { border-left: 1px solid #7f8c8d; padding-left: 20px; }

    /* --- RESPONSIVE MOBILE VIEW --- */
    @media (max-width: 768px) {
        .site-header { padding: 1rem; }
        
        .hamburger { display: flex; }

        .site-nav-links { 
            display: none; 
            width: 100%; 
            flex-direction: column; 
            text-align: center; 
            padding-top: 20px; 
            gap: 15px;
        }

        .site-nav-links.active {
            display: flex;
        }

        .admin-link { border-left: none; padding-left: 0; border-top: 1px solid #7f8c8d; padding-top: 15px; margin-top: 5px; width: 100%;}

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