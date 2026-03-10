<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php';
include 'functions.php';

$msg = "";
$msg_color = "red";

// Check if languages table exists
$table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'languages'");
if (!$table_exists || mysqli_num_rows($table_exists) == 0) {
    // Create the table if it doesn't exist
    $create_table = "CREATE TABLE IF NOT EXISTS languages (
        id INT(11) PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        code VARCHAR(10) NOT NULL,
        is_active TINYINT(1) DEFAULT 1
    )";
    mysqli_query($conn, $create_table);
    
    // Insert default languages
    $default_languages = [
        ['name' => 'English', 'code' => 'en'],
        ['name' => 'Filipino', 'code' => 'fil'],
        ['name' => 'Cebuano', 'code' => 'ceb'],
        ['name' => 'Baybayin', 'code' => 'bay']
    ];
    
    foreach ($default_languages as $lang) {
        mysqli_query($conn, "INSERT INTO languages (name, code) VALUES ('" . mysqli_real_escape_string($conn, $lang['name']) . "', '" . mysqli_real_escape_string($conn, $lang['code']) . "')");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_language'])) {
        $name = trim($_POST['name']);
        $code = trim($_POST['code']);

        if (!empty($name) && !empty($code)) {
            $stmt = $conn->prepare("INSERT INTO languages (name, code) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $code);
            if ($stmt->execute()) {
                log_activity($conn, $_SESSION['admin_id'], "Added language: " . $name);
                $msg = "Language added successfully!";
                $msg_color = "green";
            } else {
                $msg = "Error adding language: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $msg = "Please fill in all fields.";
        }
    } elseif (isset($_POST['update_language'])) {
        $id = (int)$_POST['id'];
        $name = trim($_POST['name']);
        $code = trim($_POST['code']);

        if (!empty($name) && !empty($code)) {
            $stmt = $conn->prepare("UPDATE languages SET name = ?, code = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $code, $id);
            if ($stmt->execute()) {
                log_activity($conn, $_SESSION['admin_id'], "Updated language: " . $name);
                $msg = "Language updated successfully!";
                $msg_color = "green";
            } else {
                $msg = "Error updating language: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $msg = "Please fill in all fields.";
        }
    } elseif (isset($_POST['set_default'])) {
        $id = (int)$_POST['language_id'];
        
        // Set all languages as non-default first
        mysqli_query($conn, "UPDATE languages SET is_active = 1");
        
        // Set the selected language as default
        $stmt = $conn->prepare("UPDATE languages SET is_active = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            log_activity($conn, $_SESSION['admin_id'], "Set default language");
            $msg = "Default language set successfully!";
            $msg_color = "green";
        }
        $stmt->close();
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("SELECT name FROM languages WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $language = $result->fetch_assoc();

    if ($language) {
        $stmt = $conn->prepare("DELETE FROM languages WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            log_activity($conn, $_SESSION['admin_id'], "Deleted language: " . $language['name']);
            header("Location: languages.php?msg=deleted");
            exit();
        }
    }
}

$query = "SELECT * FROM languages ORDER BY id ASC";
$result = mysqli_query($conn, $query);
$languages = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $languages[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Languages</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div class="form-container" style="max-width: 900px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #2c3e50;">Manage Languages</h2>
        <p style="text-align: center; color: #666; margin-bottom: 20px;">Available languages: English, Filipino, Cebuano, Baybayin</p>
        
        <?php if ($msg): ?>
            <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo ($msg_color === 'red' ? '#c0392b' : '#27ae60'); ?>; font-weight: bold;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div style="margin-bottom: 30px;">
            <h3 style="color: #2c3e50;">Add New Language</h3>
            <form action="languages.php" method="POST">
                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <input type="text" name="name" placeholder="Language Name" required style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <input type="text" name="code" placeholder="Language Code (e.g., en, fil, ceb, bay)" required style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <button type="submit" name="add_language" style="padding: 10px 20px; background: #2980b9; color: white; border: none; border-radius: 4px;">Add Language</button>
            </form>
        </div>

        <h3 style="color: #2c3e50;">Available Languages</h3>
        <?php if (empty($languages)) { ?>
            <p style="text-align: center; color: #777;">No languages found. Add one above.</p>
        <?php } else { ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Language Name</th>
                        <th>Code</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($languages as $language) { ?>
                        <tr>
                            <form action="languages.php" method="POST">
                                <td><?php echo $language['id']; ?></td>
                                <td><input type="text" name="name" value="<?php echo htmlspecialchars($language['name']); ?>" style="padding: 5px; border: 1px solid #ddd; border-radius: 4px;"></td>
                                <td><input type="text" name="code" value="<?php echo htmlspecialchars($language['code']); ?>" style="padding: 5px; border: 1px solid #ddd; border-radius: 4px; width: 80px;"></td>
                                <td>
                                    <?php if (!empty($language['is_active'])) { ?>
                                        <span style="color: #27ae60; font-weight: bold;">✓ Active</span>
                                    <?php } else { ?>
                                        <span style="color: #95a5a6;">Inactive</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <input type="hidden" name="id" value="<?php echo $language['id']; ?>">
                                    <button type="submit" name="update_language" style="padding: 5px 10px; background: #f39c12; color: white; border: none; border-radius: 4px; margin-right: 5px;">Update</button>
                                    <form action="languages.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="language_id" value="<?php echo $language['id']; ?>">
                                        <button type="submit" name="set_default" style="padding: 5px 10px; background: #27ae60; color: white; border: none; border-radius: 4px;">Set Active</button>
                                    </form>
                                    <a href="languages.php?delete=<?php echo $language['id']; ?>" onclick="return confirm('Are you sure?');" style="padding: 5px 10px; background: #e74c3c; color: white; text-decoration: none; border-radius: 4px; margin-left: 5px;">Delete</a>
                                </td>
                            </form>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } ?>
        
        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <h3 style="color: #2c3e50;">Language Codes Reference:</h3>
            <ul style="color: #666;">
                <li><strong>English</strong> - Code: en</li>
                <li><strong>Filipino</strong> - Code: fil</li>
                <li><strong>Cebuano</strong> - Code: ceb</li>
                <li><strong>Baybayin</strong> - Code: bay</li>
            </ul>
        </div>
    </div>

    </main>
</div>

</body>
</html>

