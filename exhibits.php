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
    <link rel="stylesheet" href="style.css">
    <style>
        .not-logged-in {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            text-align: center;
            padding: 100px 20px;
            min-height: 60vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .not-logged-in h2 {
            font-size: 2.5rem;
            margin: 0 0 20px 0;
            color: var(--gold);
        }

        .not-logged-in p {
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto 30px auto;
            color: rgba(255, 255, 255, 0.85);
        }

        .not-logged-in a {
            background: var(--gold);
            color: white;
            padding: 15px 35px;
            text-decoration: none;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: 700;
            display: inline-block;
            transition: 0.3s;
            box-shadow: 0 4px 15px rgba(197, 160, 89, 0.4);
        }

        .not-logged-in a:hover {
            background: var(--gold-hover);
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <?php if (!$is_logged_in): ?>
        
        <section class="not-logged-in">
            <h2>📚 Explore Our Artifacts</h2>
            <p>Discover the rich historical collection of Museo de Labo. Sign in to browse our digital catalog and learn about the cultural heritage of Labo, Camarines Norte.</p>
            <a href="login.php">Sign the Guestbook to Enter →</a>
        </section>

    <?php else: ?>

        <div class="container" style="padding-top: 50px; padding-bottom: 50px;">
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
                            <img src="uploads/<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                            <div class="card-body">
                                <h3 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                                <div class="card-meta">
                                    <strong>Period:</strong> <?php echo $row['artifact_year'] ? htmlspecialchars($row['artifact_year']) : 'Unknown'; ?><br>
                                    <strong>Origin:</strong> <?php echo $row['origin'] ? htmlspecialchars($row['origin']) : 'Unknown'; ?>
                                </div>
                                <p class="card-desc"><?php echo htmlspecialchars(substr($row['description'], 0, 100)); ?>...</p>
                                <a href="exhibit_detail.php?id=<?php echo $row['id']; ?>" class="btn-view">View Details →</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="grid-column: 1 / -1; text-align: center; font-size: 1.2rem; color: #7f8c8d; padding: 40px;">No artifacts found in the database. Please check back soon!</p>
                <?php endif; ?>
                
                <p id="noResultsMessage">No artifacts match your search. Try different keywords.</p>
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
        </script>

    <?php endif; ?>

    <footer>
        <p>&copy; 2026 Museo De Labo Catalog. Preserving Our Cultural Heritage for Future Generations.</p>
    </footer>
</body>
</html>