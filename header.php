<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<style>
    html, body { margin: 0 !important; padding: 0 !important; width: 100%; }

    /* Morphing Page Transition */
    .morph-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #2c3e50;
        z-index: 99999;
        pointer-events: none;
        opacity: 0;
        transform: scale(0.8, 0.8);
        transition: opacity 0.4s ease, transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 50%;
    }
    
    .morph-overlay.active {
        opacity: 1;
        transform: scale(2, 2);
    }

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
    
    .site-logo a { color: white; text-decoration: none; display: flex; align-items: center; gap: 10px; }
    .logo-img { height: 55px; width: auto; object-fit: contain; }

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

    /* --- Hamburger Menu --- */
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
    
    .site-nav-links li a { 
        color: white; 
        text-decoration: none; 
        font-size: 1rem; 
        transition: color 0.3s;
        padding: 8px 12px;
        border-radius: 4px;
    }
    
    .site-nav-links li a:hover { 
        color: #c5a059; 
    }
    
    .site-nav-links li a.active-page {
        color: #c5a059;
        background: rgba(197, 160, 89, 0.15);
    }

    .admin-link { border-left: 1px solid #7f8c8d; padding-left: 20px; }

    /* --- Mobile Responsive --- */
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
        .admin-link { border-left: none; padding-left: 0; }

        .hamburger.open span:nth-child(1) { transform: rotate(45deg) translate(6px, 6px); }
        .hamburger.open span:nth-child(2) { opacity: 0; }
        .hamburger.open span:nth-child(3) { transform: rotate(-45deg) translate(7px, -7px); }
    }
</style>

<!-- Morphing Page Transition -->
<div class="morph-overlay" id="morphOverlay"></div>

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
            <li><a href="index.php" class="nav-link <?php echo $current_page == 'index' ? 'active-page' : ''; ?>">Home</a></li>
            <li><a href="about.php" class="nav-link <?php echo $current_page == 'about' ? 'active-page' : ''; ?>">About</a></li>
            <li><a href="categories.php" class="nav-link <?php echo $current_page == 'categories' ? 'active-page' : ''; ?>">Departments</a></li>
            <li><a href="exhibits.php" class="nav-link <?php echo $current_page == 'exhibits' ? 'active-page' : ''; ?>">All Artifacts</a></li>
            <li><a href="news.php" class="nav-link <?php echo $current_page == 'news' ? 'active-page' : ''; ?>">News & Events</a></li>
            
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
        const morphOverlay = document.getElementById("morphOverlay");

        // Hamburger menu toggle
        hamburger.addEventListener("click", function() {
            navLinks.classList.toggle("active");
            hamburger.classList.toggle("open");
        });

        // Morphing Page Transition
        const navLinksAll = document.querySelectorAll('.nav-link, .site-logo a');
        
        navLinksAll.forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href && !href.startsWith('#') && !href.startsWith('javascript')) {
                    e.preventDefault();
                    
                    // Get click position for morph origin
                    const rect = this.getBoundingClientRect();
                    const x = rect.left + rect.width / 2;
                    const y = rect.top + rect.height / 2;
                    
                    // Set transform origin to click position
                    const xPercent = (x / window.innerWidth) * 100;
                    const yPercent = (y / window.innerHeight) * 100;
                    morphOverlay.style.transformOrigin = xPercent + '% ' + yPercent + '%';
                    
                    // Trigger morph in
                    morphOverlay.classList.add('active');
                    
                    setTimeout(() => {
                        window.location.href = href;
                    }, 400);
                }
            });
        });

        // Hash URL handling
        if (window.location.hash) {
            const hash = window.location.hash.substring(1);
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active-page');
                if (link.getAttribute('href') && link.getAttribute('href').includes(hash + '.php')) {
                    link.classList.add('active-page');
                }
            });
        }

        // Update active state on nav click
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active-page'));
                this.classList.add('active-page');
            });
        });

        // Handle browser back/forward
        window.addEventListener('popstate', function() {
            const hash = window.location.hash.substring(1);
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active-page');
                if (link.getAttribute('href') && link.getAttribute('href').includes(hash + '.php')) {
                    link.classList.add('active-page');
                }
            });
        });
    });
</script>

