<?php
$conn = mysqli_connect("localhost", "root", "", "Feedback_data");

if(!$conn)
    die("Connection error: " . mysqli_connect_error());
?>