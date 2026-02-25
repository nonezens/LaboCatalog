<?php 
include 'db.php'; 
include 'header.php'; 

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
            <div class="card" style="border: 1px solid #ddd; padding: 15px; border-radius: 10px;">
                <img src="uploads/<?php echo $row['image_path']; ?>" style="width:100%; height:250px; object-fit:cover;">
                <h3><?php echo $row['title']; ?></h3>
                <p><strong>Period:</strong> <?php echo $row['artifact_year']; ?></p>
                <p><strong>Origin:</strong> <?php echo $row['origin']; ?></p>
                <p><em><?php echo $row['description']; ?></em></p>
                <hr>
                <small>Generously donated by: <?php echo $row['donated_by']; ?></small>
            </div>
        <?php endwhile; ?>
    </div>
</main>