<?php
// We check if a session is already started. If not, we start one.
// This prevents annoying "Session already started" errors.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header style="background: #2c3e50; color: white; padding: 1rem 2rem; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <nav style="display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto;">
        
        <div class="logo" style="font-size: 1.5rem; font-weight: bold;">
            <a href="index.php" style="color: white; text-decoration: none;">🏛️ Museum Labo</a>
        </div>
        
        <ul style="list-style: none; display: flex; gap: 20px; margin: 0; padding: 0; align-items: center;">
            <li><a href="index.php" style="color: white; text-decoration: none;">Home</a></li>
            <li><a href="about.php" style="color: white; text-decoration: none;">About</a></li>
            <li><a href="exhibits.php" style="color: white; text-decoration: none;">Exhibits</a></li>
            <li><a href="categories.php" style="color: white; text-decoration: none;">Categories</a></li>
            
            <?php if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                
                <li style="border-left: 1px solid #7f8c8d; padding-left: 20px;">
                    <a href="add_exhibit.php" style="color: #e67e22; text-decoration: none; font-weight: bold;">+ Add Exhibit</a>
                </li>
                <li>
                    <a href="add_category.php" style="color: #e67e22; text-decoration: none; font-weight: bold;">+ Add Category</a>
                </li>
                <li>
                    <a href="logout.php" style="color: #e74c3c; text-decoration: none; font-weight: bold; margin-left: 10px;">Logout</a>
                </li>

            <?php else: ?>
                <li style="border-left: 1px solid #7f8c8d; padding-left: 20px;">
                    <a href="login.php" style="color: #95a5a6; text-decoration: none; font-size: 0.9em;">Admin Login</a>
                </li>
            <?php endif; ?>
            
        </ul>
    </nav>
</header>