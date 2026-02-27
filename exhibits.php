<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db.php'; 

$is_logged_in = isset($_SESSION['admin_logged_in']) || isset($_SESSION['guest_logged_in']);

if ($is_logged_in) {
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $cat_id = isset($_GET['cat']) ? $_GET['cat'] : '';
    $period_id = isset($_GET['period']) ? $_GET['period'] : '';
    $origin_id = isset($_GET['origin']) ? $_GET['origin'] : '';
    $sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'date_desc';

    // Determine ORDER BY clause based on sort selection
    $order_clause = "exhibits.id DESC";
    if ($sort_by === 'name_asc') {
        $order_clause = "exhibits.title ASC";
    } elseif ($sort_by === 'name_desc') {
        $order_clause = "exhibits.title DESC";
    } elseif ($sort_by === 'date_asc') {
        $order_clause = "exhibits.artifact_year ASC";
    } elseif ($sort_by === 'date_desc') {
        $order_clause = "exhibits.artifact_year DESC";
    }

    // Build dynamic query with filters - search includes category names
    $query = "SELECT exhibits.*, categories.name AS category_name FROM exhibits 
              LEFT JOIN categories ON exhibits.category_id = categories.id 
              WHERE (exhibits.title LIKE ? OR exhibits.donated_by LIKE ? OR exhibits.description LIKE ? OR categories.name LIKE ?)";
    $params = ["%$search%", "%$search%", "%$search%", "%$search%"];
    $types = "ssss";

    if ($cat_id) {
        $query .= " AND exhibits.category_id = ?";
        $params[] = (int)$cat_id;
        $types .= "i";
    }
    
    if ($period_id) {
        $query .= " AND exhibits.artifact_year = ?";
        $params[] = $period_id;
        $types .= "s";
    }
    
    if ($origin_id) {
        $query .= " AND exhibits.origin = ?";
        $params[] = $origin_id;
        $types .= "s";
    }

    $query .= " ORDER BY " . $order_clause;

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch distinct values for filter dropdowns
    $all_categories = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
    $all_periods = $conn->query("SELECT DISTINCT artifact_year FROM exhibits WHERE artifact_year IS NOT NULL AND artifact_year != '' ORDER BY artifact_year ASC");
    $all_origins = $conn->query("SELECT DISTINCT origin FROM exhibits WHERE origin IS NOT NULL AND origin != '' ORDER BY origin ASC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collection | Museum Labo Catalog</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/exhibits.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <?php if (!$is_logged_in): ?>
        
        <section class="not-logged-in">
            <h2>🏛️ Explore Our Collection</h2>
            <p>Browse our curated collection of artifacts, organized by department with advanced filters. Sign in to access the digital catalog.</p>
            <a href="login.php">Sign the Guestbook to Enter →</a>
        </section>

    <?php else: ?>

        <div class="container" style="padding-top: 50px; padding-bottom: 50px;">
            <h1 class="page-title">Museum Collection</h1>

            <!-- Filter Section -->
            <div class="filter-section">
                <h3>🔍 Filter & Search Collections</h3>
                
                <form method="GET" id="filterForm">
                    <div class="filter-grid">
                        <!-- Search -->
                        <div class="filter-group">
                            <label>Search (Name, Category, Donor)</label>
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Type to search...">
                        </div>

                        <!-- Category Filter -->
                        <div class="filter-group">
                            <label>Department</label>
                            <select name="cat">
                                <option value="">-- All Departments --</option>
                                <?php if($all_categories): ?>
                                    <?php while($cat_option = $all_categories->fetch_assoc()): ?>
                                        <option value="<?php echo $cat_option['id']; ?>" <?php echo ($cat_id == $cat_option['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat_option['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Period Filter -->
                        <div class="filter-group">
                            <label>Period / Year</label>
                            <select name="period">
                                <option value="">-- All Periods --</option>
                                <?php if($all_periods): ?>
                                    <?php while($period_option = $all_periods->fetch_assoc()): ?>
                                        <option value="<?php echo htmlspecialchars($period_option['artifact_year']); ?>" <?php echo ($period_id == $period_option['artifact_year']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($period_option['artifact_year']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Origin Filter -->
                        <div class="filter-group">
                            <label>Origin / Location</label>
                            <select name="origin">
                                <option value="">-- All Origins --</option>
                                <?php if($all_origins): ?>
                                    <?php while($origin_option = $all_origins->fetch_assoc()): ?>
                                        <option value="<?php echo htmlspecialchars($origin_option['origin']); ?>" <?php echo ($origin_id == $origin_option['origin']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($origin_option['origin']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Sort By -->
                    <div style="margin-top: 15px; display: grid; grid-template-columns: 1fr auto; gap: 15px; align-items: flex-end;">
                        <div class="filter-group">
                            <label>Sort By</label>
                            <select name="sort">
                                <option value="date_desc" <?php echo ($sort_by === 'date_desc') ? 'selected' : ''; ?>>📅 Newest First</option>
                                <option value="date_asc" <?php echo ($sort_by === 'date_asc') ? 'selected' : ''; ?>>📅 Oldest First</option>
                                <option value="name_asc" <?php echo ($sort_by === 'name_asc') ? 'selected' : ''; ?>>🔤 Name (A-Z)</option>
                                <option value="name_desc" <?php echo ($sort_by === 'name_desc') ? 'selected' : ''; ?>>🔤 Name (Z-A)</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="filter-buttons">
                            <button type="submit" class="btn-filter">🔍 Apply</button>
                            <a href="exhibits.php" class="btn-filter btn-filter-clear" style="text-decoration: none; display: inline-block;">✕ Clear</a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Results Info -->
            <?php if(isset($result)): ?>
                <div class="results-info">
                    📊 Found <strong><?php echo $result->num_rows; ?></strong> artifact<?php echo ($result->num_rows != 1) ? 's' : ''; ?>
                </div>
            <?php endif; ?>

            <!-- Gallery Grid -->
            <div class="gallery-grid" id="galleryGrid">
                <?php if(isset($result) && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="card">
                            <img src="uploads/<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                            <div class="card-body">
                                <span class="card-meta">
                                    <?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?>
                                </span>
                                <h3 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                                <div class="card-info">
                                    <?php if($row['artifact_year']): ?>
                                        <strong>📅 Period:</strong> <?php echo htmlspecialchars($row['artifact_year']); ?><br>
                                    <?php endif; ?>
                                    <?php if($row['origin']): ?>
                                        <strong>🌍 Origin:</strong> <?php echo htmlspecialchars($row['origin']); ?><br>
                                    <?php endif; ?>
                                    <?php if($row['donated_by']): ?>
                                        <strong>👤 Donor:</strong> <?php echo htmlspecialchars($row['donated_by']); ?>
                                    <?php endif; ?>
                                </div>
                                <p class="card-desc"><?php echo htmlspecialchars(substr($row['description'], 0, 100)); ?>...</p>
                                <a href="exhibit_detail.php?id=<?php echo $row['id']; ?>" class="btn-view">View Details →</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="grid-column: 1 / -1; text-align: center; font-size: 1.2rem; color: #7f8c8d; padding: 40px;">
                        No artifacts found. Try adjusting your filters.
                    </p>
                <?php endif; ?>
            </div>
        </div>

    <?php endif; ?>

    <footer>
        <p>&copy; 2026 Museo De Labo Catalog. Preserving Our Cultural Heritage for Future Generations.</p>
    </footer>
</body>
</html>