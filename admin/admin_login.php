<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);
include '../connection.php';
header('Content-Type: application/json');

$admin_id = trim($_POST["admin_id"] ?? "");
$password  = $_POST["password"] ?? "";

if (empty($admin_id) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'Admin ID and password are required.']);
    pg_close($conn);
    exit;
}

$result = pg_query_params(
    $conn,
    "SELECT admin_id, password_hash FROM admins WHERE admin_id = $1",
    [$admin_id]
);

if (!$result) {
    error_log("Admin Login SQL Error: " . pg_last_error($conn));
    echo json_encode(['success' => false, 'error' => 'Something went wrong. Please try again.']);
    pg_close($conn);
    exit;
}

$row = pg_fetch_assoc($result);

$hashToCheck = $row ? $row['password_hash'] : '$2y$10$invalidsaltinvalidsaltinvalidsa';

if ($row && password_verify($password, $hashToCheck)) {
    $_SESSION['admin_id'] = $row['admin_id'];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid Admin ID or Password.']);
}

pg_close($conn);
