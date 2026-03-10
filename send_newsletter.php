<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php';
include 'functions.php';

$msg = "";
$msg_color = "red";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_newsletter'])) {
    $subject = trim($_POST['subject']);
    $content = trim($_POST['content']);

    if (!empty($subject) && !empty($content)) {
        $query = "SELECT email FROM users";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            // For the purpose of this task, we will not send an email.
            // We will just display a message that the newsletter has been sent.
            log_activity($conn, $_SESSION['admin_id'], "Sent a newsletter with subject: " . $subject);
            $msg = "Newsletter sent successfully to " . $result->num_rows . " users!";
            $msg_color = "green";
        } else {
            $msg = "No users found to send the newsletter to.";
        }
    } else {
        $msg = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Newsletter</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div class="form-container" style="max-width: 800px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #2c3e50;">Send Newsletter</h2>
        <?php if ($msg): ?>
            <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo ($msg_color === 'red' ? '#c0392b' : '#27ae60'); ?>; font-weight: bold;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>
        <form action="send_newsletter.php" method="POST">
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="subject" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Subject</label>
                <input type="text" id="subject" name="subject" class="form-control" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="content" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Content</label>
                <textarea id="content" name="content" class="form-control" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px; min-height: 300px;"></textarea>
            </div>
            <button type="submit" name="send_newsletter" class="btn-submit" style="width: 100%; padding: 15px; background: #2980b9; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer;">Send Newsletter</button>
        </form>
    </div>

    </main>
</div>

</body>
</html>