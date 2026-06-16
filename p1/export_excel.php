<?php
include 'connection.php';

$from_date = mysqli_real_escape_string($conn, $_GET['from_date'] ?? '');
$to_date   = mysqli_real_escape_string($conn, $_GET['to_date'] ?? '');

if (empty($from_date) || empty($to_date)) {
    die("Please provide both From Date and To Date.");
}

// Validate date format
$d1 = DateTime::createFromFormat('Y-m-d', $from_date);
$d2 = DateTime::createFromFormat('Y-m-d', $to_date);
if (!$d1 || !$d2) {
    die("Invalid date format.");
}

$sql = "SELECT * FROM feedback_complaint_data 
        WHERE Date_of_submission BETWEEN '$from_date' AND '$to_date'
        ORDER BY Date_of_submission ASC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

// Set headers to force download as Excel-compatible CSV
$filename = "feedback_data_" . $from_date . "_to_" . $to_date . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// Add BOM so Excel reads UTF-8 correctly
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Write header row from column names
if (mysqli_num_rows($result) > 0) {
    $fields = mysqli_fetch_fields($result);
    $headers = [];
    foreach ($fields as $field) {
        $headers[] = $field->name;
    }
    fputcsv($output, $headers);

    // Write data rows
    mysqli_data_seek($result, 0);
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, $row);
    }
} else {
    fputcsv($output, ["No records found for selected date range"]);
}

fclose($output);
mysqli_close($conn);
exit;
?>