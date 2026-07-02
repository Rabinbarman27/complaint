<?php

session_start();
header('Content-Type: application/json');

if (isset($_SESSION['admin_id'])) {
    echo json_encode(['loggedIn' => true, 'admin_id' => $_SESSION['admin_id']]);
} else {
    echo json_encode(['loggedIn' => false]);
}
?>