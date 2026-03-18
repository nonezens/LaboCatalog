<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db.php'; 

// Fetch all news/events
$query = "SELECT * FROM news_events ORDER BY date_posted DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News & Events | Museo de Labo</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/news.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <h1 class="page-title">The Museum Chronicle</h1>
    <div class="page-subtitle">Latest Updates & Upcoming Events</div>

    <div class="news-feed">
        <?php if($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                
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

</body>
</html>