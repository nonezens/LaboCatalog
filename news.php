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
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #fcfcfc; margin: 0; padding: 0; }
        
        .page-title { text-align: center; color: #2c3e50; font-size: 2.8rem; margin: 40px 0 20px 0; font-family: 'Georgia', serif; }
        .page-subtitle { text-align: center; color: #7f8c8d; font-size: 1.1rem; margin-bottom: 50px; text-transform: uppercase; letter-spacing: 2px; }
        
        /* Traditional News Feed Container */
        .news-feed { max-width: 850px; margin: 0 auto; padding: 0 20px; }
        
        /* Individual Article Block (No Flexbox!) */
        .news-article { display: block; padding-bottom: 40px; margin-bottom: 40px; border-bottom: 2px solid #eaeaea; }
        .news-article:last-child { border-bottom: none; }
        
        /* Badges */
        .news-type { display: inline-block; padding: 4px 10px; font-size: 0.75rem; font-weight: bold; color: white; text-transform: uppercase; margin-bottom: 15px; background: #3498db; letter-spacing: 1px; }
        .type-event { background: #8e44ad; }
        
        /* Classic Newspaper Headline */
        .news-title { font-size: 2.2rem; color: #111; margin: 0 0 10px 0; line-height: 1.2; font-family: 'Georgia', serif; }
        
        /* Metadata */
        .news-meta { color: #7f8c8d; font-size: 0.95rem; margin-bottom: 20px; font-weight: bold; }
        
        /* Floated Image (Allows text to wrap around it!) */
        .news-image { float: left; width: 350px; max-width: 100%; margin: 0 25px 15px 0; border-radius: 4px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        
        /* Article Text */
        .news-content { font-size: 1.15rem; line-height: 1.8; color: #333; text-align: justify; }
        
        /* Clearfix hack to ensure the border stays below the floated image */
        .clearfix::after { content: ""; clear: both; display: table; }
        
        /* Mobile Adjustments */
        @media (max-width: 768px) {
            .page-title { font-size: 2.2rem; }
            .news-title { font-size: 1.8rem; }
            
            /* On small screens, remove the float so the image sits on top of the text */
            .news-image { float: none; width: 100%; margin: 0 0 20px 0; }
            .news-content { text-align: left; }
        }
    </style>
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