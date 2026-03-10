<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php';
include 'functions.php';

$msg = "";
$msg_color = "red";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['install_plugin'])) {
        // For the purpose of this task, we will not implement the plugin installation.
        // We will just display a message.
        $msg = "Plugin installation is not yet implemented.";
    }
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action == 'activate') {
        $stmt = $conn->prepare("UPDATE plugins SET is_active = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: plugins.php");
        exit();
    } elseif ($action == 'deactivate') {
        $stmt = $conn->prepare("UPDATE plugins SET is_active = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: plugins.php");
        exit();
    } elseif ($action == 'delete') {
        // For the purpose of this task, we will not implement the plugin deletion.
        // We will just display a message.
        $msg = "Plugin deletion is not yet implemented.";
    }
}

$query = "SELECT * FROM plugins";
$result = $conn->query($query);
$plugins = [];
while ($row = $result->fetch_assoc()) {
    $plugins[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Plugins</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div class="form-container" style="max-width: 800px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #2c3e50;">Manage Plugins</h2>
        <?php if ($msg): ?>
            <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo ($msg_color === 'red' ? '#c0392b' : '#27ae60'); ?>; font-weight: bold;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div style="margin-bottom: 30px;">
            <h3 style="color: #2c3e50;">Install New Plugin</h3>
            <form action="plugins.php" method="POST" enctype="multipart/form-data">
                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <input type="file" name="plugin_file" required style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <button type="submit" name="install_plugin" style="padding: 10px 20px; background: #2980b9; color: white; border: none; border-radius: 4px;">Install Plugin</button>
            </form>
        </div>

        <h3 style="color: #2c3e50;">Existing Plugins</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th><th>Folder</th><th>Status</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($plugins as $plugin): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($plugin['name']); ?></td>
                            <td><?php echo htmlspecialchars($plugin['folder']); ?></td>
                            <td><?php echo $plugin['is_active'] ? 'Active' : 'Inactive'; ?></td>
                            <td>
                                <?php if ($plugin['is_active']): ?>
                                    <a href="plugins.php?action=deactivate&id=<?php echo $plugin['id']; ?>" style="padding: 5px 10px; background: #f39c12; color: white; text-decoration: none; border-radius: 4px;">Deactivate</a>
                                <?php else: ?>
                                    <a href="plugins.php?action=activate&id=<?php echo $plugin['id']; ?>" style="padding: 5px 10px; background: #27ae60; color: white; text-decoration: none; border-radius: 4px;">Activate</a>
                                <?php endif; ?>
                                <a href="plugins.php?action=delete&id=<?php echo $plugin['id']; ?>" onclick="return confirm('Are you sure?');" style="padding: 5px 10px; background: #e74c3c; color: white; text-decoration: none; border-radius: 4px;">Delete</a>
                            </td>
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