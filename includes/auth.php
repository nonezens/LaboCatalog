<?php
// Common admin auth & DB loader
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit();
}
?>

