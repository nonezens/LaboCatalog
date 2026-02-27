<?php 
// PHP Logic & Database connection
include 'db.php'; 

$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query to fetch categories
// We fetch them all so the JavaScript has everything it needs to filter instantly!
$query = "SELECT * FROM categories WHERE name LIKE ? ORDER BY name ASC";
$search_param = "%$search%";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Departments | Museum Labo Catalog</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9f9f9; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .page-title { text-align: center; color: #2c3e50; font-size: 2.5rem; margin-bottom: 20px; }
        
        /* Search Bar Styles */
        .search-container { max-width: 600px; margin: 0 auto 40px auto; display: flex; gap: 10px; }
        .search-wrapper { position: relative; flex: 1; }
        .search-input { width: 100%; box-sizing: border-box; padding: 12px 20px; border: 1px solid #ddd; border-radius: 30px; font-size: 1rem; outline: none; transition: 0.3s; }
        .search-input:focus { border-color: #2980b9; box-shadow: 0 0 8px rgba(41, 128, 185, 0.3); }
        .btn-search { padding: 12px 25px; background: #2980b9; color: white; border: none; border-radius: 30px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        .btn-search:hover { background: #1c5980; }
        .btn-clear { padding: 12px 20px; color: #7f8c8d; text-decoration: none; font-weight: bold; }

        /* Category Grid Styles */
        .cat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px; }
        
        /* Added transition to make the hiding/showing smoother */
        .cat-card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center; transition: transform 0.3s, opacity 0.3s; border: 1px solid #eee; display: block; }
        .cat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        
        .cat-card img { width: 100%; height: 200px; object-fit: cover; }
        .cat-body { padding: 20px; }
        .cat-title { color: #2c3e50; font-size: 1.5rem; margin: 0 0 15px 0; }
        .btn-view { display: inline-block; padding: 10px 20px; background: #2c3e50; color: white; text-decoration: none; border-radius: 4px; font-weight: bold; transition: 0.3s; }
        .btn-view:hover { background: #c5a059; }

        #noResultsMessage { grid-column: 1 / -1; text-align: center; font-size: 1.2rem; color: #7f8c8d; padding: 40px; display: none; }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="container">
        <h1 class="page-title">Explore Museum Departments</h1>

        <form action="categories.php" method="GET" class="search-container" id="searchForm">
            <div class="search-wrapper">
                <input type="text" id="searchInput" name="search" class="search-input" placeholder="Search departments (e.g., Ancient Egypt)..." value="<?php echo htmlspecialchars($search); ?>" autocomplete="off" onkeyup="liveFilter()">
            </div>

            <button type="submit" class="btn-search">🔍 Search</button>
            <a href="categories.php" class="btn-clear">Clear</a>
        </form>

        <div class="cat-grid" id="galleryGrid">
            <?php if($result->num_rows > 0): ?>
                <?php while($cat = $result->fetch_assoc()): 
                    // Make the category name lowercase for the hidden search data
                    $searchable_text = strtolower($cat['name']);
                ?>
                    <div class="cat-card" data-search="<?php echo htmlspecialchars($searchable_text); ?>">
                        <img src="uploads/<?php echo $cat['image_path']; ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>">
                        <div class="cat-body">
                            <h3 class="cat-title"><?php echo htmlspecialchars($cat['name']); ?></h3>
                            <a href="exhibits.php?cat=<?php echo $cat['id']; ?>" class="btn-view">View Artifacts &rarr;</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="grid-column: 1 / -1; text-align: center; font-size: 1.2rem; color: #7f8c8d; padding: 40px;">No departments found in the database.</p>
            <?php endif; ?>

            <p id="noResultsMessage">No departments match your live search.</p>
        </div>
    </div>

    <script>
        function liveFilter() {
            // 1. Get what the user typed and make it lowercase
            let query = document.getElementById('searchInput').value.toLowerCase();
            
            // 2. Get all the category cards on the page
            let cards = document.querySelectorAll('.cat-card');
            let hasVisibleCards = false;

            // 3. Loop through every card
            cards.forEach(card => {
                // Read the secret data-search attribute
                let searchableText = card.getAttribute('data-search');

                // Show or hide based on the match
                if (searchableText.includes(query)) {
                    card.style.display = 'block'; 
                    hasVisibleCards = true;
                } else {
                    card.style.display = 'none'; 
                }
            });

            // 4. Manage the "No results" message
            let noResultsMsg = document.getElementById('noResultsMessage');
            if (hasVisibleCards) {
                noResultsMsg.style.display = 'none';
            } else {
                noResultsMsg.style.display = 'block';
            }
        }

        // Stop the "Enter" key from reloading the page
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault(); 
        });
    </script>

</body>
</html>