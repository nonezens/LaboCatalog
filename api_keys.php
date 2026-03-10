<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php';
include 'functions.php';

$msg = "";
$msg_color = "red";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['generate_api_key'])) {
        $name = trim($_POST['name']);

        if (!empty($name)) {
            $api_key = bin2hex(random_bytes(32));
            $stmt = $conn->prepare("INSERT INTO api_keys (name, api_key) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $api_key);
            if ($stmt->execute()) {
                log_activity($conn, $_SESSION['admin_id'], "Generated API key: " . $name);
                $msg = "API key generated successfully!";
                $msg_color = "green";
            } else {
                $msg = "Error generating API key: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $msg = "Please enter a name for the API key.";
        }
    }
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action == 'activate') {
        $stmt = $conn->prepare("UPDATE api_keys SET is_active = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: api_keys.php");
        exit();
    } elseif ($action == 'deactivate') {
        $stmt = $conn->prepare("UPDATE api_keys SET is_active = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: api_keys.php");
        exit();
    } elseif ($action == 'delete') {
        $stmt = $conn->prepare("SELECT name FROM api_keys WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $api_key = $result->fetch_assoc();

        if ($api_key) {
            $stmt = $conn->prepare("DELETE FROM api_keys WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                log_activity($conn, $_SESSION['admin_id'], "Deleted API key: " . $api_key['name']);
                header("Location: api_keys.php?msg=deleted");
                exit();
            }
        }
    }
}

$query = "SELECT * FROM api_keys";
$result = $conn->query($query);
$api_keys = [];
while ($row = $result->fetch_assoc()) {
    $api_keys[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage API Keys</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div class="form-container" style="max-width: 800px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #2c3e50;">Manage API Keys</h2>
        <?php if ($msg): ?>
            <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo ($msg_color === 'red' ? '#c0392b' : '#27ae60'); ?>; font-weight: bold;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div style="margin-bottom: 30px;">
            <h3 style="color: #2c3e50;">Generate New API Key</h3>
            <form action="api_keys.php" method="POST">
                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <input type="text" name="name" placeholder="API Key Name" required style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <button type="submit" name="generate_api_key" style="padding: 10px 20px; background: #2980b9; color: white; border: none; border-radius: 4px;">Generate API Key</button>
            </form>
        </div>

        <h3 style="color: #2c3e50;">Existing API Keys</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Name</th><th>API Key</th><th>Status</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($api_keys as $key): ?>
                        <tr>
                            <td><?php echo $key['id']; ?></td>
                            <td><?php echo htmlspecialchars($key['name']); ?></td>
                            <td><code><?php echo htmlspecialchars($key['api_key']); ?></code></td>
                            <td><?php echo $key['is_active'] ? 'Active' : 'Inactive'; ?></td>
                            <td>
                                <?php if ($key['is_active']): ?>
                                    <a href="api_keys.php?action=deactivate&id=<?php echo $key['id']; ?>" style="padding: 5px 10px; background: #f39c12; color: white; text-decoration: none; border-radius: 4px;">Deactivate</a>
                                <?php else: ?>
                                    <a href="api_keys.php?action=activate&id=<?php echo $key['id']; ?>" style="padding: 5px 10px; background: #27ae60; color: white; text-decoration: none; border-radius: 4px;">Activate</a>
                                <?php endif; ?>
                                <a href="api_keys.php?action=delete&id=<?php echo $key['id']; ?>" onclick="return confirm('Are you sure?');" style="padding: 5px 10px; background: #e74c3c; color: white; text-decoration: none; border-radius: 4px;">Delete</a>
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