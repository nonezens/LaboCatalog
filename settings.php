<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php';
include 'functions.php';

$msg = "";
$msg_color = "red";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_settings'])) {
    $site_title = trim($_POST['site_title']);
    $records_per_page = (int)$_POST['records_per_page'];

    $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_name = ?");
    $stmt->bind_param("ss", $site_title, 'site_title');
    $stmt->execute();

    $stmt->bind_param("is", $records_per_page, 'records_per_page');
    $stmt->execute();
    
    log_activity($conn, $_SESSION['admin_id'], "Updated application settings");
    $msg = "Settings updated successfully!";
    $msg_color = "green";
}

$query = "SELECT * FROM settings";
$result = $conn->query($query);
$settings = [];
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_name']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div class="form-container" style="max-width: 800px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #2c3e50;">Settings</h2>
        <?php if ($msg): ?>
            <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo ($msg_color === 'red' ? '#c0392b' : '#27ae60'); ?>; font-weight: bold;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>
        <form action="settings.php" method="POST">
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="site_title" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Site Title</label>
                <input type="text" id="site_title" name="site_title" class="form-control" value="<?php echo htmlspecialchars($settings['site_title']); ?>" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="records_per_page" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Records Per Page</label>
                <input type="number" id="records_per_page" name="records_per_page" class="form-control" value="<?php echo (int)$settings['records_per_page']; ?>" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <button type="submit" name="update_settings" class="btn-submit" style="width: 100%; padding: 15px; background: #2980b9; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer;">Update Settings</button>
        </form>
    </div>

    </main>
</div>

</body>
</html>