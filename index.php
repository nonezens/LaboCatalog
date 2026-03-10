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
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/index.css">
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

    <!-- JS -->
    <script src="js/index.js"></script>

</body>
</html>

