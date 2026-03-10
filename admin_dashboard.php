<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php'; 

// Fetch basic stats
$total_exhibits = $conn->query("SELECT id FROM exhibits")->num_rows;
$total_categories = $conn->query("SELECT id FROM categories")->num_rows;
$total_guests = $conn->query("SELECT id FROM guests")->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body style="background: #f4f7f6; margin: 0; font-family: sans-serif;">

    <?php include 'header.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <div style="display: flex; justify-content: space-around; margin: 20px 0;">
        <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center;">
            <h3 style="color: #2c3e50;">Recently Added Artifacts</h3>
            <p style="font-size: 2rem; color: #3498db;"><?php echo $total_exhibits; ?></p>
        </div>
        <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center;">
            <h3 style="color: #2c3e50;">Recently Added Departments</h3>
            <p style="font-size: 2rem; color: #e74c3c;"><?php echo $total_categories; ?></p>
        </div>
        <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center;">
            <h3 style="color: #2c3e50;">Recent Visitors</h3>
            <p style="font-size: 2rem; color: #2ecc71;"><?php echo $total_guests; ?></p>
        </div>
    </div>

    <div style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center;">
        <h1 style="color: #2c3e50;">Welcome back, Admin!</h1>
        <p style="color: #7f8c8d; font-size: 1.1rem;">Use the left menu to manage visitors, artifacts, and departments.</p>
    </div>

    <div style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center; margin-top: 20px;">
        <h2 style="color: #2c3e50;">Visitor Statistics</h2>
        <canvas id="visitorChart" width="400" height="200"></canvas>
    </div>

    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
<?php
$visitor_query = "SELECT DATE_FORMAT(visit_date, '%Y-%m') AS month, COUNT(*) AS visitor_count FROM guests GROUP BY DATE_FORMAT(visit_date, '%Y-%m') ORDER BY month ASC";
$visitor_result = $conn->query($visitor_query);
$visitor_data = [];
while ($row = $visitor_result->fetch_assoc()) {
    $visitor_data[] = $row;
}
?>
const visitorChartCanvas = document.getElementById('visitorChart').getContext('2d');
const visitorChart = new Chart(visitorChartCanvas, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($visitor_data, 'month')); ?>,
        datasets: [{
            label: 'Number of Visitors',
            data: <?php echo json_encode(array_column($visitor_data, 'visitor_count')); ?>,
            backgroundColor: 'rgba(52, 152, 219, 0.5)',
            borderColor: 'rgba(52, 152, 219, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

</body>
</html>