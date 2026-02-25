<?php 
include 'db.php'; 
include 'header.php'; 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the admin session is NOT set
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect them to the login page
    header("Location: login.php");
    exit(); 
}
if(isset($_POST['submit'])) {
    $title = $_POST['title'];
    $cat = $_POST['category_id'];
    $desc = $_POST['description'];
    $donor = $_POST['donated_by'];
    $year = $_POST['artifact_year'];
    $origin = $_POST['origin'];
    
    // Image Handling
    $imgName = $_FILES['image']['name'];
    $tmpName = $_FILES['image']['tmp_name'];
    $folder = "uploads/" . $imgName;

    if(move_uploaded_file($tmpName, $folder)) {
        // Updated Prepared Statement with new columns
        $stmt = $conn->prepare("INSERT INTO exhibits (title, category_id, image_path, description, donated_by, artifact_year, origin) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisssss", $title, $cat, $imgName, $desc, $donor, $year, $origin);
        
        if($stmt->execute()) {
            echo "<div class=\"message success\">Artifact added successfully!</div>";
        } else {
            echo "<div class=\"message error\">Error: ".htmlspecialchars($conn->error, ENT_QUOTES)."</div>";
        }
    }
}

// Fetch categories for the dropdown
$categories = $conn->query("SELECT * FROM categories");
?>

<div class="form-container">
    <h2>Register New Artifact</h2>
    <form action="add_exhibit.php" method="POST" enctype="multipart/form-data">

        <div class="form-group">
            <label class="form-label">Artifact Name</label>
            <input class="form-input" type="text" name="title" required>
        </div>

        <div class="form-group">
            <label class="form-label">Category</label>
            <select class="form-select" name="category_id" required>
                <?php while($cat = $categories->fetch_assoc()): ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name'], ENT_QUOTES); ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Description / History</label>
            <textarea class="form-textarea" name="description" rows="4"></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Year Created/Found (e.g., 500 BC)</label>
            <input class="form-input" type="text" name="artifact_year">
        </div>

        <div class="form-group">
            <label class="form-label">Place of Origin</label>
            <input class="form-input" type="text" name="origin" placeholder="e.g. Rome, Italy">
        </div>

        <div class="form-group">
            <label class="form-label">Donated By</label>
            <input class="form-input" type="text" name="donated_by">
        </div>

        <div class="form-group">
            <label class="form-label">Artifact Image</label>
            <input class="form-file" id="exhibitImage" type="file" name="image" accept="image/*" required>
            <img id="exhibitPreview" class="preview-img" src="#" alt="" style="display:none;" />
        </div>

        <button type="submit" name="submit" class="btn-primary">Save to Collection</button>
    </form>
</div>

<script>
document.getElementById('exhibitImage').addEventListener('change', function(e){
    const [file] = this.files;
    const img = document.getElementById('exhibitPreview');
    if(file){
        img.src = URL.createObjectURL(file);
        img.style.display = 'block';
    } else {
        img.style.display = 'none';
    }
});
</script>