<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);
session_destroy();
header('Content-Type: application/json');
echo json_encode(['success' => true]);
?>