<?php 
include 'db.php'; 
include 'header.php'; 
session_start();

// Check if the admin session is NOT set
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect them to the login page
    header("Location: login.php");
    exit(); // Stop running the rest of the code
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
            echo "<div style='color:green; text-align:center;'>Artifact added successfully!</div>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

// Fetch categories for the dropdown
$categories = $conn->query("SELECT * FROM categories");
?>

<div style="max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; font-family: sans-serif;">
    <h2>Register New Artifact</h2>
    <form action="add_exhibit.php" method="POST" enctype="multipart/form-data">
        
        <label>Artifact Name:</label>
        <input type="text" name="title" style="width:100%; margin-bottom:10px;" required>

        <label>Category:</label>
        <select name="category_id" style="width:100%; margin-bottom:10px;" required>
            <?php while($cat = $categories->fetch_assoc()): ?>
                <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
            <?php endwhile; ?>
        </select>

        <label>Description / History:</label>
        <textarea name="description" rows="4" style="width:100%; margin-bottom:10px;"></textarea>

        <label>Year Created/Found (e.g., 500 BC):</label>
        <input type="text" name="artifact_year" style="width:100%; margin-bottom:10px;">

        <label>Place of Origin:</label>
        <input type="text" name="origin" style="width:100%; margin-bottom:10px;" placeholder="e.g. Rome, Italy">

        <label>Donated By:</label>
        <input type="text" name="donated_by" style="width:100%; margin-bottom:10px;">

        <label>Artifact Image:</label>
        <input type="file" name="image" accept="image/*" style="width:100%; margin-bottom:20px;" required>

        <button type="submit" name="submit" style="width:100%; padding:10px; background:#2c3e50; color:white; border:none; cursor:pointer;">
            Save to Collection
        </button>
    </form>
</div>