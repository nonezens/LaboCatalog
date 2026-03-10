<?php
include 'db.php'; 

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$query = "SELECT * FROM exhibits";
$total_query = "SELECT COUNT(*) AS total FROM exhibits";
$where_clauses = [];
$params = [];
$types = '';

if ($category_id > 0) {
    $where_clauses[] = "category_id = ?";
    $params[] = $category_id;
    $types .= 'i';
}

if (!empty($search)) {
    $where_clauses[] = "(title LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}

if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(' AND ', $where_clauses);
    $total_query .= " WHERE " . implode(' AND ', $where_clauses);
}

$query .= " ORDER BY id DESC LIMIT ?, ?";
$params[] = $offset;
$params[] = $limit;
$types .= 'ii';

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Remove limit and offset for total count
array_pop($params);
array_pop($params);
$types = substr($types, 0, -2);

$total_stmt = $conn->prepare($total_query);
if (!empty($types)) {
    $total_stmt->bind_param($types, ...$params);
}
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_pages = ceil($total_row['total'] / $limit);

$categories_query = "SELECT * FROM categories ORDER BY name ASC";
$categories_result = $conn->query($categories_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artifacts</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>

    <div class="artifacts-container" style="padding: 20px;">
        <h1 style="color: #2c3e50; text-align: center;">Artifacts</h1>

        <div class="filter-container" style="margin-bottom: 20px; text-align: center;">
            <form action="artifacts.php" method="GET" style="display: inline-block;">
                <label for="search" style="font-weight: bold;">Search:</label>
                <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search); ?>" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <label for="category_id" style="font-weight: bold;">Filter by Category:</label>
                <select name="category_id" id="category_id" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="0">All Categories</option>
                    <?php 
                    $categories_result->data_seek(0);
                    while ($category = $categories_result->fetch_assoc()): 
                    ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo ($category['id'] == $category_id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" style="padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 4px;">Filter</button>
            </form>
        </div>

        <div class="artifacts-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            <?php if($result && $result->num_rows > 0): while($row = $result->fetch_assoc()): ?>
                <div class="artifact-card" style="background: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); overflow: hidden;">
                    <img src="uploads/<?php echo $row['image_path']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" style="width: 100%; height: 200px; object-fit: cover;">
                    <div style="padding: 20px;">
                        <h3 style="margin-top: 0; color: #2c3e50;"><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p style="color: #7f8c8d;"><?php echo substr(htmlspecialchars($row['description']), 0, 100); ?>...</p>
                        <a href="exhibit_detail.php?id=<?php echo $row['id']; ?>" style="color: #3498db; text-decoration: none; font-weight: bold;">Read More</a>
                    </div>
                </div>
            <?php endwhile; else: ?>
                <p style="text-align: center; grid-column: 1 / -1;">No artifacts found.</p>
            <?php endif; ?>
        </div>

        <div class="pagination" style="margin-top: 20px; text-align: center;">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&category_id=<?php echo $category_id; ?>&search=<?php echo urlencode($search); ?>" style="padding: 10px 15px; margin: 0 5px; background: <?php echo ($i == $page) ? '#3498db' : '#fff'; ?>; color: <?php echo ($i == $page) ? '#fff' : '#3498db'; ?>; border: 1px solid #ddd; text-decoration: none; border-radius: 4px;"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    </div>

</body>
</html>