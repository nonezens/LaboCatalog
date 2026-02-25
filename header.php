<?php
// We check if a session is already started. If not, we start one.
// This prevents annoying "Session already started" errors.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<link rel="stylesheet" href="style.css">

<header class="site-header">
    <nav style="display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto;">
        
        <div class="logo">
            <img src="/uploads/TOURISM LOGO.png" alt="Logo">
            <a href="index.php"> Museo De Labo</a>
            <a href="index.php">(ᜋᜓᜐᜒᜂ ᜇᜒ ᜎᜊᜓ)</a>
        </div>
        
        <ul class="nav-list">
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="exhibits.php">Exhibits</a></li>
            <li><a href="categories.php">Categories</a></li>
            
            <?php if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                
                <li class="nav-divider">
                    <a href="add_exhibit.php" class="accent">+ Add Exhibit</a>
                </li>
                <li>
                    <a href="add_category.php" class="accent">+ Add Category</a>
                </li>
                <li>
                    <a href="logout.php" class="danger">Logout</a>
                </li>

            <?php else: ?>
                <li class="nav-divider">
                    <a href="login.php" class="muted">Admin Login</a>
                </li>
            <?php endif; ?>
            
        </ul>
    </nav>
</header>