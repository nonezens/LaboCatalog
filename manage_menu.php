<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php';
include 'functions.php';

$msg = "";
$msg_color = "red";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_menu_item'])) {
        $title = trim($_POST['title']);
        $url = trim($_POST['url']);
        $parent_id = (int)$_POST['parent_id'];

        if (!empty($title) && !empty($url)) {
            $stmt = $conn->prepare("INSERT INTO menu (title, url, parent_id) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $title, $url, $parent_id);
            if ($stmt->execute()) {
                log_activity($conn, $_SESSION['admin_id'], "Added menu item: " . $title);
                $msg = "Menu item added successfully!";
                $msg_color = "green";
            } else {
                $msg = "Error adding menu item: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $msg = "Please fill in all fields.";
        }
    } elseif (isset($_POST['update_menu_item'])) {
        $id = (int)$_POST['id'];
        $title = trim($_POST['title']);
        $url = trim($_POST['url']);
        $parent_id = (int)$_POST['parent_id'];

        if (!empty($title) && !empty($url)) {
            $stmt = $conn->prepare("UPDATE menu SET title = ?, url = ?, parent_id = ? WHERE id = ?");
            $stmt->bind_param("ssii", $title, $url, $parent_id, $id);
            if ($stmt->execute()) {
                log_activity($conn, $_SESSION['admin_id'], "Updated menu item: " . $title);
                $msg = "Menu item updated successfully!";
                $msg_color = "green";
            } else {
                $msg = "Error updating menu item: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $msg = "Please fill in all fields.";
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("SELECT title FROM menu WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $menu_item = $result->fetch_assoc();

    if ($menu_item) {
        $stmt = $conn->prepare("DELETE FROM menu WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            log_activity($conn, $_SESSION['admin_id'], "Deleted menu item: " . $menu_item['title']);
            header("Location: manage_menu.php?msg=deleted");
            exit();
        }
    }
}

$query = "SELECT * FROM menu ORDER BY parent_id, id ASC";
$result = $conn->query($query);
$menu_items = [];
while ($row = $result->fetch_assoc()) {
    $menu_items[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menu</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div class="form-container" style="max-width: 800px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #2c3e50;">Manage Menu</h2>
        <?php if ($msg): ?>
            <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo ($msg_color === 'red' ? '#c0392b' : '#27ae60'); ?>; font-weight: bold;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div style="margin-bottom: 30px;">
            <h3 style="color: #2c3e50;">Add New Menu Item</h3>
            <form action="manage_menu.php" method="POST">
                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <input type="text" name="title" placeholder="Title" required style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <input type="text" name="url" placeholder="URL" required style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <select name="parent_id" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="0">No Parent</option>
                        <?php foreach ($menu_items as $item): ?>
                            <option value="<?php echo $item['id']; ?>"><?php echo htmlspecialchars($item['title']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="add_menu_item" style="padding: 10px 20px; background: #2980b9; color: white; border: none; border-radius: 4px;">Add Menu Item</button>
            </form>
        </div>

        <h3 style="color: #2c3e50;">Existing Menu Items</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Title</th><th>URL</th><th>Parent ID</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($menu_items as $item): ?>
                        <tr>
                            <form action="manage_menu.php" method="POST">
                                <td><?php echo $item['id']; ?></td>
                                <td><input type="text" name="title" value="<?php echo htmlspecialchars($item['title']); ?>" style="padding: 5px; border: 1px solid #ddd; border-radius: 4px;"></td>
                                <td><input type="text" name="url" value="<?php echo htmlspecialchars($item['url']); ?>" style="padding: 5px; border: 1px solid #ddd; border-radius: 4px;"></td>
                                <td>
                                    <select name="parent_id" style="padding: 5px; border: 1px solid #ddd; border-radius: 4px;">
                                        <option value="0">No Parent</option>
                                        <?php foreach ($menu_items as $parent_item): ?>
                                            <?php if ($parent_item['id'] != $item['id']): ?>
                                                <option value="<?php echo $parent_item['id']; ?>" <?php echo ($parent_item['id'] == $item['parent_id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($parent_item['title']); ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" name="update_menu_item" style="padding: 5px 10px; background: #f39c12; color: white; border: none; border-radius: 4px;">Update</button>
                                    <a href="manage_menu.php?delete=<?php echo $item['id']; ?>" onclick="return confirm('Are you sure?');" style="padding: 5px 10px; background: #e74c3c; color: white; text-decoration: none; border-radius: 4px;">Delete</a>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    </main>
</div>

</body>
</html>