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

$result = pg_query($conn, "SELECT id, employee_id FROM employees ORDER BY id ASC");

if (!$result) {
    error_log("Get employees error: " . pg_last_error($conn));
    echo json_encode(['success' => false, 'error' => 'Could not load employees.']);
    pg_close($conn);
    exit;
}

$rows = pg_fetch_all($result) ?: [];
echo json_encode(['success' => true, 'data' => $rows]);
pg_close($conn);
?>