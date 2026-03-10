<?php
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
        header("Location: admin_dashboard.php"); // Admin goes to Dashboard
        exit();
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
        $msg = "Request submitted! Please wait for Admin approval to log in.";
        $msg_color = "green";
    } else {
        $msg = "Error submitting request.";
    }
}

// --- 3. GUEST LOGIN (Name Only!) ---
if (isset($_POST['guest_login'])) {
    $name = trim($_POST['login_name']);

    $stmt = $conn->prepare("SELECT * FROM guests WHERE guest_name = ? ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['status'] == 'approved') {
            $_SESSION['guest_logged_in'] = true;
            $_SESSION['guest_name'] = $row['guest_name'];
            
            // FIXED: Guest now goes to the homepage!
            header("Location: index.php"); 
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Access Request | Museo de Labo</title>
    <style>
        .login-wrapper {
            max-width: 900px; 
            margin: 40px auto; 
            display: flex; 
            gap: 20px; 
            font-family: 'Segoe UI', Tahoma, sans-serif;
            padding: 0 15px;
            box-sizing: border-box;
        }
        
        .box-panel {
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            box-sizing: border-box;
        }

        .guest-section { flex: 2; }
        .admin-section { flex: 1; align-self: flex-start; }

        .form-input {
            width: 100%; 
            padding: 12px; 
            margin-bottom: 15px; 
            box-sizing: border-box; 
            border: 1px solid #ddd; 
            border-radius: 4px;
            font-family: inherit;
        }

        .form-grid {
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 15px;
        }

        .full-width { grid-column: 1 / -1; }

        .phone-group {
            display: flex; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            overflow: hidden; 
            background: white;
        }
        .phone-prefix {
            padding: 12px 15px; 
            background: #eee; 
            color: #555; 
            border-right: 1px solid #ddd; 
            font-weight: bold;
        }
        .phone-input {
            padding: 12px; 
            border: none; 
            width: 100%; 
            outline: none;
            box-sizing: border-box;
        }

        .btn-submit {
            width: 100%; 
            padding: 12px; 
            color: white; 
            border: none; 
            font-weight: bold; 
            border-radius: 4px; 
            cursor: pointer; 
            transition: 0.3s;
        }
        .btn-gold { background: #c5a059; }
        .btn-gold:hover { background: #b48a3d; }
        .btn-dark { background: #2c3e50; }
        .btn-dark:hover { background: #1a252f; }
        .btn-gray { background: #7f8c8d; }
        .btn-gray:hover { background: #636e72; }

        /* --- RESPONSIVE MEDIA QUERIES --- */
        /* Tablets and small laptops */
        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
            }
            .guest-section, .admin-section {
                width: 100%;
            }
        }

        /* Cellphones */
        @media (max-width: 480px) {
            .form-grid {
                grid-template-columns: 1fr; /* Stacks the two-column form into one column */
            }
            .full-width {
                grid-column: 1; /* Reset full-width to single column */
            }
            .box-panel {
                padding: 20px; /* Slightly less padding on small screens to save space */
            }
        }
    </style>
</head>
<body style="background-color: #f9f9f9; margin: 0;">

    <div class="login-wrapper">
        
        <div class="box-panel guest-section">
            
            <?php if ($msg): ?>
                <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 20px; color: <?php echo $msg_color; ?>; font-weight: bold;">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <h2 style="color: #2c3e50; margin-top: 0; border-bottom: 2px solid #eee; padding-bottom: 10px;">Visitor Access</h2>
            
            <div style="background: #f4f7f6; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                <h3 style="margin-top:0; color: #c5a059;">Already Approved? Log In Here</h3>
                <form method="POST">
                    <input type="text" name="login_name" class="form-input" placeholder="Enter your Full Name" required>
                    <button type="submit" name="guest_login" class="btn-submit btn-gold">Enter Museum Catalog</button>
                </form>
            </div>

            <h3 style="color: #2c3e50;">New Visitor? Request Access</h3>
            <form method="POST" class="form-grid">
                
                <input type="text" name="guest_name" class="form-input full-width" placeholder="Full Name" style="margin-bottom: 0;" required>
                
                <select name="gender" class="form-input" style="margin-bottom: 0; background: white;" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
                
                <select name="nationality" class="form-input" style="margin-bottom: 0; background: white;" required>
                    <option value="">Select Nationality</option>
                    <option value="Filipino">Filipino</option>
                    <option value="American">American</option>
                    <option value="Japanese">Japanese</option>
                    <option value="Chinese">Chinese</option>
                    <option value="Korean">Korean</option>
                    <option value="European">European</option>
                    <option value="Other">Other</option>
                </select>
                
                <select name="residence" class="form-input full-width" style="margin-bottom: 0; background: white;" required>
                    <option value="">Select Place of Residence</option>
                    <option value="Labo, Camarines Norte">Labo, Camarines Norte</option>
                    <option value="Daet, Camarines Norte">Daet, Camarines Norte</option>
                    <option value="Other Municipality (Camarines Norte)">Other Municipality (Camarines Norte)</option>
                    <option value="Outside Camarines Norte (Philippines)">Outside Camarines Norte (Philippines)</option>
                    <option value="Outside Philippines">Outside Philippines</option>
                </select>
                
                <input type="number" name="num_days" class="form-input" placeholder="No. of Days Visiting" min="1" style="margin-bottom: 0;" required>
                
                <div class="phone-group">
                    <span class="phone-prefix">+63</span>
                    <input type="tel" name="contact_no" class="phone-input" placeholder="912 345 6789" required pattern="[0-9]{10}" title="Please enter a valid 10-digit mobile number">
                </div>
                
                <input type="text" name="purpose" class="form-input full-width" placeholder="Purpose of Visit (e.g., Tourism, Research)" style="margin-bottom: 0;" required>
                
                <button type="submit" name="request_access" class="btn-submit btn-dark full-width">Submit Request</button>
            </form>
        </div>

        <div class="box-panel admin-section">
            <h3 style="color: #95a5a6; margin-top: 0; text-align: center;">Admin Portal</h3>
            <form method="POST">
                <input type="text" name="username" class="form-input" placeholder="Admin Username" required>
                <input type="password" name="password" class="form-input" placeholder="Password" required>
                <button type="submit" name="admin_login" class="btn-submit btn-gray">Login</button>
            </form>
        </div>

    </div>
</body>
</html>