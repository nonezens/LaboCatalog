<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php';
include 'functions.php';

$msg = "";
$msg_color = "red";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_theme'])) {
    $active_theme = trim($_POST['active_theme']);

    $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_name = 'active_theme'");
    $stmt->bind_param("s", $active_theme);
    
    if ($stmt->execute()) {
        log_activity($conn, $_SESSION['admin_id'], "Updated the application theme");
        $msg = "Theme updated successfully!";
        $msg_color = "green";
    } else {
        $msg = "Error updating theme: " . $stmt->error;
    }
    $stmt->close();
}

$query = "SELECT * FROM themes";
$themes_result = $conn->query($query);

$settings_query = "SELECT * FROM settings WHERE setting_name = 'active_theme'";
$settings_result = $conn->query($settings_query);
$active_theme = $settings_result->fetch_assoc()['setting_value'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Themes</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div class="form-container" style="max-width: 800px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #2c3e50;">Themes</h2>
        <?php if ($msg): ?>
            <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo ($msg_color === 'red' ? '#c0392b' : '#27ae60'); ?>; font-weight: bold;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>
        <form action="themes.php" method="POST">
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="active_theme" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Active Theme</label>
                <select name="active_theme" id="active_theme" class="form-control" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px;">
                    <?php while ($theme = $themes_result->fetch_assoc()): ?>
                        <option value="<?php echo $theme['css_file']; ?>" <?php echo ($theme['css_file'] == $active_theme) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($theme['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" name="update_theme" class="btn-submit" style="width: 100%; padding: 15px; background: #2980b9; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer;">Update Theme</button>
        </form>
    </div>

    </main>
</div>

</body>
</html>