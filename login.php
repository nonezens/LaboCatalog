<?php
session_start();
include 'db.php';
include 'header.php';

$msg = "";
$msg_color = "red";

// --- 1. ADMIN LOGIN ---
if (isset($_POST['admin_login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_dashboard.php"); exit();
    } else {
        $msg = "Invalid admin credentials!";
    }
}

// --- 2. GUEST REGISTRATION (Request Access) ---
if (isset($_POST['request_access'])) {
    $name = $_POST['guest_name']; $gender = $_POST['gender']; $residence = $_POST['residence'];
    $nationality = $_POST['nationality']; $days = $_POST['num_days']; $purpose = $_POST['purpose'];
    $contact = $_POST['contact_no'];

    $stmt = $conn->prepare("INSERT INTO guests (guest_name, gender, residence, nationality, num_days, purpose, contact_no) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiss", $name, $gender, $residence, $nationality, $days, $purpose, $contact);
    
    if ($stmt->execute()) {
        $msg = "Request submitted! Please wait for Admin approval to log in.";
        $msg_color = "green";
    } else {
        $msg = "Error submitting request.";
    }
}

// --- 3. GUEST LOGIN (Check Approval) ---
if (isset($_POST['guest_login'])) {
    $name = $_POST['login_name'];
    $contact = $_POST['login_contact'];

    $stmt = $conn->prepare("SELECT * FROM guests WHERE guest_name = ? AND contact_no = ? ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("ss", $name, $contact);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['status'] == 'approved') {
            $_SESSION['guest_logged_in'] = true;
            $_SESSION['guest_name'] = $row['guest_name'];
            header("Location: index.php"); exit();
        } else if ($row['status'] == 'pending') {
            $msg = "Your request is still pending admin approval.";
        } else {
            $msg = "Your request to access the catalog was declined.";
        }
    } else {
        $msg = "Record not found. Ensure your name and contact match exactly, or request access first.";
    }
}
?>

<div style="max-width: 800px; margin: 40px auto; display: flex; gap: 20px; flex-wrap: wrap; font-family: 'Segoe UI', Tahoma, sans-serif;">
    
    <div style="flex: 2; min-width: 300px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        
        <?php if ($msg): ?>
            <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 20px; color: <?php echo $msg_color; ?>; font-weight: bold;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <h2 style="color: #2c3e50; margin-top: 0; border-bottom: 2px solid #eee; padding-bottom: 10px;">Visitor Access</h2>
        
        <div style="background: #f4f7f6; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
            <h3 style="margin-top:0; color: #c5a059;">Already Approved? Log In Here</h3>
            <form method="POST">
                <input type="text" name="login_name" placeholder="Full Name" style="width: 100%; padding: 10px; margin-bottom: 10px; box-sizing:border-box;" required>
                <input type="text" name="login_contact" placeholder="Contact Number" style="width: 100%; padding: 10px; margin-bottom: 10px; box-sizing:border-box;" required>
                <button type="submit" name="guest_login" style="width: 100%; padding: 12px; background: #c5a059; color: white; border: none; font-weight: bold; border-radius: 4px; cursor: pointer;">Enter Museum Catalog</button>
            </form>
        </div>

        <h3 style="color: #2c3e50;">New Visitor? Request Access</h3>
        <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <input type="text" name="guest_name" placeholder="Full Name" required style="padding: 10px; border: 1px solid #ddd; border-radius:4px; grid-column: 1 / -1;">
            
            <select name="gender" required style="padding: 10px; border: 1px solid #ddd; border-radius:4px;">
                <option value="">Select Gender</option><option value="Male">Male</option><option value="Female">Female</option><option value="Other">Other</option>
            </select>
            
            <input type="text" name="nationality" placeholder="Nationality" required style="padding: 10px; border: 1px solid #ddd; border-radius:4px;">
            <input type="text" name="residence" placeholder="Place of Residence" required style="padding: 10px; border: 1px solid #ddd; border-radius:4px; grid-column: 1 / -1;">
            
            <input type="number" name="num_days" placeholder="No. of Days Visiting" required style="padding: 10px; border: 1px solid #ddd; border-radius:4px;">
            <input type="text" name="contact_no" placeholder="Contact Number" required style="padding: 10px; border: 1px solid #ddd; border-radius:4px;">
            
            <input type="text" name="purpose" placeholder="Purpose of Visit (e.g., Tourism, Research)" required style="padding: 10px; border: 1px solid #ddd; border-radius:4px; grid-column: 1 / -1;">
            
            <button type="submit" name="request_access" style="grid-column: 1 / -1; padding: 12px; background: #2c3e50; color: white; border: none; font-weight: bold; border-radius: 4px; cursor: pointer;">Submit Request</button>
        </form>
    </div>

    <div style="flex: 1; min-width: 250px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); align-self: flex-start;">
        <h3 style="color: #95a5a6; margin-top: 0; text-align: center;">Admin Portal</h3>
        <form method="POST">
            <input type="text" name="username" placeholder="Admin Username" style="width: 100%; padding: 10px; margin-bottom: 15px; box-sizing:border-box;" required>
            <input type="password" name="password" placeholder="Password" style="width: 100%; padding: 10px; margin-bottom: 20px; box-sizing:border-box;" required>
            <button type="submit" name="admin_login" style="width: 100%; padding: 12px; background: #7f8c8d; color: white; border: none; font-weight: bold; border-radius: 4px; cursor: pointer;">Login</button>
        </form>
    </div>

</div>