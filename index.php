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
    <link rel="stylesheet" href="style.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            text-align: center;
            padding: 120px 20px;
        }

        .hero-section h1 {
            font-size: 3.5rem;
            margin: 0 0 20px 0;
            letter-spacing: 1px;
            font-weight: 800;
        }

        .hero-section p {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.85);
            max-width: 700px;
            margin: 0 auto 30px auto;
            line-height: 1.6;
        }

        .about-section { margin-bottom: 60px; }
        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
            background: white;
            padding: 50px;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        .about-text { font-size: 1.1rem; color: #555; line-height: 1.8; }
        .about-text p { margin-bottom: 20px; }
        .about-text strong { color: var(--primary); }

        .about-image {
            background: linear-gradient(135deg, rgba(19, 113, 55, 0.1), rgba(197, 160, 89, 0.1));
            border-radius: 8px;
            height: 320px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #aaa;
            font-style: italic;
            border: 2px solid var(--gold);
        }

        @media (max-width: 768px) {
            .hero-section h1 { font-size: 2.5rem; }
            .hero-section { padding: 80px 20px; }
            .about-grid { grid-template-columns: 1fr; padding: 30px; gap: 30px; }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <section class="hero-section">
        <h1>THE GREAT ARCHIVE</h1>
        <p>Discover the rich heritage of Labo, Camarines Norte through artifacts and digital collections.</p>
        <?php if (!$is_logged_in): ?>
            <a href="login.php" class="btn-view" style="background: var(--gold); color: white; padding: 15px 35px; display: inline-block; border-radius: 30px; margin-top: 10px; border: 2px solid var(--gold);">Sign Guestbook to Explore</a>
        <?php else: ?>
            <a href="exhibits.php" class="btn-view" style="background: var(--gold); color: white; padding: 15px 35px; display: inline-block; border-radius: 30px; margin-top: 10px; border: 2px solid var(--gold);">Enter the Catalog</a>
        <?php endif; ?>
    </section>

    <div class="container about-section">
        <h2 class="page-title">Welcome to Museo de Labo</h2>
        <div class="about-grid">
            <div class="about-text">
                <p>
                    <strong>Museo de Labo</strong> serves as the primary custodian of Labo's historical artifacts, cultural relics, and artistic heritage in Camarines Norte.
                </p>
                <p>
                    Our mission is to educate, inspire, and connect locals and visitors with the vibrant legacy of our municipality. From ancient indigenous roots to colonial heritage, every piece in our collection tells a unique story.
                </p>
                <p>
                    Through this digital catalog, researchers, students, and history enthusiasts can securely explore our collections from anywhere in the world.
                </p>
            </div>
            <div class="about-image">🏛️</div>
        </div>
    </div>

    <?php if ($is_logged_in): ?>
    <section class="latest-section" style="background: white; padding: 60px 0;">
        <div class="container">
            <h2 class="page-title">Latest Acquisitions</h2>
            <p style="text-align: center; color: #7f8c8d; margin-bottom: 30px; font-size: 1.1rem;">Explore the newest additions to our collection</p>
            
            <div class="gallery-grid">
                <?php if(isset($recent_result) && $recent_result->num_rows > 0): ?>
                    <?php while($row = $recent_result->fetch_assoc()): ?>
                        <div class="card">
                            <img src="uploads/<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                            <div class="card-body">
                                <span class="card-meta" style="color: var(--primary);">📅 <?php echo htmlspecialchars($row['artifact_year']); ?></span>
                                <h3 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                                <p class="card-desc"><?php echo htmlspecialchars(substr($row['description'], 0, 85)); ?>...</p>
                                <a href="exhibit_detail.php?id=<?php echo $row['id']; ?>" class="btn-view">View Details →</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="grid-column: 1 / -1; text-align: center; color: #7f8c8d; padding: 40px; font-size: 1.1rem;">No artifacts yet. Check back soon!</p>
                <?php endif; ?>
            </div>

            <div style="text-align: center; margin-top: 40px;">
                <a href="exhibits.php" class="btn-secondary">View All Artifacts →</a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <footer>
        <p>&copy; 2026 Museo De Labo Catalog. Preserving Our Cultural Heritage for Future Generations.</p>
    </footer>
</body>
</html>