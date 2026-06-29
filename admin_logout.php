<?php

session_start();
unset($_SESSION['admin_id']);
session_destroy();
header('Content-Type: application/json');
echo json_encode(['success' => true]);
?>