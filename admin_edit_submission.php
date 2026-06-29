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

$id    = trim($_POST['id'] ?? '');
$field = trim($_POST['field'] ?? '');
$value = trim($_POST['value'] ?? '');

// Whitelist of editable columns — prevents arbitrary column injection
$allowed_fields = [
    'operation', 'given_by', 'date_of_submission', 'depatment_section',
    'incident_description', 'main_error_category', 'sub_error_categor',
    'root_cause', 'immediate_correction', 'corrective_action',
    'preventive_action', 'patient_consequences'
];

if (empty($id) || !is_numeric($id)) {
    echo json_encode(['success' => false, 'error' => 'Invalid record ID.']);
    exit;
}

if (!in_array($field, $allowed_fields)) {
    echo json_encode(['success' => false, 'error' => 'Invalid field.']);
    exit;
}

// Build query safely — field name is whitelisted above so safe to interpolate
$result = pg_query_params($conn,
    "UPDATE feedback_complaint_data SET $field = $1 WHERE id = $2",
    [$value, $id]
);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    error_log('Edit submission error: ' . pg_last_error($conn));
    echo json_encode(['success' => false, 'error' => 'Could not update record.']);
}

pg_close($conn);
?>