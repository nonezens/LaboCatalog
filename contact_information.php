<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php';
include 'functions.php';

$msg = "";
$msg_color = "red";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_contact_information'])) {
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);

    if (!empty($address) && !empty($phone) && !empty($email)) {
        $stmt = $conn->prepare("UPDATE contact_information SET address = ?, phone = ?, email = ? WHERE id = 1");
        $stmt->bind_param("sss", $address, $phone, $email);
        
        if ($stmt->execute()) {
            log_activity($conn, $_SESSION['admin_id'], "Updated contact information");
            $msg = "Contact information updated successfully!";
            $msg_color = "green";
        } else {
            $msg = "Error updating contact information: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $msg = "Please fill in all fields.";
    }
}

$query = "SELECT * FROM contact_information WHERE id = 1";
$result = $conn->query($query);
$contact_information = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Contact Information</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div class="form-container" style="max-width: 800px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #2c3e50;">Edit Contact Information</h2>
        <?php if ($msg): ?>
            <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo ($msg_color === 'red' ? '#c0392b' : '#27ae60'); ?>; font-weight: bold;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>
        <form action="contact_information.php" method="POST">
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="address" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Address</label>
                <input type="text" id="address" name="address" class="form-control" value="<?php echo htmlspecialchars($contact_information['address']); ?>" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="phone" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Phone</label>
                <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($contact_information['phone']); ?>" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="email" style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($contact_information['email']); ?>" required style="width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <button type="submit" name="update_contact_information" class="btn-submit" style="width: 100%; padding: 15px; background: #2980b9; color: white; border: none; font-weight: bold; border-radius: 4px; font-size: 1.1rem; cursor: pointer;">Update Contact Information</button>
        </form>
    </div>

    </main>
</div>

</body>
</html>