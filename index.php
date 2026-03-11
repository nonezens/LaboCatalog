<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db.php'; 

// 1. Check if the user is logged in (Guest or Admin)
$is_logged_in = isset($_SESSION['guest_logged_in']) || isset($_SESSION['admin_logged_in']);

// 2. Fetch the latest acquisitions for ALL visitors (logged in or not)
$recent_query = "SELECT * FROM exhibits ORDER BY id DESC LIMIT 4";
$recent_result = $conn->query($recent_query);

// 3. Fetch news/events for carousel
$news_query = "SELECT * FROM news_events ORDER BY date_posted DESC LIMIT 5";
$news_result = $conn->query($news_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | Museo de Labo</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/index.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
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

    <!-- News & Events Carousel -->
<?php 
    $news_result2 = $conn->query("SELECT * FROM news_events ORDER BY date_posted DESC LIMIT 5");
    if($news_result2 && $news_result2->num_rows > 0): 
        $news_items = [];
        while($row = $news_result2->fetch_assoc()) {
            $news_items[] = $row;
        }
        $itemCount = count($news_items);
    ?>
    <div class="news-carousel-section">
        <h2 class="section-title">News & Events</h2>
        <?php if($itemCount > 1): ?>
        <button class="carousel-btn news-carousel-btn prev" onclick="moveNewsCarousel(-1)">&#10094;</button>
        <button class="carousel-btn news-carousel-btn next" onclick="moveNewsCarousel(1)">&#10095;</button>
        <?php endif; ?>
        <div class="carousel-container">
            <div class="carousel-track" id="newsCarouselTrack">
                <?php 
                // No duplication - items displayed once, JavaScript handles auto-advance
                foreach($news_items as $index => $row): ?>
                    <div class="carousel-card">
                        <?php if(!empty($row['image_path'])): ?>
                        <img src="uploads/<?php echo $row['image_path']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="news-card-image">
                        <?php endif; ?>
                        <span class="news-badge <?php echo $row['type'] == 'event' ? 'type-event' : ''; ?>">
                            <?php echo $row['type'] == 'event' ? '📅 Event' : '📰 News'; ?>
                        </span>
                        <h3 class="news-card-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p class="news-card-date">
                            <?php if($row['type'] == 'event' && $row['event_date']): ?>
                                <?php echo date("F j, Y", strtotime($row['event_date'])); ?>
                            <?php else: ?>
                                <?php echo date("F j, Y", strtotime($row['date_posted'])); ?>
                            <?php endif; ?>
                        </p>
                        <p class="news-card-excerpt"><?php echo htmlspecialchars(substr($row['content'], 0, 100)); ?>...</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

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
<li><span>🎟️</span> <span><strong>Admission:</strong> Free (Digital catalog access via online registration)</span></li>
                    </ul>
                </div>

            </div>
            <div class="about-image">
                <img src="uploads/background.jpg" alt="Museo de Labo Building" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;" onerror="this.style.display='none'; this.parentNode.innerHTML='[Insert Museum Photo Here]';">
            </div>
        </div>
    </div>

    <hr class="section-divider">
    
    <div class="container" style="padding-top: 0;">
        <h2 class="section-title" id="recentTitle">Latest Acquisitions</h2>
        <p style="text-align: center; color: #7f8c8d; margin-bottom: 30px;">Get a sneak peek at the newest historical pieces added to our archives.</p>
        
        <?php 
        // Get total count of all exhibits
        $total_count_query = "SELECT COUNT(*) as total FROM exhibits";
        $total_count_result = $conn->query($total_count_query);
        $total_exhibits = $total_count_result->fetch_assoc()['total'];
        
        // Fetch all exhibits for JavaScript navigation (if more than 6)
        $all_exhibits_query = "SELECT * FROM exhibits ORDER BY id DESC";
        $all_exhibits_result = $conn->query($all_exhibits_query);
        $all_exhibit_items = [];
        while($row = $all_exhibits_result->fetch_assoc()) {
            $all_exhibit_items[] = $row;
        }
        
        // Only show first 6 for display
        $exhibit_items = array_slice($all_exhibit_items, 0, 6);
        
        $has_navigation = $total_exhibits > 6;
        ?>
        
        <!-- 3D Carousel for Latest Acquisitions -->
        <div class="carousel">
            <div class="carousel__body">
                <div class="carousel__slider">
                    <?php 
                    // Get all exhibits for the carousel (we'll use all available)
                    $carousel_query = "SELECT * FROM exhibits ORDER BY id DESC";
                    $carousel_result = $conn->query($carousel_query);
                    $exhibit_count = 0;
                    while($row = $carousel_result->fetch_assoc()): 
                        $exhibit_count++;
                    ?>
                    <div class="carousel__slider__item">
                        <div class="item__3d-frame">
                            <?php if ($is_logged_in): ?>
                                <a href="exhibit_detail.php?id=<?php echo $row['id']; ?>" class="card-link" style="text-decoration: none; color: inherit;">
                            <?php else: ?>
                                <div class="card-link" style="cursor: pointer;" onclick="alert('Please register to view full details!');">
                            <?php endif; ?>
                            <div class="item__3d-frame__box">
                                <img src="uploads/<?php echo $row['image_path']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0;">
                                <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.7); padding: 15px;">
                                    <h1 style="font-size: 1.2em; margin: 0; color: #fff;"><?php echo htmlspecialchars($row['title']); ?></h1>
                                    <?php if ($is_logged_in): ?>
                                    <p style="font-size: 0.8em; margin: 5px 0 0 0; color: #ccc;">Period: <?php echo $row['artifact_year'] ? htmlspecialchars($row['artifact_year']) : 'Unknown'; ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($is_logged_in): ?>
                                </a>
                            <?php else: ?>
                                </div>
                            <?php endif; ?>
                            <div class="item__3d-frame__box item__3d-frame__box--left"></div>
                            <div class="item__3d-frame__box item__3d-frame__box--right"></div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php if ($exhibit_count > 1): ?>
            <div class="carousel__prev">&#10094;</div>
            <div class="carousel__next">&#10095;</div>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <?php if (!$is_logged_in): ?>
                <a href="login.php" style="color: #c5a059; font-weight: bold; text-decoration: none; font-size: 1.1rem;">Register to View All Artifacts &rarr;</a>
            <?php else: ?>
                <a href="exhibits.php" style="color: #c5a059; font-weight: bold; text-decoration: none; font-size: 1.1rem;">View All Artifacts &rarr;</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="js/index.js"></script>

</body>
</html>

