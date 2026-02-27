<?php 
// 1. Start Session
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db.php'; 

// 2. Check if the user is logged in
$is_logged_in = isset($_SESSION['admin_logged_in']) || isset($_SESSION['guest_logged_in']);

// 3. ONLY fetch categories if they are allowed to see them
if ($is_logged_in) {
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    $query = "SELECT * FROM categories WHERE name LIKE ? ORDER BY name ASC";
    $search_param = "%$search%";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departments | Museum Labo Catalog</title>
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

        .cat-card {
            text-align: center;
        }

        .cat-body {
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <?php if (!$is_logged_in): ?>
        
        <section class="not-logged-in">
            <h2>🏛️ Explore Museum Departments</h2>
            <p>Browse our organized collection of artifacts by department. Sign in to access the full digital catalog of Museo de Labo.</p>
            <a href="login.php">Sign the Guestbook to Enter →</a>
        </section>

    <?php else: ?>

        <div class="container" style="padding-top: 50px; padding-bottom: 50px;">
            <h1 class="page-title">Museum Departments</h1>

            <form action="categories.php" method="GET" class="search-container" id="searchForm">
                <div class="search-wrapper">
                    <input type="text" id="searchInput" name="search" class="search-input" placeholder="Search departments (e.g., Ancient Egypt)..." value="<?php echo htmlspecialchars($search); ?>" autocomplete="off" onkeyup="liveFilter()">
                </div>

                <button type="submit" class="btn-search">🔍 Search</button>
                <a href="categories.php" class="btn-clear">Clear</a>
            </form>

            <div class="gallery-grid" id="galleryGrid">
                <?php if(isset($result) && $result->num_rows > 0): ?>
                    <?php while($cat = $result->fetch_assoc()): 
                        $searchable_text = strtolower($cat['name']);
                    ?>
                        <div class="card cat-card" data-search="<?php echo htmlspecialchars($searchable_text); ?>">
                            <img src="uploads/<?php echo htmlspecialchars($cat['image_path']); ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>">
                            <div class="card-body cat-body">
                                <h3 class="card-title"><?php echo htmlspecialchars($cat['name']); ?></h3>
                                <a href="exhibits.php?cat=<?php echo $cat['id']; ?>" class="btn-view">View Artifacts →</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="grid-column: 1 / -1; text-align: center; font-size: 1.2rem; color: #7f8c8d; padding: 40px;">No departments found in the database. Please check back soon!</p>
                <?php endif; ?>

                <p id="noResultsMessage">No departments match your search. Try different keywords.</p>
            </div>
        </div>

        <script>
            function liveFilter() {
                let query = document.getElementById('searchInput').value.toLowerCase();
                let cards = document.querySelectorAll('.cat-card');
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

    <footer>
        <p>&copy; 2026 Museo De Labo Catalog. Preserving Our Cultural Heritage for Future Generations.</p>
    </footer>
</body>

</body>
</html>