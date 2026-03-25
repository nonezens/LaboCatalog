<?php
session_start();
require_once __DIR__ . '/../db.php';

// Security check: Only admins can download data
if (!isset($_SESSION['admin_logged_in'])) { 
    header("Location: login.php"); 
    exit(); 
}

// Check if a specific month was selected in the filter
$selected_month = isset($_GET['filter_month']) ? $_GET['filter_month'] : '';

$query = "SELECT * FROM guests";

// If a month is selected, filter the results and name the file accordingly
if ($selected_month != '') {
    $query .= " WHERE DATE_FORMAT(visit_date, '%Y-%m') = '" . $conn->real_escape_string($selected_month) . "'";
    $filename = "Museo_Visitors_" . $selected_month . ".csv";
} else {
    $filename = "Museo_Visitors_All_Time.csv";
}

$query .= " ORDER BY visit_date DESC";
$result = $conn->query($query);

// Set HTTP headers to force the browser to download it as a CSV (Excel) file
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// Open the output stream
$output = fopen('php://output', 'w');

// 1. Output the Excel Column Headings (Now including Group Data!)
fputcsv($output, array(
    'Visit Date', 
    'Time', 
    'Visitor Type', 
    'Organization / School', 
    'Guest / Rep Name', 
    'Total Pax', 
    'Male Count', 
    'Female Count', 
    'Rep Gender', 
    'Contact Number', 
    'Residence', 
    'Nationality', 
    'Purpose of Visit', 
    'Days of Stay'
));

// 2. Loop through the database and fill the Excel rows
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        
        // Format the date and time nicely
        $date = date("Y-m-d", strtotime($row['visit_date']));
        $time = date("h:i A", strtotime($row['visit_date']));
        
        // Safely pull the new group columns (with fallbacks just in case older records don't have them)
        $visitor_type = isset($row['visitor_type']) ? $row['visitor_type'] : 'Individual';
        $organization = isset($row['organization']) ? $row['organization'] : 'N/A';
        $headcount = isset($row['headcount']) ? $row['headcount'] : 1;
        
        // If it's an old record without male/female count, default to the individual's gender
        $male_count = isset($row['male_count']) ? $row['male_count'] : ($row['gender'] == 'Male' ? 1 : 0);
        $female_count = isset($row['female_count']) ? $row['female_count'] : ($row['gender'] == 'Female' ? 1 : 0);
        $num_days = isset($row['num_days']) ? $row['num_days'] : 1;

        // Write the row to the Excel/CSV file
        fputcsv($output, array(
            $date,
            $time,
            $visitor_type,
            $organization,
            $row['guest_name'],
            $headcount,
            $male_count,
            $female_count,
            $row['gender'],
            $row['contact_no'],
            $row['residence'],
            $row['nationality'],
            $row['purpose'],
            $num_days
        ));
    }
}

// Close the stream and stop the script
fclose($output);
exit();
?>