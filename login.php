<?php
ob_start();
session_start();
include 'db.php';

$msg = "";
$msg_color = "red";

// Check if database connection is working
if (!$conn) {
    die("Database connection failed. Please check db.php configuration.");
}

// Check if required tables exist
$tables_to_check = ['admins', 'guests'];
$missing_tables = [];
foreach ($tables_to_check as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if (!$result || $result->num_rows == 0) {
        $missing_tables[] = $table;
    }
}

if (!empty($missing_tables)) {
    // Auto-create missing tables
    $create_admins = "CREATE TABLE IF NOT EXISTS admins (
        id INT(11) NOT NULL AUTO_INCREMENT,
        username VARCHAR(50) NOT NULL,
        password VARCHAR(255) NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY username (username)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    $create_guests = "CREATE TABLE IF NOT EXISTS guests (
        id INT(11) NOT NULL AUTO_INCREMENT,
        guest_name VARCHAR(100) NOT NULL,
        gender VARCHAR(20) NOT NULL,
        residence VARCHAR(255) NOT NULL,
        nationality VARCHAR(100) NOT NULL,
        num_days INT(11) NOT NULL,
        purpose VARCHAR(255) NOT NULL,
        contact_no VARCHAR(50) NOT NULL,
        access_id VARCHAR(50) DEFAULT NULL,
        visit_date TIMESTAMP NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    // Create admins table
    if (in_array('admins', $missing_tables)) {
        if (!$conn->query($create_admins)) {
            die("Failed to create admins table: " . $conn->error);
        }
        // Insert default admin if not exists
        $check_admin = $conn->query("SELECT * FROM admins WHERE username = 'admin'");
        if ($check_admin->num_rows == 0) {
            $hashed_password = password_hash('password123', PASSWORD_DEFAULT);
            $conn->query("INSERT INTO admins (username, password) VALUES ('admin', '$hashed_password')");
        }
    }

    // Create guests table
    if (in_array('guests', $missing_tables)) {
        if (!$conn->query($create_guests)) {
            die("Failed to create guests table: " . $conn->error);
        }
    }
}

// --- 1. ADMIN LOGIN ---
if (isset($_POST['admin_login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    if ($stmt === false) {
        $msg = "Database error: " . $conn->error;
    } else {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Verify password using password_verify for hashed passwords
            if (password_verify($password, $row['password']) || $row['password'] === $password) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $username;
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $msg = "Invalid admin credentials!";
            }
        } else {
            $msg = "Invalid admin credentials!";
        }
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
    $col_check = $conn->query("SHOW COLUMNS FROM guests LIKE 'access_id'");
    $col_exists = ($col_check && $col_check->num_rows > 0);

    if ($col_exists) {
        // Generate unique Access ID
        $year = date("Y");
        $random_num = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $access_id = "LABO-{$year}-{$random_num}";

        $stmt = $conn->prepare("INSERT INTO guests (guest_name, gender, residence, nationality, num_days, purpose, contact_no, access_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            $msg = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("ssssisss", $name, $gender, $residence, $nationality, $days, $purpose, $contact, $access_id);
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO guests (guest_name, gender, residence, nationality, num_days, purpose, contact_no) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            $msg = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("ssssiss", $name, $gender, $residence, $nationality, $days, $purpose, $contact);
        }
    }

    if (empty($msg) && $stmt && $stmt->execute()) {
        // Auto-login after registration
        $_SESSION['guest_logged_in'] = true;
        $_SESSION['guest_name'] = $name;
        header("Location: categories.php"); exit();
    } elseif (empty($msg)) {
        $msg = "Error submitting request: " . ($stmt ? $stmt->error : $conn->error);
    }
}

// --- 3. GUEST LOGIN (Name Only!) ---
if (isset($_POST['guest_login'])) {
    $name = trim($_POST['login_name']);

    // Check the database using ONLY the guest's name
    $stmt = $conn->prepare("SELECT * FROM guests WHERE guest_name = ? ORDER BY id DESC LIMIT 1");
    if ($stmt === false) {
        $msg = "Database error: " . $conn->error;
    } else {
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

            <h3 class="login-title">New Visitor? Register Here</h3>
            <form method="POST" class="login-form-grid">

                <input type="text" name="guest_name" placeholder="Full Name" required class="login-input-full">

                <select name="gender" required class="login-select">
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>

                <select name="nationality" required class="login-select">
                    <option value="">Select Nationality</option>
                    <option value="Filipino">Filipino</option>
                    <option value="American">American</option>
                    <option value="Japanese">Japanese</option>
                    <option value="Chinese">Chinese</option>
                    <option value="Korean">Korean</option>
                    <option value="European">European</option>
                    <option value="Other">Other</option>
                </select>

                <select name="residence" required class="login-input-full">
                    <option value="">Select Place of Residence</option>
                    <option value="Labo, Camarines Norte">Labo, Camarines Norte</option>
                    <option value="Daet, Camarines Norte">Daet, Camarines Norte</option>
                    <option value="Other Municipality (Camarines Norte)">Other Municipality (Camarines Norte)</option>
                    <option value="Outside Camarines Norte (Philippines)">Outside Camarines Norte (Philippines)</option>
                    <option value="Outside Philippines">Outside Philippines</option>
                </select>

                <input type="number" name="num_days" placeholder="No. of Days Visiting" min="1" required class="login-input">

                <div class="phone-wrapper">
                    <span class="phone-prefix">+63</span>
                    <input type="tel" name="contact_no" placeholder="912 345 6789" required class="phone-input" pattern="[0-9]{10}" title="Please enter a valid 10-digit mobile number">
                </div>

                <input type="text" name="purpose" placeholder="Purpose of Visit (e.g., Tourism, Research)" required class="login-input-full">

                <button type="submit" name="request_access" class="login-btn">Register & Access Catalog</button>
            </form>
        </div>

        <div class="login-admin">
            <h3 class="admin-title">Admin Portal</h3>
            <form method="POST">
                <input type="text" name="username" placeholder="Admin Username" class="login-input" required>
                <input type="password" name="password" placeholder="Password" class="login-input" required>
                <button type="submit" name="admin_login" class="admin-login-btn">Login</button>
            </form>
        </div>
    </div>

<?php include 'footer.php'; ?>
