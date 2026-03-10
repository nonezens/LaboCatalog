<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php';
include 'functions.php';

$msg = "";
$msg_color = "red";
$user = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user'])) {
    $id = $_POST['id'];
    $username = trim($_POST['username']);
    $role = trim($_POST['role']);

    if (!empty($username) && !empty($role)) {
        $stmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
        $stmt->bind_param("ssi", $username, $role, $id);
        
        if ($stmt->execute()) {
            log_activity($conn, $_SESSION['admin_id'], "Edited user: " . $username);
            $msg = "User updated successfully!";
            $msg_color = "green";
            // Refresh user data
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
        } else {
            $msg = "Error updating user: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $msg = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div class="form-container">
        <?php if ($msg): ?>
            <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo ($msg_color === 'red' ? '#c0392b' : '#27ae60'); ?>; font-weight: bold;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <?php if ($user): ?>
            <form action="edit_user.php?id=<?php echo $user['id']; ?>" method="POST" class="form-grid">
                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-control" required>
                        <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                    </select>
                </div>
                <div class="full-width">
                    <button type="submit" name="edit_user" class="btn-submit">Update User</button>
                </div>
            </form>
        <?php else: ?>
            <p>User not found.</p>
        <?php endif; ?>
    </div>

    </main>
</div>

</body>
</html>