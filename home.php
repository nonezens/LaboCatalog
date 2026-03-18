<?php
// This file redirects to the SPA
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting...</title>
    <script>
        window.location.href = 'index.php#home';
    </script>
</head>
<body>
    <p>Redirecting to home...</p>
</body>
</html>

    <!-- Hero Section -->
    <div class="hero">
        <h1>Welcome to Museo de Labo</h1>
        <p>Preserving the rich history, culture, and heritage of Camarines Norte. Step through our doors to uncover the stories of our ancestors and the treasures of our past.</p>
        <?php if (!$is_logged_in): ?>
            <a href="login.php" class="hero-btn">Sign Digital Guestbook</a>
        <?php else: ?>
            <a href="exhibits.php" class="hero-btn">Enter the Catalog</a>
        <?php endif; ?>
    </div>

    <!-- News Carousel -->
    <section class="home-section">
        <div class="news-carousel-wrapper">
            <?php if (!empty($news_items)): ?>
                <div class="news-carousel">
                    <div class="news-slides-container">
                        <?php foreach($news_items as $index => $news): ?>
                            <div class="news-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>">
                                <div class="slide-content">
                                    <span class="news-badge <?php echo $news['type'] == 'event' ? 'event-badge' : 'news-badge'; ?>">
                                        <?php echo $news['type'] == 'event' ? '📅 Event' : '📰 News'; ?>
                                    </span>
                                    <h2><?php echo htmlspecialchars($news['title']); ?></h2>
                                    <p><?php echo htmlspecialchars(mb_strimwidth($news['content'], 0, 200, "...")); ?></p>
                                    <a href="news.php" class="read-more">Read More →</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (count($news_items) > 1): ?>
                        <div class="carousel-controls">
                            <button class="carousel-btn prev-btn" aria-label="Previous">❮</button>
                            <div class="dot-indicators">
                                <?php foreach($news_items as $index => $news): ?>
                                    <button class="dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>" aria-label="Go to slide <?php echo $index + 1; ?>"></button>
                                <?php endforeach; ?>
                            </div>
                            <button class="carousel-btn next-btn" aria-label="Next">❯</button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- About Section -->
    <section class="home-section">
        <h2 class="section-title">About the Museum</h2>
        <div class="about-container">
            <div class="about-content">
                <p>Located in the heart of Camarines Norte, the <strong>Museo de Labo</strong> serves as the primary custodian of the municipality's historical artifacts, cultural relics, and artistic heritage.</p>
                <p>Our mission is to educate, inspire, and connect both locals and tourists with the vibrant legacy of Labo. From ancient indigenous roots to the Spanish colonial era and the rich mining history of the region, every piece in our collection tells a unique story.</p>
                
                <div class="visit-info">
                    <h3>Plan Your Visit</h3>
                    <ul>
                        <li><span class="icon">📍</span> <span><strong>Location:</strong> Municipal Hall Compound, Labo, Camarines Norte</span></li>
                        <li><span class="icon">🕐</span> <span><strong>Hours:</strong> Monday to Friday, 8:00 AM - 5:00 PM</span></li>
                        <li><span class="icon">🎟️</span> <span><strong>Admission:</strong> Free (Please sign our visitor logbook upon arrival)</span></li>
                    </ul>
                </div>
            </div>
            <div class="about-image">
                <img src="uploads/background.jpg" alt="Museo de Labo" 
     onerror="this.parentNode.innerHTML='<div style=\'background: #ddd; display: flex; align-items: center; justify-content: center; height: 100%; color: #999; border-radius: 8px;\'>Museum Photo</div>';">
            </div>
        </div>
    </section>

    <!-- Latest Acquisitions Gallery -->
    <section class="home-section">
        <h2 class="section-title">Latest Acquisitions</h2>
        <p class="section-subtitle">Discover the newest historical pieces added to our archives</p>
        
        <?php if (!empty($exhibits_items)): ?>
            <div class="gallery-container">
                <button class="gallery-nav prev-nav" aria-label="Previous items">&#10094;</button>
                
                <div class="gallery-viewport">
                    <div class="gallery-track" id="galleryTrack">
                        <?php foreach($exhibits_items as $exhibit): ?>
                            <a href="<?php echo $is_logged_in ? 'exhibit_detail.php?id='.$exhibit['id'] : 'login.php'; ?>" class="gallery-item">
                                <div class="item-image">
                                    <img src="uploads/<?php echo htmlspecialchars($exhibit['image_path']); ?>" alt="<?php echo htmlspecialchars($exhibit['title']); ?>">
                                </div>
                                <div class="item-details">
                                    <h3><?php echo htmlspecialchars($exhibit['title']); ?></h3>
                                    <?php if ($is_logged_in): ?>
                                        <p class="item-meta">
                                            <strong>Period:</strong> <?php echo !empty($exhibit['artifact_year']) ? htmlspecialchars($exhibit['artifact_year']) : 'Unknown'; ?>
                                        </p>
                                    <?php else: ?>
                                        <p class="restricted-msg">Sign guestbook to view details</p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <button class="gallery-nav next-nav" aria-label="Next items">&#10095;</button>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #999; padding: 40px;">No artifacts available yet. Check back soon!</p>
        <?php endif; ?>

        <?php if ($is_logged_in): ?>
            <div class="view-all-btn">
                <a href="exhibits.php">View Entire Catalog →</a>
            </div>
        <?php endif; ?>
    </section>

    <script src="js/home.js"></script>
</body>
</html>
