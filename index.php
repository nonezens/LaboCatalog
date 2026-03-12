<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db.php'; 

// 1. Check if the user is logged in (Guest or Admin)
$is_logged_in = isset($_SESSION['guest_logged_in']) || isset($_SESSION['admin_logged_in']);

// 2. Fetch data
$news_query = "SELECT * FROM news_events ORDER BY date_posted DESC LIMIT 5";
$news_result = $conn->query($news_query);

$categories_query = "SELECT * FROM categories ORDER BY name ASC";
$categories_result = $conn->query($categories_query);

$all_exhibits_query = "SELECT * FROM exhibits ORDER BY id DESC";
$all_exhibits_result = $conn->query($all_exhibits_query);
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
    
    <style>
        /* Tab Navigation Styles */
        .tab-nav {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
            flex-wrap: wrap;
        }
        
        .tab-btn {
            padding: 12px 25px;
            border: none;
            background: white;
            color: #2c3e50;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            border-radius: 25px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .tab-btn:hover {
            background: #c5a059;
            color: white;
        }
        
        .tab-btn.active {
            background: #c5a059;
            color: white;
        }
        
        /* Tab Content Sections - Animated Fade-in */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .tab-content {
            display: none; /* Keep hidden by default */
            width: 100%;
        }
        
        .tab-content.active {
            display: block; /* Change to block to show */
            animation: fadeIn 0.6s ease-in-out forwards; /* Apply the animation */
        }
        
        /* Static Hero */
        .hero { 
            background: linear-gradient(rgba(44, 62, 80, 0.8), rgba(26, 37, 47, 0.8));
            color: white; 
            text-align: center; 
            padding: 100px 20px; 
            border-bottom: 5px solid #c5a059;
        }

        .hero h1 { font-size: 3.5rem; margin: 0 0 15px 0; letter-spacing: 2px; text-shadow: 2px 2px 8px rgba(0,0,0,0.5); }
        .hero p { font-size: 1.2rem; color: #ecf0f1; max-width: 700px; margin: 0 auto 30px auto; line-height: 1.6; text-shadow: 1px 1px 4px rgba(0,0,0,0.5); }

        .hero-btn { 
            display: inline-block; padding: 15px 35px; background: #c5a059; color: white; 
            text-decoration: none; border-radius: 30px; font-size: 1.1rem; font-weight: bold; 
            transition: 0.3s; box-shadow: 0 4px 15px rgba(197, 160, 89, 0.4);
        }
        .hero-btn:hover { background: #b48a3d; transform: translateY(-3px) scale(1.05); }

        /* Static Sections */
        .section-title { text-align: center; color: #2c3e50; font-size: 2.2rem; margin-bottom: 40px; }
        .section-title::after { content: ''; display: block; width: 80px; height: 3px; background: #c5a059; margin: 15px auto 0 auto; }

        /* About */
        .about-text { font-size: 1.1rem; color: #333; line-height: 1.7; text-align: center; max-width: 800px; margin: 0 auto; }
        .about-text p { margin-bottom: 20px; }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }
        .info-box {
            background: #fdfdfd;
            border: 1px solid #eee;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
        }
        .info-box h3 {
            margin-top: 0;
            color: #c5a059;
        }

        /* Categories Grid */
        .cat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px; padding: 20px 0; }
        .cat-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .cat-card:hover { transform: translateY(-5px); }
        .cat-card img { width: 100%; height: 200px; object-fit: cover; }
        .cat-body { padding: 20px; text-align: center; }
        .cat-title { margin: 0 0 15px 0; color: #2c3e50; }
        .btn-view { display: inline-block; padding: 10px 20px; background: #c5a059; color: white; text-decoration: none; border-radius: 20px; font-weight: bold; }

        /* Exhibits Grid */
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px; }
        .card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eee; }
        .card img { width: 100%; height: 220px; object-fit: cover; }
        .card-body { padding: 20px; }
        .card-title { margin: 0 0 10px 0; color: #2c3e50; }
        .card-meta { font-size: 0.9rem; color: #7f8c8d; margin-bottom: 15px; }

        /* Guest Banner */
        .guest-banner { background: linear-gradient(rgba(44, 62, 80, 0.9), rgba(26, 37, 47, 0.9)); color: white; text-align: center; padding: 60px 20px; margin: 20px; border-radius: 12px; }
        .guest-banner h3 { font-size: 2rem; margin-bottom: 15px; color: #c5a059; }
        .cta-btn { display: inline-block; padding: 15px 35px; background: #c5a059; color: white; text-decoration: none; border-radius: 30px; font-size: 1.1rem; font-weight: bold; margin-top: 20px; }

        /* Carousel */
        .carousel { position: relative; display: block; width: 100%; box-sizing: border-box; margin: 0 auto; max-width: 100%; min-height: 450px; overflow: hidden; }
        .carousel__prev, .carousel__next { position: absolute; top: 50%; transform: translateY(-50%); width: 50px; height: 50px; cursor: pointer; z-index: 100; display: flex; align-items: center; justify-content: center; font-size: 30px; color: #c5a059; background: rgba(44, 62, 80, 0.8); border-radius: 50%; }
        .carousel__prev:hover, .carousel__next:hover { transform: translateY(-50%) scale(1.25); background: #c5a059; color: white; }
        .carousel__prev { left: 5%; }
        .carousel__next { right: 5%; }
        .carousel__body { width: 100%; padding: 20px 0 60px 0; overflow: visible; position: relative; }
        .carousel__slider { position: relative; transition: transform 0.6s ease-in-out; display: flex; justify-content: flex-start; align-items: center; }
        .carousel__slider__item { position: relative; display: block; box-sizing: border-box; margin: 0 20px; flex-shrink: 0; }
        .item__3d-frame { position: relative; width: 100%; height: 100%; transition: transform 0.6s ease-in-out; transform-style: preserve-3d; }
        .item__3d-frame__box { display: flex; align-items: center; justify-content: center; position: absolute; width: 100%; height: 100%; box-sizing: border-box; border-color: #c5a059; background: #fff; border-width: 3px; border-style: solid; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .item__3d-frame__box--right, .item__3d-frame__box--left { top: 0; width: 35px; height: 100%; backface-visibility: hidden; background: #b48a3d; }
        .item__3d-frame__box--left { left: 0; border-left-width: 5px; transform: translate3d(1px, 0, -35px) rotateY(-90deg); transform-origin: 0%; }
        .item__3d-frame__box--right { right: 0; border-right-width: 5px; transform: translate3d(-1px, 0, -35px) rotateY(90deg); transform-origin: 100%; }
        .carousel__slider__item--active .item__3d-frame { transform: perspective(1200px) rotateY(0deg); z-index: 10; }

        /* Header Animation Control */
        .site-header, .site-header .logo-img, .site-header .logo-text, .site-header .baybayin-text {
            transition: all 0.4s ease-in-out;
        }
        .site-header.header-large {
            background: transparent !important;
            box-shadow: none !important;
        }
        .header-large .logo-img {
            height: 80px;
        }
        .header-large .logo-text {
            font-size: 2rem;
        }
        .header-large .baybayin-text {
            font-size: 1rem;
        }

        /* News Carousel Simple */
        .news-carousel-container {
            position: relative;
            max-width: 900px;
            margin: 0 auto;
        }
        .news-card {
            display: none;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid #eee;
        }
        .news-card.active {
            display: block;
            animation: fadeIn 0.5s ease-in-out;
        }
        .news-card-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }
        .news-nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(44, 62, 80, 0.7);
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.3s;
            z-index: 10;
        }
        .news-nav-btn:hover {
            background: #c5a059;
        }
        .news-nav-btn.prev {
            left: -25px;
        }
        .news-nav-btn.next {
            right: -25px;
        }
        @media (max-width: 950px) {
            .news-nav-btn.prev { left: 10px; }
            .news-nav-btn.next { right: 10px; }
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <!-- HOME SECTION -->
    <div id="home" class="tab-content active">
        <div class="hero">
            <h1>Welcome to Museo de Labo</h1>
            <p>Preserving the rich history, culture, and heritage of Camarines Norte.</p>
            <?php if (!$is_logged_in): ?>
                <a href="login.php" class="hero-btn">Request Access to Digital Catalog</a>
            <?php else: ?>
                <a href="categories.php" class="hero-btn">Enter the Catalog</a>
            <?php endif; ?>
        </div>

        <!-- News & Events -->
        <div class="container" style="padding-top: 40px;">
            <?php 
            $news_result2 = $conn->query("SELECT * FROM news_events ORDER BY date_posted DESC LIMIT 5");
            if($news_result2 && $news_result2->num_rows > 0): 
                // Fetch all news items into an array
                $news_items = [];
                while($row = $news_result2->fetch_assoc()) {
                    $news_items[] = $row;
                }
            ?>
            <h2 class="section-title">News & Events</h2>
            <div class="news-carousel-container">
                <div class="news-cards-wrapper">
                    <?php foreach($news_items as $index => $row): ?>
                        <div class="news-card <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>">
                            <a href="news.php#news-<?php echo $row['id']; ?>" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; height: 100%;">
                                <?php if(!empty($row['image_path'])): ?>
                                    <img src="uploads/<?php echo $row['image_path']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="news-card-image">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h3 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                                    <p class="card-meta">
                                        <span class="swipe-badge <?php echo $row['type'] == 'event' ? 'type-event' : ''; ?>">
                                            <?php echo $row['type'] == 'event' ? '📅 Event' : '📰 News'; ?>
                                        </span>
                                        <br>
                                        <?php echo date("F j, Y", strtotime($row['date_posted'])); ?>
                                    </p>
                                    <p><?php echo htmlspecialchars(substr($row['content'], 0, 150)); ?>...</p>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($news_items) > 1): ?>
                    <button class="news-nav-btn prev" id="news-prev">&#10094;</button>
                    <button class="news-nav-btn next" id="news-next">&#10095;</button>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Latest Acquisitions -->
        <div class="container">
            <h2 class="section-title">Latest Acquisitions</h2>
            <div class="carousel">
                <div class="carousel__body">
                    <div class="carousel__slider">
                        <?php 
                        $carousel_result = $conn->query("SELECT * FROM exhibits ORDER BY id DESC");
                        $exhibit_count = 0;
                        while($row = $carousel_result->fetch_assoc()): 
                            $exhibit_count++;
                        ?>
                        <div class="carousel__slider__item">
                            <div class="item__3d-frame">
                                <?php if ($is_logged_in): ?>
                                    <a href="exhibit_detail.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; color: inherit;">
                                <?php else: ?>
                                    <div style="cursor: pointer;" onclick="alert('Please register to view full details!')">
                                <?php endif; ?>
                                <div class="item__3d-frame__box">
                                    <img src="uploads/<?php echo $row['image_path']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0;">
                                    <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.7); padding: 15px;">
                                        <h1 style="font-size: 1.2em; margin: 0; color: #fff;"><?php echo htmlspecialchars($row['title']); ?></h1>
                                    </div>
                                </div>
                                <?php if ($is_logged_in): ?></a><?php else: ?></div><?php endif; ?>
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
        </div>
    </div>

    <!-- DEPARTMENTS SECTION -->
    <div id="departments" class="tab-content">
        <div class="container">
            <h2 class="section-title">Museum Departments</h2>
            
            <?php if (!$is_logged_in): ?>
                <div class="guest-banner">
                    <h3>Experience History in Person</h3>
                    <p>Discover the rich heritage of Camarines Norte.</p>
                    <a href="login.php" class="cta-btn">Sign the Guestbook to Enter</a>
                </div>
            <?php else: ?>
                <div class="cat-grid">
                    <?php if($categories_result && $categories_result->num_rows > 0): ?>
                        <?php while($cat = $categories_result->fetch_assoc()): ?>
                            <div class="cat-card">
                                <img src="uploads/<?php echo $cat['image_path']; ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>">
                                <div class="cat-body">
                                    <h3 class="cat-title"><?php echo htmlspecialchars($cat['name']); ?></h3>
                                    <a href="exhibits.php?cat=<?php echo $cat['id']; ?>" class="btn-view">View Artifacts</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="grid-column: 1 / -1; text-align: center;">No departments found.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ALL ARTIFACTS SECTION -->
    <div id="artifacts" class="tab-content">
        <div class="container">
            <h2 class="section-title">All Artifacts</h2>
            
            <?php if (!$is_logged_in): ?>
                <div class="guest-banner">
                    <h3>View Our Complete Collection</h3>
                    <p>Register to access full details about each artifact.</p>
                    <a href="login.php" class="cta-btn">Sign the Guestbook to Enter</a>
                </div>
                <div class="gallery-grid">
                    <?php
                    $preview_result = $conn->query("SELECT id, title, image_path FROM exhibits ORDER BY id DESC");
                    if($preview_result && $preview_result->num_rows > 0):
                        while($row = $preview_result->fetch_assoc()):
                    ?>
                        <div class="card">
                            <img src="uploads/<?php echo $row['image_path']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" onclick="alert('Please register to view full details!')" style="cursor: pointer;">
                            <div class="card-body">
                                <h3 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                                <p class="card-meta" style="color: #c5a059; font-weight: bold;">🔒 Login to view</p>
                            </div>
                        </div>
                    <?php endwhile; endif; ?>
                </div>
            <?php else: ?>
                <div class="gallery-grid">
                    <?php if($all_exhibits_result && $all_exhibits_result->num_rows > 0): ?>
                        <?php while($row = $all_exhibits_result->fetch_assoc()): ?>
                            <a href="exhibit_detail.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; color: inherit;">
                                <div class="card">
                                    <img src="uploads/<?php echo $row['image_path']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                                    <div class="card-body">
                                        <h3 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                                        <div class="card-meta">
                                            <strong>Period:</strong> <?php echo $row['artifact_year'] ?: 'Unknown'; ?><br>
                                            <strong>Origin:</strong> <?php echo $row['origin'] ?: 'Unknown'; ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="grid-column: 1 / -1; text-align: center;">No artifacts found.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ABOUT SECTION -->
    <div id="about" class="tab-content">
        <div class="container">
            <h2 class="section-title">About the Museum</h2>
            <div class="about-text">
                <p>Founded in 2026, the <strong>Museo de Labo</strong> began as a digital initiative to document and preserve the rich cultural history of our region. Our mission is to provide an accessible platform where students, historians, and enthusiasts can explore artifacts that define our shared human experience.</p>
                <p>We believe that history should be interactive and inclusive. Through our digital exhibits, we bring the museum experience directly to your screen, ensuring that even the most fragile artifacts can be studied and appreciated without risk of damage.</p>
            </div>

            <div class="info-grid">
                <div class="info-box">
                    <h3>🕒 Visiting Hours</h3>
                    <p>Monday - Friday: 9:00 AM - 5:00 PM</p>
                    <p>Saturday: 10:00 AM - 4:00 PM</p>
                    <p>Sunday: Closed</p>
                </div>
                <div class="info-box">
                    <h3>📍 Contact Us</h3>
                    <p><strong>Address:</strong> 123 Heritage Lane, Labo, Philippines</p>
                    <p><strong>Email:</strong> info@labomuseum.ph</p>
                    <p><strong>Phone:</strong> +63 (054) 123-4567</p>
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="js/index.js"></script>
    <?php if ($is_admin): ?>
    <script src="js/admin.js"></script>
    <script src="js/manage.js"></script>
    <?php endif; ?>

</body>
</html>
