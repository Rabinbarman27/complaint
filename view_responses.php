<?php

session_start();
if (!isset($_SESSION['employee_id'])) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}
include 'connection.php';
header('Content-Type: application/json');

$sql = "SELECT id, form_no, Operation, Given_by, Date_of_submission, Depatment_section,
               Incident_description, Main_Error_category, Sub_Error_categor,
               avg_impact_score, avg_freq_score, avg_risk_score,
               root_cause, immediate_correction, corrective_action,
               preventive_action, patient_consequences
        FROM feedback_complaint_data
        ORDER BY id DESC";

$result = pg_query($conn, $sql);

if (!$result) {
    error_log("SQL Error: " . pg_last_error($conn));
    echo json_encode(['success' => false, 'error' => 'Could not load responses.']);
    pg_close($conn);
    exit;
}

$rows = pg_fetch_all($result) ?: [];

echo json_encode(['success' => true, 'data' => $rows]);

pg_close($conn);
