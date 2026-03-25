<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | Museo de Labo</title>
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/hero.css">
    <link rel="stylesheet" href="assets/css/news.css">
    <link rel="stylesheet" href="assets/css/about.css">
    <link rel="stylesheet" href="assets/css/gallery.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
</head>
<body>

    <?php include dirname(__DIR__, 2) . '/templates/components/header.php'; ?>

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
        <div class="news-section-padding">
            <div class="news-carousel-container">
                <?php foreach ($news_items as $index => $news): ?>
                    <div class="news-slide <?php echo $news['type'] == 'event' ? 'event' : ''; ?> <?php echo $index === 0 ? 'active' : ''; ?>">
                        <div class="featured-content">
                            <span class="featured-badge">
                                <?php echo $news['type'] == 'event' ? 'Upcoming Event' : 'Latest News'; ?>
                            </span>
                            <h3><?php echo htmlspecialchars($news['title']); ?></h3>
                            <p><?php echo htmlspecialchars(mb_strimwidth($news['content'], 0, 150, '...')); ?></p>
                            <a href="news.php" class="btn-read-more">View full details &rarr;</a>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (count($news_items) > 1): ?>
                <div class="news-controls">
                    <?php foreach ($news_items as $index => $news): ?>
                        <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>" data-news-index="<?php echo $index; ?>"></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="container <?php echo !empty($news_items) ? 'container--no-top-padding' : ''; ?>">
        <h2 class="section-title">About the Museum</h2>
        <div class="about-grid">
            <div class="about-text">
                <p>Located in the heart of Camarines Norte, the <strong>Museo de Labo</strong> serves as the primary custodian of the municipality's historical artifacts, cultural relics, and artistic heritage.</p>
                <p>Our mission is to educate, inspire, and connect both locals and tourists with the vibrant legacy of Labo. From ancient indigenous roots to the Spanish colonial era and the rich mining history of the region, every piece in our collection tells a unique story.</p>

                <div class="visitor-info">
                    <h4>Plan Your Visit</h4>
                    <ul>
                        <li><span>Location:</span> <span><strong>Municipal Hall Compound, Labo, Camarines Norte</strong></span></li>
                        <li><span>Hours:</span> <span><strong>Monday to Friday, 8:00 AM - 5:00 PM</strong></span></li>
                        <li><span>Admission:</span> <span><strong>Free (Please sign our visitor logbook upon arrival)</strong></span></li>
                    </ul>
                </div>
            </div>
            <div class="about-image">
                <img class="about-image-media" src="uploads/background.jpg" alt="Museo de Labo Building" onerror="this.style.display='none'; this.parentNode.innerHTML='[Insert Museum Photo Here]';">
            </div>
        </div>
    </div>

    <div class="container container--tight-top">
        <h2 class="section-title">Latest Acquisitions</h2>
        <p class="section-subtitle">Get a sneak peek at the newest historical pieces added to our archives.</p>

        <?php if (!empty($exhibits_items)): ?>
            <div class="gallery-wrapper">
                <button class="slider-arrow left-arrow" data-gallery-dir="-1" aria-label="Previous exhibits">&#10094;</button>
                <button class="slider-arrow right-arrow" data-gallery-dir="1" aria-label="Next exhibits">&#10095;</button>

                <div class="gallery-track" id="galleryTrack">
                    <?php foreach ($exhibits_items as $row): ?>
                        <a href="<?php echo $is_logged_in ? 'exhibit_detail.php?id=' . $row['id'] : 'login.php'; ?>" class="gallery-card">
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
                                    <p class="restricted-note">
                                        Historical details are restricted.<br>
                                        <strong class="restricted-note-highlight">Click to Sign Guestbook</strong>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php else: ?>
            <p class="empty-message">Check back soon for new artifacts!</p>
        <?php endif; ?>

        <?php if ($is_logged_in): ?>
        <div class="catalog-cta-wrap">
            <a href="exhibits.php" class="catalog-cta-link">View Entire Catalog &rarr;</a>
        </div>
        <?php endif; ?>
    </div>

    <script src="assets/js/news-carousel.js"></script>
    <script src="assets/js/gallery-slider.js"></script>
</body>
</html>
