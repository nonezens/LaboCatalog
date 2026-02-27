<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php';

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];
    
    $new_status = 'pending';
    if ($action == 'approve') $new_status = 'approved';
    if ($action == 'reject') $new_status = 'rejected';

    $stmt = $conn->prepare("UPDATE guests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $id);
    $stmt->execute();
}
header("Location: admin_dashboard.php");
exit();
?>