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
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/exhibits.css">
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

    <?php endif; ?>

    <!-- JS -->
    <script src="js/exhibits.js"></script>

</body>
</html>

