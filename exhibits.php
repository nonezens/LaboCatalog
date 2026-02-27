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
    <title>Exhibits | Museum Labo Catalog</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9f9f9; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .page-title { text-align: center; color: #2c3e50; font-size: 2.5rem; margin-bottom: 20px; }
        
        /* Search Bar Styles */
        .search-container { max-width: 600px; margin: 0 auto 40px auto; display: flex; gap: 10px; }
        .search-wrapper { position: relative; flex: 1; }
        .search-input { width: 100%; box-sizing: border-box; padding: 12px 20px; border: 1px solid #ddd; border-radius: 30px; font-size: 1rem; outline: none; transition: 0.3s; }
        .search-input:focus { border-color: #c5a059; box-shadow: 0 0 8px rgba(197, 160, 89, 0.3); }
        .btn-search { padding: 12px 25px; background: #2c3e50; color: white; border: none; border-radius: 30px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        .btn-search:hover { background: #1a252f; }
        .btn-clear { padding: 12px 20px; color: #7f8c8d; text-decoration: none; font-weight: bold; }

        /* Gallery Styles */
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px; }
        .card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: transform 0.3s, opacity 0.3s; border: 1px solid #eee; display: flex; flex-direction: column; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .card img { width: 100%; height: 220px; object-fit: cover; }
        .card-body { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
        .card-title { margin: 0 0 10px 0; color: #2c3e50; font-size: 1.4rem; }
        .card-meta { font-size: 0.9rem; color: #7f8c8d; margin-bottom: 15px; }
        .card-desc { font-size: 0.95rem; color: #555; line-height: 1.5; margin-bottom: 20px; flex-grow: 1; }
        .btn-view { display: block; text-align: center; padding: 10px; background: #c5a059; color: white; text-decoration: none; border-radius: 4px; font-weight: bold; transition: 0.3s; }
        .btn-view:hover { background: #b48a3d; }
        
        #noResultsMessage { grid-column: 1 / -1; text-align: center; font-size: 1.2rem; color: #7f8c8d; padding: 40px; display: none; }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <?php if (!$is_logged_in): ?>
        
        <div style="background: linear-gradient(135deg, #2c3e50, #1a252f); color: white; text-align: center; padding: 80px 20px; min-height: 60vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0 0 15px 0; font-size: 2.5rem; letter-spacing: 1px;">Experience History in Person</h3>
            <p style="margin: 0 0 40px 0; font-size: 1.2rem; color: #ecf0f1; max-width: 600px; line-height: 1.6;">
                Discover the rich heritage of Camarines Norte. Visit the real artifacts at the <strong style="color: #c5a059;">Museo de Labo</strong> in Labo!
            </p>
            
            <p style="color: #95a5a6; font-size: 1rem; margin-bottom: 15px;">Want to browse the digital collection?</p>
            <a href="login.php" style="padding: 15px 35px; background: #c5a059; color: white; text-decoration: none; border-radius: 30px; font-size: 1.1rem; font-weight: bold; transition: 0.3s; box-shadow: 0 4px 15px rgba(197, 160, 89, 0.4);">
                Sign the Guestbook to Enter
            </a>
        </div>

    <?php else: ?>

        <div class="container">
            <h1 class="page-title">Museum Collection</h1>

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
                    <?php while($row = $result->fetch_assoc()): 
                        $searchable_text = strtolower($row['title'] . " " . $row['artifact_year'] . " " . $row['origin'] . " " . $row['donated_by'] . " " . $row['description']);
                    ?>
                        <div class="card" data-search="<?php echo htmlspecialchars($searchable_text); ?>">
                            <img src="uploads/<?php echo $row['image_path']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                            <div class="card-body">
                                <h3 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                                <div class="card-meta">
                                    <strong>Period:</strong> <?php echo $row['artifact_year'] ? htmlspecialchars($row['artifact_year']) : 'Unknown'; ?><br>
                                    <strong>Origin:</strong> <?php echo $row['origin'] ? htmlspecialchars($row['origin']) : 'Unknown'; ?>
                                </div>
                                <p class="card-desc"><?php echo htmlspecialchars(substr($row['description'], 0, 100)); ?>...</p>
                                <a href="exhibit_detail.php?id=<?php echo $row['id']; ?>" class="btn-view">View Full Details</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="grid-column: 1 / -1; text-align: center; font-size: 1.2rem; color: #7f8c8d; padding: 40px;">No artifacts found in the database.</p>
                <?php endif; ?>
                
                <p id="noResultsMessage">No artifacts match your live search.</p>
            </div>
        </div>

        <script>
            function liveFilter() {
                let query = document.getElementById('searchInput').value.toLowerCase();
                let cards = document.querySelectorAll('.card');
                let hasVisibleCards = false;

                cards.forEach(card => {
                    let searchableText = card.getAttribute('data-search');
                    if (searchableText.includes(query)) {
                        card.style.display = 'flex'; 
                        hasVisibleCards = true;
                    } else {
                        card.style.display = 'none'; 
                    }
                });

                let noResultsMsg = document.getElementById('noResultsMessage');
                if (hasVisibleCards) {
                    noResultsMsg.style.display = 'none';
                } else {
                    noResultsMsg.style.display = 'block';
                }
            }

            document.getElementById('searchForm').addEventListener('submit', function(e) {
                e.preventDefault(); 
            });
        </script>

    <?php endif; ?>

</body>
</html>