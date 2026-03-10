<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php';
include 'functions.php';

$msg = "";
$msg_color = "red";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['restore'])) {
    if (isset($_FILES['backup_file']) && $_FILES['backup_file']['error'] == 0) {
        $db_host = 'localhost';
        $db_user = 'root';
        $db_pass = '';
        $db_name = 'museum_db';
        $backup_file = $_FILES['backup_file']['tmp_name'];

        $command = "mysql --host={$db_host} --user={$db_user} --password={$db_pass} {$db_name} < {$backup_file}";

        system($command, $return_var);

        if ($return_var === 0) {
            log_activity($conn, $_SESSION['admin_id'], "Restored the database from a backup");
            $msg = "Database restored successfully!";
            $msg_color = "green";
        } else {
            $msg = "Error restoring database.";
        }
    } else {
        $msg = "Please choose a file to restore.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restore Database</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div class="form-container" style="max-width: 600px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #2c3e50;">Restore Database</h2>
        <?php if ($msg): ?>
            <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo ($msg_color === 'red' ? '#c0392b' : '#27ae60'); ?>; font-weight: bold;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>
        <form action="restore.php" method="POST" enctype="multipart/form-data">
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="backup_file" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">SQL Backup File</label>
                <input type="file" id="backup_file" name="backup_file" class="form-control" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <button type="submit" name="restore" class="btn-submit" style="width: 100%; padding: 15px; background: #e74c3c; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer;">Restore Database</button>
        </form>
    </div>

    </main>
</div>

</body>
</html>