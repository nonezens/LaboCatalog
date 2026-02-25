<?php
session_start(); // This MUST be the very first thing in your file
include 'db.php';
include 'header.php';

$error = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the user exists in the database
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Success! Set the session variable
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        
        // Redirect to the add exhibit page
        header("Location: add_exhibit.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<div style="max-width: 400px; margin: 60px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; font-family: sans-serif; background: white; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
    <h2 style="text-align: center; color: #2c3e50;">Admin Login</h2>
    
    <?php if ($error): ?>
        <p style="color: red; text-align: center;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label>Username:</label>
        <input type="text" name="username" style="width: 100%; padding: 10px; margin: 10px 0 20px 0;" required>

        <label>Password:</label>
        <input type="password" name="password" style="width: 100%; padding: 10px; margin: 10px 0 20px 0;" required>

        <button type="submit" name="login" style="width: 100%; padding: 10px; background: #2c3e50; color: white; border: none; border-radius: 4px; cursor: pointer;">Login</button>
    </form>
</div>