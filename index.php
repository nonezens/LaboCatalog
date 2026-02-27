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
    <title>Welcome | Museo de Labo</title>
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

        /* About Section */
        .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .about-text { font-size: 1.1rem; color: #555; line-height: 1.8; }
        .about-text p { margin-bottom: 20px; }
        .about-image { background: #eee; border-radius: 8px; height: 300px; display: flex; align-items: center; justify-content: center; color: #aaa; font-style: italic; border: 2px dashed #ccc; }

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
        
        /* Responsive */
        @media (max-width: 768px) { .about-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="hero">
        <h1>Welcome to Museo de Labo</h1>
        <p>Preserving the rich history, culture, and heritage of Camarines Norte. Explore our digital catalog to uncover the stories of our ancestors and the treasures of our past.</p>
        
        <?php if (!$is_logged_in): ?>
            <a href="login.php" class="hero-btn">Sign Guestbook to Explore</a>
        <?php else: ?>
            <a href="categories.php" class="hero-btn">Enter the Catalog</a>
        <?php endif; ?>
    </div>

    <div class="container">
        <h2 class="section-title">About the Museum</h2>
        <div class="about-grid">
            <div class="about-text">
                <p>Located in the heart of Camarines Norte, the <strong>Museo de Labo</strong> serves as the primary custodian of the municipality's historical artifacts, cultural relics, and artistic heritage.</p>
                <p>Our mission is to educate, inspire, and connect both locals and tourists with the vibrant legacy of Labo. From the ancient indigenous roots to the Spanish colonial era and the rich mining history of the region, every piece in our collection tells a unique story.</p>
                <p>Can't visit us in person? Our new digital catalog allows researchers, students, and history enthusiasts to browse our collections securely from anywhere in the world.</p>
            </div>
            <div class="about-image">
                <img src="museum-exterior.jpg" alt="Museo de Labo Building" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;" onerror="this.style.display='none'; this.parentNode.innerHTML='[Insert Museum Photo Here]';">
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