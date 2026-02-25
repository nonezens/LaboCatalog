<?php
include 'db.php';
include 'header.php';

if (!isset($_GET['id'])) {
    header("Location: exhibits.php");
    exit();
}

$id = (int)$_GET['id'];

// Fetch the exhibit
$stmt = $conn->prepare("SELECT e.*, c.name as category_name FROM exhibits e LEFT JOIN categories c ON e.category_id = c.id WHERE e.id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: exhibits.php");
    exit();
}

$exhibit = $result->fetch_assoc();
?>

<style>
    .exhibit-container {
        max-width: 1000px;
        margin: 40px auto;
        background: white;
        padding: 40px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .exhibit-header {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }

    .exhibit-image {
        width: 100%;
        max-height: 500px;
        object-fit: cover;
        border-radius: 8px;
    }

    .exhibit-info h1 {
        font-size: 2.5rem;
        margin: 0 0 20px 0;
        color: #1a1a1a;
    }

    .exhibit-meta {
        display: grid;
        gap: 15px;
        margin-bottom: 20px;
    }

    .meta-item {
        display: grid;
        grid-template-columns: 150px 1fr;
    }

    .meta-label {
        font-weight: bold;
        color: #c5a059;
        text-transform: uppercase;
        font-size: 0.9rem;
    }

    .meta-value {
        color: #555;
        font-size: 1.1rem;
    }

    .exhibit-description {
        background: #f9f9f9;
        padding: 20px;
        border-left: 4px solid #c5a059;
        margin: 30px 0;
        border-radius: 4px;
    }

    .exhibit-description h3 {
        margin-top: 0;
        color: #1a1a1a;
    }

    .exhibit-description p {
        color: #555;
        line-height: 1.6;
        margin: 0;
    }

    .btn-group {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }

    .btn {
        padding: 12px 30px;
        text-decoration: none;
        border-radius: 4px;
        font-weight: bold;
        transition: 0.3s;
        border: none;
        cursor: pointer;
        font-size: 1rem;
    }

    .btn-back {
        background: #555;
        color: white;
    }

    .btn-back:hover {
        background: #333;
    }

    .btn-edit {
        background: #c5a059;
        color: white;
    }

    .btn-edit:hover {
        background: #b48a3d;
    }

    @media (max-width: 768px) {
        .exhibit-header {
            grid-template-columns: 1fr;
        }

        .exhibit-info h1 {
            font-size: 1.8rem;
        }

        .meta-item {
            grid-template-columns: 100px 1fr;
        }
    }
</style>

<div class="exhibit-container">
    <div class="exhibit-header">
        <div>
            <img src="uploads/<?php echo htmlspecialchars($exhibit['image_path'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($exhibit['title'], ENT_QUOTES); ?>" class="exhibit-image">
        </div>
        <div class="exhibit-info">
            <h1><?php echo htmlspecialchars($exhibit['title'], ENT_QUOTES); ?></h1>
            
            <div class="exhibit-meta">
                <div class="meta-item">
                    <span class="meta-label">Category:</span>
                    <span class="meta-value"><?php echo htmlspecialchars($exhibit['category_name'], ENT_QUOTES); ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Period:</span>
                    <span class="meta-value"><?php echo htmlspecialchars($exhibit['artifact_year'], ENT_QUOTES); ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Origin:</span>
                    <span class="meta-value"><?php echo htmlspecialchars($exhibit['origin'], ENT_QUOTES); ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Donated By:</span>
                    <span class="meta-value"><?php echo htmlspecialchars($exhibit['donated_by'], ENT_QUOTES); ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="exhibit-description">
        <h3>History & Description</h3>
        <p><?php echo htmlspecialchars($exhibit['description'], ENT_QUOTES); ?></p>
    </div>

    <div class="btn-group">
        <a href="exhibits.php" class="btn btn-back">← Back to Exhibits</a>
        <?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
        <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
            <a href="edit_exhibit.php?id=<?php echo (int)$exhibit['id']; ?>" class="btn btn-edit">Edit Artifact</a>
        <?php endif; ?>
    </div>
</div>
