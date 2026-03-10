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
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9f9f9; margin: 0; padding: 0; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .page-title { text-align: center; color: #2c3e50; font-size: 2.5rem; margin-bottom: 40px; }
        
        .news-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px; }
        .news-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: transform 0.3s; border: 1px solid #eee; display: flex; flex-direction: column; }
        .news-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .news-card img { width: 100%; height: 200px; object-fit: cover; background: #eee; }
        
        .news-body { padding: 25px; flex-grow: 1; display: flex; flex-direction: column; }
        .news-type { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; color: white; margin-bottom: 15px; text-transform: uppercase; align-self: flex-start; }
        .type-news { background: #3498db; }
        .type-event { background: #8e44ad; }
        
        .news-title { margin: 0 0 10px 0; color: #2c3e50; font-size: 1.4rem; line-height: 1.3; }
        .news-date { font-size: 0.85rem; color: #95a5a6; margin-bottom: 15px; font-weight: bold; }
        .news-content { font-size: 1rem; color: #555; line-height: 1.6; margin-bottom: 20px; }
        
        @media (max-width: 768px) {
            .news-grid { grid-template-columns: 1fr; }
            .page-title { font-size: 2rem; }
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="container">
        <h1 class="page-title">Museum News & Upcoming Events</h1>

        <div class="news-grid">
            <?php if($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="news-card">
                        <?php if($row['image_path']): ?>
                            <img src="uploads/<?php echo $row['image_path']; ?>" alt="News Image">
                        <?php endif; ?>
                        
                        <div class="news-body">
                            <span class="news-type <?php echo $row['type'] == 'event' ? 'type-event' : 'type-news'; ?>">
                                <?php echo $row['type'] == 'event' ? '📅 Event' : '📰 News'; ?>
                            </span>
                            
                            <h3 class="news-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                            
                            <div class="news-date">
                                <?php if($row['type'] == 'event' && $row['event_date']): ?>
                                    Event Date: <?php echo date("F j, Y", strtotime($row['event_date'])); ?>
                                <?php else: ?>
                                    Posted: <?php echo date("F j, Y", strtotime($row['date_posted'])); ?>
                                <?php endif; ?>
                            </div>
                            
                            <div class="news-content">
                                <?php echo nl2br(htmlspecialchars($row['content'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="grid-column: 1 / -1; text-align: center; font-size: 1.2rem; color: #7f8c8d; padding: 40px;">No news or events at the moment. Check back soon!</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>