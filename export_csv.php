<?php

session_start();
if (!isset($_SESSION['employee_id']) && !isset($_SESSION['admin_id'])) {
    http_response_code(401);
    die("Unauthorized. Please log in.");
}
include 'connection.php';

$from = trim($_GET['from_date'] ?? '');
$to   = trim($_GET['to_date'] ?? '');

// ---- Validate dates ----
if (empty($from) || empty($to)) {
    die("Invalid date range.");
}

$from_dt = DateTime::createFromFormat('Y-m-d', $from);
$to_dt   = DateTime::createFromFormat('Y-m-d', $to);

if (!$from_dt || $from_dt->format('Y-m-d') !== $from ||
    !$to_dt   || $to_dt->format('Y-m-d')   !== $to) {
    die("Invalid date format.");
}

if ($from_dt > $to_dt) {
    die("From date cannot be after To date.");
}

// ---- Query using pg_query_params (SQL injection safe) ----
$sql = "SELECT * FROM feedback_complaint_data 
        WHERE Date_of_submission BETWEEN $1 AND $2
        ORDER BY Date_of_submission ASC";

$result = pg_query_params($conn, $sql, [$from, $to]);

if (!$result) {
    error_log("Export CSV query failed: " . pg_last_error($conn));
    die("Something went wrong. Please try again.");
}

// ---- Sanitize filename ----
$safe_from = preg_replace('/[^0-9\-]/', '', $from);
$safe_to   = preg_replace('/[^0-9\-]/', '', $to);

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=feedback_data_{$safe_from}_to_{$safe_to}.csv");
header("Pragma: no-cache");
header("Expires: 0");

$output = fopen("php://output", "w");

// Header row
fputcsv($output, [
    'ID', 'Form No.', 'Operation', 'Given By', 'Date', 'Department/Section',
    'Incident Description', 'Main Error Category', 'Sub Error Category',
    'Active Error', 'Latent Error', 'Cognitive Error', 'Non-Cognitive Error',
    'Root Cause', 'Avg Impact Score', 'Avg Freq Score', 'Avg Risk Score',
    'Immediate Correction', 'Corrective Action', 'Preventive Action', 'Patient Consequences',
    'Risk Description 1', 'Impact Score 1', 'Freq Score 1',
    'Risk Description 2', 'Impact Score 2', 'Freq Score 2',
    'Risk Description 3', 'Impact Score 3', 'Freq Score 3',
    'Risk Description 4', 'Impact Score 4', 'Freq Score 4',
    'Risk Description 5', 'Impact Score 5', 'Freq Score 5'
]);

// Data rows
while ($row = pg_fetch_assoc($result)) {
    fputcsv($output, [
        $row['id'] ?? '',
        $row['form_no'] ?? '',
        $row['operation'] ?? '',
        $row['given_by'] ?? '',
        $row['date_of_submission'] ?? '',
        $row['depatment_section'] ?? '',
        $row['incident_description'] ?? '',
        $row['main_error_category'] ?? '',
        $row['sub_error_categor'] ?? '',
        $row['active_error'] ?? '',
        $row['latent_error'] ?? '',
        $row['cognitive_error'] ?? '',
        $row['non_cognitive_error'] ?? '',
        $row['root_cause'] ?? '',
        $row['avg_impact_score'] ?? '',
        $row['avg_freq_score'] ?? '',
        $row['avg_risk_score'] ?? '',
        $row['immediate_correction'] ?? '',
        $row['corrective_action'] ?? '',
        $row['preventive_action'] ?? '',
        $row['patient_consequences'] ?? '',
        $row['risk_discription1'] ?? '',
        $row['impact_score1'] ?? '',
        $row['freq_score1'] ?? '',
        $row['risk_discription2'] ?? '',
        $row['impact_score2'] ?? '',
        $row['freq_score2'] ?? '',
        $row['risk_discription3'] ?? '',
        $row['impact_score3'] ?? '',
        $row['freq_score3'] ?? '',
        $row['risk_discription4'] ?? '',
        $row['impact_score4'] ?? '',
        $row['freq_score4'] ?? '',
        $row['risk_discription5'] ?? '',
        $row['impact_score5'] ?? '',
        $row['freq_score5'] ?? ''
    ]);
}

fclose($output);
pg_close($conn);
?>