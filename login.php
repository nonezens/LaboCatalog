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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access the Collection | Museo de Labo</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

<div class="login-container">
    
    <div class="login-section">
        
        <?php if ($msg): ?>
            <div class="alert-box <?php echo ($msg_color === 'green') ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <h2>Visitor Access</h2>

        <!-- Existing Visitor Login -->
        <div class="login-form">
            <h3>Already Approved?</h3>
            <p class="login-subtitle">Sign in with your registered name and contact</p>
            <form method="POST">
                <div class="form-group" style="margin-bottom: 12px;">
                    <input type="text" name="login_name" class="form-input" placeholder="Full Name" required>
                </div>
                <div class="form-group" style="margin-bottom: 16px;">
                    <input type="text" name="login_contact" class="form-input" placeholder="Contact Number" required>
                </div>
                <button type="submit" name="guest_login" class="btn-primary">Enter Museum Catalog</button>
            </form>
        </div>

        <div class="divider"></div>

        <!-- New Visitor Request -->
        <div class="login-form">
            <h3>New Visitor?</h3>
            <p class="login-subtitle">Request access to browse our digital collection</p>
            <form method="POST">
                <div class="login-form-row full">
                    <div class="form-group">
                        <input type="text" name="guest_name" class="form-input" placeholder="Full Name" required>
                    </div>
                </div>

                <div class="login-form-row">
                    <div class="form-group">
                        <select name="gender" class="form-input" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" name="nationality" class="form-input" placeholder="Nationality" required>
                    </div>
                </div>

                <div class="login-form-row full">
                    <div class="form-group">
                        <input type="text" name="residence" class="form-input" placeholder="Place of Residence" required>
                    </div>
                </div>

                <div class="login-form-row">
                    <div class="form-group">
                        <input type="number" name="num_days" class="form-input" placeholder="No. of Days Visiting" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="contact_no" class="form-input" placeholder="Contact Number" required>
                    </div>
                </div>

                <div class="login-form-row full">
                    <div class="form-group">
                        <input type="text" name="purpose" class="form-input" placeholder="Purpose (e.g., Tourism, Research)" required>
                    </div>
                </div>
                
                <button type="submit" name="request_access" class="btn-primary">Submit Request</button>
            </form>
        </div>
    </div>

    <div class="admin-panel">
        <h3>Admin Portal</h3>
        <p style="text-align: center; color: #7f8c8d; font-size: 0.95rem; margin-bottom: 20px;">Authorized museum staff only</p>
        
        <form method="POST">
            <div class="form-group" style="margin-bottom: 12px;">
                <input type="text" name="username" class="form-input" placeholder="Admin Username" required>
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <input type="password" name="password" class="form-input" placeholder="Password" required>
            </div>
            <button type="submit" name="admin_login" class="btn-primary" style="background: var(--primary);">Login to Dashboard</button>
        </form>
    </div>

</div>
<footer>
    <p>&copy; 2026 Museo De Labo Catalog. Preserving Our Cultural Heritage for Future Generations.</p>
</footer>

</body>
</html>