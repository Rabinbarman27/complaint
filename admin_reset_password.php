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
error_reporting(E_ALL);
ini_set('display_errors', 0);

$employee_id = trim($_POST['employee_id'] ?? '');
$new_password = $_POST['new_password'] ?? '';

if (empty($employee_id) || empty($new_password)) {
    echo json_encode(['success' => false, 'error' => 'Employee ID and new password are required.']);
    exit;
}

if (strlen($new_password) < 6) {
    echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters.']);
    exit;
}

// Check employee exists
$check = pg_query_params($conn, "SELECT id FROM employees WHERE employee_id = $1", [$employee_id]);
if (!$check || pg_num_rows($check) === 0) {
    echo json_encode(['success' => false, 'error' => 'Employee not found.']);
    pg_close($conn);
    exit;
}

$hash = password_hash($new_password, PASSWORD_DEFAULT);

$result = pg_query_params($conn,
    "UPDATE employees SET password_hash = $1 WHERE employee_id = $2",
    [$hash, $employee_id]
);

if ($result && pg_affected_rows($result) > 0) {
    echo json_encode(['success' => true]);
} else {
    error_log('Reset password error: ' . pg_last_error($conn));
    echo json_encode(['success' => false, 'error' => 'Something went wrong.']);
}

pg_close($conn);
?>