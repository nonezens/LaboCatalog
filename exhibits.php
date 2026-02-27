<?php 
include 'db.php'; 
include 'header.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if a category filter or search term is applied via URL
$cat_id = isset($_GET['cat']) ? $_GET['cat'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// if filtering by category, also get its name for display
$category_name = '';
if ($cat_id) {
    $catStmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
    $catStmt->bind_param("i", $cat_id);
    $catStmt->execute();
    $catRes = $catStmt->get_result();
    if ($catRow = $catRes->fetch_assoc()) {
        $category_name = $catRow['name'];
    }
}


// Build query depending on filters
$grouped = false;
if ($cat_id && $search) {
    $like = "%" . $search . "%";
    $stmt = $conn->prepare("SELECT * FROM exhibits WHERE category_id = ? AND (title LIKE ? OR description LIKE ?)");
    $stmt->bind_param("iss", $cat_id, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} elseif ($cat_id) {
    $stmt = $conn->prepare("SELECT * FROM exhibits WHERE category_id = ?");
    $stmt->bind_param("i", $cat_id);
    $stmt->execute();
    $result = $stmt->get_result();
} elseif ($search) {
    $like = "%" . $search . "%";
    $stmt = $conn->prepare("SELECT * FROM exhibits WHERE title LIKE ? OR description LIKE ?");
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // no filter/search: we will group exhibits by category
    $grouped = true;
    $allGroups = [];
    $catQuery = $conn->query("SELECT * FROM categories");
    while ($c = $catQuery->fetch_assoc()) {
        $stmt = $conn->prepare("SELECT * FROM exhibits WHERE category_id = ?");
        $stmt->bind_param("i", $c['id']);
        $stmt->execute();
        $res = $stmt->get_result();
        $items = [];
        while ($r = $res->fetch_assoc()) {
            $items[] = $r;
        }
        if (count($items) > 0) {
            $allGroups[] = ['category' => $c, 'exhibits' => $items];
        }
    }
}
?>

<main>
    <!-- search and heading container -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h1 style="margin:0;">
            <?php
                if ($search) {
                    echo 'Search Results for "' . htmlspecialchars($search) . '"';
                } elseif ($cat_id) {
                    if ($category_name) {
                        echo 'Exhibits in ' . htmlspecialchars($category_name);
                    } else {
                        echo 'Filtered Exhibits';
                    }
                } else {
                    echo 'All Exhibits';
                }
            ?>
        </h1>
        <form method="get" action="exhibits.php" style="margin:0;">
            <?php if ($cat_id): ?>
                <input type="hidden" name="cat" value="<?php echo htmlspecialchars($cat_id); ?>">
            <?php endif; ?>
            <input type="text" name="search" placeholder="Search exhibits" value="<?php echo htmlspecialchars($search); ?>" style="padding:5px;">
            <button type="submit" style="padding:5px 10px;">Go</button>
        </form>
    </div>
    <?php if ($grouped): ?>
        <?php foreach ($allGroups as $grp): ?>
            <h2 style="margin-top:30px;"><?php echo htmlspecialchars($grp['category']['name']); ?></h2>
            <div class="gallery" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <?php foreach ($grp['exhibits'] as $row): ?>
                    <div class="card" style="border: 1px solid #ddd; padding: 15px; border-radius: 10px; cursor: pointer; transition: 0.3s;" onclick="window.location.href='view_exhibit.php?id=<?php echo $row['id']; ?>'">
                        <img src="uploads/<?php echo $row['image_path']; ?>" style="width:100%; height:250px; object-fit:cover;">
                        <h3><?php echo $row['title']; ?></h3>
                        <p><strong>Period:</strong> <?php echo $row['artifact_year']; ?></p>
                        <p><strong>Origin:</strong> <?php echo $row['origin']; ?></p>
                        <p><em><?php echo substr($row['description'], 0, 100); ?>...</em></p>
                        <hr>
                        <small>Generously donated by: <?php echo $row['donated_by']; ?></small>
                        <div style="margin-top: 10px;">
                            <a href="view_exhibit.php?id=<?php echo $row['id']; ?>" style="display: inline-block; background: #c5a059; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; margin-right: 5px; font-weight: bold;">View Details</a>
                            <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
                                <a href="edit_exhibit.php?id=<?php echo $row['id']; ?>" style="display: inline-block; background: #555; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; margin-right: 5px; font-weight: bold;">Edit</a>
                                <a href="delete_exhibit.php?id=<?php echo $row['id']; ?>" style="display: inline-block; background: #d32f2f; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-weight: bold;" onclick="return confirm('Are you sure you want to delete this artifact?');">Delete</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="gallery" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="card" style="border: 1px solid #ddd; padding: 15px; border-radius: 10px; cursor: pointer; transition: 0.3s;" onclick="window.location.href='view_exhibit.php?id=<?php echo $row['id']; ?>'">
                    <img src="uploads/<?php echo $row['image_path']; ?>" style="width:100%; height:250px; object-fit:cover;">
                    <h3><?php echo $row['title']; ?></h3>
                    <p><strong>Period:</strong> <?php echo $row['artifact_year']; ?></p>
                    <p><strong>Origin:</strong> <?php echo $row['origin']; ?></p>
                    <p><em><?php echo substr($row['description'], 0, 100); ?>...</em></p>
                    <hr>
                    <small>Generously donated by: <?php echo $row['donated_by']; ?></small>
                    <div style="margin-top: 10px;">
                        <a href="view_exhibit.php?id=<?php echo $row['id']; ?>" style="display: inline-block; background: #c5a059; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; margin-right: 5px; font-weight: bold;">View Details</a>
                        <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
                            <a href="edit_exhibit.php?id=<?php echo $row['id']; ?>" style="display: inline-block; background: #555; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; margin-right: 5px; font-weight: bold;">Edit</a>
                            <a href="delete_exhibit.php?id=<?php echo $row['id']; ?>" style="display: inline-block; background: #d32f2f; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-weight: bold;" onclick="return confirm('Are you sure you want to delete this artifact?');">Delete</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
    <?php while($row = $result->fetch_assoc()): ?>
            <div class="card" style="border: 1px solid #ddd; padding: 15px; border-radius: 10px; cursor: pointer; transition: 0.3s;" onclick="window.location.href='view_exhibit.php?id=<?php echo $row['id']; ?>'">
                <img src="uploads/<?php echo $row['image_path']; ?>" style="width:100%; height:250px; object-fit:cover;">
                <h3><?php echo $row['title']; ?></h3>
                <p><strong>Period:</strong> <?php echo $row['artifact_year']; ?></p>
                <p><strong>Origin:</strong> <?php echo $row['origin']; ?></p>
                <p><em><?php echo substr($row['description'], 0, 100); ?>...</em></p>
                <hr>
                <small>Generously donated by: <?php echo $row['donated_by']; ?></small>
                <div style="margin-top: 10px;">
                    <a href="view_exhibit.php?id=<?php echo $row['id']; ?>" style="display: inline-block; background: #c5a059; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; margin-right: 5px; font-weight: bold;">View Details</a>
                    <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
                        <a href="edit_exhibit.php?id=<?php echo $row['id']; ?>" style="display: inline-block; background: #555; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; margin-right: 5px; font-weight: bold;">Edit</a>
                        <a href="delete_exhibit.php?id=<?php echo $row['id']; ?>" style="display: inline-block; background: #d32f2f; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-weight: bold;" onclick="return confirm('Are you sure you want to delete this artifact?');">Delete</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</main>