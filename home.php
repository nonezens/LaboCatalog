<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db.php'; 

$is_logged_in = isset($_SESSION['guest_logged_in']) || isset($_SESSION['admin_logged_in']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | Museo de Labo</title>
    
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/index.css">
    <style>
        .home-hero {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('uploads/background.jpg') no-repeat center center/cover;
            color: white;
            padding: 150px 20px;
            text-align: center;
        }
        .home-hero h1 {
            font-size: 4rem;
            margin: 0;
            font-weight: 700;
        }
        .home-hero p {
            font-size: 1.3rem;
            max-width: 600px;
            margin: 15px auto 30px auto;
        }
        .home-section {
            padding: 80px 20px;
        }
        .home-section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 50px;
            color: #2c3e50;
        }
        .home-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
    </style>
</head>
<body class="home-page">

    <?php include 'header.php'; ?>

    <div class="home-hero">
        <h1>Discover the Soul of Labo</h1>
        <p>A curated collection of our town's most treasured artifacts and stories.</p>
        <a href="exhibits.php" class="hero-btn">Explore Collection</a>
    </div>

    <div id="latest-acquisitions" class="home-section">
        <div class="container">
            <h2 class="home-section-title">Latest Acquisitions</h2>
            <div class="acquisitions-grid-new">
                <?php
                $latest_query = "SELECT * FROM exhibits ORDER BY id DESC LIMIT 3";
                $latest_result = $conn->query($latest_query);
                if ($latest_result->num_rows > 0) {
                    while($row = $latest_result->fetch_assoc()) {
                        echo '<div class="acquisition-card-new">';
                        echo '<a href="exhibit_detail.php?id='.$row['id'].'">';
                        echo '<div class="card-image-new">';
                        echo '<img src="uploads/' . $row['image_path'] . '" alt="'.htmlspecialchars($row['title']).'">';
                        echo '</div>';
                        echo '<div class="card-content-new">';
                        echo '<h3 class="card-title-new">'.htmlspecialchars($row['title']).'</h3>';
                        echo '</div>';
                        echo '</a>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>

<<<<<<< Updated upstream
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
        
        <div class="gallery-grid">
            <?php 
            $recent_exhibits = array_slice($all_exhibit_items, 0, 6);
            foreach($recent_exhibits as $row): 
            ?>
                <a href="exhibit_detail.php?id=<?php echo $row['id']; ?>" class="card-link" style="text-decoration: none; color: inherit;">
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
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <?php if (!$is_logged_in): ?>
                <a href="login.php" style="color: #c5a059; font-weight: bold; text-decoration: none; font-size: 1.1rem;">Register to View All Artifacts →</a>
            <?php else: ?>
                <a href="exhibits.php" style="color: #c5a059; font-weight: bold; text-decoration: none; font-size: 1.1rem;">View All Artifacts →</a>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <?php if (!$is_logged_in): ?>
                <a href="login.php" style="color: #c5a059; font-weight: bold; text-decoration: none; font-size: 1.1rem;">Register to View All Artifacts &rarr;</a>
            <?php else: ?>
                <a href="exhibits.php" style="color: #c5a059; font-weight: bold; text-decoration: none; font-size: 1.1rem;">View All Artifacts &rarr;</a>
            <?php endif; ?>
=======
    <div id="news-events" class="home-section" style="background-color: #f8f9fa;">
        <div class="container">
            <h2 class="home-section-title">News & Events</h2>
            <div class="acquisitions-grid-new">
                <?php
                $news_query = "SELECT * FROM news_events ORDER BY date_posted DESC LIMIT 3";
                $news_result = $conn->query($news_query);
                if ($news_result->num_rows > 0) {
                    while($row = $news_result->fetch_assoc()) {
                        echo '<div class="acquisition-card-new">';
                        echo '<a href="news.php#news-'.$row['id'].'">';
                        if (!empty($row['image_path'])) {
                            echo '<div class="card-image-new">';
                            echo '<img src="uploads/' . $row['image_path'] . '" alt="'.htmlspecialchars($row['title']).'">';
                            echo '</div>';
                        }
                        echo '<div class="card-content-new">';
                        echo '<h3 class="card-title-new">'.htmlspecialchars($row['title']).'</h3>';
                        echo '<p class="card-meta">'.date("F j, Y", strtotime($row['date_posted'])).'</p>';
                        echo '</div>';
                        echo '</a>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
>>>>>>> Stashed changes
        </div>
    </div>

    <?php include 'footer.php'; ?>

</body>
</html>
