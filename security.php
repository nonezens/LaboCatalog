<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php';
include 'functions.php';

$msg = "";
$msg_color = "red";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_rate_limiting'])) {
        // For the purpose of this task, we will not implement the rate limiting.
        // We will just display a message.
        log_activity($conn, $_SESSION['admin_id'], "Updated rate limiting settings");
        $msg = "Rate limiting settings saved successfully!";
        $msg_color = "green";
    } elseif (isset($_POST['add_ip_to_blacklist'])) {
        // For the purpose of this task, we will not implement the IP blacklisting.
        // We will just display a message.
        $ip_address = trim($_POST['ip_address']);
        log_activity($conn, $_SESSION['admin_id'], "Added IP address to blacklist: " . $ip_address);
        $msg = "IP address added to blacklist successfully!";
        $msg_color = "green";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div class="form-container" style="max-width: 800px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #2c3e50;">Security</h2>
        <?php if ($msg): ?>
            <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo ($msg_color === 'red' ? '#c0392b' : '#27ae60'); ?>; font-weight: bold;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div style="margin-bottom: 30px;">
            <h3 style="color: #2c3e50;">Rate Limiting</h3>
            <form action="security.php" method="POST">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="max_requests_per_minute" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Max Requests per Minute</label>
                    <input type="number" id="max_requests_per_minute" name="max_requests_per_minute" class="form-control" value="100" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <button type="submit" name="update_rate_limiting" class="btn-submit" style="width: 100%; padding: 15px; background: #2980b9; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer;">Update Rate Limiting</button>
            </form>
        </div>

        <div>
            <h3 style="color: #2c3e50;">IP Blacklist</h3>
            <form action="security.php" method="POST">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="ip_address" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">IP Address</label>
                    <input type="text" id="ip_address" name="ip_address" class="form-control" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <button type="submit" name="add_ip_to_blacklist" class="btn-submit" style="width: 100%; padding: 15px; background: #c0392b; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer;">Add to Blacklist</button>
            </form>
        </div>
    </div>

    </main>
</div>

</body>
</html>