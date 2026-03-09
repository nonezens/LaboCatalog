<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php'; 

// Fetch categories for the filter dropdown
$cat_query = "SELECT * FROM categories ORDER BY name ASC";
$cat_result = $conn->query($cat_query);

$query = "SELECT exhibits.*, categories.name AS cat_name FROM exhibits LEFT JOIN categories ON exhibits.category_id = categories.id ORDER BY exhibits.id DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Artifacts</title>
    <style>
        .filter-container {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: center;
        }
        .filter-container input, .filter-container select {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <h3 class="table-title">🖼️ Manage Artifacts</h3>

    <div class="table-container">
        <div class="filter-container">
            <input type="text" id="search" placeholder="Search by title, description...">
            <select id="category">
                <option value="">All Departments</option>
                <?php while($cat = $cat_result->fetch_assoc()): ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                <?php endwhile; ?>
            </select>
            <input type="text" id="date" placeholder="Filter by year (e.g., 18th Century)">
        </div>

        <div id="artifacts-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Image</th><th>Title</th><th>Department</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if($result->num_rows > 0): while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><img src="uploads/<?php echo $row['image_path']; ?>" width="60" height="60" style="object-fit:cover; border-radius:4px;"></td>
                    <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                    <td><?php echo $row['cat_name'] ? htmlspecialchars($row['cat_name']) : '<em style="color:#e74c3c;">Uncategorized</em>'; ?></td>
                    <td>
                        <a href="edit_exhibit.php?id=<?php echo $row['id']; ?>" class="action-btn btn-edit">✎ Edit</a>
                        <a href="delete_exhibit.php?id=<?php echo $row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete this artifact?');">🗑️ Delete</a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="5" style="text-align: center; padding: 20px;">No artifacts found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const categoryInput = document.getElementById('category');
    const dateInput = document.getElementById('date');
    const tableContainer = document.getElementById('artifacts-table');

    function fetchArtifacts() {
        let search = searchInput.value;
        let category = categoryInput.value;
        let date = dateInput.value;

        let formData = new FormData();
        formData.append('search', search);
        formData.append('category_id', category);
        formData.append('artifact_year', date);

        fetch('fetch_artifacts.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            tableContainer.innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
    }

    searchInput.addEventListener('keyup', fetchArtifacts);
    categoryInput.addEventListener('change', fetchArtifacts);
    dateInput.addEventListener('keyup', fetchArtifacts);
});
</script>

</body>
</html>