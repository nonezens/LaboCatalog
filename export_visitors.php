<?php
// 1. Security Check
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php'; 

// 2. Get the month filter if one was applied
$selected_month = isset($_GET['filter_month']) ? $_GET['filter_month'] : '';

// 3. Name the downloaded file dynamically
$filename = "Museo_Labo_Visitors_" . ($selected_month ? $selected_month : "All_Time") . ".csv";

// 4. Send headers to force the browser to download a CSV file
header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");

// 5. Open output stream
$output = fopen('php://output', 'w');

// Add a special UTF-8 BOM so Microsoft Excel reads special characters perfectly
fputs($output, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF)));

// 6. Write the Column Headers into the Excel file
fputcsv($output, array('Visit Date', 'Guest Name', 'Contact Number', 'Gender', 'Nationality', 'Place of Residence', 'Purpose of Visit', 'Days Staying', 'Approval Status'));

// 7. Fetch the data from the database
$query = "SELECT * FROM guests";
if ($selected_month != '') {
    $query .= " WHERE DATE_FORMAT(visit_date, '%Y-%m') = '" . $conn->real_escape_string($selected_month) . "'";
}
$query .= " ORDER BY visit_date DESC";
$result = $conn->query($query);

// 8. Loop through the data and write each row to the Excel file
while ($row = $result->fetch_assoc()) {
    fputcsv($output, array(
        date("M d, Y g:i A", strtotime($row['visit_date'])),
        $row['guest_name'],
        $row['contact_no'],
        $row['gender'],
        $row['nationality'],
        $row['residence'],
        $row['purpose'],
        $row['num_days'],
        $row['status']
    ));
}

// Close the stream and stop running PHP
fclose($output);
exit();
?>