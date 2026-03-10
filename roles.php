<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php';
include 'functions.php';

$msg = "";
$msg_color = "red";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_role'])) {
        $name = trim($_POST['name']);

        if (!empty($name)) {
            $stmt = $conn->prepare("INSERT INTO roles (name) VALUES (?)");
            $stmt->bind_param("s", $name);
            if ($stmt->execute()) {
                log_activity($conn, $_SESSION['admin_id'], "Added role: " . $name);
                $msg = "Role added successfully!";
                $msg_color = "green";
            } else {
                $msg = "Error adding role: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $msg = "Please fill in all fields.";
        }
    } elseif (isset($_POST['update_role'])) {
        $id = (int)$_POST['id'];
        $name = trim($_POST['name']);

        if (!empty($name)) {
            $stmt = $conn->prepare("UPDATE roles SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $name, $id);
            if ($stmt->execute()) {
                log_activity($conn, $_SESSION['admin_id'], "Updated role: " . $name);
                $msg = "Role updated successfully!";
                $msg_color = "green";
            } else {
                $msg = "Error updating role: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $msg = "Please fill in all fields.";
        }
    } elseif (isset($_POST['assign_permissions'])) {
        $role_id = (int)$_POST['role_id'];
        $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];

        $stmt = $conn->prepare("DELETE FROM role_permissions WHERE role_id = ?");
        $stmt->bind_param("i", $role_id);
        $stmt->execute();

        if (!empty($permissions)) {
            $stmt = $conn->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
            foreach ($permissions as $permission_id) {
                $stmt->bind_param("ii", $role_id, $permission_id);
                $stmt->execute();
            }
        }
        log_activity($conn, $_SESSION['admin_id'], "Updated permissions for role ID: " . $role_id);
        $msg = "Permissions updated successfully!";
        $msg_color = "green";
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("SELECT name FROM roles WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $role = $result->fetch_assoc();

    if ($role) {
        $stmt = $conn->prepare("DELETE FROM roles WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $stmt = $conn->prepare("DELETE FROM role_permissions WHERE role_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            log_activity($conn, $_SESSION['admin_id'], "Deleted role: " . $role['name']);
            header("Location: roles.php?msg=deleted");
            exit();
        }
    }
}

$roles_query = "SELECT * FROM roles ORDER BY name ASC";
$roles_result = $conn->query($roles_query);
$roles = [];
while ($row = $roles_result->fetch_assoc()) {
    $roles[] = $row;
}

$permissions_query = "SELECT * FROM permissions ORDER BY name ASC";
$permissions_result = $conn->query($permissions_query);
$permissions = [];
while ($row = $permissions_result->fetch_assoc()) {
    $permissions[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Roles</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div class="form-container" style="max-width: 800px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #2c3e50;">Manage Roles and Permissions</h2>
        <?php if ($msg): ?>
            <div style="background: #f8f9fa; border-left: 4px solid <?php echo $msg_color; ?>; padding: 15px; margin-bottom: 25px; color: <?php echo ($msg_color === 'red' ? '#c0392b' : '#27ae60'); ?>; font-weight: bold;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div style="display: flex; gap: 20px;">
            <div style="flex: 1;">
                <h3 style="color: #2c3e50;">Add New Role</h3>
                <form action="roles.php" method="POST">
                    <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <input type="text" name="name" placeholder="Role Name" required style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <button type="submit" name="add_role" style="padding: 10px 20px; background: #2980b9; color: white; border: none; border-radius: 4px;">Add Role</button>
                </form>

                <h3 style="color: #2c3e50; margin-top: 30px;">Existing Roles</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th><th>Name</th><th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($roles as $role): ?>
                                <tr>
                                    <form action="roles.php" method="POST">
                                        <td><?php echo $role['id']; ?></td>
                                        <td><input type="text" name="name" value="<?php echo htmlspecialchars($role['name']); ?>" style="padding: 5px; border: 1px solid #ddd; border-radius: 4px;"></td>
                                        <td>
                                            <input type="hidden" name="id" value="<?php echo $role['id']; ?>">
                                            <button type="submit" name="update_role" style="padding: 5px 10px; background: #f39c12; color: white; border: none; border-radius: 4px;">Update</button>
                                            <a href="roles.php?delete=<?php echo $role['id']; ?>" onclick="return confirm('Are you sure?');" style="padding: 5px 10px; background: #e74c3c; color: white; text-decoration: none; border-radius: 4px;">Delete</a>
                                        </td>
                                    </form>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="flex: 1;">
                <h3 style="color: #2c3e50;">Assign Permissions</h3>
                <form action="roles.php" method="POST">
                    <div style="margin-bottom: 10px;">
                        <label for="role_id" style="font-weight: bold;">Role:</label>
                        <select name="role_id" id="role_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label style="font-weight: bold;">Permissions:</label>
                        <?php foreach ($permissions as $permission): ?>
                            <div style="display: block;">
                                <input type="checkbox" name="permissions[]" value="<?php echo $permission['id']; ?>" id="perm_<?php echo $permission['id']; ?>">
                                <label for="perm_<?php echo $permission['id']; ?>"><?php echo htmlspecialchars($permission['name']); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" name="assign_permissions" style="padding: 10px 20px; background: #27ae60; color: white; border: none; border-radius: 4px;">Assign Permissions</button>
                </form>
            </div>
        </div>
    </div>

    </main>
</div>

</body>
</html>