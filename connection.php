<?php

$conn = pg_connect("host=localhost port=5432 dbname=Feedback_data user=postgres password=KINGVEGETA12345#");

if (!$conn) {
    die("Connection failed: ");
}
?>
