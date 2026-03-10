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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($exhibit['title']); ?> | Museum Labo Catalog</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/exhibit_detail.css">
</head>
<body>

<div class="detail-container">
    <div class="detail-grid">
        
        <div class="detail-image" id="imageContainer">
            <img id="exhibitImage" src="uploads/<?php echo htmlspecialchars($exhibit['image_path']); ?>" alt="<?php echo htmlspecialchars($exhibit['title']); ?>">
        </div>

        <div class="detail-info" id="infoContainer">
            <h1 id="titleElement"><?php echo htmlspecialchars($exhibit['title']); ?></h1>
            <span class="detail-badge" id="badgeElement"><?php echo $exhibit['category_name'] ? htmlspecialchars($exhibit['category_name']) : 'Uncategorized'; ?></span>
            
            <div class="meta-data" id="metaElement">
                <p><strong>Period / Year:</strong> <?php echo $exhibit['artifact_year'] ? htmlspecialchars($exhibit['artifact_year']) : 'Unknown'; ?></p>
                <p><strong>Origin:</strong> <?php echo $exhibit['origin'] ? htmlspecialchars($exhibit['origin']) : 'Unknown'; ?></p>
                <p><strong>Donated By:</strong> <?php echo $exhibit['donated_by'] ? htmlspecialchars($exhibit['donated_by']) : 'Museum Archive'; ?></p>
            </div>

            <div class="description" id="descElement">
                <?php 
                echo nl2br(htmlspecialchars($exhibit['description'])); 
                ?>
            </div>

            <a href="exhibits.php" class="back-link" id="backLink">&larr; Back to Exhibits Gallery</a>
        </div>

    </div>
</div>

<!-- The Modal -->
<div id="imageModal" class="image-modal">
    <span class="modal-close" id="modalClose">&times;</span>
    <img class="modal-content" id="modalImage">
</div>

<!-- JS -->
<script src="js/exhibit_detail.js"></script>

</body>
</html>

