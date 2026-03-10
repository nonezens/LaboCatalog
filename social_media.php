<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php';
include 'functions.php';

$msg = "";
$msg_color = "red";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_social_media'])) {
        $name = trim($_POST['name']);
        $url = trim($_POST['url']);
        $icon = trim($_POST['icon']);

        if (!empty($name) && !empty($url) && !empty($icon)) {
            $stmt = $conn->prepare("INSERT INTO social_media (name, url, icon) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $url, $icon);
            if ($stmt->execute()) {
                log_activity($conn, $_SESSION['admin_id'], "Added social media link: " . $name);
                $msg = "Social media link added successfully!";
                $msg_color = "green";
            } else {
                $msg = "Error adding social media link: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $msg = "Please fill in all fields.";
        }
    } elseif (isset($_POST['update_social_media'])) {
        $id = (int)$_POST['id'];
        $name = trim($_POST['name']);
        $url = trim($_POST['url']);
        $icon = trim($_POST['icon']);

        if (!empty($name) && !empty($url) && !empty($icon)) {
            $stmt = $conn->prepare("UPDATE social_media SET name = ?, url = ?, icon = ? WHERE id = ?");
            $stmt->bind_param("sssi", $name, $url, $icon, $id);
            if ($stmt->execute()) {
                log_activity($conn, $_SESSION['admin_id'], "Updated social media link: " . $name);
                $msg = "Social media link updated successfully!";
                $msg_color = "green";
            } else {
                $msg = "Error updating social media link: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $msg = "Please fill in all fields.";
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("SELECT name FROM social_media WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $social_media = $result->fetch_assoc();

    if ($social_media) {
        $stmt = $conn->prepare("DELETE FROM social_media WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            log_activity($conn, $_SESSION['admin_id'], "Deleted social media link: " . $social_media['name']);
            header("Location: social_media.php?msg=deleted");
            exit();
        }
    }
}

$query = "SELECT * FROM social_media ORDER BY name ASC";
$result = $conn->query($query);
$social_media_links = [];
while ($row = $result->fetch_assoc()) {
    $social_media_links[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Social Media</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div class="form-container" style="max-width: 800px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #2c3e50;">Manage Social Media</h2>
        <?php if ($msg): ?>
            <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo ($msg_color === 'red' ? '#c0392b' : '#27ae60'); ?>; font-weight: bold;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div style="margin-bottom: 30px;">
            <h3 style="color: #2c3e50;">Add New Social Media Link</h3>
            <form action="social_media.php" method="POST">
                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <input type="text" name="name" placeholder="Name" required style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <input type="text" name="url" placeholder="URL" required style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <input type="text" name="icon" placeholder="Icon (e.g., fab fa-facebook)" required style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <button type="submit" name="add_social_media" style="padding: 10px 20px; background: #2980b9; color: white; border: none; border-radius: 4px;">Add Social Media Link</button>
            </form>
        </div>

        <h3 style="color: #2c3e50;">Existing Social Media Links</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Name</th><th>URL</th><th>Icon</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($social_media_links as $link): ?>
                        <tr>
                            <form action="social_media.php" method="POST">
                                <td><?php echo $link['id']; ?></td>
                                <td><input type="text" name="name" value="<?php echo htmlspecialchars($link['name']); ?>" style="padding: 5px; border: 1px solid #ddd; border-radius: 4px;"></td>
                                <td><input type="text" name="url" value="<?php echo htmlspecialchars($link['url']); ?>" style="padding: 5px; border: 1px solid #ddd; border-radius: 4px;"></td>
                                <td><input type="text" name="icon" value="<?php echo htmlspecialchars($link['icon']); ?>" style="padding: 5px; border: 1px solid #ddd; border-radius: 4px;"></td>
                                <td>
                                    <input type="hidden" name="id" value="<?php echo $link['id']; ?>">
                                    <button type="submit" name="update_social_media" style="padding: 5px 10px; background: #f39c12; color: white; border: none; border-radius: 4px;">Update</button>
                                    <a href="social_media.php?delete=<?php echo $link['id']; ?>" onclick="return confirm('Are you sure?');" style="padding: 5px 10px; background: #e74c3c; color: white; text-decoration: none; border-radius: 4px;">Delete</a>
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