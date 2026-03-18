<?php 
session_start();
include 'db.php'; 

// ==================== PHP LOGIC ====================

// Check if user is logged in
$is_logged_in = isset($_SESSION['guest_logged_in']) || isset($_SESSION['admin_logged_in']);

// Fetch News Items
$news_query = "SELECT * FROM news_events ORDER BY id DESC LIMIT 5";
$news_result = $conn->query($news_query);
$news_items = [];
if($news_result && $news_result->num_rows > 0) {
    while($row = $news_result->fetch_assoc()) {
        $news_items[] = $row;
    }
}

// Fetch Latest Exhibits
$recent_query = "SELECT * FROM exhibits ORDER BY id DESC LIMIT 8";
$recent_result = $conn->query($recent_query);
$exhibits_items = [];
if($recent_result && $recent_result->num_rows > 0) {
    while($row = $recent_result->fetch_assoc()) {
        $exhibits_items[] = $row;
    }
}

// Fetch All News (for news section)
$all_news_query = "SELECT * FROM news_events ORDER BY date_posted DESC";
$all_news_result = $conn->query($all_news_query);

// Fetch Categories
$categories_query = "SELECT * FROM categories ORDER BY name ASC";
$categories_result = $conn->query($categories_query);

// Fetch All Exhibits
$all_exhibits_query = "SELECT * FROM exhibits ORDER BY id DESC";
$all_exhibits_result = $conn->query($all_exhibits_query);

// Handle Login/Registration
$msg = "";
$msg_color = "red";

