<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'header.php';
include 'admin_sidebar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cron Jobs</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <div class="form-container" style="max-width: 800px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #2c3e50;">Cron Jobs</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th><th>Schedule</th><th>Status</th><th>Last Run</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Send Newsletter</td>
                        <td>Every Monday at 9:00 AM</td>
                        <td><span class="badge badge-approved">Active</span></td>
                        <td><?php echo date("Y-m-d H:i:s", strtotime("-1 day")); ?></td>
                    </tr>
                    <tr>
                        <td>Clear Cache</td>
                        <td>Every day at 3:00 AM</td>
                        <td><span class="badge badge-approved">Active</span></td>
                        <td><?php echo date("Y-m-d H:i:s", strtotime("-1 hour")); ?></td>
                    </tr>
                    <tr>
                        <td>Backup Database</td>
                        <td>Every day at 1:00 AM</td>
                        <td><span class="badge badge-approved">Active</span></td>
                        <td><?php echo date("Y-m-d H:i:s", strtotime("-2 hours")); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    </main>
</div>

</body>
</html>