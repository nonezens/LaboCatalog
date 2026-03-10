<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php'; 
include 'functions.php';

$msg = "";
$msg_color = "red";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if (!empty($username) && !empty($password) && !empty($role)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $role);
        
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            log_activity($conn, $_SESSION['admin_id'], "Added user: " . $username);
            $msg = "User added successfully!";
            $msg_color = "green";
        } else {
            $msg = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $msg = "Please fill in all fields.";
    }
}

$query = "SELECT * FROM users";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div class="form-container" style="max-width: 600px; margin: 0 auto 30px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #2c3e50;">Add New User</h2>
        <?php if ($msg): ?>
            <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo ($msg_color === 'red' ? '#c0392b' : '#27ae60'); ?>; font-weight: bold;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>
        <form action="manage_users.php" method="POST" class="form-grid">
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="username" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Username</label>
                <input type="text" id="username" name="username" class="form-control" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="password" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Password</label>
                <input type="password" id="password" name="password" class="form-control" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="role" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Role</label>
                <select name="role" id="role" class="form-control" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" name="add_user" class="btn-submit" style="width: 100%; padding: 15px; background: #2980b9; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer;">Add User</button>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Username</th><th>Role</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if($result && $result->num_rows > 0): while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['role']); ?></td>
                <td>
                    <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="action-btn btn-edit">✎ Edit</a>
                    <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete this user?');">🗑️ Delete</a>
                </td>
            </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="4" style="text-align: center; padding: 20px;">No users found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    </main>
</div>

</body>
</html>