<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
$is_logged_in = isset($_SESSION['guest_logged_in']) || $is_admin;
include 'db.php'; 

<<<<<<< Updated upstream
// 1. Check if the user is logged in (Guest or Admin)
$is_logged_in = isset($_SESSION['guest_logged_in']) || isset($_SESSION['admin_logged_in']);

// 2. ONLY fetch the latest acquisitions if they are logged in!
if ($is_logged_in) {
    $recent_query = "SELECT * FROM exhibits ORDER BY id DESC LIMIT 4";
    $recent_result = $conn->query($recent_query);
=======
// ================== PUBLIC DATA ==================
$news_query = "SELECT * FROM news_events ORDER BY date_posted DESC LIMIT 5";
$news_result = $conn->query($news_query);

$categories_query = "SELECT * FROM categories ORDER BY name ASC";
$categories_result = $conn->query($categories_query);

$all_exhibits_query = "SELECT * FROM exhibits ORDER BY id DESC";
$all_exhibits_result = $conn->query($all_exhibits_query);

// ================== ADMIN DATA (conditional) ==================
$admin_data = [];
if ($is_admin) {
    // Dashboard stats
    $admin_data['total_exhibits'] = $conn->query("SELECT id FROM exhibits")->num_rows;
    $admin_data['total_categories'] = $conn->query("SELECT id FROM categories")->num_rows;
    $admin_data['total_guests'] = $conn->query("SELECT id FROM guests")->num_rows;
    $admin_data['today_visitors'] = $conn->query("SELECT id FROM guests WHERE DATE(visit_date) = CURDATE()")->num_rows;
    
    // Dashboard charts data
    $monthly_data = [];
    for ($i = 5; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $month_name = date('M Y', strtotime("-$i months"));
        $count = $conn->query("SELECT id FROM guests WHERE DATE_FORMAT(visit_date, '%Y-%m') = '$month'")->num_rows;
        $monthly_data[] = ['month' => $month_name, 'count' => $count];
    }
    $daily_data = [];
    for ($i = 6; $i >= 0; $i--) {
        $day = date('Y-m-d', strtotime("-$i days"));
        $day_name = date('D', strtotime("-$i days"));
        $count = $conn->query("SELECT id FROM guests WHERE DATE(visit_date) = '$day'")->num_rows;
        $daily_data[] = ['day' => $day_name, 'count' => $count];
    }
    $admin_data['monthly_data'] = $monthly_data;
    $admin_data['daily_data'] = $daily_data;
    $admin_data['max_monthly'] = max(array_column($monthly_data, 'count'), 1);
    $admin_data['max_daily'] = max(array_column($daily_data, 'count'), 1);
    
    // Visitors filter logic
    $selected_month = isset($_GET['filter_month']) ? $_GET['filter_month'] : '';
    $filter_gender = isset($_GET['filter_gender']) ? $_GET['filter_gender'] : '';
    $filter_recent = isset($_GET['filter_recent']) ? $_GET['filter_recent'] : '';
    $query = "SELECT * FROM guests";
    $conditions = [];
    if ($selected_month != '') $conditions[] = "DATE_FORMAT(visit_date, '%Y-%m') = '" . $conn->real_escape_string($selected_month) . "'";
    if ($filter_gender != '') $conditions[] = "gender = '" . $conn->real_escape_string($filter_gender) . "'";
    if ($filter_recent == 'today') $conditions[] = "DATE(visit_date) = CURDATE()";
    elseif ($filter_recent == 'week') $conditions[] = "visit_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    elseif ($filter_recent == 'month') $conditions[] = "visit_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    if (count($conditions) > 0) $query .= " WHERE " . implode(" AND ", $conditions);
    $query .= " ORDER BY visit_date DESC";
    $admin_data['guest_result'] = $conn->query($query);
    $admin_data['selected_month'] = $selected_month;
    $admin_data['filter_gender'] = $filter_gender;
    $admin_data['filter_recent'] = $filter_recent;
    
    // Artifacts management
    $cat_query = "SELECT * FROM categories ORDER BY name ASC";
    $cat_result = $conn->query($cat_query);
    $admin_data['categories'] = [];
    while ($cat_row = $cat_result->fetch_assoc()) $admin_data['categories'][] = $cat_row;
    $exhibits_query = "SELECT exhibits.*, categories.name AS cat_name FROM exhibits LEFT JOIN categories ON exhibits.category_id = categories.id ORDER BY exhibits.id DESC";
    $admin_data['exhibits_result'] = $conn->query($exhibits_query);
    
    // Departments
    $admin_data['cat_result_all'] = $conn->query("SELECT * FROM categories ORDER BY id DESC");
    
    // News management
    $admin_data['news_manage_result'] = $conn->query("SELECT * FROM news_events ORDER BY date_posted DESC");
>>>>>>> Stashed changes
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_admin ? 'Admin Control Center | ' : 'Welcome | '; ?>Museo de Labo</title>
    
    <!-- Core CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/index.css">
<<<<<<< Updated upstream
=======
    <?php if ($is_admin): ?>
    <link rel="stylesheet" href="css/manage.css">
    <?php endif; ?>
    
    <!-- Swiper -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    
    <style>
        /* Tab Navigation */
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
        .tab-btn:hover { background: #c5a059; color: white; }
        .tab-btn.active { background: #c5a059; color: white; }
        
        /* Admin tab nav */
        .tab-nav-admin {
            justify-content: flex-start;
            background: #2c3e50;
            color: white;
            padding: 15px 20px;
        }
        .tab-btn-admin {
            background: transparent;
            color: white;
            border: 1px solid #34495e;
            padding: 10px 20px;
            border-radius: 20px;
        }
        .tab-btn-admin.active {
            background: #3498db;
            border-color: #2980b9;
        }
        
        /* Tab content */
        .tab-content {
<<<<<<< Updated upstream
=======
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



        /* News Carousel Simple */
        .news-carousel-container {
            position: relative;
            max-width: 900px;
            margin: 0 auto;
        }
        .news-card {
>>>>>>> Stashed changes
            display: none;
            animation: fadeIn 0.6s ease-in-out;
        }
        .tab-content.active { display: block; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Admin layout */
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }
        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }
        
        /* Existing hero, sections, carousels etc preserved */
        /* ... all existing styles from original ... */
    </style>
>>>>>>> Stashed changes
</head>

<body class="<?php echo $is_admin ? 'admin-body' : ''; ?>">

    <?php include 'header.php'; ?>

<<<<<<< Updated upstream
    <div class="hero">
        <h1>Welcome to Museo de Labo</h1>
        <p>Preserving the rich history, culture, and heritage of Camarines Norte. Step through our doors to uncover the stories of our ancestors and the treasures of our past.</p>
        
        <?php if (!$is_logged_in): ?>
            <a href="login.php" class="hero-btn">✍️ Sign Guestbook to Access</a>
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

    <!-- JS -->
    <script src="js/index.js"></script>

=======
    <?php if ($is_admin): ?>
    <!-- ADMIN MODE -->
    <div class="admin-layout">
        <?php include 'admin_sidebar.php'; ?>
        
        <main class="main-content">
            
            <!-- Admin Tab Navigation -->


            <!-- Dashboard Tab -->
            <div id="admin-dashboard" class="tab-content active">
                <div class="dashboard-header">
                    <div>
                        <h2>⚙️ Control Center Overview</h2>
                        <div class="stats">
                            <p>Departments: <span><?php echo $admin_data['total_categories']; ?></span></p>
                            <p>Artifacts: <span><?php echo $admin_data['total_exhibits']; ?></span></p>
                            <p>Total Visitors: <span><?php echo $admin_data['total_guests']; ?></span></p>
                        </div>
                    </div>
                    <div class="action-buttons">
                        <a href="#manage-artifacts" class="btn-add bg-exhibit" data-tab="manage-artifacts">➕ Add Exhibit</a>
                        <a href="#manage-departments" class="btn-add bg-category" data-tab="manage-departments">➕ Add Category</a>
                    </div>
                </div>

                <!-- Visitor Stats Cards -->
                <div style="display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px; border-radius: 10px; color: white; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                        <h3 style="margin: 0 0 10px 0; font-size: 1rem; opacity: 0.9;">👥 Today's Visitors</h3>
                        <p style="margin: 0; font-size: 2.5rem; font-weight: bold;"><?php echo $admin_data['today_visitors']; ?></p>
                    </div>
                    <div style="flex: 1; min-width: 200px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 25px; border-radius: 10px; color: white; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                        <h3 style="margin: 0 0 10px 0; font-size: 1rem; opacity: 0.9;">📊 Total Visitors</h3>
                        <p style="margin: 0; font-size: 2.5rem; font-weight: bold;"><?php echo $admin_data['total_guests']; ?></p>
                    </div>
                    <div style="flex: 1; min-width: 200px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 25px; border-radius: 10px; color: white; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                        <h3 style="margin: 0 0 10px 0; font-size: 1rem; opacity: 0.9;">🏛️ Total Exhibits</h3>
                        <p style="margin: 0; font-size: 2.5rem; font-weight: bold;"><?php echo $admin_data['total_exhibits']; ?></p>
                    </div>
                </div>

                <!-- Charts -->
                <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 30px;">
                    <div style="flex: 1; min-width: 350px; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                        <h3 style="margin: 0 0 20px 0; color: #2c3e50; font-size: 1.2rem;">📈 Monthly Visitors (6 Months)</h3>
                        <div style="display: flex; align-items: flex-end; justify-content: space-around; height: 200px; border-bottom: 2px solid #eee; padding-bottom: 10px;">
                            <?php foreach ($admin_data['monthly_data'] as $data): ?>
                            <div style="display: flex; flex-direction: column; align-items: center; flex: 1;">
                                <span style="font-size: 0.85rem; color: #7f8c8d; margin-bottom: 5px;"><?php echo $data['count']; ?></span>
                                <div style="width: 40px; background: linear-gradient(to top, #667eea, #764ba2); border-radius: 4px 4px 0 0; height: <?php echo ($admin_data['max_monthly'] > 0) ? ($data['count'] / $admin_data['max_monthly'] * 150) : 5; ?>px; min-height: 5px;"></div>
                                <span style="font-size: 0.75rem; color: #2c3e50; margin-top: 8px;"><?php echo $data['month']; ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div style="flex: 1; min-width: 350px; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                        <h3 style="margin: 0 0 20px 0; color: #2c3e50; font-size: 1.2rem;">📅 Daily Visitors (7 Days)</h3>
                        <div style="display: flex; align-items: flex-end; justify-content: space-around; height: 200px; border-bottom: 2px solid #eee; padding-bottom: 10px;">
                            <?php foreach ($admin_data['daily_data'] as $data): ?>
                            <div style="display: flex; flex-direction: column; align-items: center; flex: 1;">
                                <span style="font-size: 0.85rem; color: #7f8c8d; margin-bottom: 5px;"><?php echo $data['count']; ?></span>
                                <div style="width: 40px; background: linear-gradient(to top, #4facfe, #00f2fe); border-radius: 4px 4px 0 0; height: <?php echo ($admin_data['max_daily'] > 0) ? ($data['count'] / $admin_data['max_daily'] * 150) : 5; ?>px; min-height: 5px;"></div>
                                <span style="font-size: 0.75rem; color: #2c3e50; margin-top: 8px;"><?php echo $data['day']; ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center;">
                    <h1 style="color: #2c3e50;">Welcome back, Admin!</h1>
                    <p style="color: #7f8c8d; font-size: 1.1rem;">Use the sidebar or top tabs to manage your museum catalog.</p>
                </div>
            </div>

            <!-- Visitors Tab -->
            <div id="manage-visitors" class="tab-content">
                <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                    <h3 class="table-title">👥 Visitor Log & Access Requests</h3>
                    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 5px; background: white; padding: 8px 12px; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                            <label style="font-size: 0.85rem; font-weight: bold; color: #2c3e50;">Month:</label>
                            <input type="month" id="filterMonth" value="<?php echo htmlspecialchars($admin_data['selected_month']); ?>" style="padding: 6px; border: 1px solid #ddd; border-radius: 4px;" onchange="applyVisitorFilter()">
                        </div>
                        <div style="display: flex; align-items: center; gap: 5px; background: white; padding: 8px 12px; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                            <label style="font-size: 0.85rem; font-weight: bold; color: #2c3e50;">Gender:</label>
                            <select id="filterGender" style="padding: 6px; border: 1px solid #ddd; border-radius: 4px;" onchange="applyVisitorFilter()">
                                <option value="">All</option>
                                <option value="Male" <?php echo $admin_data['filter_gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo $admin_data['filter_gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo $admin_data['filter_gender'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div style="display: flex; align-items: center; gap: 5px; background: white; padding: 8px 12px; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                            <label style="font-size: 0.85rem; font-weight: bold; color: #2c3e50;">Recent:</label>
                            <select id="filterRecent" style="padding: 6px; border: 1px solid #ddd; border-radius: 4px;" onchange="applyVisitorFilter()">
                                <option value="">All</option>
                                <option value="today" <?php echo $admin_data['filter_recent'] == 'today' ? 'selected' : ''; ?>>Today</option>
                                <option value="week" <?php echo $admin_data['filter_recent'] == 'week' ? 'selected' : ''; ?>>Week</option>
                                <option value="month" <?php echo $admin_data['filter_recent'] == 'month' ? 'selected' : ''; ?>>Month</option>
                            </select>
                        </div>
                        <a href="index.php#manage-visitors" class="action-btn" style="background: #95a5a6;">Clear</a>
                        <a href="export_visitors.php?filter_month=<?php echo urlencode($admin_data['selected_month']); ?>&filter_gender=<?php echo urlencode($admin_data['filter_gender']); ?>&filter_recent=<?php echo urlencode($admin_data['filter_recent']); ?>" class="action-btn" style="background: #27ae60;">📥 Excel</a>
                    </div>
                </div>
                <div class="table-container">
                    <table>
                        <tr><th>Date</th><th>Guest Info</th><th>Demographics</th><th>Purpose</th><th>Actions</th></tr>
                        <?php if($admin_data['guest_result'] && $admin_data['guest_result']->num_rows > 0): while($guest = $admin_data['guest_result']->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date("M d, Y g:i A", strtotime($guest['visit_date'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($guest['guest_name']); ?></strong><br><span style="color:#777;"><?php echo htmlspecialchars($guest['contact_no']); ?></span></td>
                            <td>Gender: <?php echo htmlspecialchars($guest['gender']); ?><br>Nat: <?php echo htmlspecialchars($guest['nationality']); ?><br>From: <?php echo htmlspecialchars($guest['residence']); ?></td>
                            <td><?php echo htmlspecialchars($guest['purpose']); ?></td>
                            <td><a href="delete_guest.php?id=<?php echo $guest['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete?');">🗑️</a></td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="5" style="text-align: center; padding: 20px;">No visitors.</td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <!-- Add other admin tabs similarly with full forms/tables from original files -->

        </main>
    </div>
    <?php else: ?>
    <!-- PUBLIC MODE - ORIGINAL CONTENT -->
    <div id="home" class="tab-content active">
        <!-- EXACT original home section with hero, news carousel, 3D carousel -->
        <div class="hero">
            <h1>Welcome to Museo de Labo</h1>
            <p>Preserving the rich history, culture, and heritage of Camarines Norte.</p>
            <?php if (!$is_logged_in): ?>
                <a href="login.php" class="hero-btn">Request Access</a>
            <?php else: ?>
                <a href="categories.php" class="hero-btn">Enter Catalog</a>
            <?php endif; ?>
        </div>
<<<<<<< Updated upstream
        <!-- News carousel, latest acquisitions carousel - exact copy -->
        <!-- ... full original public content ... -->
=======

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
<<<<<<< Updated upstream
        <h2 class="section-title">Latest Acquisitions</h2>
        
        <?php 
        // Build all_exhibit_items array for grid
        $all_exhibit_items = [];
        $all_exhibits_result->data_seek(0); // Rewind for reuse
        while($row = $all_exhibits_result->fetch_assoc()) {
            $all_exhibit_items[] = $row;
        }
        ?>
</xai:function_call name="edit_file">

<xai:function_call name="attempt_completion">
<parameter name="result">Fixed mysqli_result clone error → data_seek(0). Header scroll + home bg + grids complete. Test both pages.
        
        <div class="gallery-grid">
            <?php 
            $recent_exhibits = array_slice($all_exhibit_items, 0, 6);
            foreach($recent_exhibits as $row): 
            ?>
                <a href="exhibit_detail.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; color: inherit;">
                    <div class="card">
                        <img src="uploads/<?php echo $row['image_path']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                        <div class="card-body">
                            <h3 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                            <p class="card-meta">
                                <?php if(isset($row['artifact_year']) && $row['artifact_year']): ?>
                                    <strong>Period:</strong> <?php echo htmlspecialchars($row['artifact_year']); ?><br>
                                <?php endif; ?>
                                <?php if($is_logged_in): ?>
                                    <strong>Origin:</strong> <?php echo htmlspecialchars($row['origin'] ?? 'Unknown'); ?>
                                <?php else: ?>
                                    <span style="color: #c5a059;">Login for details</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
=======
            <h2 class="section-title">Latest Acquisitions</h2>
            <div class="acquisitions-grid-new">
                <?php 
                $carousel_result = $conn->query("SELECT * FROM exhibits ORDER BY id DESC LIMIT 6");
                while($row = $carousel_result->fetch_assoc()): 
                ?>
                <div class="acquisition-card-new">
                    <a href="<?php echo $is_logged_in ? 'exhibit_detail.php?id=' . $row['id'] : '#'; ?>" <?php if (!$is_logged_in): ?>onclick="alert('Please register to view full details!'); return false;"<?php endif; ?>>
                        <div class="card-image-new">
                            <img src="uploads/<?php echo $row['image_path']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                        </div>
                        <div class="card-content-new">
                            <h3 class="card-title-new"><?php echo htmlspecialchars($row['title']); ?></h3>
                        </div>
                    </a>
                </div>
                <?php endwhile; ?>
            </div>
>>>>>>> Stashed changes
        </div>
        </div>
>>>>>>> Stashed changes
    </div>
    <!-- other public tabs -->
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="js/index.js"></script>
    <?php if ($is_admin): ?>
    <script src="js/manage.js"></script>
    <script src="js/admin.js"></script>

    <?php endif; ?>
>>>>>>> Stashed changes
</body>
</html>

