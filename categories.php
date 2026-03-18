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
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/categories.css">
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
            <h1 class="page-title">Explore Museum Departments</h1>

            <form action="categories.php" method="GET" class="search-container" id="searchForm">
                <div class="search-wrapper">
                    <input type="text" id="searchInput" name="search" class="search-input" placeholder="Search departments (e.g., Ancient Egypt)..." value="<?php echo htmlspecialchars($search); ?>" autocomplete="off" onkeyup="liveFilter()">
                </div>

                <button type="submit" class="btn-search">🔍 Search</button>
                <a href="categories.php" class="btn-clear">Clear</a>
            </form>

            <div class="cat-grid" id="galleryGrid">
                <?php if(isset($result) && $result->num_rows > 0): ?>
                    <?php while($cat = $result->fetch_assoc()): 
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

        <script src="js/categories.js"></script>

    <?php endif; ?>

</body>
</html>