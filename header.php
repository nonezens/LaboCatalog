<?php
// Check if a session is already active before starting one
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<style>
    /* Remove default browser gaps */
    html, body { 
        margin: 0 !important; 
        padding: 0 !important; 
        width: 100%;
    }

    /* Your Sticky Header */
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
    
    .site-nav { display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto; }
    
    /* Logo Styles */
    .site-logo a { 
        color: white; 
        text-decoration: none; 
        font-size: 1.5rem; 
        font-weight: bold; 
        display: flex; 
        align-items: center; 
        gap: 10px; 
    }
    .logo-img {
        height: 40px; 
        width: auto;
        object-fit: contain;
    }

    .site-nav-links { list-style: none; display: flex; gap: 20px; margin: 0; padding: 0; align-items: center; }
    .site-nav-links li a { color: white; text-decoration: none; font-size: 1rem; transition: color 0.3s; }
    .site-nav-links li a:hover { color: #c5a059; }
    .admin-link { border-left: 1px solid #7f8c8d; padding-left: 20px; }
</style>

<header class="site-header">
    <nav class="site-nav">
        
        <div class="site-logo">
            <a href="index.php">
                <img src="logo.png" alt="Museum Logo" class="logo-img">
                Museum Labo
            </a>
        </div>
        
        <ul class="site-nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="exhibits.php">Exhibits</a></li>
            <li><a href="categories.php">Categories</a></li>
            
            <?php if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                
                <li class="admin-link">
                    <a href="admin_dashboard.php" style="color: #3498db; font-weight: bold;">⚙️ Dashboard</a>
                </li>
                <li>
                    <a href="logout.php" style="color: #e74c3c; font-weight: bold; margin-left: 10px;">Logout</a>
                </li>
            
            <?php elseif(isset($_SESSION['guest_logged_in']) && $_SESSION['guest_logged_in'] === true): ?>
                
                <li class="admin-link" style="color: #bdc3c7; font-size: 0.95rem;">
                    Welcome, <?php echo htmlspecialchars($_SESSION['guest_name']); ?>!
                </li>
                <li>
                    <a href="logout.php" style="color: #e74c3c; font-weight: bold; margin-left: 10px;">Leave</a>
                </li>
            
            <?php else: ?>
                
                <li class="admin-link"><a href="login.php" style="color: #95a5a6; font-size: 0.9em;">Login / Access</a></li>
            
            <?php endif; ?>
        </ul>
        
    </nav>
</header>