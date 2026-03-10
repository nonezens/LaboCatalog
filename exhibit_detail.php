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
        
        /* Image container with morphing animation */
        .detail-image { 
            flex: 1; 
            min-width: 300px; 
            opacity: 0;
            transform: scale(0.8) rotateY(-20deg);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .detail-image.visible {
            opacity: 1;
            transform: scale(1) rotateY(0);
        }
        
        .detail-image img { 
            width: 100%; 
            border-radius: 8px; 
            box-shadow: 0 10px 20px rgba(0,0,0,0.15); 
            cursor: pointer;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
        }
        .detail-image img:hover {
            transform: scale(1.03);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        
        /* Info container with morphing animation */
        .detail-info { 
            flex: 1; 
            min-width: 300px; 
            opacity: 0;
            transform: translateX(50px);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .detail-info.visible {
            opacity: 1;
            transform: translateX(0);
        }
        
        .detail-info h1 { 
            font-size: 2.5rem; 
            color: #2c3e50; 
            margin-top: 0; 
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .detail-info h1.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .badge { 
            background: #e67e22; 
            color: white; 
            padding: 5px 10px; 
            border-radius: 4px; 
            font-size: 0.9rem; 
            text-transform: uppercase;
            display: inline-block;
            opacity: 0;
            transform: scale(0);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transition-delay: 0.2s;
        }
        
        .badge.visible {
            opacity: 1;
            transform: scale(1);
        }
        
        .meta-data { 
            margin: 20px 0; 
            padding: 15px; 
            background: #f9f9f9; 
            border-left: 4px solid #2c3e50; 
            opacity: 0;
            transform: translateX(-30px);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            transition-delay: 0.3s;
        }
        
        .meta-data.visible {
            opacity: 1;
            transform: translateX(0);
        }
        
        .meta-data p { margin: 5px 0; }
        
        .description { 
            font-size: 1.1rem; 
            line-height: 1.8; 
            color: #444; 
            margin-top: 20px; 
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            transition-delay: 0.4s;
        }
        
        .description.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .back-link { 
            display: inline-block; 
            margin-top: 30px; 
            color: #2c3e50; 
            text-decoration: none; 
            font-weight: bold; 
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.4s ease;
            transition-delay: 0.5s;
        }
        
        .back-link.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .back-link:hover { 
            text-decoration: underline; 
            color: #c5a059;
            transform: translateX(5px);
        }

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
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .image-modal.active {
            display: flex;
            opacity: 1;
        }
        
        .modal-content {
            margin: auto;
            display: block;
            max-width: 90%;
            max-height: 90%;
            transform: scale(0.5);
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .image-modal.active .modal-content {
            transform: scale(1);
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
            color: #c5a059;
            transform: rotate(90deg);
        }
    </style>
</head>
<body>

<div class="detail-container">
    <div class="detail-grid">
        
        <div class="detail-image" id="imageContainer">
            <img id="exhibitImage" src="uploads/<?php echo htmlspecialchars($exhibit['image_path']); ?>" alt="<?php echo htmlspecialchars($exhibit['title']); ?>">
        </div>

        <div class="detail-info" id="infoContainer">
            <h1 id="titleElement"><?php echo htmlspecialchars($exhibit['title']); ?></h1>
            <span class="badge" id="badgeElement"><?php echo $exhibit['category_name'] ? htmlspecialchars($exhibit['category_name']) : 'Uncategorized'; ?></span>
            
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

<script>
    // Trigger animations on page load
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            document.getElementById('imageContainer').classList.add('visible');
        }, 100);
        
        setTimeout(function() {
            document.getElementById('infoContainer').classList.add('visible');
        }, 200);
        
        setTimeout(function() {
            document.getElementById('titleElement').classList.add('visible');
        }, 300);
        
        setTimeout(function() {
            document.getElementById('badgeElement').classList.add('visible');
        }, 400);
        
        setTimeout(function() {
            document.getElementById('metaElement').classList.add('visible');
        }, 500);
        
        setTimeout(function() {
            document.getElementById('descElement').classList.add('visible');
        }, 600);
        
        setTimeout(function() {
            document.getElementById('backLink').classList.add('visible');
        }, 700);

        // Modal functionality
        const modal = document.getElementById("imageModal");
        const img = document.getElementById("exhibitImage");
        const modalImg = document.getElementById("modalImage");
        const closeBtn = document.getElementById("modalClose");

        img.onclick = function() {
            modal.classList.add('active');
            modalImg.src = this.src;
        }

        closeBtn.onclick = function() {
            modal.classList.remove('active');
        }

        modal.onclick = function(event) {
            if (event.target === modal) {
                modal.classList.remove('active');
            }
        }

        // Escape key to close modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                modal.classList.remove('active');
            }
        });
    });
</script>

</body>
</html>

