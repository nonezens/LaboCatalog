<?php
include 'db.php';

$search = $_POST['search'] ?? '';
$category_id = $_POST['category_id'] ?? '';
$artifact_year = $_POST['artifact_year'] ?? '';

$sql = "SELECT exhibits.*, categories.name AS cat_name 
        FROM exhibits 
        LEFT JOIN categories ON exhibits.category_id = categories.id";

$conditions = [];
$params = [];
$types = '';

if (!empty($search)) {
    $conditions[] = "(exhibits.title LIKE ? OR exhibits.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}

if (!empty($category_id)) {
    $conditions[] = "exhibits.category_id = ?";
    $params[] = $category_id;
    $types .= 'i';
}

if (!empty($artifact_year)) {
    $conditions[] = "exhibits.artifact_year LIKE ?";
    $params[] = "%$artifact_year%";
    $types .= 's';
}

if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

$sql .= " ORDER BY exhibits.id DESC";

$stmt = $conn->prepare($sql);

if (count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

?>
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
        <tr><td colspan="5" style="text-align: center; padding: 20px;">No artifacts found matching your criteria.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
