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
error_reporting(E_ALL);
ini_set('display_errors', 0);

$employee_id = trim($_POST['employee_id'] ?? '');
$password    = $_POST['password'] ?? '';

if (empty($employee_id) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'Employee ID and password are required.']);
    exit;
}

// Hash the password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Check if employee_id already exists
$check = pg_query_params($conn, "SELECT id FROM employees WHERE employee_id = $1", [$employee_id]);
if (pg_num_rows($check) > 0) {
    echo json_encode(['success' => false, 'error' => 'Employee ID already exists.']);
    exit;
}

// Insert
$result = pg_query_params($conn, "INSERT INTO employees (employee_id, password_hash) VALUES ($1, $2)", [$employee_id, $password_hash]);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    error_log('Add employee error: ' . pg_last_error($conn));
    echo json_encode(['success' => false, 'error' => 'Something went wrong. Please try again.']);
}

pg_close($conn);
?>