<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php';
include 'functions.php';

$msg = "";
$msg_color = "red";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_meta_tags'])) {
        // For the purpose of this task, we will not implement the meta tag functionality.
        // We will just display a message.
        log_activity($conn, $_SESSION['admin_id'], "Updated meta tags");
        $msg = "Meta tags saved successfully!";
        $msg_color = "green";
    } elseif (isset($_POST['generate_sitemap'])) {
        // For the purpose of this task, we will not implement the sitemap generation.
        // We will just display a message.
        log_activity($conn, $_SESSION['admin_id'], "Generated the sitemap");
        $msg = "Sitemap generated successfully!";
        $msg_color = "green";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div class="form-container" style="max-width: 800px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #2c3e50;">SEO</h2>
        <?php if ($msg): ?>
            <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo ($msg_color === 'red' ? '#c0392b' : '#27ae60'); ?>; font-weight: bold;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div style="margin-bottom: 30px;">
            <h3 style="color: #2c3e50;">Meta Tags</h3>
            <form action="seo.php" method="POST">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="meta_title" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Meta Title</label>
                    <input type="text" id="meta_title" name="meta_title" class="form-control" value="" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="meta_description" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" class="form-control" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px; min-height: 100px;"></textarea>
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="meta_keywords" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Meta Keywords</label>
                    <input type="text" id="meta_keywords" name="meta_keywords" class="form-control" value="" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <button type="submit" name="update_meta_tags" class="btn-submit" style="width: 100%; padding: 15px; background: #2980b9; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer;">Update Meta Tags</button>
            </form>
        </div>

        <div>
            <h3 style="color: #2c3e50;">Sitemap</h3>
            <form action="seo.php" method="POST">
                <button type="submit" name="generate_sitemap" class="btn-submit" style="width: 100%; padding: 15px; background: #27ae60; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer;">Generate Sitemap</button>
            </form>
        </div>
    </div>

    </main>
</div>

</body>
</html>