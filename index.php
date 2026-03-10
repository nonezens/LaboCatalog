<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db.php'; 

// 1. Check if the user is logged in (Guest or Admin)
$is_logged_in = isset($_SESSION['guest_logged_in']) || isset($_SESSION['admin_logged_in']);

// 2. ONLY fetch the latest acquisitions if they are logged in!
if ($is_logged_in) {
    $recent_query = "SELECT * FROM exhibits ORDER BY id DESC LIMIT 4";
    $recent_result = $conn->query($recent_query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | Museo de Labo</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            padding: 0; 
            background-image: linear-gradient(rgba(249, 249, 249, 0.92), rgba(249, 249, 249, 0.92)), url('uploads/FB_IMG_1773027688672.jpg');
            background-attachment: fixed;
            background-size: cover;
            background-position: center;
        }
        
        /* Hero Section with Animations */
        .hero { 
            background: linear-gradient(rgba(44, 62, 80, 0.8), rgba(26, 37, 47, 0.8));
            color: white; 
            text-align: center; 
            padding: 100px 20px; 
            border-bottom: 5px solid #c5a059;
            opacity: 0;
            transform: translateY(-30px);
            animation: heroFadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
        
        @keyframes heroFadeIn {
            to { opacity: 1; transform: translateY(0); }
        }
        
        .hero h1 { 
            font-size: 3.5rem; 
            margin: 0 0 15px 0; 
            letter-spacing: 2px;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
            opacity: 0;
            transform: translateX(-50px);
            animation: slideInLeft 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            animation-delay: 0.3s;
        }
        
        .hero p { 
            font-size: 1.2rem; 
            color: #ecf0f1; 
            max-width: 700px; 
            margin: 0 auto 30px auto; 
            line-height: 1.6; 
            text-shadow: 1px 1px 4px rgba(0,0,0,0.5);
            opacity: 0;
            transform: translateX(50px);
            animation: slideInRight 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            animation-delay: 0.5s;
        }
        
        @keyframes slideInLeft {
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes slideInRight {
            to { opacity: 1; transform: translateX(0); }
        }
        
        .hero-btn { 
            display: inline-block; 
            padding: 15px 35px; 
            background: #c5a059; 
            color: white; 
            text-decoration: none; 
            border-radius: 30px; 
            font-size: 1.1rem; 
            font-weight: bold; 
            transition: 0.3s; 
            box-shadow: 0 4px 15px rgba(197, 160, 89, 0.4);
            opacity: 0;
            transform: scale(0.8);
            animation: popIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            animation-delay: 0.7s;
        }
        
        @keyframes popIn {
            to { opacity: 1; transform: scale(1); }
        }
        
        .hero-btn:hover { background: #b48a3d; transform: translateY(-3px) scale(1.05); }

        /* Container & Sections */
        .container { max-width: 1200px; margin: 0 auto; padding: 60px 20px; }
        .section-title { 
            text-align: center; 
            color: #2c3e50; 
            font-size: 2.2rem; 
            margin-bottom: 40px;
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .section-title.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .section-title::after { 
            content: ''; 
            display: block; 
            width: 80px; 
            height: 3px; 
            background: #c5a059; 
            margin: 15px auto 0 auto; 
        }

        /* About Section */
        .about-grid { 
            display: grid; 
            grid-template-columns: 1fr; 
            gap: 20px; 
            text-align: center; 
        }
        .about-text { 
            font-size: 1.1rem; 
            color: #333;
            line-height: 1.7; 
            text-shadow: 1px 1px 3px rgba(255,255,255,0.7);
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .about-text.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .about-text p { margin-bottom: 20px; }
        .about-text p:first-of-type {
            font-size: 1.2rem;
            color: #2c3e50;
        }
        .about-text strong {
             color: #b48a3d;
             font-weight: 600;
        }
        .about-image { display: none; }

        /* Visitor Info Guide */
        .visitor-info { margin-top: 25px; padding-top: 25px; border-top: 1px solid #eee; }
        .visitor-info h4 { margin-top: 0; color: #c5a059; font-size: 1.2rem; margin-bottom: 15px; }
        .visitor-info ul { list-style-type: none; padding: 0; margin: 0; }
        .visitor-info li { margin-bottom: 10px; display: flex; align-items: flex-start; gap: 10px; }
        .visitor-info li strong { color: #2c3e50; }

        /* Latest Acquisitions Grid */
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px; margin-top: 20px; }
        .card-link { text-decoration: none; color: inherit; display: flex; }
        .card { 
            background: white; 
            border-radius: 8px; 
            overflow: hidden; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
            transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s; 
            border: 1px solid #eee; 
            display: flex; 
            flex-direction: column; 
            width: 100%;
            opacity: 0;
            transform: translateY(40px) scale(0.95);
        }
        
        .card.visible {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        
        .card-link:hover .card { 
            transform: translateY(-5px); 
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border-color: #c5a059;
        }
        .card img { width: 100%; height: 220px; object-fit: cover; transition: transform 0.4s ease; }
        .card-link:hover .card img { transform: scale(1.1); }
        .card-body { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
        .card-title { margin: 0 0 10px 0; color: #2c3e50; font-size: 1.3rem; transition: color 0.3s; }
        .card-link:hover .card-title { color: #c5a059; }
        .card-meta { font-size: 0.9rem; color: #7f8c8d; margin-bottom: 15px; }
        
        /* Staggered animation delays for cards */
        .gallery-grid .card-link:nth-child(1) .card { transition-delay: 0.1s; }
        .gallery-grid .card-link:nth-child(2) .card { transition-delay: 0.2s; }
        .gallery-grid .card-link:nth-child(3) .card { transition-delay: 0.3s; }
        .gallery-grid .card-link:nth-child(4) .card { transition-delay: 0.4s; }
        
        /* Responsive */
        @media (max-width: 768px) { 
            .about-grid { grid-template-columns: 1fr; } 
            .hero { padding: 60px 20px; }
            .hero h1 { font-size: 2.2rem; }
            .hero p { font-size: 1rem; }
            .section-title { font-size: 1.8rem; }
            .visitor-info ul { flex-direction: column; }
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="hero">
        <h1>Welcome to Museo de Labo</h1>
        <p>Preserving the rich history, culture, and heritage of Camarines Norte. Step through our doors to uncover the stories of our ancestors and the treasures of our past.</p>
        
        <?php if (!$is_logged_in): ?>
            <a href="login.php" class="hero-btn">Request Access to Digital Catalog</a>
        <?php else: ?>
            <a href="categories.php" class="hero-btn">Enter the Catalog</a>
        <?php endif; ?>
    </div>

    <div class="container">
        <h2 class="section-title" id="aboutTitle">About the Museum</h2>
        <div class="about-grid">
            <div class="about-text" id="aboutText">
                <p>Located in the heart of Camarines Norte, the <strong>Museo de Labo</strong> serves as the primary custodian of the municipality's historical artifacts, cultural relics, and artistic heritage.</p>
                <p>Our mission is to educate, inspire, and connect both locals and tourists with the vibrant legacy of Labo. From ancient indigenous roots to the Spanish colonial era and the rich mining history of the region, every piece in our collection tells a unique story.</p>
                <p>Step through our doors and immerse yourself in history. We warmly invite students, researchers, and history enthusiasts to walk our halls and experience the rich heritage of Camarines Norte firsthand. Plan your visit today, join one of our guided tours, and let our curated exhibits transport you through time!</p>
                
                <div class="visitor-info">
                    <h4>Plan Your Visit</h4>
                    <ul>
                        <li><span>📍</span> <span><strong>Location:</strong> <a href="https://www.google.com/maps/dir/?api=1&destination=Municipal+Hall+Compound, Labo, Camarines Norte" target="_blank" style="color: inherit; text-decoration: none;">Municipal Hall Compound, Labo, Camarines Norte</a></span></li>
                        <li><span>🕒</span> <span><strong>Hours:</strong> Monday to Friday, 8:00 AM - 5:00 PM</span></li>
                        <li><span>🎟️</span> <span><strong>Admission:</strong> Free (Please sign our visitor logbook upon arrival)</span></li>
                    </ul>
                </div>

            </div>
            <div class="about-image">
                <img src="uploads/background.jpg" alt="Museo de Labo Building" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;" onerror="this.style.display='none'; this.parentNode.innerHTML='[Insert Museum Photo Here]';">
            </div>
        </div>
    </div>

    <?php if ($is_logged_in): ?>
    <div class="container" style="padding-top: 0;">
        <h2 class="section-title" id="recentTitle">Latest Acquisitions</h2>
        <p style="text-align: center; color: #7f8c8d; margin-bottom: 30px;">Get a sneak peek at the newest historical pieces added to our archives.</p>
        
        <div class="gallery-grid">
            <?php if($recent_result->num_rows > 0): ?>
                <?php while($row = $recent_result->fetch_assoc()): ?>
                    <a href="exhibit_detail.php?id=<?php echo $row['id']; ?>" class="card-link">
                        <div class="card">
                            <img src="uploads/<?php echo $row['image_path']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                            <div class="card-body">
                                <h3 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                                <div class="card-meta">
                                    <strong>Period:</strong> <?php echo $row['artifact_year'] ? htmlspecialchars($row['artifact_year']) : 'Unknown'; ?><br>
                                    <strong>Origin:</strong> <?php echo $row['origin'] ? htmlspecialchars($row['origin']) : 'Labo'; ?>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="grid-column: 1 / -1; text-align: center; font-size: 1.2rem; color: #7f8c8d; padding: 40px;">Check back soon for new artifacts!</p>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="exhibits.php" style="color: #c5a059; font-weight: bold; text-decoration: none; font-size: 1.1rem;">View All Artifacts &rarr;</a>
        </div>
    </div>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set hash
            history.replaceState(null, null, '#home');
            
            // Animate sections on scroll
            const aboutTitle = document.getElementById('aboutTitle');
            const aboutText = document.getElementById('aboutText');
            const recentTitle = document.getElementById('recentTitle');
            const cards = document.querySelectorAll('.card');
            
            const observerOptions = { root: null, rootMargin: '0px', threshold: 0.2 };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        if (entry.target.id === 'aboutTitle') {
                            entry.target.classList.add('visible');
                        } else if (entry.target.id === 'aboutText') {
                            entry.target.classList.add('visible');
                        } else if (entry.target.id === 'recentTitle') {
                            entry.target.classList.add('visible');
                        }
                    }
                });
            }, observerOptions);
            
            if (aboutTitle) observer.observe(aboutTitle);
            if (aboutText) observer.observe(aboutText);
            if (recentTitle) observer.observe(recentTitle);
            
            // Animate cards when visible
            const cardObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        cardObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            
            cards.forEach(card => cardObserver.observe(card));
        });
    </script>

</body>
</html>

