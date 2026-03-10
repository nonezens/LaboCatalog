<?php
include 'db.php';
session_start();

$msg = "";
$msg_color = "red";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['forgot_password'])) {
    $email = trim($_POST['email']);

    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $token = bin2hex(random_bytes(50));
            $expires = date("U") + 1800; // 30 minutes

            $stmt = $conn->prepare("UPDATE users SET password_reset_token = ?, password_reset_expires = ? WHERE email = ?");
            $stmt->bind_param("sss", $token, $expires, $email);
            $stmt->execute();
            
            // For the purpose of this task, we will not send an email.
            // We will just display the link.
            $msg = "Password reset link: <a href='reset_password.php?token=" . $token . "'>reset_password.php?token=" . $token . "</a>";
            $msg_color = "green";
        } else {
            $msg = "No user with that email address exists.";
        }
    } else {
        $msg = "Please enter a valid email address.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>

    <div class="form-container" style="max-width: 400px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #2c3e50;">Forgot Password</h2>
        <?php if ($msg): ?>
            <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo ($msg_color === 'red' ? '#c0392b' : '#27ae60'); ?>; font-weight: bold;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>
        <form action="forgot_password.php" method="POST">
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="email" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <button type="submit" name="forgot_password" class="btn-submit" style="width: 100%; padding: 15px; background: #2980b9; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer;">Send Password Reset Link</button>
        </form>
        <p style="text-align: center; margin-top: 20px;">
            <a href="login.php" style="color: #3498db; text-decoration: none; font-weight: bold;">Back to Login</a>
        </p>
    </div>

</body>
</html>