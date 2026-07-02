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

$employee_id = trim($_POST['employee_id'] ?? '');

if (empty($employee_id)) {
    echo json_encode(['success' => false, 'error' => 'Employee ID is required.']);
    exit;
}

$result = pg_query_params($conn,
    "DELETE FROM employees WHERE employee_id = $1",
    [$employee_id]
);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    error_log('Delete employee error: ' . pg_last_error($conn));
    echo json_encode(['success' => false, 'error' => 'Could not delete employee.']);
}

pg_close($conn);
?>