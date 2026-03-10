<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db.php'; 

// 1. Check if the user is logged in (Guest or Admin)
$is_logged_in = isset($_SESSION['guest_logged_in']) || isset($_SESSION['admin_logged_in']);

// 2. Fetch the single most recent News/Event to feature on the homepage
$news_query = "SELECT * FROM news_events ORDER BY date_posted DESC LIMIT 1";
$news_result = $conn->query($news_query);
$featured_news = $news_result->num_rows > 0 ? $news_result->fetch_assoc() : null;

// 3. ONLY fetch the latest acquisitions if they are logged in!
if ($is_logged_in) {
    $recent_query = "SELECT * FROM exhibits ORDER BY id DESC LIMIT 4";
    $recent_result = $conn->query($recent_query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Welcome | Museo de Labo</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9f9f9; margin: 0; padding: 0; }
        
        /* Hero Section */
        .hero { 
            background: linear-gradient(rgba(44, 62, 80, 0.9), rgba(26, 37, 47, 0.9)), url('hero-bg.jpg') center/cover; 
            color: white; 
            text-align: center; 
            padding: 100px 20px; 
            border-bottom: 5px solid #c5a059;
        }
        .hero h1 { font-size: 3.5rem; margin: 0 0 15px 0; letter-spacing: 2px; }
        .hero p { font-size: 1.2rem; color: #ecf0f1; max-width: 700px; margin: 0 auto 30px auto; line-height: 1.6; }
        .hero-btn { display: inline-block; padding: 15px 35px; background: #c5a059; color: white; text-decoration: none; border-radius: 30px; font-size: 1.1rem; font-weight: bold; transition: 0.3s; box-shadow: 0 4px 15px rgba(197, 160, 89, 0.4); }
        .hero-btn:hover { background: #b48a3d; transform: translateY(-3px); }

        /* Container & Sections */
        .container { max-width: 1200px; margin: 0 auto; padding: 60px 20px; }
        .section-title { text-align: center; color: #2c3e50; font-size: 2.2rem; margin-bottom: 40px; }
        .section-title::after { content: ''; display: block; width: 80px; height: 3px; background: #c5a059; margin: 15px auto 0 auto; }

        /* --- NEW: Featured News Banner --- */
        .featured-news-banner {
            background: white;
            border-left: 5px solid #3498db;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            margin: -40px auto 40px auto; /* Pulls it up slightly over the hero section boundary */
            max-width: 1000px;
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .featured-news-banner.event { border-left-color: #8e44ad; }
        .featured-badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; color: white; margin-bottom: 10px; text-transform: uppercase; background: #3498db; }
        .featured-news-banner.event .featured-badge { background: #8e44ad; }
        .featured-content { flex-grow: 1; }
        .featured-content h3 { margin: 0 0 10px 0; color: #2c3e50; font-size: 1.5rem; }
        .featured-content p { margin: 0 0 15px 0; color: #555; line-height: 1.5; font-size: 1rem; }
        .btn-read-more { color: #c5a059; font-weight: bold; text-decoration: none; transition: 0.3s; }
        .btn-read-more:hover { color: #2c3e50; }

        /* About Section */
        .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .about-text { font-size: 1.1rem; color: #555; line-height: 1.8; }
        .about-text p { margin-bottom: 20px; }
        .about-image { background: #eee; border-radius: 8px; height: 100%; min-height: 350px; display: flex; align-items: center; justify-content: center; color: #aaa; font-style: italic; border: 2px dashed #ccc; }

        /* Visitor Info Guide */
        .visitor-info { margin-top: 25px; padding-top: 25px; border-top: 1px solid #eee; }
        .visitor-info h4 { margin-top: 0; color: #c5a059; font-size: 1.2rem; margin-bottom: 15px; }
        .visitor-info ul { list-style-type: none; padding: 0; margin: 0; }
        .visitor-info li { margin-bottom: 10px; display: flex; align-items: flex-start; gap: 10px; }
        .visitor-info li strong { color: #2c3e50; }

        /* Latest Acquisitions Grid (Only for logged-in users) */
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px; margin-top: 20px; }
        .card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: transform 0.3s; border: 1px solid #eee; display: flex; flex-direction: column; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .card img { width: 100%; height: 220px; object-fit: cover; }
        .card-body { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
        .card-title { margin: 0 0 10px 0; color: #2c3e50; font-size: 1.3rem; }
        .card-meta { font-size: 0.9rem; color: #7f8c8d; margin-bottom: 15px; }
        .btn-view { display: block; text-align: center; padding: 10px; background: #2c3e50; color: white; text-decoration: none; border-radius: 4px; font-weight: bold; transition: 0.3s; margin-top: auto; }
        .btn-view:hover { background: #c5a059; }
        
        /* --- RESPONSIVE ADJUSTMENTS --- */
        @media (max-width: 768px) { 
            .about-grid { grid-template-columns: 1fr; } 
            .hero { padding: 60px 20px; }
            .hero h1 { font-size: 2.2rem; }
            .hero p { font-size: 1rem; }
            .section-title { font-size: 1.8rem; }
            .visitor-info ul { flex-direction: column; }
            .featured-news-banner { flex-direction: column; align-items: flex-start; margin-top: 20px; margin-bottom: 20px; }
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

    <?php if ($featured_news): ?>
        <div style="padding: 0 20px;">
            <div class="featured-news-banner <?php echo $featured_news['type'] == 'event' ? 'event' : ''; ?>">
                <div class="featured-content">
                    <span class="featured-badge">
                        <?php echo $featured_news['type'] == 'event' ? '📅 Upcoming Event' : '📰 Latest News'; ?>
                    </span>
                    <h3><?php echo htmlspecialchars($featured_news['title']); ?></h3>
                    
                    <p><?php echo htmlspecialchars(mb_strimwidth($featured_news['content'], 0, 150, "...")); ?></p>
                    
                    <a href="news.php" class="btn-read-more">View full details &rarr;</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="container" style="<?php echo $featured_news ? 'padding-top: 0;' : ''; ?>">
        <h2 class="section-title">About the Museum</h2>
        <div class="about-grid">
            <div class="about-text">
                <p>Located in the heart of Camarines Norte, the <strong>Museo de Labo</strong> serves as the primary custodian of the municipality's historical artifacts, cultural relics, and artistic heritage.</p>
                <p>Our mission is to educate, inspire, and connect both locals and tourists with the vibrant legacy of Labo. From ancient indigenous roots to the Spanish colonial era and the rich mining history of the region, every piece in our collection tells a unique story.</p>
                <p>Step through our doors and immerse yourself in history. We warmly invite students, researchers, and history enthusiasts to walk our halls and experience the rich heritage of Camarines Norte firsthand. Plan your visit today, join one of our guided tours, and let our curated exhibits transport you through time!</p>
                
                <div class="visitor-info">
                    <h4>Plan Your Visit</h4>
                    <ul>
                        <li><span>📍</span> <span><strong>Location:</strong> Municipal Hall Compound, Labo, Camarines Norte</span></li>
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
        <h2 class="section-title">Latest Acquisitions</h2>
        <p style="text-align: center; color: #7f8c8d; margin-bottom: 30px;">Get a sneak peek at the newest historical pieces added to our archives.</p>
        
        <div class="gallery-grid">
            <?php if($recent_result->num_rows > 0): ?>
                <?php while($row = $recent_result->fetch_assoc()): ?>
                    <div class="card">
                        <img src="uploads/<?php echo $row['image_path']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                        <div class="card-body">
                            <h3 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                            <div class="card-meta">
                                <strong>Period:</strong> <?php echo $row['artifact_year'] ? htmlspecialchars($row['artifact_year']) : 'Unknown'; ?><br>
                                <strong>Origin:</strong> <?php echo $row['origin'] ? htmlspecialchars($row['origin']) : 'Labo'; ?>
                            </div>
                            <a href="exhibit_detail.php?id=<?php echo $row['id']; ?>" class="btn-view">View Details</a>
                        </div>
                    </div>
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

</body>
</html>