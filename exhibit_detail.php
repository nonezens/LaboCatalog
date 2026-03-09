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
    <style>
        .detail-container { max-width: 1000px; margin: 40px auto; padding: 20px; font-family: 'Georgia', serif; }
        .detail-grid { display: flex; flex-wrap: wrap; gap: 40px; }
        .detail-image { flex: 1; min-width: 300px; }
        .detail-image img { 
            width: 100%; 
            border-radius: 8px; 
            box-shadow: 0 10px 20px rgba(0,0,0,0.15); 
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .detail-image img:hover {
            transform: scale(1.03);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        .detail-info { flex: 1; min-width: 300px; }
        .detail-info h1 { font-size: 2.5rem; color: #2c3e50; margin-top: 0; }
        .badge { background: #e67e22; color: white; padding: 5px 10px; border-radius: 4px; font-size: 0.9rem; text-transform: uppercase; }
        .meta-data { margin: 20px 0; padding: 15px; background: #f9f9f9; border-left: 4px solid #2c3e50; }
        .meta-data p { margin: 5px 0; }
        .description { font-size: 1.1rem; line-height: 1.8; color: #444; margin-top: 20px; }
        .back-link { display: inline-block; margin-top: 30px; color: #2c3e50; text-decoration: none; font-weight: bold; }
        .back-link:hover { text-decoration: underline; }

        /* --- Image Modal Styles --- */
        .image-modal {
            display: none; 
            position: fixed; 
            z-index: 10000; 
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto; 
            background-color: rgba(0,0,0,0.9);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            margin: auto;
            display: block;
            max-width: 90%;
            max-height: 90%;
        }
        .modal-close {
            position: absolute;
            top: 20px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
            cursor: pointer;
        }
        .modal-close:hover,
        .modal-close:focus {
            color: #bbb;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="detail-container">
    <div class="detail-grid">
        
        <div class="detail-image">
            <img id="exhibitImage" src="uploads/<?php echo htmlspecialchars($exhibit['image_path']); ?>" alt="<?php echo htmlspecialchars($exhibit['title']); ?>">
        </div>

        <div class="detail-info">
            <h1><?php echo htmlspecialchars($exhibit['title']); ?></h1>
            <span class="badge"><?php echo $exhibit['category_name'] ? htmlspecialchars($exhibit['category_name']) : 'Uncategorized'; ?></span>
            
            <div class="meta-data">
                <p><strong>Period / Year:</strong> <?php echo $exhibit['artifact_year'] ? htmlspecialchars($exhibit['artifact_year']) : 'Unknown'; ?></p>
                <p><strong>Origin:</strong> <?php echo $exhibit['origin'] ? htmlspecialchars($exhibit['origin']) : 'Unknown'; ?></p>
                <p><strong>Donated By:</strong> <?php echo $exhibit['donated_by'] ? htmlspecialchars($exhibit['donated_by']) : 'Museum Archive'; ?></p>
            </div>

            <div class="description">
                <?php 
                echo nl2br(htmlspecialchars($exhibit['description'])); 
                ?>
            </div>

            <a href="exhibits.php" class="back-link">&larr; Back to Exhibits Gallery</a>
        </div>

    </div>
</div>

<!-- The Modal -->
<div id="imageModal" class="image-modal">
    <span class="modal-close" id="modalClose">&times;</span>
    <img class="modal-content" id="modalImage">
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the modal elements
        const modal = document.getElementById("imageModal");
        const img = document.getElementById("exhibitImage");
        const modalImg = document.getElementById("modalImage");
        const closeBtn = document.getElementById("modalClose");

        // When the user clicks on the image, open the modal
        img.onclick = function() {
            modal.style.display = "flex";
            modalImg.src = this.src;
        }

        // When the user clicks on <span> (x), close the modal
        closeBtn.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere on the modal background, close it
        modal.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    });
</script>

</body>
</html>