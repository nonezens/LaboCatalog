<?php 
include 'db.php'; 
include 'header.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if a category filter is applied via URL
$cat_id = isset($_GET['cat']) ? $_GET['cat'] : null;

if ($cat_id) {
    $stmt = $conn->prepare("SELECT * FROM exhibits WHERE category_id = ?");
    $stmt->bind_param("i", $cat_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM exhibits");
}
?>

<main>
    <h1><?php echo $cat_id ? "Filtered Exhibits" : "All Exhibits"; ?></h1>
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
                        <a href="edit_exhibit.php?id=<?php echo $row['id']; ?>" style="display: inline-block; background: #555; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-weight: bold;">Edit</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</main>