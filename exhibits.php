<?php 
// 1. Start Session
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db.php'; 

// 2. Check if the user is logged in (Admin or Guest)
$is_logged_in = isset($_SESSION['admin_logged_in']) || isset($_SESSION['guest_logged_in']);

// 3. ONLY fetch data from the database if they are allowed to see it!
if ($is_logged_in) {
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $cat_id = isset($_GET['cat']) ? $_GET['cat'] : null;

    $query = "SELECT * FROM exhibits WHERE (title LIKE ? OR donated_by LIKE ? OR description LIKE ?)";
    $params = ["%$search%", "%$search%", "%$search%"];
    $types = "sss";

    if ($cat_id) {
        $query .= " AND category_id = ?";
        $params[] = $cat_id;
        $types .= "i";
    }
    $query .= " ORDER BY id DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exhibits | Museum Labo Catalog</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9f9f9; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        
        /* Title with morphing animation */
        .page-title { 
            text-align: center; 
            color: #2c3e50; 
            font-size: 2.5rem; 
            margin-bottom: 20px;
            opacity: 0;
            transform: translateY(-30px) scale(0.8);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .page-title.visible {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        
        /* Search Bar Styles */
        .search-container { 
            max-width: 600px; 
            margin: 0 auto 40px auto; 
            display: flex; 
            gap: 10px;
            opacity: 0;
            transform: translateX(-50px);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            transition-delay: 0.2s;
        }
        
        .search-container.visible {
            opacity: 1;
            transform: translateX(0);
        }
        
        .search-wrapper { position: relative; flex: 1; }
        .search-input { 
            width: 100%; 
            box-sizing: border-box; 
            padding: 12px 20px; 
            border: 1px solid #ddd; 
            border-radius: 30px; 
            font-size: 1rem; 
            outline: none; 
            transition: 0.3s; 
        }
        .search-input:focus { 
            border-color: #c5a059; 
            box-shadow: 0 0 8px rgba(197, 160, 89, 0.3);
            transform: scale(1.02);
        }
        .btn-search { 
            padding: 12px 25px; 
            background: #2c3e50; 
            color: white; 
            border: none; 
            border-radius: 30px; 
            cursor: pointer; 
            font-weight: bold; 
            transition: 0.3s; 
        }
        .btn-search:hover { background: #1a252f; transform: scale(1.05); }
        .btn-clear { 
            padding: 12px 20px; 
            color: #7f8c8d; 
            text-decoration: none; 
            font-weight: bold; 
            transition: 0.3s;
        }
        .btn-clear:hover { color: #2c3e50; transform: translateX(5px); }

        /* Gallery Styles */
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px; }
        .card-link { text-decoration: none; color: inherit; display: flex; }
        .card { 
            background: white; 
            border-radius: 8px; 
            overflow: hidden; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
            transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s; 
            border: 1px solid #eee; 
            display: flex; 
            flex-direction: column; 
            width: 100%;
        }
        .card-link:hover .card { 
            transform: translateY(-5px) scale(1.02); 
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border-color: #c5a059;
        }
        .card img { width: 100%; height: 220px; object-fit: cover; transition: transform 0.4s ease; }
        .card-link:hover .card img { transform: scale(1.1); }
        .card-body { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
        .card-title { margin: 0 0 10px 0; color: #2c3e50; font-size: 1.4rem; transition: color 0.3s; }
        .card-link:hover .card-title { color: #c5a059; }
        .card-meta { font-size: 0.9rem; color: #7f8c8d; margin-bottom: 15px; }
        .card-desc { font-size: 0.95rem; color: #555; line-height: 1.5; margin-bottom: 20px; flex-grow: 1; }
        
        /* --- Flying/Scroll Animations with Morphing --- */
        .card-link {
            opacity: 0;
            transform: translateY(60px) scale(0.8) rotateX(-15deg);
            transition: opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1), 
                        transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-link.visible {
            opacity: 1;
            transform: translateY(0) scale(1) rotateX(0);
        }
        
        .card-link:nth-child(1) { transition-delay: 0.1s; }
        .card-link:nth-child(2) { transition-delay: 0.15s; }
        .card-link:nth-child(3) { transition-delay: 0.2s; }
        .card-link:nth-child(4) { transition-delay: 0.25s; }
        .card-link:nth-child(5) { transition-delay: 0.3s; }
        .card-link:nth-child(6) { transition-delay: 0.35s; }
        .card-link:nth-child(7) { transition-delay: 0.4s; }
        .card-link:nth-child(8) { transition-delay: 0.45s; }
        .card-link:nth-child(9) { transition-delay: 0.5s; }
        .card-link:nth-child(10) { transition-delay: 0.55s; }
        .card-link:nth-child(11) { transition-delay: 0.6s; }
        .card-link:nth-child(12) { transition-delay: 0.65s; }
        
        #noResultsMessage { grid-column: 1 / -1; text-align: center; font-size: 1.2rem; color: #7f8c8d; padding: 40px; display: none; }
        
        /* --- RESPONSIVE --- */
        @media (max-width: 768px) {
            .search-container { 
                flex-direction: column;
                gap: 15px; 
            }
            .btn-search, .btn-clear { 
                width: 100%; 
                text-align: center; 
                box-sizing: border-box;
            }
            .page-title { font-size: 2rem; }
            .gallery-grid, .cat-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <?php if (!$is_logged_in): ?>
        
        <!-- VISITOR VIEW: Show images only without details -->
        <div class="container">
            <h1 class="page-title" id="pageTitle">Museum Collection Preview</h1>
            <p style="text-align: center; color: #7f8c8d; margin-bottom: 40px;" id="pageSubtitle">Sign the guestbook to view full details</p>
            
            <div class="gallery-grid" id="galleryGrid">
                <?php
                $preview_result = $conn->query("SELECT id, title, image_path FROM exhibits ORDER BY id DESC");
                if($preview_result && $preview_result->num_rows > 0):
                    $count = 0;
                    while($row = $preview_result->fetch_assoc()):
                        $count++;
                ?>
                    <div class="card-link" data-search="<?php echo strtolower($row['title']); ?>" style="transition-delay: <?php echo 0.1 + ($count * 0.05); ?>s;">
                        <div class="card">
                            <img src="uploads/<?php echo $row['image_path']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" style="cursor: pointer;" onclick="alert('Please sign the guestbook to view full details!')">
                            <div class="card-body">
                                <h3 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                                <p class="card-desc" style="color: #c5a059; font-weight: bold;">🔒 Login to view details</p>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; endif; ?>
            </div>
            
            <div style="text-align: center; margin-top: 40px; opacity: 0; transform: translateY(20px); transition: all 0.5s ease; transition-delay: 0.8s;" id="loginCTA">
                <a href="login.php" class="btn-add bg-exhibit" style="display: inline-block; text-decoration: none; padding: 15px 40px; font-size: 1.1rem;">📝 Sign Guestbook to Access</a>
            </div>
        </div>

        <script>
            // Animations for visitor preview
            document.addEventListener('DOMContentLoaded', function() {
                // Title animation
                setTimeout(() => document.getElementById('pageTitle').classList.add('visible'), 100);
                setTimeout(() => document.getElementById('pageSubtitle').classList.add('visible'), 200);
                setTimeout(() => document.getElementById('loginCTA').classList.add('visible'), 600);
                
                // Card animations
                const cardLinks = document.querySelectorAll('.card-link');
                const observerOptions = { root: null, rootMargin: '0px', threshold: 0.1 };
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, observerOptions);
                cardLinks.forEach(link => observer.observe(link));
                
                // Set hash
                history.replaceState(null, null, '#artifacts');
            });
        </script>

    <?php else: ?>

        <div class="container">
            <h1 class="page-title" id="pageTitle">Museum Collection</h1>

            <form action="exhibits.php" method="GET" class="search-container" id="searchForm">
                <?php if($cat_id): ?>
                    <input type="hidden" name="cat" value="<?php echo htmlspecialchars($cat_id); ?>">
                <?php endif; ?>
                
                <div class="search-wrapper">
                    <input type="text" id="searchInput" name="search" class="search-input" placeholder="Search artifacts, periods, or donors..." value="<?php echo htmlspecialchars($search); ?>" autocomplete="off" onkeyup="liveFilter()">
                </div>

                <button type="submit" class="btn-search">🔍 Search</button>
                <a href="exhibits.php<?php echo $cat_id ? '?cat='.$cat_id : ''; ?>" class="btn-clear">Clear</a>
            </form>

            <div class="gallery-grid" id="galleryGrid">
                <?php if(isset($result) && $result->num_rows > 0): ?>
                    <?php $count = 0; while($row = $result->fetch_assoc()): 
                        $count++;
                        $searchable_text = strtolower($row['title'] . " " . $row['artifact_year'] . " " . $row['origin'] . " " . $row['donated_by'] . " " . $row['description']);
                    ?>
                        <a href="exhibit_detail.php?id=<?php echo $row['id']; ?>" class="card-link" data-search="<?php echo htmlspecialchars($searchable_text); ?>" style="transition-delay: <?php echo 0.1 + ($count * 0.05); ?>s;">
                            <div class="card">
                                <img src="uploads/<?php echo $row['image_path']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                                <div class="card-body">
                                    <h3 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                                    <div class="card-meta">
                                        <strong>Period:</strong> <?php echo $row['artifact_year'] ? htmlspecialchars($row['artifact_year']) : 'Unknown'; ?><br>
                                        <strong>Origin:</strong> <?php echo $row['origin'] ? htmlspecialchars($row['origin']) : 'Unknown'; ?>
                                    </div>
                                    <p class="card-desc"><?php echo htmlspecialchars(substr($row['description'], 0, 100)); ?>...</p>
                                </div>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="grid-column: 1 / -1; text-align: center; font-size: 1.2rem; color: #7f8c8d; padding: 40px;">No artifacts found in the database.</p>
                <?php endif; ?>
                
                <p id="noResultsMessage">No artifacts match your live search.</p>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Title animation
                setTimeout(() => document.getElementById('pageTitle').classList.add('visible'), 100);
                setTimeout(() => document.getElementById('searchForm').classList.add('visible'), 200);
                
                // Set hash
                history.replaceState(null, null, '#artifacts');
                
                // Card scroll animations
                const cardLinks = document.querySelectorAll('.card-link');
                const observerOptions = { root: null, rootMargin: '0px', threshold: 0.1 };
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, observerOptions);
                cardLinks.forEach(link => observer.observe(link));
            });

            function liveFilter() {
                let query = document.getElementById('searchInput').value.toLowerCase();
                let cardLinks = document.querySelectorAll('.card-link');
                let hasVisibleCards = false;

                cardLinks.forEach(link => {
                    let searchableText = link.getAttribute('data-search');
                    if (searchableText.includes(query)) {
                        link.style.display = 'flex';
                        link.classList.remove('visible');
                        void link.offsetWidth;
                        link.classList.add('visible');
                        hasVisibleCards = true;
                    } else {
                        link.style.display = 'none';
                    }
                });

                let noResultsMsg = document.getElementById('noResultsMessage');
                noResultsMsg.style.display = hasVisibleCards ? 'none' : 'block';
            }

            document.getElementById('searchForm').addEventListener('submit', function(e) {
                e.preventDefault(); 
            });
        </script>

    <?php endif; ?>

</body>
</html>