// Admin Login
if (isset($_POST['admin_login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['admin_logged_in'] = true;
        $msg = "Admin login successful!";
        $msg_color = "green";
        $is_logged_in = true;
    } else {
        $msg = "Invalid Admin Username or Password.";
        $msg_color = "red";
    }
}

// Guest Registration
if (isset($_POST['register_guest'])) {
    $visitor_type = $_POST['visitor_type'];
    $name = trim($_POST['guest_name']); 
    $gender = $_POST['gender']; 
    $residence = $_POST['residence'];
    $nationality = $_POST['nationality']; 
    
    if ($visitor_type === 'Group') {
        $organization = trim($_POST['organization']);
        $male_count = (int)$_POST['male_count'];
        $female_count = (int)$_POST['female_count'];
        $headcount = $male_count + $female_count; 
    } else {
        $organization = 'N/A';
        $headcount = 1;
        $male_count = ($gender == 'Male') ? 1 : 0;
        $female_count = ($gender == 'Female') ? 1 : 0;
    }
    
    $num_days = isset($_POST['num_days']) ? (int)$_POST['num_days'] : 1;
    $purpose = isset($_POST['purpose']) ? $_POST['purpose'] : 'Visit';
    $contact = isset($_POST['contact_no']) ? "+63" . ltrim(trim($_POST['contact_no']), '0') : '';

    $stmt = $conn->prepare("INSERT INTO guests (guest_name, visitor_type, organization, gender, residence, nationality, headcount, male_count, female_count, num_days, purpose, contact_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt) {
        $stmt->bind_param("ssssssiiiiss", $name, $visitor_type, $organization, $gender, $residence, $nationality, $headcount, $male_count, $female_count, $num_days, $purpose, $contact);
        if ($stmt->execute()) {
            $_SESSION['guest_logged_in'] = true;
            $_SESSION['guest_name'] = $name;
            $msg = "Welcome! You've been registered.";
            $msg_color = "green";
            $is_logged_in = true;
        } else {
            $msg = "Error saving data: " . $stmt->error;
            $msg_color = "red";
        }
    }
}

// Guest Login
if (isset($_POST['guest_login'])) {
    $name = trim($_POST['login_name']);
    $stmt = $conn->prepare("SELECT * FROM guests WHERE guest_name = ? LIMIT 1");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['guest_logged_in'] = true;
        $_SESSION['guest_name'] = $name;
        $msg = "Welcome back!";
        $msg_color = "green";
        $is_logged_in = true;
    } else {
        $msg = "Name not found. Please sign the guestbook first!";
        $msg_color = "red";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Museo de Labo - Single Page App</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/about.css">
    <link rel="stylesheet" href="css/news.css">
    <link rel="stylesheet" href="css/categories.css">
    <link rel="stylesheet" href="css/exhibits.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/admin-sidebar.css">
    <link rel="stylesheet" href="css/admin-dashboard.css">
    <style>
        /* SPA Section Management */
        .spa-section {
            display: none;
        }
        .spa-section.active {
            display: block;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <!-- ==================== HOME SECTION ==================== -->
    <div id="home" class="spa-section active">
        <div class="hero">
            <h1>Welcome to Museo de Labo</h1>
            <p>Preserving the rich history, culture, and heritage of Camarines Norte. Step through our doors to uncover the stories of our ancestors and the treasures of our past.</p>
            
            <?php if (!$is_logged_in): ?>
                <a href="#login" class="hero-btn">Sign Digital Guestbook</a>
            <?php else: ?>
                <a href="#allartifacts" class="hero-btn">Enter the Catalog</a>
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
                                <a href="#news" class="btn-read-more">View full details &rarr;</a>
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
                            <a href="<?php echo $is_logged_in ? '#allartifacts' : '#login'; ?>" class="gallery-card">
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
                <a href="#allartifacts" style="color: #c5a059; font-weight: bold; text-decoration: none; font-size: 1.1rem; border: 2px solid #c5a059; padding: 12px 25px; border-radius: 30px; transition: 0.3s;">View Entire Catalog &rarr;</a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ==================== ABOUT SECTION ==================== -->
    <div id="about" class="spa-section">
        <section class="about-hero">
            <h1>Our Story</h1>
            <p>Preserving the heritage of Labo for generations to come.</p>
        </section>

        <div class="container">
            <div class="content-section">
                <h2>Mission & History</h2>
                <p>Founded in 2026, the <strong>Museum Labo Catalog</strong> began as a digital initiative to document and preserve the rich cultural history of our region. Our mission is to provide an accessible platform where students, historians, and enthusiasts can explore artifacts that define our shared human experience.</p>
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

            <div class="content-section" style="margin-top: 60px;">
                <h2>Find Us</h2>
                <div class="location-map">
                    <p style="color: #777;">[ Interactive Map Placeholder ]</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== NEWS SECTION ==================== -->
    <div id="news" class="spa-section">
        <h1 class="page-title">The Museum Chronicle</h1>
        <div class="page-subtitle">Latest Updates & Upcoming Events</div>

        <div class="news-feed">
            <?php if($all_news_result->num_rows > 0): ?>
                <?php while($row = $all_news_result->fetch_assoc()): ?>
                    
                    <article class="news-article clearfix">
                        
                        <span class="news-type <?php echo $row['type'] == 'event' ? 'type-event' : ''; ?>">
                            <?php echo $row['type'] == 'event' ? '📅 Upcoming Event' : '📰 Museum News'; ?>
                        </span>
                        
                        <h2 class="news-title"><?php echo htmlspecialchars($row['title']); ?></h2>
                        
                        <div class="news-meta">
                            <?php if($row['type'] == 'event' && $row['event_date']): ?>
                                Scheduled for: <?php echo date("F j, Y", strtotime($row['event_date'])); ?>
                            <?php else: ?>
                                Published on: <?php echo date("F j, Y", strtotime($row['date_posted'])); ?>
                            <?php endif; ?>
                        </div>
                        
                        <?php if($row['image_path']): ?>
                            <img src="uploads/<?php echo $row['image_path']; ?>" alt="News Image" class="news-image">
                        <?php endif; ?>
                        
                        <div class="news-content">
                            <?php echo nl2br(htmlspecialchars($row['content'])); ?>
                        </div>
                        
                    </article>

                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; font-size: 1.2rem; color: #7f8c8d; padding: 40px;">No news or events at the moment. Check back soon!</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- ==================== DEPARTMENTS SECTION ==================== -->
    <div id="departments" class="spa-section">
        <?php if (!$is_logged_in): ?>
            
            <div style="background: linear-gradient(135deg, #2c3e50, #1a252f); color: white; text-align: center; padding: 80px 20px; min-height: 60vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                <h3 style="margin: 0 0 15px 0; font-size: 2.5rem; letter-spacing: 1px;">Experience History in Person</h3>
                <p style="margin: 0 0 40px 0; font-size: 1.2rem; color: #ecf0f1; max-width: 600px; line-height: 1.6;">
                    Discover the rich heritage of Camarines Norte. Visit the real artifacts at the <strong style="color: #c5a059;">Museo de Labo</strong> in Labo!
                </p>
                
                <p style="color: #95a5a6; font-size: 1rem; margin-bottom: 15px;">Want to browse the digital collection?</p>
                <a href="#login" style="padding: 15px 35px; background: #c5a059; color: white; text-decoration: none; border-radius: 30px; font-size: 1.1rem; font-weight: bold; transition: 0.3s; box-shadow: 0 4px 15px rgba(197, 160, 89, 0.4);">
                    Sign the Guestbook to Enter
                </a>
            </div>

        <?php else: ?>

            <div class="container">
                <h1 class="page-title">Explore Museum Departments</h1>

                <div class="search-container">
                    <div class="search-wrapper">
                        <input type="text" id="deptSearchInput" class="search-input" placeholder="Search departments (e.g., Ancient Egypt)..." autocomplete="off" onkeyup="liveFilterDepts()">
                    </div>
                    <button type="button" class="btn-search" onclick="document.getElementById('deptSearchInput').value=''; liveFilterDepts();">Clear</button>
                </div>

                <div class="cat-grid" id="deptGrid">
                    <?php if($categories_result && $categories_result->num_rows > 0): ?>
                        <?php while($cat = $categories_result->fetch_assoc()): 
                            $searchable_text = strtolower($cat['name']);
                        ?>
                            <div class="cat-card" data-search="<?php echo htmlspecialchars($searchable_text); ?>">
                                <img src="uploads/<?php echo $cat['image_path']; ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>">
                                <div class="cat-body">
                                    <h3 class="cat-title"><?php echo htmlspecialchars($cat['name']); ?></h3>
                                    <a href="#allartifacts" class="btn-view">View Artifacts &rarr;</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="grid-column: 1 / -1; text-align: center; font-size: 1.2rem; color: #7f8c8d; padding: 40px;">No departments found in the database.</p>
                    <?php endif; ?>

                    <p id="deptNoResultsMessage" style="grid-column: 1 / -1; text-align: center; font-size: 1.2rem; color: #7f8c8d; padding: 40px; display: none;">No departments match your search.</p>
                </div>
            </div>

        <?php endif; ?>
    </div>

    <!-- ==================== ALL ARTIFACTS SECTION ==================== -->
    <div id="allartifacts" class="spa-section">
        <?php if (!$is_logged_in): ?>
            
            <div style="background: linear-gradient(135deg, #2c3e50, #1a252f); color: white; text-align: center; padding: 80px 20px; min-height: 60vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                <h3 style="margin: 0 0 15px 0; font-size: 2.5rem; letter-spacing: 1px;">Premium Collection Awaits</h3>
                <p style="margin: 0 0 40px 0; font-size: 1.2rem; color: #ecf0f1; max-width: 600px; line-height: 1.6;">
                    Get exclusive access to our complete artifact collection.
                </p>
                
                <p style="color: #95a5a6; font-size: 1rem; margin-bottom: 15px;">Sign the guestbook to unlock full access:</p>
                <a href="#login" style="padding: 15px 35px; background: #c5a059; color: white; text-decoration: none; border-radius: 30px; font-size: 1.1rem; font-weight: bold; transition: 0.3s; box-shadow: 0 4px 15px rgba(197, 160, 89, 0.4);">
                    Sign Guestbook & Enter
                </a>
            </div>

        <?php else: ?>

            <div class="container">
                <h1 class="page-title">All Artifacts</h1>

                <div class="search-container">
                    <div class="search-wrapper">
                        <input type="text" id="artifactsSearchInput" class="search-input" placeholder="Search artifacts..." autocomplete="off" onkeyup="liveFilterArtifacts()">
                    </div>
                    <button type="button" class="btn-search" onclick="document.getElementById('artifactsSearchInput').value=''; liveFilterArtifacts();">Clear</button>
                </div>

                <div class="gallery-grid" id="artifactsGrid">
                    <?php if($all_exhibits_result && $all_exhibits_result->num_rows > 0): ?>
                        <?php while($art = $all_exhibits_result->fetch_assoc()): 
                            $searchable_text = strtolower($art['title']);
                        ?>
                            <a href="#allartifacts" class="card" data-search="<?php echo htmlspecialchars($searchable_text); ?>">
                                <img src="uploads/<?php echo htmlspecialchars($art['image_path']); ?>" alt="<?php echo htmlspecialchars($art['title']); ?>">
                                <div class="card-body">
                                    <h3 class="card-title"><?php echo htmlspecialchars($art['title']); ?></h3>
                                    <div class="card-meta">
                                        <strong>Period:</strong> <?php echo !empty($art['artifact_year']) ? htmlspecialchars($art['artifact_year']) : 'Unknown'; ?><br>
                                        <strong>Origin:</strong> <?php echo !empty($art['origin']) ? htmlspecialchars($art['origin']) : 'Labo'; ?>
                                    </div>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="grid-column: 1 / -1; text-align: center; font-size: 1.2rem; color: #7f8c8d; padding: 40px;">No artifacts found in the database.</p>
                    <?php endif; ?>

                    <p id="artifactsNoResultsMessage" style="grid-column: 1 / -1; text-align: center; font-size: 1.2rem; color: #7f8c8d; padding: 40px; display: none;">No artifacts match your search.</p>
                </div>
            </div>

        <?php endif; ?>
    </div>

    <!-- ==================== LOGIN SECTION ==================== -->
    <div id="login" class="spa-section">
        <div class="page-container">
            
            <?php if($msg): ?>
                <div style="color: <?php echo $msg_color == 'green' ? 'green' : 'red'; ?>; text-align: center; margin-bottom: 15px; padding: 10px; background: <?php echo $msg_color == 'green' ? '#d4edda' : '#fdeedc'; ?>; border-radius: 4px;">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <h2>Sign Digital Guestbook</h2>
                <form method="POST" onsubmit="window.location.hash='#login'; return true;">
                    
                    <div class="form-group">
                        <label>Visitor Type</label>
                        <select name="visitor_type" id="visitor_type" class="form-control" onchange="toggleGroupFields()" required>
                            <option value="Individual">Individual / Family</option>
                            <option value="Group">School / Organization / Tour Group</option>
                        </select>
                    </div>

                    <div id="group_fields">
                        <div class="form-group">
                            <label>Organization / School Name</label>
                            <input type="text" name="organization" id="org_input" class="form-control" placeholder="e.g., Labo National High School">
                        </div>
                        <div class="row">
                            <div class="form-group col">
                                <label>Number of Males</label>
                                <input type="number" name="male_count" id="male_input" class="form-control" min="0" value="0">
                            </div>
                            <div class="form-group col">
                                <label>Number of Females</label>
                                <input type="number" name="female_count" id="female_input" class="form-control" min="0" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label id="name_label">Full Name</label>
                        <input type="text" name="guest_name" class="form-control" placeholder="Juan Dela Cruz" required>
                    </div>

                    <div class="row">
                        <div class="form-group col">
                            <label>Gender</label>
                            <select name="gender" class="form-control" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group col">
                            <label>Contact Number</label>
                            <input type="tel" name="contact_no" class="form-control" placeholder="09123456789" pattern="09[0-9]{9}" maxlength="11" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col">
                            <label>Residence (Town/City)</label>
                            <input type="text" name="residence" class="form-control" placeholder="e.g., Labo" required>
                        </div>
                        <div class="form-group col">
                            <label>Nationality</label>
                            <input type="text" name="nationality" class="form-control" value="Filipino" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col">
                            <label>Purpose of Visit</label>
                            <input type="text" name="purpose" class="form-control" placeholder="e.g., Research, Tour" required>
                        </div>
                        <div class="form-group col">
                            <label>Days of Stay</label>
                            <input type="number" name="num_days" class="form-control" value="1" min="1" required>
                        </div>
                    </div>

                    <button type="submit" name="register_guest" class="btn btn-gold">Sign & Enter Catalog</button>
                </form>
            </div>

            <div class="card" style="border-top: 4px solid #2c3e50;">
                <h2 style="font-size: 1.5rem;">Returning Visitor?</h2>
                <form method="POST" onsubmit="window.location.hash='#home'; return true;">
                    <div class="form-group">
                        <label>Your Name</label>
                        <input type="text" name="login_name" class="form-control" placeholder="Juan Dela Cruz" required>
                    </div>
                    <button type="submit" name="guest_login" class="btn btn-dark">Enter as Returning Guest</button>
                </form>
            </div>

            <?php if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                <!-- Already logged in -->
            <?php else: ?>
                <div class="card" style="border-top: 4px solid #2980b9; display: none;" id="adminLoginCard">
                    <h2>Admin Login</h2>
                    <form method="POST" onsubmit="window.location.hash='#admin_dashboard'; return true;">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" name="admin_login" class="btn btn-blue">Login to Dashboard</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ==================== ADMIN DASHBOARD SECTION ==================== -->
    <?php if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
    <div id="admin_dashboard" class="spa-section">
        <div class="admin-layout">
            <aside class="sidebar">
                <h3>Admin Menu</h3>
                <ul class="sidebar-menu">
                    <li><a href="manage_exhibits.php" target="_blank">Manage Artifacts</a></li>
                    <li><a href="manage_departments.php" target="_blank">Manage Departments</a></li>
                    <li><a href="manage_news.php" target="_blank">Manage News</a></li>
                    <li><a href="manage_visitors.php" target="_blank">Manage Visitors</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </aside>

            <main class="main-content">
                <div class="welcome-banner">
                    <h2>Welcome, Admin!</h2>
                    <p>Manage the museum's digital catalog and visitor information.</p>
                </div>

                <div class="dashboard-grid">
                    <div class="stat-card card-visitors">
                        <div class="stat-info">
                            <h3><?php 
                                $visitor_count = $conn->query("SELECT COUNT(*) as count FROM guests")->fetch_assoc()['count'];
                                echo $visitor_count;
                            ?></h3>
                            <p>Total Visitors</p>
                        </div>
                        <div class="stat-icon">👥</div>
                    </div>

                    <div class="stat-card card-artifacts">
                        <div class="stat-info">
                            <h3><?php 
                                $artifact_count = $conn->query("SELECT COUNT(*) as count FROM exhibits")->fetch_assoc()['count'];
                                echo $artifact_count;
                            ?></h3>
                            <p>Total Artifacts</p>
                        </div>
                        <div class="stat-icon">🏛️</div>
                    </div>

                    <div class="stat-card card-news">
                        <div class="stat-info">
                            <h3><?php 
                                $news_count = $conn->query("SELECT COUNT(*) as count FROM news_events")->fetch_assoc()['count'];
                                echo $news_count;
                            ?></h3>
                            <p>News & Events</p>
                        </div>
                        <div class="stat-icon">📰</div>
                    </div>

                    <div class="stat-card card-departments">
                        <div class="stat-info">
                            <h3><?php 
                                $dept_count = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];
                                echo $dept_count;
                            ?></h3>
                            <p>Departments</p>
                        </div>
                        <div class="stat-icon">📂</div>
                    </div>
                </div>

                <h3 style="margin-top: 40px;">Quick Actions</h3>
                <div class="quick-actions">
                    <a href="manage_exhibits.php" target="_blank" class="action-card">
                        <span>🖼️</span>
                        Manage Artifacts
                    </a>
                    <a href="manage_departments.php" target="_blank" class="action-card">
                        <span>📁</span>
                        Manage Departments
                    </a>
                    <a href="manage_news.php" target="_blank" class="action-card">
                        <span>📝</span>
                        Manage News
                    </a>
                    <a href="manage_visitors.php" target="_blank" class="action-card">
                        <span>📋</span>
                        View Visitors
                    </a>
                </div>
            </main>
        </div>
    </div>
    <?php endif; ?>

    <script src="js/header.js"></script>
    <script src="js/login.js"></script>

    <script>
        // ==================== NEWS CAROUSEL LOGIC ====================
        let newsIndex = 0;
        let newsSlides = [];
        let dots = [];
        let newsTimer;

        function showNews(index) {
            if (newsSlides.length === 0) return;
            if (index >= newsSlides.length) { newsIndex = 0; }
            else if (index < 0) { newsIndex = newsSlides.length - 1; }
            else { newsIndex = index; }

            newsSlides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));

            if (newsSlides[newsIndex]) newsSlides[newsIndex].classList.add('active');
            if (dots[newsIndex]) dots[newsIndex].classList.add('active');
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

        function initializeNewsCarousel() {
            newsSlides = document.querySelectorAll('.news-slide');
            dots = document.querySelectorAll('.dot');
            if (newsSlides.length > 0) {
                newsIndex = 0;
                showNews(newsIndex);
                startNewsTimer();
            }
        }

        // ==================== GALLERY SLIDER LOGIC ====================
        let currentGalleryIndex = 0;

        function moveGallery(direction) {
            const track = document.getElementById('galleryTrack');
            if (!track) return;
            
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

        // ==================== LIVE FILTER FUNCTIONS ====================
        function liveFilterDepts() {
            const deptSearchInput = document.getElementById('deptSearchInput');
            if (!deptSearchInput) return;
            
            const query = deptSearchInput.value.toLowerCase();
            const cards = document.querySelectorAll('#deptGrid .cat-card');
            let hasVisibleCards = false;

            cards.forEach(card => {
                const searchableText = card.getAttribute('data-search');
                if (searchableText && searchableText.includes(query)) {
                    card.style.display = 'block'; 
                    hasVisibleCards = true;
                } else {
                    card.style.display = 'none'; 
                }
            });

            const noResultsMsg = document.getElementById('deptNoResultsMessage');
            if (noResultsMsg) {
                noResultsMsg.style.display = hasVisibleCards ? 'none' : 'block';
            }
        }

        function liveFilterArtifacts() {
            const artifactsSearchInput = document.getElementById('artifactsSearchInput');
            if (!artifactsSearchInput) return;
            
            const query = artifactsSearchInput.value.toLowerCase();
            const cards = document.querySelectorAll('#artifactsGrid .card');
            let hasVisibleCards = false;

            cards.forEach(card => {
                const searchableText = card.getAttribute('data-search');
                if (searchableText && searchableText.includes(query)) {
                    card.style.display = 'flex'; 
                    hasVisibleCards = true;
                } else {
                    card.style.display = 'none'; 
                }
            });

            const noResultsMsg = document.getElementById('artifactsNoResultsMessage');
            if (noResultsMsg) {
                noResultsMsg.style.display = hasVisibleCards ? 'none' : 'block';
            }
        }

        // ==================== HASH ROUTING ====================
        function handleRouting() {
            const hash = window.location.hash.slice(1) || 'home';
            
            // Hide all sections
            document.querySelectorAll('.spa-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Show the requested section
            const targetSection = document.getElementById(hash);
            if (targetSection) {
                targetSection.classList.add('active');
                window.scrollTo(0, 0);
                
                // Initialize JS when section becomes active
                if (hash === 'home') {
                    setTimeout(initializeNewsCarousel, 100);
                }
            } else {
                document.getElementById('home').classList.add('active');
                setTimeout(initializeNewsCarousel, 100);
            }
        }

        // Handle hash changes
        window.addEventListener('hashchange', handleRouting);
        
        // Handle initial load
        document.addEventListener('DOMContentLoaded', function() {
            handleRouting();
            initializeNewsCarousel();
        });

        // Prevent form submission from navigating away (stay on SPA)
        document.addEventListener('submit', function(e) {
            // Allow form to submit normally to process PHP backend
            // Page will reload with new session state
        }, true);
    </script>
</body>
</html>
