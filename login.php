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
            <!-- Visitor/Guest Section (Main Form) -->
            <div class="login-visitor login-section">
                <h2 class="login-title">Sign Digital Guestbook</h2>
                <p class="login-subtitle">Welcome to Museo de Labo. Please sign our digital guestbook to access the catalog.</p>
                <form method="POST" id="guest-form">
                    <div class="login-input-full">
                        <label for="visitor_type">Visitor Type *</label>
                        <select name="visitor_type" id="visitor_type" class="login-select" onchange="toggleGroupFields()" required>
                            <option value="Individual">👤 Individual / Family</option>
                            <option value="Group">🏫 School / Organization / Tour Group</option>
                        </select>
                    </div>

                    <div id="group_fields" style="display: none;">
                        <div class="login-section" style="background: #f8f9fa;">
                            <h3 style="color: #c5a059; margin-top: 0;">Group Information</h3>
                            <div class="login-input-full">
                                <label>Organization / School</label>
                                <input type="text" name="organization" class="login-input" placeholder="e.g., Labo National High School">
                            </div>
                            <div class="login-form-grid">
                                <div>
                                    <label>Number of Males</label>
                                    <input type="number" name="male_count" class="login-input" min="0" value="0">
                                </div>
                                <div>
                                    <label>Number of Females</label>
                                    <input type="number" name="female_count" class="login-input" min="0" value="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="login-input-full">
                        <label for="guest_name">Full Name *</label>
                        <input type="text" name="guest_name" id="guest_name" class="login-input" placeholder="Enter your complete name" required>
                    </div>

                    <div class="login-form-grid">
                        <div>
                            <label for="gender">Gender *</label>
                            <select name="gender" id="gender" class="login-select" required>
                                <option value="">Select...</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other / Prefer not to say</option>
                            </select>
                        </div>
                        <div>
                            <label for="contact_no">Contact Number *</label>
                            <div class="phone-wrapper">
                                <span class="phone-prefix">+63</span>
                                <input type="tel" name="contact_no" id="contact_no" class="phone-input" placeholder="912 345 6789" pattern="[0-9]{10}" required>
                            </div>
                        </div>
                    </div>

                    <div class="login-form-grid">
                        <div>
                            <label for="residence">Residence (Town/City) *</label>
                            <input type="text" name="residence" id="residence" class="login-input" placeholder="Labo, Camarines Norte" required>
                        </div>
                        <div>
                            <label for="nationality">Nationality *</label>
                            <input type="text" name="nationality" id="nationality" class="login-input" value="Filipino" required>
                        </div>
                    </div>

                    <div class="login-form-grid">
                        <div>
                            <label for="purpose">Purpose of Visit *</label>
                            <input type="text" name="purpose" id="purpose" class="login-input" placeholder="Research, Educational Tour, etc." required>
                        </div>
                        <div>
                            <label for="num_days">Planned Stay (days) *</label>
                            <input type="number" name="num_days" id="num_days" class="login-input" min="1" max="30" value="1" required>
                        </div>
                    </div>

                    <button type="submit" name="request_access" class="login-btn login-btn-full">
                        <span>✨ Sign Guestbook & Enter Catalog</span>
                    </button>
                </form>

                <!-- Quick Guest Login -->
                <div class="login-section" style="margin-top: 25px;">
                    <h3>Already Registered?</h3>
                    <form method="POST" class="login-input-full">
                        <div class="login-input-full">
                            <input type="text" name="login_name" class="login-input" placeholder="Enter your exact name to login" style="text-align: center; font-size: 1.1rem;" required>
                        </div>
                        <button type="submit" name="guest_login" class="login-btn-secondary login-btn-full">🚀 Quick Access</button>
                    </form>
                </div>
            </div>

            <!-- Admin Login Panel -->
            <div class="login-admin login-section">
                <h3 class="login-title admin-title">🔐 Admin Login</h3>
                <p style="color: #95a5a6; font-size: 0.9rem; margin: 0 0 20px 0; font-style: italic;">Demo: <code>admin</code> / <code>password123</code></p>
                <form method="POST">
                    <div class="login-input-full">
                        <label>Username</label>
                        <input type="text" name="username" class="login-input" placeholder="admin" autocomplete="username" required>
                    </div>
                    <div class="login-input-full">
                        <label>Password</label>
                        <input type="password" name="password" class="login-input" placeholder="password123" autocomplete="current-password" required>
                    </div>
                    <button type="submit" name="admin_login" class="login-btn admin-login-btn">⚙️ Dashboard</button>
                </form>
            </div>
        </div>

    </div>

    <?php include 'footer.php'; ?>

    <script>
        // Toggle group fields
        function toggleGroupFields() {
            const type = document.getElementById('visitor_type')?.value;
            const groupFields = document.getElementById('group_fields');
            if (groupFields) {
                groupFields.style.display = type === 'Group' ? 'block' : 'none';
            }
        }

        // Enhanced form validation and UX
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    let valid = true;
                    this.querySelectorAll('[required]').forEach(field => {
                        if (!field.value.trim()) {
                            field.style.borderColor = '#e74c3c';
                            field.style.boxShadow = '0 0 0 2px rgba(231, 76, 60, 0.2)';
                            valid = false;
                        } else {
                            field.style.borderColor = '';
                            field.style.boxShadow = '';
                        }
                    });
                    if (!valid) {
                        e.preventDefault();
                        this.scrollIntoView({ behavior: 'smooth' });
                    }
                });

                // Real-time phone validation
                const phoneInput = this.querySelector('.phone-input');
                if (phoneInput) {
                    phoneInput.addEventListener('input', function() {
                        this.value = this.value.replace(/[^0-9]/g, '').slice(0,10);
                    });
                }
            });

            // Input focus animations
            document.querySelectorAll('.login-input, .login-select').forEach(input => {
                input.addEventListener('focus', () => input.parentElement.style.transform = 'scale(1.02)');
                input.addEventListener('blur', () => input.parentElement.style.transform = '');
            });

            // Auto-hide message after 5s
            const message = document.querySelector('.login-message');
            if (message) {
                setTimeout(() => {
                    message.style.opacity = '0';
                    message.style.transition = 'opacity 0.5s';
                }, 5000);
            }
        });
    </script>

