<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db.php'; 
// ... your PHP logic ...
?>
<!DOCTYPE html>
<html lang="en">
<head>
    </head>
<body>

    <?php include 'header.php'; ?>

    <main id="main-content">
        
        <div class="hero">
            <h1>Welcome to Museo de Labo</h1>
            </div>

    </main> <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="js/index.js"></script>

</body>
</html>