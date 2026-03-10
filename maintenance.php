<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php';
include 'functions.php';

$msg = "";
$msg_color = "red";
$maintenance_flag_file = 'maintenance.flag';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['enable_maintenance_mode'])) {
        file_put_contents($maintenance_flag_file, 'enabled');
        log_activity($conn, $_SESSION['admin_id'], "Enabled maintenance mode");
        $msg = "Maintenance mode enabled!";
        $msg_color = "green";
    } elseif (isset($_POST['disable_maintenance_mode'])) {
        if (file_exists($maintenance_flag_file)) {
            unlink($maintenance_flag_file);
        }
        log_activity($conn, $_SESSION['admin_id'], "Disabled maintenance mode");
        $msg = "Maintenance mode disabled!";
        $msg_color = "green";
    }
}

$is_maintenance_mode = file_exists($maintenance_flag_file);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div class="form-container" style="max-width: 800px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #2c3e50;">Maintenance Mode</h2>
        <?php if ($msg): ?>
            <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo ($msg_color === 'red' ? '#c0392b' : '#27ae60'); ?>; font-weight: bold;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>
        <form action="maintenance.php" method="POST">
            <?php if ($is_maintenance_mode): ?>
                <p>Maintenance mode is currently <strong>enabled</strong>.</p>
                <button type="submit" name="disable_maintenance_mode" class="btn-submit" style="width: 100%; padding: 15px; background: #27ae60; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer;">Disable Maintenance Mode</button>
            <?php else: ?>
                <p>Maintenance mode is currently <strong>disabled</strong>.</p>
                <button type="submit" name="enable_maintenance_mode" class="btn-submit" style="width: 100%; padding: 15px; background: #e74c3c; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer;">Enable Maintenance Mode</button>
            <?php endif; ?>
        </form>
    </div>

    </main>
</div>

</body>
</html>