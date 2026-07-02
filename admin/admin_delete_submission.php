<?php

session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}
include '../connection.php';
header('Content-Type: application/json');

$id = trim($_POST['id'] ?? '');

if (empty($id) || !is_numeric($id)) {
    echo json_encode(['success' => false, 'error' => 'Invalid record ID.']);
    exit;
}

$result = pg_query_params($conn,
    "DELETE FROM feedback_complaint_data WHERE id = $1",
    [$id]
);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    error_log('Delete submission error: ' . pg_last_error($conn));
    echo json_encode(['success' => false, 'error' => 'Could not delete record.']);
}

pg_close($conn);
?>