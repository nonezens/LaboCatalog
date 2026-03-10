<?php
ob_start();
session_start();
include 'db.php';
include 'functions.php';

$msg = "";
$msg_color = "red";

// --- 1. ADMIN LOGIN ---
if (isset($_POST['admin_login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $row['id'];
            log_activity($conn, $row['id'], "Admin logged in");
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $msg = "Invalid admin credentials!";
        }
    } else {
        $msg = "Invalid admin credentials!";
    }
}

// --- 2. GUEST REGISTRATION (Request Access) ---
if (isset($_POST['request_access'])) {
    $name = trim($_POST['guest_name']);
    $gender = $_POST['gender'];
    $residence = $_POST['residence'];
    $nationality = $_POST['nationality'];
    $days = $_POST['num_days'];
    $purpose = $_POST['purpose'];
    
    // Automatically prepend +63 to the typed number
    $contact = "+63" . ltrim(trim($_POST['contact_no']), '0');

    $stmt = $conn->prepare("INSERT INTO guests (guest_name, gender, residence, nationality, num_days, purpose, contact_no) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiss", $name, $gender, $residence, $nationality, $days, $purpose, $contact);
    
    if ($stmt->execute()) {
        $guest_id = $stmt->insert_id;
        log_activity($conn, $guest_id, "Guest requested access");
        $msg = "Request submitted! Please wait for Admin approval to log in.";
        $msg_color = "green";
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
            $_SESSION['guest_id'] = $row['id'];
            log_activity($conn, $row['id'], "Guest logged in");
            header("Location: categories.php");
            exit();
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

<?php include 'header.php'; ?>

<div style="max-width: 800px; margin: 40px auto; display: flex; gap: 20px; flex-wrap: wrap; font-family: 'Segoe UI', Tahoma, sans-serif;">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
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
                <input type="text" name="login_name" placeholder="Enter your Full Name" style="width: 100%; padding: 10px; margin-bottom: 15px; box-sizing:border-box; border: 1px solid #ddd; border-radius: 4px;" required>

                <button type="submit" name="guest_login" style="width: 100%; padding: 12px; background: #c5a059; color: white; border: none; font-weight: bold; border-radius: 4px; cursor: pointer; transition: 0.3s;">Enter Museum Catalog</button>
            </form>
        </div>

        <h3 style="color: #2c3e50;">New Visitor? Request Access</h3>
        <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            
            <input type="text" name="guest_name" placeholder="Full Name" required style="padding: 10px; border: 1px solid #ddd; border-radius:4px; grid-column: 1 / -1;">
            
            <select name="gender" required style="padding: 10px; border: 1px solid #ddd; border-radius:4px; background: white;">
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            
            <select name="nationality" required style="padding: 10px; border: 1px solid #ddd; border-radius:4px; background: white;">
                <option value="">Select Nationality</option>
                <option value="Filipino">Filipino</option>
                <option value="American">American</option>
                <option value="Japanese">Japanese</option>
                <option value="Chinese">Chinese</option>
                <option value="Korean">Korean</option>
                <option value="European">European</option>
                <option value="Other">Other</option>
            </select>
            
            <select name="residence" required style="padding: 10px; border: 1px solid #ddd; border-radius:4px; background: white; grid-column: 1 / -1;">
                <option value="">Select Place of Residence</option>
                <option value="Labo, Camarines Norte">Labo, Camarines Norte</option>
                <option value="Daet, Camarines Norte">Daet, Camarines Norte</option>
                <option value="Other Municipality (Camarines Norte)">Other Municipality (Camarines Norte)</option>
                <option value="Outside Camarines Norte (Philippines)">Outside Camarines Norte (Philippines)</option>
                <option value="Outside Philippines">Outside Philippines</option>
            </select>
            
            <input type="number" name="num_days" placeholder="No. of Days Visiting" min="1" required style="padding: 10px; border: 1px solid #ddd; border-radius:4px;">
            
            <div style="display: flex; border: 1px solid #ddd; border-radius:4px; overflow: hidden; background: white;">
                <span style="padding: 10px 15px; background: #eee; color: #555; border-right: 1px solid #ddd; font-weight: bold;">+63</span>
                <input type="tel" name="contact_no" placeholder="912 345 6789" required style="padding: 10px; border: none; width: 100%; outline: none;" pattern="[0-9]{10}" title="Please enter a valid 10-digit mobile number">
            </div>
            
            <input type="text" name="purpose" placeholder="Purpose of Visit (e.g., Tourism, Research)" required style="padding: 10px; border: 1px solid #ddd; border-radius:4px; grid-column: 1 / -1;">
            
            <button type="submit" name="request_access" style="grid-column: 1 / -1; padding: 12px; background: #2c3e50; color: white; border: none; font-weight: bold; border-radius: 4px; cursor: pointer; transition: 0.3s;">Submit Request</button>
        </form>
    </div>

        <p style="text-align: center; margin-top: 20px;">
            Don't have an account? <a href="register.php" style="color: #3498db; text-decoration: none; font-weight: bold;">Register here</a>
        </p>
    </div>

    <div style="flex: 1; min-width: 250px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); align-self: flex-start;">
        <h3 style="color: #95a5a6; margin-top: 0; text-align: center;">Admin Portal</h3>
        <form method="POST">
            <input type="text" name="username" placeholder="Admin Username" style="width: 100%; padding: 10px; margin-bottom: 15px; box-sizing:border-box; border: 1px solid #ddd; border-radius: 4px;" required>
            <input type="password" name="password" placeholder="Password" style="width: 100%; padding: 10px; margin-bottom: 20px; box-sizing:border-box; border: 1px solid #ddd; border-radius: 4px;" required>
            <button type="submit" name="admin_login" style="width: 100%; padding: 12px; background: #7f8c8d; color: white; border: none; font-weight: bold; border-radius: 4px; cursor: pointer; transition: 0.3s;">Login</button>
        </form>
        <p style="text-align: center; margin-top: 10px;">
            <a href="forgot_password.php" style="color: #95a5a6; font-size: 0.9em; text-decoration: none;">Forgot Password?</a>
        </p>
    </div>

</div>