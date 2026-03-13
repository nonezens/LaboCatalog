<?php
session_start();
include 'db.php';

$msg = "";

// --- 1. ADMIN LOGIN LOGIC ---
if (isset($_POST['admin_login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $msg = "<div style='color: red; text-align: center; margin-bottom: 15px; padding: 10px; background: #fdeedc; border-radius: 4px;'>Invalid Admin Username or Password.</div>";
    }
}

// --- 2. GUEST REGISTRATION LOGIC (Sign & Enter) ---
if (isset($_POST['register_guest'])) {
    $visitor_type = $_POST['visitor_type'];
    $name = trim($_POST['guest_name']); 
    $gender = $_POST['gender']; 
    $residence = $_POST['residence'];
    $nationality = $_POST['nationality']; 
    
    // Group vs Individual Logic
    if ($visitor_type === 'Group') {
        $organization = trim($_POST['organization']);
        $male_count = (int)$_POST['male_count'];
        $female_count = (int)$_POST['female_count'];
        $headcount = $male_count + $female_count; 
    } else {
        $organization = 'N/A';
        $headcount = 1;
        $male_count = ($gender == 'Male') ? 1 : 0;
        $female_count = ($gender == 'Female') ? 1 : 0;
    }
    
    $num_days = isset($_POST['num_days']) ? (int)$_POST['num_days'] : 1;
    $purpose = isset($_POST['purpose']) ? $_POST['purpose'] : 'Visit';
    $contact = isset($_POST['contact_no']) ? "+63" . ltrim(trim($_POST['contact_no']), '0') : '';

    $stmt = $conn->prepare("INSERT INTO guests (guest_name, visitor_type, organization, gender, residence, nationality, headcount, male_count, female_count, num_days, purpose, contact_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt) {
        $stmt->bind_param("ssssssiiiiss", $name, $visitor_type, $organization, $gender, $residence, $nationality, $headcount, $male_count, $female_count, $num_days, $purpose, $contact);
        if ($stmt->execute()) {
            $_SESSION['guest_logged_in'] = true;
            $_SESSION['guest_name'] = $name;
            header("Location: index.php"); 
            exit();
        } else {
            $msg = "<div style='color: red; text-align: center; margin-bottom: 15px;'>Error saving data: " . $stmt->error . "</div>";
        }
    } else {
        $msg = "<div style='color: red; text-align: center; margin-bottom: 15px;'>Database configuration error.</div>";
    }
}

// --- 3. RETURNING GUEST LOGIC ---
if (isset($_POST['guest_login'])) {
    $name = trim($_POST['login_name']);
    $stmt = $conn->prepare("SELECT * FROM guests WHERE guest_name = ? LIMIT 1");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['guest_logged_in'] = true;
        $_SESSION['guest_name'] = $name;
        header("Location: index.php");
        exit();
    } else {
        $msg = "<div style='color: red; text-align: center; margin-bottom: 15px; padding: 10px; background: #fdeedc; border-radius: 4px;'>Name not found. Please sign the guestbook below!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Guestbook | Museo de Labo</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f4f7f6; margin: 0; padding: 0; }
        .page-container { max-width: 600px; margin: 40px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 30px; border-top: 5px solid #c5a059; }
        h2 { margin-top: 0; color: #2c3e50; text-align: center; margin-bottom: 20px; font-size: 1.8rem; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #555; font-weight: bold; font-size: 0.9rem; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-family: inherit; font-size: 1rem; }
        .btn { width: 100%; padding: 12px; border: none; border-radius: 4px; font-weight: bold; font-size: 1rem; cursor: pointer; transition: 0.3s; color: white; margin-top: 10px; }
        
        .btn-gold { background: #c5a059; } .btn-gold:hover { background: #b48a3d; }
        .btn-dark { background: #2c3e50; } .btn-dark:hover { background: #1a252f; }
        .btn-blue { background: #2980b9; } .btn-blue:hover { background: #2471a3; }
        
        .row { display: flex; gap: 15px; flex-wrap: wrap; }
        .col { flex: 1; min-width: 200px; }
        
        #group_fields { display: none; background: #f9f9f9; padding: 15px; border-radius: 6px; border: 1px dashed #ccc; margin-bottom: 15px; }

        /* MAGIC FIX: Removes up/down arrows from all number fields */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="number"] {
            appearance: textfield;
            -moz-appearance: textfield; /* For Firefox */
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="page-container">
        
        <?php echo $msg; ?>

        <?php if(isset($_GET['admin'])): ?>
            <div class="card" style="border-top-color: #2980b9;">
                <h2>Admin Login</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" name="admin_login" class="btn btn-blue">Login to Dashboard</button>
                </form>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="login.php" style="color: #7f8c8d; text-decoration: none; font-weight: bold;">&larr; Back to Guestbook</a>
            </div>

        <?php else: ?>
            
            <div class="card">
                <h2>Sign Digital Guestbook</h2>
                <form method="POST">
                    
                    <div class="form-group">
                        <label>Visitor Type</label>
                        <select name="visitor_type" id="visitor_type" class="form-control" onchange="toggleGroupFields()" required>
                            <option value="Individual">Individual / Family</option>
                            <option value="Group">School / Organization / Tour Group</option>
                        </select>
                    </div>

                    <div id="group_fields">
                        <div class="form-group">
                            <label>Organization / School Name</label>
                            <input type="text" name="organization" id="org_input" class="form-control" placeholder="e.g., Labo National High School">
                        </div>
                        <div class="row">
                            <div class="form-group col">
                                <label>Number of Males</label>
                                <input type="number" name="male_count" id="male_input" class="form-control" min="0" value="0">
                            </div>
                            <div class="form-group col">
                                <label>Number of Females</label>
                                <input type="number" name="female_count" id="female_input" class="form-control" min="0" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label id="name_label">Full Name</label>
                        <input type="text" name="guest_name" class="form-control" placeholder="Juan Dela Cruz" required>
                    </div>

                    <div class="row">
                        <div class="form-group col">
                            <label>Gender</label>
                            <select name="gender" class="form-control" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group col">
                            <label>Contact Number</label>
                            <input type="tel" name="contact_no" class="form-control" placeholder="09123456789" pattern="09[0-9]{9}" maxlength="11" title="Please enter exactly 11 digits starting with 09 (e.g., 09123456789)" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col">
                            <label>Residence (Town/City)</label>
                            <input type="text" name="residence" class="form-control" placeholder="e.g., Labo" required>
                        </div>
                        <div class="form-group col">
                            <label>Nationality</label>
                            <input type="text" name="nationality" class="form-control" value="Filipino" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col">
                            <label>Purpose of Visit</label>
                            <input type="text" name="purpose" class="form-control" placeholder="e.g., Research, Tour" required>
                        </div>
                        <div class="form-group col">
                            <label>Days of Stay</label>
                            <input type="number" name="num_days" class="form-control" value="1" min="1" required>
                        </div>
                    </div>

                    <button type="submit" name="register_guest" class="btn btn-gold">Sign & Enter Catalog</button>
                </form>
            </div>

            <div class="card" style="border-top-color: #2c3e50;">
                <h2 style="font-size: 1.5rem;">Returning Visitor?</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Enter Registered Name</label>
                        <input type="text" name="login_name" class="form-control" required>
                    </div>
                    <button type="submit" name="guest_login" class="btn btn-dark">Quick Access</button>
                </form>
            </div>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="login.php?admin=1" style="color: #95a5a6; text-decoration: none; font-size: 0.95rem; font-weight: bold;">Admin Login Portal</a>
            </div>

        <?php endif; ?>

    </div>

    <script>
        function toggleGroupFields() {
            var type = document.getElementById("visitor_type").value;
            var groupFields = document.getElementById("group_fields");
            var nameLabel = document.getElementById("name_label");
            var orgInput = document.getElementById("org_input");

            if (type === "Group") {
                groupFields.style.display = "block";
                nameLabel.innerText = "Representative's Full Name";
                orgInput.required = true;
            } else {
                groupFields.style.display = "none";
                nameLabel.innerText = "Full Name";
                orgInput.required = false;
            }
        }
    </script>
</body>
</html>