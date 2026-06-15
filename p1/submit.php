<?php
include 'connection.php';

$Operation           = mysqli_real_escape_string($conn, $_POST["operation"] ?? "");
$Given_by            = mysqli_real_escape_string($conn, $_POST["given_by"] ?? "");
$Date_of_submission  = mysqli_real_escape_string($conn, $_POST["date"] ?? "");
$Depatment_Section   = mysqli_real_escape_string($conn, $_POST["department_section"] ?? "");
$incident_description = mysqli_real_escape_string($conn, $_POST["incident_description"] ?? "");
$main_category       = mysqli_real_escape_string($conn, $_POST["main_category"] ?? "");
$active_error        = isset($_POST["active_error"]) ? "yes" : "no";
$latent_error        = isset($_POST["latent_error"]) ? "yes" : "no";
$cognitive_error     = isset($_POST["cognitive_error"]) ? "yes" : "no";
$non_cognitive_error = isset($_POST["non_cognitive_error"]) ? "yes" : "no";
$root_cause          = mysqli_real_escape_string($conn, $_POST["root_cause"] ?? "");
$freq_score          = mysqli_real_escape_string($conn, $_POST["freq_score"] ?? "");
$impact_score        = mysqli_real_escape_string($conn, $_POST["impact_score"] ?? "");
$corrective_action   = mysqli_real_escape_string($conn, $_POST["corrective_action"] ?? "");
$preventive_action   = mysqli_real_escape_string($conn, $_POST["preventive_action"] ?? "");
$patient_consequences = mysqli_real_escape_string($conn, $_POST["patient_consequences"] ?? "");
$immediate_correction = mysqli_real_escape_string($conn, $_POST["immediate_correction"] ?? "");
$sub_error = "";

if ($main_category === "pre") {
    $sub_error = mysqli_real_escape_string($conn, $_POST["pre_analytic_error"] ?? "");
} elseif ($main_category === "analytic") {
    $sub_error = mysqli_real_escape_string($conn, $_POST["analytic_error"] ?? "");
} elseif ($main_category === "post") {
    $sub_error = mysqli_real_escape_string($conn, $_POST["post_analytic_error"] ?? "");
} elseif ($main_category === "others") {
    $sub_error = mysqli_real_escape_string($conn, $_POST["no_lab_error"] ?? "");
}


if (
    empty($Operation) || empty($Given_by) || empty($Date_of_submission) ||
    empty($Depatment_Section) || empty($incident_description) ||
    empty($main_category) || empty($sub_error) ||
    empty($root_cause) || empty($freq_score) || empty($impact_score) ||
    empty($corrective_action) || empty($preventive_action) || 
    empty($patient_consequences) || empty($immediate_correction)
) {
    die("<center><h3>Please fill all required fields!</h3></center>");
}

$sql = "INSERT INTO feedback_complaint_data 
        (Operation, Given_by, Date_of_submission, Depatment_section, 
         Incident_description, Main_Error_category, Sub_Error_categor,
         active_error, latent_error, cognitive_error, non_cognitive_error,
         Root_cause, Risk_Score, immediate_correction, corrective_action, 
         preventive_action, patient_consequences)
        VALUES
        ('$Operation','$Given_by','$Date_of_submission','$Depatment_Section',
         '$incident_description','$main_category','$sub_error',
         '$active_error','$latent_error','$cognitive_error','$non_cognitive_error',
         '$root_cause','$freq_score','$impact_score',
         '$corrective_action','$preventive_action','$patient_consequences')";
if (mysqli_query($conn, $sql)) {
    echo "<center><h1>Feedback Updated </h1></center>";
} else {
    echo "<h1>" . mysqli_error($conn) . "</h1>";
}
mysqli_close($conn);
