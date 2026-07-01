<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);
include 'connection.php';
header('Content-Type: application/json');

$employee_id = trim($_POST["employee_id"] ?? "");
$password    = $_POST["password"] ?? "";

if (empty($employee_id) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'Employee ID and password are required.']);
    pg_close($conn);
    exit;
}

$result = pg_query_params($conn, 
    "SELECT employee_id, password_hash FROM employees WHERE employee_id = $1", 
    [$employee_id]
);

if (!$result) {
    error_log("Login SQL Error: " . pg_last_error($conn));
    echo json_encode(['success' => false, 'error' => 'Something went wrong. Please try again.']);
    pg_close($conn);
    exit;
}

$row = pg_fetch_assoc($result);

// Timing-safe: always run password_verify even if no row found
$hashToCheck = $row ? $row['password_hash'] : '$2y$10$invalidsaltinvalidsaltinvalidsa';

if ($row && password_verify($password, $hashToCheck)) {
    $_SESSION['employee_id'] = $row['employee_id'];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid Employee ID or Password.']);
}

pg_close($conn);
?>