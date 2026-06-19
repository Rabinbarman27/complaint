<?php
include 'connection.php';

$result = pg_query($conn, "SELECT last_value + 1 AS next_id FROM feedback_complaint_data_id_seq");
$row = pg_fetch_assoc($result);
$next_id = $row['next_id'];

echo "FORM-" . str_pad($next_id, 4, "0", STR_PAD_LEFT);

pg_close($conn);
?>