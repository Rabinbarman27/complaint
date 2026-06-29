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

$employee_id = trim($_POST['employee_id'] ?? '');
$password    = $_POST['password'] ?? '';
if (!preg_match('/^[a-zA-Z0-9_-]+$/', $employee_id)) {
    echo json_encode(['success' => false, 'error' => 'Employee ID can only contain letters, numbers, hyphens and underscores.']);
    exit;
}

if (empty($employee_id) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'Employee ID and password are required.']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters.']);
    exit;
}

$check = pg_query_params($conn, "SELECT id FROM employees WHERE employee_id = $1", [$employee_id]);
if (pg_num_rows($check) > 0) {
    echo json_encode(['success' => false, 'error' => 'Employee ID already exists.']);
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);
$result = pg_query_params($conn,
    "INSERT INTO employees (employee_id, password_hash) VALUES ($1, $2)",
    [$employee_id, $password_hash]
);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    error_log('Add employee error: ' . pg_last_error($conn));
    echo json_encode(['success' => false, 'error' => 'Something went wrong.']);
}

pg_close($conn);
?>