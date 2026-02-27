<?php 
// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php'; 
include 'header.php'; 

// 1. Check if an ID was passed in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<h2 style='text-align:center; margin-top:50px;'>Artifact not found!</h2>";
    exit();
}

$id = $_GET['id'];

// 2. Fetch the exhibit details AND its category name using a JOIN
$query = "
    SELECT exhibits.*, categories.name AS category_name 
    FROM exhibits 
    LEFT JOIN categories ON exhibits.category_id = categories.id 
    WHERE exhibits.id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// 3. Check if the exhibit actually exists
if ($result->num_rows === 0) {
    echo "<h2 style='text-align:center; margin-top:50px;'>Artifact not found!</h2>";
    exit();
}

$exhibit = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $exhibit['title']; ?> | Museum Labo Catalog</title>
    <style>
        .detail-container { max-width: 1000px; margin: 40px auto; padding: 20px; font-family: 'Georgia', serif; }
        .detail-grid { display: flex; flex-wrap: wrap; gap: 40px; }
        .detail-image { flex: 1; min-width: 300px; }
        .detail-image img { width: 100%; border-radius: 8px; box-shadow: 0 10px 20px rgba(0,0,0,0.15); }
        .detail-info { flex: 1; min-width: 300px; }
        .detail-info h1 { font-size: 2.5rem; color: #2c3e50; margin-top: 0; }
        .badge { background: #e67e22; color: white; padding: 5px 10px; border-radius: 4px; font-size: 0.9rem; text-transform: uppercase; }
        .meta-data { margin: 20px 0; padding: 15px; background: #f9f9f9; border-left: 4px solid #2c3e50; }
        .meta-data p { margin: 5px 0; }
        .description { font-size: 1.1rem; line-height: 1.8; color: #444; margin-top: 20px; }
        .back-link { display: inline-block; margin-top: 30px; color: #2c3e50; text-decoration: none; font-weight: bold; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="detail-container">
    <div class="detail-grid">
        
        <div class="detail-image">
            <img src="uploads/<?php echo $exhibit['image_path']; ?>" alt="<?php echo $exhibit['title']; ?>">
        </div>

        <div class="detail-info">
            <h1><?php echo $exhibit['title']; ?></h1>
            <span class="badge"><?php echo $exhibit['category_name'] ? $exhibit['category_name'] : 'Uncategorized'; ?></span>
            
            <div class="meta-data">
                <p><strong>Period / Year:</strong> <?php echo $exhibit['artifact_year'] ? $exhibit['artifact_year'] : 'Unknown'; ?></p>
                <p><strong>Origin:</strong> <?php echo $exhibit['origin'] ? $exhibit['origin'] : 'Unknown'; ?></p>
                <p><strong>Donated By:</strong> <?php echo $exhibit['donated_by'] ? $exhibit['donated_by'] : 'Museum Archive'; ?></p>
            </div>

            <div class="description">
                <?php 
                // nl2br safely converts line breaks in the text area to actual HTML <br> tags
                echo nl2br(htmlspecialchars($exhibit['description'])); 
                ?>
            </div>

            <a href="exhibits.php" class="back-link">&larr; Back to Exhibits Gallery</a>
        </div>

    </div>
</div>

</body>
</html>