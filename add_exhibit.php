<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php"); exit(); 
}

include 'db.php'; 
include 'header.php'; 

$message = "";
if(isset($_POST['submit'])) {
    $title = $_POST['title']; $cat = $_POST['category_id']; $desc = $_POST['description'];
    $donor = $_POST['donated_by']; $year = $_POST['artifact_year']; $origin = $_POST['origin'];
    
    $imgName = $_FILES['image']['name'];
    $tmpName = $_FILES['image']['tmp_name'];

    if(move_uploaded_file($tmpName, "uploads/" . $imgName)) {
        $stmt = $conn->prepare("INSERT INTO exhibits (title, category_id, image_path, description, donated_by, artifact_year, origin) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisssss", $title, $cat, $imgName, $desc, $donor, $year, $origin);
        if($stmt->execute()) {
            $message = "<div class='alert success'>Artifact safely added to the museum vault!</div>";
        } else {
            $message = "<div class='alert error'>Error: " . $conn->error . "</div>";
        }
    }
}

$categories = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Artifact | Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-card { max-width: 800px; margin: 50px auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .admin-card h2 { text-align: center; color: #2c3e50; margin-top: 0; margin-bottom: 30px; font-size: 2rem; border-bottom: 2px solid #c5a059; display: inline-block; padding-bottom: 10px; margin-left: 50%; transform: translateX(-50%); }
        
        .form-row { display: flex; gap: 20px; margin-bottom: 20px; }
        .form-col { flex: 1; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-size: 1rem; transition: 0.3s; font-family: inherit; }
        .form-control:focus { border-color: #c5a059; outline: none; box-shadow: 0 0 5px rgba(197, 160, 89, 0.3); }
        textarea.form-control { resize: vertical; min-height: 120px; }
        
        .btn-submit { width: 100%; padding: 16px; background: #e67e22; color: white; border: none; border-radius: 6px; font-size: 1.2rem; font-weight: bold; cursor: pointer; transition: 0.3s; margin-top: 20px; }
        .btn-submit:hover { background: #d35400; transform: translateY(-2px); }

        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-weight: bold; }
        .success { background: #e8f8f5; color: #1abc9c; border: 1px solid #1abc9c; }
        .error { background: #fdedec; color: #e74c3c; border: 1px solid #e74c3c; }
    </style>
</head>
<body>

<div class="admin-card">
    <h2>Add New Artifact</h2>
    
    <?php echo $message; ?>

    <form method="POST" enctype="multipart/form-data">
        
        <div class="form-row">
            <div class="form-col">
                <label>Artifact Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="form-col">
                <label>Museum Department</label>
                <select name="category_id" class="form-control" required>
                    <option value="">-- Select Department --</option>
                    <?php while($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-col">
                <label>Period / Year</label>
                <input type="text" name="artifact_year" class="form-control" placeholder="e.g., 500 BC">
            </div>
            <div class="form-col">
                <label>Place of Origin</label>
                <input type="text" name="origin" class="form-control" placeholder="e.g., Rome, Italy">
            </div>
        </div>

        <div class="form-group">
            <label>Historical Description</label>
            <textarea name="description" class="form-control" placeholder="Enter the history and details of the artifact..."></textarea>
        </div>

        <div class="form-row">
            <div class="form-col">
                <label>Donated By / Source</label>
                <input type="text" name="donated_by" class="form-control" placeholder="e.g., The Smith Family">
            </div>
            <div class="form-col">
                <label>Artifact Image</label>
                <input type="file" name="image" class="form-control" accept="image/*" required>
            </div>
        </div>

        <button type="submit" name="submit" class="btn-submit">💾 Save to Collection</button>
    </form>
</div>

</body>
</html>