<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

if (!isset($_SESSION['employee_id']) && !isset($_SESSION['admin_id'])) {
    http_response_code(401);
    exit;
}

include '../connection.php';

$result = pg_query($conn, "SELECT last_value + 1 AS next_id FROM feedback_complaint_data_id_seq");

if (!$result) {
    error_log("get_form_number error: " . pg_last_error($conn));
    echo '0001';
    pg_close($conn);
    exit;
}

$row = pg_fetch_assoc($result);
$next_id = $row['next_id'] ?? 1;

echo str_pad($next_id, 4, "0", STR_PAD_LEFT);

pg_close($conn);
?>