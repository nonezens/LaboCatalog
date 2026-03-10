<?php
// 1. Security Check
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include 'db.php'; 

// 2. Get all filters
$selected_month = isset($_GET['filter_month']) ? $_GET['filter_month'] : '';
$filter_gender = isset($_GET['filter_gender']) ? $_GET['filter_gender'] : '';
$filter_recent = isset($_GET['filter_recent']) ? $_GET['filter_recent'] : '';

// 3. Build query with filters
$query = "SELECT * FROM guests";
$conditions = [];

if ($selected_month != '') {
    $conditions[] = "DATE_FORMAT(visit_date, '%Y-%m') = '" . $conn->real_escape_string($selected_month) . "'";
}

if ($filter_gender != '') {
    $conditions[] = "gender = '" . $conn->real_escape_string($filter_gender) . "'";
}

if ($filter_recent == 'today') {
    $conditions[] = "DATE(visit_date) = CURDATE()";
} elseif ($filter_recent == 'week') {
    $conditions[] = "visit_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
} elseif ($filter_recent == 'month') {
    $conditions[] = "visit_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
}

if (count($conditions) > 0) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY visit_date DESC";
$result = $conn->query($query);

// 4. Generate filename
$filename = "Museo_Labo_Visitors_" . date("Y-m-d") . ".xls";

// 5. Send headers for Excel download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");

// 6. Output HTML-formatted Excel with styling
echo '<?xml version="1.0" encoding="UTF-8"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
<Styles>
 <Style ss:ID="Header">
  <Font ss:Bold="1" ss:Color="#FFFFFF"/>
  <Interior ss:Color="#2C3E50" ss:Pattern="Solid"/>
  <Alignment ss:Horizontal="Center"/>
 </Style>
 <Style ss:ID="Title">
  <Font ss:Bold="1" ss:Size="16" ss:Color="#2C3E50"/>
  <Alignment ss:Horizontal="Center"/>
 </Style>
 <Style ss:ID="Date">
  <Alignment ss:Horizontal="Center"/>
 </Style>
 <Style ss:ID="AltRow">
  <Interior ss:Color="#F8F9FA" ss:Pattern="Solid"/>
 </Style>
</Styles>
<Worksheet ss:Name="Visitor Log">
<Table>';

// Title Row
echo '<Row ss:StyleID="Title">
 <Cell ss:MergeAcross="8"><Data ss:Type="String">MUSEO DE LABO - Visitor Log Report</Data></Cell>
</Row>';

// Date generated
echo '<Row>
 <Cell ss:MergeAcross="8"><Data ss:Type="String">Generated: ' . date("F j, Y g:i A") . '</Data></Cell>
</Row>';

// Empty row
echo '<Row><Cell ss:MergeAcross="8"></Cell></Row>';

// Column Headers
$headers = array('No.', 'Date', 'Guest Name', 'Contact Number', 'Gender', 'Nationality', 'Residence', 'Purpose', 'Days', 'Status');
echo '<Row ss:StyleID="Header">';
foreach ($headers as $header) {
    echo '<Cell><Data ss:Type="String">' . $header . '</Data></Cell>';
}
echo '</Row>';

// Data rows
$no = 1;
while ($row = $result->fetch_assoc()) {
    $rowStyle = ($no % 2 == 0) ? 'ss:StyleID="AltRow"' : '';
    echo "<Row $rowStyle>";
    echo '<Cell><Data ss:Type="Number">' . $no . '</Data></Cell>';
    echo '<Cell ss:StyleID="Date"><Data ss:Type="String">' . date("M d, Y g:i A", strtotime($row['visit_date'])) . '</Data></Cell>';
    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['guest_name']) . '</Data></Cell>';
    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['contact_no']) . '</Data></Cell>';
    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['gender']) . '</Data></Cell>';
    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['nationality']) . '</Data></Cell>';
    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['residence']) . '</Data></Cell>';
    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['purpose']) . '</Data></Cell>';
    echo '<Cell><Data ss:Type="Number">' . $row['num_days'] . '</Data></Cell>';
    echo '<Cell><Data ss:Type="String">' . ucfirst($row['status']) . '</Data></Cell>';
    echo '</Row>';
    $no++;
}

// Summary row
$total = $no - 1;
echo '<Row><Cell ss:MergeAcross="8"></Cell></Row>';
echo "<Row>
 <Cell ss:MergeAcross=\"8\"><Data ss:Type=\"String\">Total Visitors: $total</Data></Cell>
</Row>";

echo '</Table>
</Worksheet>
</Workbook>';
exit();
?>
