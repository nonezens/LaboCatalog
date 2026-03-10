<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'header.php';
include 'admin_sidebar.php';

$error_log_path = ini_get('error_log');
$error_log_content = file_exists($error_log_path) ? file_get_contents($error_log_path) : 'No error log file found.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Logs</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <div class="form-container" style="max-width: 800px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #2c3e50;">Error Logs</h2>
        <pre style="background: #eee; padding: 20px; border-radius: 4px; white-space: pre-wrap;"><?php echo htmlspecialchars($error_log_content); ?></pre>
    </div>

    </main>
</div>

</body>
</html>