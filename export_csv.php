<?php

include 'connection.php';

$from = pg_escape_string($conn, $_GET['from_date'] ?? '');
$to   = pg_escape_string($conn, $_GET['to_date'] ?? '');

if (empty($from) || empty($to)) {
    die("Invalid date range.");
}

$sql = "SELECT * FROM feedback_complaint_data 
        WHERE Date_of_submission BETWEEN '$from' AND '$to'
        ORDER BY Date_of_submission ASC";

$result = pg_query($conn, $sql);

if (!$result) {
    die("Query failed: " . pg_last_error($conn));
}

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=feedback_data_{$from}_to_{$to}.csv");
header("Pragma: no-cache");
header("Expires: 0");

$output = fopen("php://output", "w");

// Header row
fputcsv($output, [
    'ID', 'Operation', 'Given By', 'Date', 'Department/Section',
    'Incident Description', 'Main Error Category', 'Sub Error Category',
    'Active Error', 'Latent Error', 'Cognitive Error', 'Non-Cognitive Error',
    'Root Cause', 'Avg Impact Score', 'Avg Freq Score',
    'Immediate Correction', 'Corrective Action', 'Preventive Action', 'Patient Consequences',
    'Risk Description 1', 'Impact Score 1', 'Freq Score 1',
    'Risk Description 2', 'Impact Score 2', 'Freq Score 2',
    'Risk Description 3', 'Impact Score 3', 'Freq Score 3',
    'Risk Description 4', 'Impact Score 4', 'Freq Score 4',
    'Risk Description 5', 'Impact Score 5', 'Freq Score 5'
]);

// Data rows — pg_fetch_assoc instead of mysqli_fetch_assoc
while ($row = pg_fetch_assoc($result)) {
    fputcsv($output, [
        $row['id'] ?? '',
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