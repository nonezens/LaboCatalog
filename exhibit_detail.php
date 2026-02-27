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
    <link rel="stylesheet" href="style.css">
    <style>
        .detail-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }

        .detail-header h1 {
            margin: 0;
            font-size: 2.5rem;
            color: var(--gold);
        }

        .detail-container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: start;
            margin-bottom: 60px;
        }

        .detail-image {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .detail-image img {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 8px;
        }

        .detail-info h2 {
            font-size: 2rem;
            color: var(--dark);
            margin-top: 0;
        }

        .badge {
            background: var(--primary);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 20px;
            letter-spacing: 0.5px;
        }

        .meta-data {
            background: linear-gradient(135deg, rgba(19, 113, 55, 0.05), rgba(197, 160, 89, 0.05));
            padding: 25px;
            border-radius: 8px;
            border-left: 4px solid var(--gold);
            margin: 20px 0;
        }

        .meta-data p {
            margin: 12px 0;
            font-size: 1.05rem;
            color: var(--dark);
        }

        .meta-data strong {
            color: var(--primary);
            font-weight: 700;
        }

        .description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #555;
            margin-top: 30px;
            padding: 25px;
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow-sm);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 40px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
            font-size: 1.05rem;
            transition: 0.3s;
        }

        .back-link:hover {
            color: var(--gold);
            gap: 12px;
        }

        .admin-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }

        .btn-admin {
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 700;
            text-align: center;
            transition: 0.3s;
            display: inline-block;
        }

        .btn-edit {
            background: var(--gold);
            color: white;
        }

        .btn-edit:hover {
            background: var(--gold-hover);
            transform: translateY(-2px);
        }

        .btn-delete {
            background: #e74c3c;
            color: white;
        }

        .btn-delete:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .detail-grid { grid-template-columns: 1fr; gap: 30px; }
            .detail-header h1 { font-size: 1.8rem; }
            .detail-info h2 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <section class="detail-header">
        <h1><?php echo htmlspecialchars($exhibit['title']); ?></h1>
    </section>

    <div class="detail-container">
        <div class="detail-grid">
            <div class="detail-image">
                <img src="uploads/<?php echo htmlspecialchars($exhibit['image_path']); ?>" alt="<?php echo htmlspecialchars($exhibit['title']); ?>">
            </div>

            <div class="detail-info">
                <h2><?php echo htmlspecialchars($exhibit['title']); ?></h2>
                <span class="badge"><?php echo $exhibit['category_name'] ? htmlspecialchars($exhibit['category_name']) : 'Uncategorized'; ?></span>
                
                <div class="meta-data">
                    <p><strong>📅 Period / Year:</strong> <?php echo $exhibit['artifact_year'] ? htmlspecialchars($exhibit['artifact_year']) : 'Unknown'; ?></p>
                    <p><strong>🌍 Origin / Location:</strong> <?php echo $exhibit['origin'] ? htmlspecialchars($exhibit['origin']) : 'Unknown'; ?></p>
                    <p><strong>🎁 Donated By / Source:</strong> <?php echo $exhibit['donated_by'] ? htmlspecialchars($exhibit['donated_by']) : 'Museum Collection'; ?></p>
                </div>

                <h3 style="color: var(--dark); margin-top: 30px;">About This Artifact</h3>
            </div>
        </div>

        <div class="description">
            <?php echo nl2br(htmlspecialchars($exhibit['description'])); ?>
        </div>

        <?php if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
        <div class="admin-actions">
            <a href="edit_exhibit.php?id=<?php echo $exhibit['id']; ?>" class="btn-admin btn-edit">✏️ Edit Artifact</a>
            <a href="delete_exhibit.php?id=<?php echo $exhibit['id']; ?>" class="btn-admin btn-delete" onclick="return confirm('Are you sure you want to delete this artifact?');">🗑️ Delete</a>
        </div>
        <?php endif; ?>

        <a href="exhibits.php" class="back-link">← Back to Gallery</a>
    </div>

    <footer>
        <p>&copy; 2026 Museo De Labo Catalog. Preserving Our Cultural Heritage for Future Generations.</p>
    </footer>
</body>
</html>