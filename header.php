<?php
// Check if a session is already active before starting one
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Museo de Labo</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<body>
<header class="site-header" id="siteHeader">
    <nav class="site-nav">
        
        <div class="site-logo">
            <a href="index.php">
                <img src="uploads/tourism-logo.png" alt="Museum Logo" class="logo-img">
                <span class="logo-text">
                    <span class="main-title">Museo De Labo</span>
                    <span class="tagline">ᜋᜓᜐᜒᜂ ᜇᜒ ᜎᜊᜓ</span>
                </span>
            </a>
        </div>
        
        <ul class="site-nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="exhibits.php">Collection</a></li>
            
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

<script src="js/header.js"></script>
</body>
</html>

