<?php

session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}
include 'connection.php';
header('Content-Type: application/json');

$result = pg_query(
    $conn,
    "SELECT form_no, operation, given_by, date_of_submission, depatment_section,
            incident_description, main_error_category, sub_error_categor,
            root_cause, avg_impact_score, avg_freq_score, avg_risk_score,
            immediate_correction, corrective_action, preventive_action, 
            patient_consequences, id
     FROM feedback_complaint_data
     ORDER BY id DESC"
);
if (!$result) {
    error_log("Get submissions error: " . pg_last_error($conn));
    echo json_encode(['success' => false, 'error' => 'Could not load submissions.']);
    pg_close($conn);
    exit;
}

$rows = pg_fetch_all($result) ?: [];
echo json_encode(['success' => true, 'data' => $rows]);
pg_close($conn);
