<?php
include 'connection.php';
$result = mysqli_query($conn, "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'Feedback_data' AND TABLE_NAME = 'feedback_complaint_data'");
$row = mysqli_fetch_assoc($result);
$next_id = $row['AUTO_INCREMENT'];
echo "FORM-" . str_pad($next_id, 4, "0", STR_PAD_LEFT);
mysqli_close($conn);
?>