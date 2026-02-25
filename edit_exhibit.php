<?php
include 'db.php';
include 'header.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    header("Location: exhibits.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : (int)$_POST['id'];

// Handle update
if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $cat = (int)$_POST['category_id'];
    $desc = $_POST['description'];
    $donor = $_POST['donated_by'];
    $year = $_POST['artifact_year'];
    $origin = $_POST['origin'];
    $oldImage = $_POST['old_image'] ?? '';

    $imageName = $oldImage;
    if (!empty($_FILES['image']) && isset($_FILES['image']['tmp_name']) && $_FILES['image']['error'] === 0) {
        $imgName = basename($_FILES['image']['name']);
        $tmpName = $_FILES['image']['tmp_name'];
        $folder = "uploads/" . $imgName;
        if (move_uploaded_file($tmpName, $folder)) {
            // remove old file if different
            if ($oldImage !== '' && $oldImage !== $imgName && file_exists(__DIR__ . '/uploads/' . $oldImage)) {
                @unlink(__DIR__ . '/uploads/' . $oldImage);
            }
            $imageName = $imgName;
        }
    }

    $stmt = $conn->prepare("UPDATE exhibits SET title = ?, category_id = ?, image_path = ?, description = ?, donated_by = ?, artifact_year = ?, origin = ? WHERE id = ?");
    $stmt->bind_param('sisssssi', $title, $cat, $imageName, $desc, $donor, $year, $origin, $id);
    if ($stmt->execute()) {
        $success = 'Artifact updated successfully.';
    } else {
        $error = 'Error: ' . htmlspecialchars($conn->error, ENT_QUOTES);
    }
}

// Load exhibit
$stmt = $conn->prepare("SELECT * FROM exhibits WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    header("Location: exhibits.php");
    exit();
}
$exhibit = $res->fetch_assoc();

// Fetch categories
$categories = $conn->query("SELECT * FROM categories");
?>

<div class="form-container">
    <h2>Edit Artifact</h2>
    <?php if (!empty($success)): ?>
        <div class="message success"><?php echo htmlspecialchars($success, ENT_QUOTES); ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="message error"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></div>
    <?php endif; ?>

    <form action="edit_exhibit.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo (int)$exhibit['id']; ?>">
        <input type="hidden" name="old_image" value="<?php echo htmlspecialchars($exhibit['image_path'], ENT_QUOTES); ?>">

        <div class="form-group">
            <label class="form-label">Artifact Name</label>
            <input class="form-input" type="text" name="title" required value="<?php echo htmlspecialchars($exhibit['title'], ENT_QUOTES); ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Category</label>
            <select class="form-select" name="category_id" required>
                <?php while($cat = $categories->fetch_assoc()): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['id'] == $exhibit['category_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name'], ENT_QUOTES); ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Description / History</label>
            <textarea class="form-textarea" name="description" rows="4"><?php echo htmlspecialchars($exhibit['description'], ENT_QUOTES); ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Year Created/Found (e.g., 500 BC)</label>
            <input class="form-input" type="text" name="artifact_year" value="<?php echo htmlspecialchars($exhibit['artifact_year'], ENT_QUOTES); ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Place of Origin</label>
            <input class="form-input" type="text" name="origin" value="<?php echo htmlspecialchars($exhibit['origin'], ENT_QUOTES); ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Donated By</label>
            <input class="form-input" type="text" name="donated_by" value="<?php echo htmlspecialchars($exhibit['donated_by'], ENT_QUOTES); ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Artifact Image</label>
            <input class="form-file" id="exhibitImage" type="file" name="image" accept="image/*">
            <?php if (!empty($exhibit['image_path'])): ?>
                <img id="exhibitPreview" class="preview-img" src="uploads/<?php echo htmlspecialchars($exhibit['image_path'], ENT_QUOTES); ?>" alt="" />
            <?php else: ?>
                <img id="exhibitPreview" class="preview-img" src="#" alt="" style="display:none;" />
            <?php endif; ?>
        </div>

        <button type="submit" name="submit" class="btn-primary">Save Changes</button>
        <a href="exhibits.php" class="btn-secondary">Back to Exhibits</a>
    </form>
</div>

<script>
document.getElementById('exhibitImage').addEventListener('change', function(e){
    const [file] = this.files;
    const img = document.getElementById('exhibitPreview');
    if(file){
        img.src = URL.createObjectURL(file);
        img.style.display = 'block';
    }
});
</script>

?>
