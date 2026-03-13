<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db.php'; 

// 1. Check if the user is logged in (Guest or Admin)
$is_logged_in = isset($_SESSION['guest_logged_in']) || isset($_SESSION['admin_logged_in']);

// 2. Fetch up to 5 of the most recent News/Events for the Carousel
$news_query = "SELECT * FROM news_events ORDER BY id DESC LIMIT 5";
$news_result = $conn->query($news_query);
$news_items = [];
if($news_result && $news_result->num_rows > 0) {
    while($row = $news_result->fetch_assoc()) {
        $news_items[] = $row;
    }
}

// 3. Fetch the latest 8 acquisitions for the Gallery Slider
$recent_query = "SELECT * FROM exhibits ORDER BY id DESC LIMIT 8";
$recent_result = $conn->query($recent_query);
$exhibits_items = [];
if($recent_result && $recent_result->num_rows > 0) {
    while($row = $recent_result->fetch_assoc()) {
        $exhibits_items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Welcome | Museo de Labo</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9f9f9; margin: 0; padding: 0; }
        
        /* --- Hero Section --- */
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

        .container { max-width: 1200px; margin: 0 auto; padding: 60px 20px; }
        .section-title { text-align: center; color: #2c3e50; font-size: 2.2rem; margin-bottom: 40px; }
        .section-title::after { content: ''; display: block; width: 80px; height: 3px; background: #c5a059; margin: 15px auto 0 auto; }

        /* --- News Carousel --- */
        .news-carousel-container { background: white; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); margin: -40px auto 40px auto; max-width: 1000px; position: relative; z-index: 10; overflow: hidden; }
        .news-slide { display: none; padding: 30px; border-left: 5px solid #3498db; align-items: center; gap: 20px; animation: fadeIn 0.5s ease-in-out; }
        .news-slide.active { display: flex; }
        .news-slide.event { border-left-color: #8e44ad; }
        .featured-badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; color: white; margin-bottom: 10px; text-transform: uppercase; background: #3498db; }
        .news-slide.event .featured-badge { background: #8e44ad; }
        .featured-content { flex-grow: 1; text-align: left; }
        .featured-content h3 { margin: 0 0 10px 0; color: #2c3e50; font-size: 1.5rem; }
        .featured-content p { margin: 0 0 15px 0; color: #555; line-height: 1.5; font-size: 1rem; }
        .btn-read-more { color: #c5a059; font-weight: bold; text-decoration: none; transition: 0.3s; }
        .btn-read-more:hover { color: #2c3e50; }
        
        .news-controls { text-align: center; padding-bottom: 15px; }
        .dot { height: 10px; width: 10px; margin: 0 5px; background-color: #ddd; border-radius: 50%; display: inline-block; cursor: pointer; transition: 0.3s; }
        .dot.active { background-color: #c5a059; transform: scale(1.3); }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }

        /* --- About Section --- */
        .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .about-text { font-size: 1.1rem; color: #555; line-height: 1.8; }
        .about-text p { margin-bottom: 20px; }
        .about-image { background: #eee; border-radius: 8px; height: 100%; min-height: 350px; display: flex; align-items: center; justify-content: center; color: #aaa; font-style: italic; border: 2px dashed #ccc; overflow: hidden; }

        .visitor-info { margin-top: 25px; padding-top: 25px; border-top: 1px solid #eee; }
        .visitor-info h4 { margin-top: 0; color: #c5a059; font-size: 1.2rem; margin-bottom: 15px; }
        .visitor-info ul { list-style-type: none; padding: 0; margin: 0; }
        .visitor-info li { margin-bottom: 10px; display: flex; align-items: flex-start; gap: 10px; }
        .visitor-info li strong { color: #2c3e50; }

        /* --- Multi-Item Gallery Slider --- */
        .gallery-wrapper { position: relative; max-width: 1200px; margin: 0 auto; padding: 0 50px; overflow: hidden; }
        .gallery-track { display: flex; gap: 20px; transition: transform 0.5s ease-in-out; }
        
        /* The entire card is now a clickable link! */
        .gallery-card { flex: 0 0 calc(33.333% - 13.33px); background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eee; display: flex; flex-direction: column; transition: transform 0.3s; text-decoration: none; color: inherit; cursor: pointer; }
        .gallery-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .gallery-card .img-container { background: #111; height: 320px; width: 100%; display: flex; align-items: center; justify-content: center; overflow: hidden;}
        .gallery-card img { width: 100%; height: 100%; object-fit: contain; }
        
        .card-body { padding: 20px; display: flex; flex-direction: column; flex-grow: 1; }
        .card-title { margin: 0 0 10px 0; color: #2c3e50; font-size: 1.2rem; text-align: center; transition: color 0.3s; }
        .gallery-card:hover .card-title { color: #c5a059; } /* Title turns gold on hover! */
        .card-meta { font-size: 0.85rem; color: #7f8c8d; margin-bottom: 15px; text-align: center; }

        /* Circular Navigation Arrows */
        .slider-arrow { position: absolute; top: 45%; transform: translateY(-50%); width: 45px; height: 45px; border-radius: 50%; background: #2c3e50; color: white; border: none; font-size: 1.2rem; cursor: pointer; z-index: 10; display: flex; align-items: center; justify-content: center; transition: background 0.3s, transform 0.3s; box-shadow: 0 4px 10px rgba(0,0,0,0.2); }
        .slider-arrow:hover { background: #c5a059; transform: translateY(-50%) scale(1.1); }
        .left-arrow { left: 0; }
        .right-arrow { right: 0; }

        /* Mobile Adjustments */
        @media (max-width: 992px) {
            .gallery-card { flex: 0 0 calc(50% - 10px); } 
        }
        @media (max-width: 768px) { 
            .about-grid { grid-template-columns: 1fr; } 
            .hero { padding: 60px 20px; }
            .hero h1 { font-size: 2.2rem; }
            .news-slide { flex-direction: column; text-align: center; padding: 20px; }
            .featured-content { text-align: center; }
            
            .gallery-wrapper { padding: 0 40px; }
            .gallery-card { flex: 0 0 100%; }
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="hero">
        <h1>Welcome to Museo de Labo</h1>
        <p>Preserving the rich history, culture, and heritage of Camarines Norte. Step through our doors to uncover the stories of our ancestors and the treasures of our past.</p>
        
        <?php if (!$is_logged_in): ?>
            <a href="login.php" class="hero-btn">Sign Digital Guestbook</a>
        <?php else: ?>
            <a href="categories.php" class="hero-btn">Enter the Catalog</a>
        <?php endif; ?>
    </div>

    <?php if (!empty($news_items)): ?>
        <div style="padding: 0 20px;">
            <div class="news-carousel-container">
                <?php foreach($news_items as $index => $news): ?>
                    <div class="news-slide <?php echo $news['type'] == 'event' ? 'event' : ''; ?> <?php echo $index === 0 ? 'active' : ''; ?>">
                        <div class="featured-content">
                            <span class="featured-badge">
                                <?php echo $news['type'] == 'event' ? '📅 Upcoming Event' : '📰 Latest News'; ?>
                            </span>
                            <h3><?php echo htmlspecialchars($news['title']); ?></h3>
                            <p><?php echo htmlspecialchars(mb_strimwidth($news['content'], 0, 150, "...")); ?></p>
                            <a href="news.php" class="btn-read-more">View full details &rarr;</a>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if (count($news_items) > 1): ?>
                <div class="news-controls">
                    <?php foreach($news_items as $index => $news): ?>
                        <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>" onclick="currentNewsSlide(<?php echo $index; ?>)"></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="container" style="<?php echo !empty($news_items) ? 'padding-top: 0;' : ''; ?>">
        <h2 class="section-title">About the Museum</h2>
        <div class="about-grid">
            <div class="about-text">
                <p>Located in the heart of Camarines Norte, the <strong>Museo de Labo</strong> serves as the primary custodian of the municipality's historical artifacts, cultural relics, and artistic heritage.</p>
                <p>Our mission is to educate, inspire, and connect both locals and tourists with the vibrant legacy of Labo. From ancient indigenous roots to the Spanish colonial era and the rich mining history of the region, every piece in our collection tells a unique story.</p>
                
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

    <div class="container" style="padding-top: 20px;">
        <h2 class="section-title">Latest Acquisitions</h2>
        <p style="text-align: center; color: #7f8c8d; margin-bottom: 30px;">Get a sneak peek at the newest historical pieces added to our archives.</p>
        
        <?php if(!empty($exhibits_items)): ?>
            <div class="gallery-wrapper">
                <button class="slider-arrow left-arrow" onclick="moveGallery(-1)">&#10094;</button>
                <button class="slider-arrow right-arrow" onclick="moveGallery(1)">&#10095;</button>

                <div class="gallery-track" id="galleryTrack">
                    <?php foreach($exhibits_items as $index => $row): ?>
                        <a href="<?php echo $is_logged_in ? 'exhibit_detail.php?id='.$row['id'] : 'login.php'; ?>" class="gallery-card">
                            <div class="img-container">
                                <img src="uploads/<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                            </div>
                            <div class="card-body">
                                <h3 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                                
                                <?php if ($is_logged_in): ?>
                                    <div class="card-meta">
                                        <strong>Period:</strong> <?php echo !empty($row['artifact_year']) ? htmlspecialchars($row['artifact_year']) : 'Unknown'; ?><br>
                                        <strong>Origin:</strong> <?php echo !empty($row['origin']) ? htmlspecialchars($row['origin']) : 'Labo'; ?>
                                    </div>
                                <?php else: ?>
                                    <p style="color: #999; font-style: italic; font-size: 0.85rem; text-align: center; flex-grow: 1; margin: 5px 0;">
                                        Historical details are restricted.<br>
                                        <strong style="color: #c5a059; margin-top: 5px; display: inline-block;">Click to Sign Guestbook</strong>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
        <?php else: ?>
            <p style="text-align: center; font-size: 1.2rem; color: #7f8c8d; padding: 40px;">Check back soon for new artifacts!</p>
        <?php endif; ?>

        <?php if ($is_logged_in): ?>
        <div style="text-align: center; margin-top: 50px;">
            <a href="exhibits.php" style="color: #c5a059; font-weight: bold; text-decoration: none; font-size: 1.1rem; border: 2px solid #c5a059; padding: 12px 25px; border-radius: 30px; transition: 0.3s;">View Entire Catalog &rarr;</a>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // 1. NEWS CAROUSEL LOGIC
        let newsIndex = 0;
        const newsSlides = document.querySelectorAll('.news-slide');
        const dots = document.querySelectorAll('.dot');
        let newsTimer;

        function showNews(index) {
            if (newsSlides.length === 0) return;
            if (index >= newsSlides.length) { newsIndex = 0; }
            else if (index < 0) { newsIndex = newsSlides.length - 1; }
            else { newsIndex = index; }

            newsSlides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));

            newsSlides[newsIndex].classList.add('active');
            if(dots.length > 0) dots[newsIndex].classList.add('active');
        }

        function currentNewsSlide(index) {
            clearInterval(newsTimer); 
            showNews(index);
            startNewsTimer(); 
        }

        function startNewsTimer() {
            if (newsSlides.length > 1) {
                newsTimer = setInterval(() => { showNews(newsIndex + 1); }, 5000); 
            }
        }
        showNews(newsIndex);
        startNewsTimer();

        // 2. MULTI-ITEM GALLERY SLIDER LOGIC
        let currentGalleryIndex = 0;

        function moveGallery(direction) {
            const track = document.getElementById('galleryTrack');
            const cards = track.querySelectorAll('.gallery-card');
            if (cards.length === 0) return;

            let visibleItems = 3; 
            if (window.innerWidth <= 768) { visibleItems = 1; } 
            else if (window.innerWidth <= 992) { visibleItems = 2; } 

            const maxIndex = cards.length - visibleItems;

            currentGalleryIndex += direction;
            
            if (currentGalleryIndex > maxIndex) { currentGalleryIndex = 0; }
            if (currentGalleryIndex < 0) { currentGalleryIndex = maxIndex; }

            const cardWidth = cards[0].getBoundingClientRect().width;
            const gap = 20; 
            const moveAmount = currentGalleryIndex * (cardWidth + gap);

            track.style.transform = `translateX(-${moveAmount}px)`;
        }

        window.addEventListener('resize', () => {
            currentGalleryIndex = 0;
            const track = document.getElementById('galleryTrack');
            if(track) track.style.transform = `translateX(0px)`;
        });

    </script>
</body>
</html>