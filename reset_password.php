<?php
include 'db.php';
session_start();

$msg = "";
$msg_color = "red";
$token = isset($_GET['token']) ? $_GET['token'] : '';

if (empty($token)) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $token = $_POST['token'];
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $msg = "Passwords do not match.";
    } elseif (!empty($password)) {
        $stmt = $conn->prepare("SELECT id, password_reset_expires FROM users WHERE password_reset_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if ($user['password_reset_expires'] >= date("U")) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ?, password_reset_token = NULL, password_reset_expires = NULL WHERE id = ?");
                $stmt->bind_param("si", $hashed_password, $user['id']);
                $stmt->execute();
                $msg = "Password reset successful! You can now log in with your new password.";
                $msg_color = "green";
            } else {
                $msg = "Password reset token has expired.";
            }
        } else {
            $msg = "Invalid password reset token.";
        }
    } else {
        $msg = "Please enter a new password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>

    <div class="form-container" style="max-width: 400px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #2c3e50;">Reset Password</h2>
        <?php if ($msg): ?>
            <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo ($msg_color === 'red' ? '#c0392b' : '#27ae60'); ?>; font-weight: bold;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>
        <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="password" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">New Password</label>
                <input type="password" id="password" name="password" class="form-control" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="confirm_password" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <button type="submit" name="reset_password" class="btn-submit" style="width: 100%; padding: 15px; background: #2980b9; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer;">Reset Password</button>
        </form>
        <p style="text-align: center; margin-top: 20px;">
            <a href="login.php" style="color: #3498db; text-decoration: none; font-weight: bold;">Back to Login</a>
        </p>
    </div>

</body>
</html>