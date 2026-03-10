<?php
ob_start();
session_start();
include 'db.php';

$msg = "";
$msg_color = "red";

// --- ADMIN LOGIN ---
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

// --- GUEST REGISTRATION (Sign Guestbook) ---
if (isset($_POST['sign_guestbook'])) {
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
        // Generate unique Access ID with timestamp for uniqueness
        $year = date("Y");
        $timestamp = substr(time(), -6);
        $random_num = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $access_id = "LABO-{$year}-{$timestamp}-{$random_num}";
        
        $stmt = $conn->prepare("INSERT INTO guests (guest_name, gender, residence, nationality, num_days, purpose, contact_no, access_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssissss", $name, $gender, $residence, $nationality, $days, $purpose, $contact, $access_id, $status);
    } else {
        $stmt = $conn->prepare("INSERT INTO guests (guest_name, gender, residence, nationality, num_days, purpose, contact_no, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssisss", $name, $gender, $residence, $nationality, $days, $purpose, $contact, $status);
    }
    
    if ($stmt->execute()) {
        // Auto-login and redirect directly to categories
        $_SESSION['guest_logged_in'] = true;
        $_SESSION['guest_name'] = $name;
        header("Location: categories.php"); exit();
    } else {
        $msg = "Error signing guestbook.";
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
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            padding-top: 80px; /* Space for fixed header */
        }
        
        .login-wrapper {
            display: flex;
            gap: 40px;
            max-width: 1200px;
            width: 100%;
            justify-content: center;
            align-items: flex-start;
            flex-wrap: wrap;
        }
        
        .guestbook-container {
            flex: 1;
            min-width: 400px;
            max-width: 600px;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .admin-container {
            width: 280px;
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .guestbook-title {
            text-align: center;
            color: #2c3e50;
            margin-top: 0;
            font-size: 1.8em;
        }
        
        .guestbook-subtitle {
            text-align: center;
            color: #c5a059;
            margin-bottom: 30px;
            font-size: 1.1em;
        }
        
        .admin-title {
            text-align: center;
            color: #7f8c8d;
            margin-top: 0;
            font-size: 1.2em;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .form-full {
            grid-column: 1 / -1;
        }
        
        .form-input, .form-select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 14px;
        }
        
        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: #c5a059;
        }
        
        .phone-wrapper {
            display: flex;
            border: 1px solid #ddd;
            border-radius: 6px;
            overflow: hidden;
        }
        
        .phone-prefix {
            padding: 12px 15px;
            background: #f5f5f5;
            border-right: 1px solid #ddd;
            font-weight: bold;
            color: #666;
        }
        
        .phone-input {
            flex: 1;
            padding: 12px;
            border: none;
            outline: none;
        }
        
        .submit-btn {
            grid-column: 1 / -1;
            padding: 15px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .submit-btn:hover {
            background: #1a252f;
            transform: translateY(-2px);
        }
        
        .admin-btn {
            width: 100%;
            padding: 12px;
            background: #7f8c8d;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .admin-btn:hover {
            background: #6c7a7d;
        }
        
        .message {
            background: #fee;
            border-left: 4px solid red;
            padding: 15px;
            margin-bottom: 20px;
            color: red;
            border-radius: 4px;
        }
        
        @media (max-width: 900px) {
            .login-wrapper {
                flex-direction: column;
                align-items: center;
            }
            .guestbook-container {
                min-width: 100%;
            }
            .admin-container {
                width: 100%;
                max-width: 400px;
            }
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="login-wrapper">
        
        <!-- Guestbook Form - Centered and Main -->
        <div class="guestbook-container">
            <?php if ($msg): ?>
                <div class="message"><?php echo $msg; ?></div>
            <?php endif; ?>

            <h2 class="guestbook-title">📖 Museo de Labo</h2>
            <p class="guestbook-subtitle">Sign Guestbook to Access the Museum</p>
            
            <form method="POST" class="form-grid">
                <div class="form-full">
                    <input type="text" name="guest_name" placeholder="Full Name" required class="form-input">
                </div>
                
                <select name="gender" required class="form-select">
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
                
                <select name="nationality" required class="form-select">
                    <option value="">Select Nationality</option>
                    <option value="Filipino">Filipino</option>
                    <option value="American">American</option>
                    <option value="Japanese">Japanese</option>
                    <option value="Chinese">Chinese</option>
                    <option value="Korean">Korean</option>
                    <option value="European">European</option>
                    <option value="Other">Other</option>
                </select>
                
                <div class="form-full">
                    <select name="residence" required class="form-select">
                        <option value="">Place of Residence (Address)</option>
                        <option value="Labo, Camarines Norte">Labo, Camarines Norte</option>
                        <option value="Daet, Camarines Norte">Daet, Camarines Norte</option>
                        <option value="Other Municipality (Camarines Norte)">Other Municipality (Camarines Norte)</option>
                        <option value="Outside Camarines Norte (Philippines)">Outside Camarines Norte (Philippines)</option>
                        <option value="Outside Philippines">Outside Philippines</option>
                    </select>
                </div>
                
                <input type="number" name="num_days" placeholder="No. of Days Visiting" min="1" required class="form-input">
                
                <div class="phone-wrapper">
                    <span class="phone-prefix">+63</span>
                    <input type="tel" name="contact_no" placeholder="912 345 6789" required class="phone-input" pattern="[0-9]{10}">
                </div>
                
                <div class="form-full">
                    <input type="text" name="purpose" placeholder="Purpose of Visit (e.g., Tourism, Research)" required class="form-input">
                </div>
                
                <button type="submit" name="sign_guestbook" class="submit-btn">✍️ Sign Guestbook & Enter Museum</button>
            </form>
        </div>

        <!-- Admin Portal - Side Panel -->
        <div class="admin-container">
            <h3 class="admin-title">🔐 Admin Portal</h3>
            <form method="POST">
                <input type="text" name="username" placeholder="Admin Username" required class="form-input" style="margin-bottom: 15px;">
                <input type="password" name="password" placeholder="Password" required class="form-input" style="margin-bottom: 20px;">
                <button type="submit" name="admin_login" class="admin-btn">Login</button>
            </form>
        </div>

    </div>

</body>
</html>

