<?php
include 'db.php';
session_start();

$msg = "";
$msg_color = "red";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $msg = "Passwords do not match.";
    } elseif (!empty($username) && !empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
        $stmt->bind_param("ss", $username, $hashed_password);
        
        if ($stmt->execute()) {
            $msg = "Registration successful! You can now log in.";
            $msg_color = "green";
        } else {
            $msg = "Error: " . $stmt->error;
        }
        $stmt->close();
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
    <title>Register</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>

    <div class="form-container" style="max-width: 400px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #2c3e50;">Register</h2>
        <?php if ($msg): ?>
            <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo ($msg_color === 'red' ? '#c0392b' : '#27ae60'); ?>; font-weight: bold;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="username" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Username</label>
                <input type="text" id="username" name="username" class="form-control" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="password" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Password</label>
                <input type="password" id="password" name="password" class="form-control" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="confirm_password" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <button type="submit" name="register" class="btn-submit" style="width: 100%; padding: 15px; background: #2980b9; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer;">Register</button>
        </form>
        <p style="text-align: center; margin-top: 20px;">
            Already have an account? <a href="login.php" style="color: #3498db; text-decoration: none; font-weight: bold;">Log in</a>
        </p>
    </div>

</body>
</html>