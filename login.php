<?php
ob_start();
session_start();
include 'db.php';

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

// --- 2. GUEST REGISTRATION (Auto-Approved) ---
if (isset($_POST['request_access'])) {
    $name = trim($_POST['guest_name']); 
    $gender = $_POST['gender']; 
    $residence = $_POST['residence'];
    $nationality = $_POST['nationality']; 
    $days = $_POST['num_days']; 
    $purpose = $_POST['purpose'];
    
    // Automatically prepend +63 to the typed number
    $contact = "+63" . ltrim(trim($_POST['contact_no']), '0');

    // Auto-approve (status = 'approved')
    $status = "approved";

    // Check if access_id column exists
    $col_exists = $conn->query("SHOW COLUMNS FROM guests LIKE 'access_id'")->num_rows > 0;
    
    if ($col_exists) {
        // Generate unique Access ID
        $year = date("Y");
        $random_num = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $access_id = "LABO-{$year}-{$random_num}";
        
        $stmt = $conn->prepare("INSERT INTO guests (guest_name, gender, residence, nationality, num_days, purpose, contact_no, access_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssissss", $name, $gender, $residence, $nationality, $days, $purpose, $contact, $access_id, $status);
    } else {
        $stmt = $conn->prepare("INSERT INTO guests (guest_name, gender, residence, nationality, num_days, purpose, contact_no, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssisss", $name, $gender, $residence, $nationality, $days, $purpose, $contact, $status);
    }
    
    if ($stmt->execute()) {
        // Auto-login after registration
        $_SESSION['guest_logged_in'] = true;
        $_SESSION['guest_name'] = $name;
        header("Location: categories.php"); exit();
    } else {
        $msg = "Error submitting request.";
    }
}

// --- 3. GUEST LOGIN (Name Only!) ---
if (isset($_POST['guest_login'])) {
    $name = trim($_POST['login_name']);

    // Check the database using ONLY the guest's name
    $stmt = $conn->prepare("SELECT * FROM guests WHERE guest_name = ? ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['status'] == 'approved') {
            $_SESSION['guest_logged_in'] = true;
            $_SESSION['guest_name'] = $row['guest_name'];
            header("Location: categories.php"); exit();
        } else if ($row['status'] == 'pending') {
            $msg = "Your request is still pending admin approval.";
        } else {
            $msg = "Your request to access the catalog was declined.";
        }
    } else {
        $msg = "Record not found. Ensure your name matches exactly, or request access first.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Museo de Labo</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="login-container">
        
        <div class="login-visitor">
            <?php if ($msg): ?>
                <div class="login-message" style="border-left-color: <?php echo $msg_color; ?>;">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <h2 class="login-title">Visitor Access</h2>
            
            <div class="login-section">
                <h3 class="login-subtitle">Already Approved? Log In Here</h3>
                <form method="POST">
                    <input type="text" name="login_name" placeholder="Enter your Full Name" class="login-input-full" style="width: 100%; padding: 10px; margin-bottom: 15px; box-sizing:border-box; border: 1px solid #ddd; border-radius: 4px;" required>

                    <button type="submit" name="guest_login" class="login-btn-secondary" style="width: 100%;">Enter Museum Catalog</button>
                </form>
            </div>

            <h3 class="login-title" style="margin-top: 30px;">New Visitor? Sign Guestbook</h3>
            <form method="POST" class="login-form-grid">
                
                <input type="text" name="guest_name" placeholder="Full Name" required class="login-input-full" style="padding: 10px; border: 1px solid #ddd; border-radius:4px;">
                
                <select name="gender" required style="padding: 10px; border: 1px solid #ddd; border-radius:4px; background: white;" class="login-select">
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
                
                <select name="nationality" required style="padding: 10px; border: 1px solid #ddd; border-radius:4px; background: white;" class="login-select">
                    <option value="">Select Nationality</option>
                    <option value="Filipino">Filipino</option>
                    <option value="American">American</option>
                    <option value="Japanese">Japanese</option>
                    <option value="Chinese">Chinese</option>
                    <option value="Korean">Korean</option>
                    <option value="European">European</option>
                    <option value="Other">Other</option>
                </select>
                
                <select name="residence" required class="login-input-full" style="padding: 10px; border: 1px solid #ddd; border-radius:4px;">
                    <option value="">Select Place of Residence</option>
                    <option value="Labo, Camarines Norte">Labo, Camarines Norte</option>
                    <option value="Daet, Camarines Norte">Daet, Camarines Norte</option>
                    <option value="Other Municipality (Camarines Norte)">Other Municipality (Camarines Norte)</option>
                    <option value="Outside Camarines Norte (Philippines)">Outside Camarines Norte (Philippines)</option>
                    <option value="Outside Philippines">Outside Philippines</option>
                </select>
                
                <input type="number" name="num_days" placeholder="No. of Days Visiting" min="1" required style="padding: 10px; border: 1px solid #ddd; border-radius:4px;" class="login-input">
                
                <div class="phone-wrapper">
                    <span class="phone-prefix">+63</span>
                    <input type="tel" name="contact_no" placeholder="912 345 6789" required class="phone-input" pattern="[0-9]{10}" title="Please enter a valid 10-digit mobile number">
                </div>
                
                <input type="text" name="purpose" placeholder="Purpose of Visit (e.g., Tourism, Research)" required class="login-input-full" style="padding: 10px; border: 1px solid #ddd; border-radius:4px;">
                
                <button type="submit" name="request_access" class="login-btn">Sign Guestbook & Access Catalog</button>
            </form>
        </div>

        <div class="login-admin">
            <h3 class="admin-title">Admin Portal</h3>
            <form method="POST">
                <input type="text" name="username" placeholder="Admin Username" style="width: 100%; padding: 10px; margin-bottom: 15px; box-sizing:border-box; border: 1px solid #ddd; border-radius: 4px;" required>
                <input type="password" name="password" placeholder="Password" style="width: 100%; padding: 10px; margin-bottom: 20px; box-sizing:border-box; border: 1px solid #ddd; border-radius: 4px;" required>
                <button type="submit" name="admin_login" style="width: 100%; padding: 12px; background: #7f8c8d; color: white; border: none; font-weight: bold; border-radius: 4px; cursor: pointer; transition: 0.3s;">Login</button>
            </form>
        </div>

    </div>

</body>
</html>

