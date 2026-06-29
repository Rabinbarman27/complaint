<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

include 'connection.php';
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

// ---- Collect POST data (no pg_escape_string needed with parameterized queries) ----
$Operation            = trim($_POST["operation"] ?? "");
$form_no              = trim($_POST["form_no"] ?? "");
$Given_by             = trim($_POST["given_by"] ?? "");
$Date_of_submission   = trim($_POST["date"] ?? "");
$Depatment_Section    = trim($_POST["department_section"] ?? "");
$incident_description = trim($_POST["incident_description"] ?? "");
$main_category        = trim($_POST["main_category"] ?? "");
$active_error         = isset($_POST["active_error"]) ? "yes" : "no";
$latent_error         = isset($_POST["latent_error"]) ? "yes" : "no";
$cognitive_error      = isset($_POST["cognitive_error"]) ? "yes" : "no";
$non_cognitive_error  = isset($_POST["non_cognitive_error"]) ? "yes" : "no";
$root_cause           = trim($_POST["root_cause"] ?? "");
$immediate_correction = trim($_POST["immediate_correction"] ?? "");
$corrective_action    = trim($_POST["corrective_action"] ?? "");
$preventive_action    = trim($_POST["preventive_action"] ?? "");
$patient_consequences = trim($_POST["patient_consequences"] ?? "");

$avg_impact_score     = trim($_POST["impact_score"] ?? "");
$avg_freq_score       = trim($_POST["freq_score"] ?? "");
$avg_risk_score       = trim($_POST["risk_score"] ?? "");

$Risk_discription1    = trim($_POST["Risk_discription1"] ?? "");
$Risk_discription2    = trim($_POST["Risk_discription2"] ?? "");
$Risk_discription3    = trim($_POST["Risk_discription3"] ?? "");
$Risk_discription4    = trim($_POST["Risk_discription4"] ?? "");
$Risk_discription5    = trim($_POST["Risk_discription5"] ?? "");
$impact_score1        = trim($_POST["impact_score1"] ?? "");
$impact_score2        = trim($_POST["impact_score2"] ?? "");
$impact_score3        = trim($_POST["impact_score3"] ?? "");
$impact_score4        = trim($_POST["impact_score4"] ?? "");
$impact_score5        = trim($_POST["impact_score5"] ?? "");
$freq_score1          = trim($_POST["freq_score1"] ?? "");
$freq_score2          = trim($_POST["freq_score2"] ?? "");
$freq_score3          = trim($_POST["freq_score3"] ?? "");
$freq_score4          = trim($_POST["freq_score4"] ?? "");
$freq_score5          = trim($_POST["freq_score5"] ?? "");

$sub_error = "";
if ($main_category === "pre") {
    $sub_error = trim($_POST["pre_analytic_error"] ?? "");
} elseif ($main_category === "analytic") {
    $sub_error = trim($_POST["analytic_error"] ?? "");
} elseif ($main_category === "post") {
    $sub_error = trim($_POST["post_analytic_error"] ?? "");
} elseif ($main_category === "others") {
    $sub_error = trim($_POST["no_lab_error"] ?? "");
}

// ---- VALIDATION ----
$errors = [];

if (empty($Operation))            $errors[] = "Operation is required";
if (empty($Given_by))             $errors[] = "Given by is required";
if (empty($Date_of_submission))   $errors[] = "Date is required";
if (empty($Depatment_Section))    $errors[] = "Department/Section is required";
if (empty($incident_description)) $errors[] = "Incident description is required";
if (empty($main_category))        $errors[] = "Error category is required";
if (empty($sub_error))            $errors[] = "Sub error category is required";
if (empty($root_cause))           $errors[] = "Root cause is required";
if (empty($immediate_correction)) $errors[] = "Immediate correction is required";
if (empty($corrective_action))    $errors[] = "Corrective action is required";

$valid_operations = ["Complaint", "Feedback"];
if (!empty($Operation) && !in_array($Operation, $valid_operations))
    $errors[] = "Invalid operation value";

$valid_given_by = ["Doctor", "Patient", "Attender", "Staff", "Others"];
if (!empty($Given_by) && !in_array($Given_by, $valid_given_by))
    $errors[] = "Invalid 'Given by' value";

$valid_departments = ["Bio_Chemistry", "Hematology", "Micro_Biology", "Clinical_Pathology", "Histo_Pathology", "Molecular_Biology"];
if (!empty($Depatment_Section) && !in_array($Depatment_Section, $valid_departments))
    $errors[] = "Invalid department/section value";

$valid_main_categories = ["pre", "analytic", "post", "others"];
if (!empty($main_category) && !in_array($main_category, $valid_main_categories))
    $errors[] = "Invalid error category value";

$valid_consequences = ["yes", "no"];
if (!empty($patient_consequences) && !in_array($patient_consequences, $valid_consequences))
    $errors[] = "Invalid patient consequences value";

if (!empty($Date_of_submission)) {
    $d = DateTime::createFromFormat('Y-m-d', $Date_of_submission);
    if (!$d || $d->format('Y-m-d') !== $Date_of_submission) {
        $errors[] = "Invalid date format";
    } elseif ($d > new DateTime()) {
        $errors[] = "Date cannot be in the future";
    }
}

$range_score_fields = [
    'Average Impact Score' => $avg_impact_score,
    'Average Frequency Score' => $avg_freq_score,
    'Impact Score 1' => $impact_score1,
    'Freq Score 1' => $freq_score1,
    'Impact Score 2' => $impact_score2,
    'Freq Score 2' => $freq_score2,
    'Impact Score 3' => $impact_score3,
    'Freq Score 3' => $freq_score3,
    'Impact Score 4' => $impact_score4,
    'Freq Score 4' => $freq_score4,
    'Impact Score 5' => $impact_score5,
    'Freq Score 5' => $freq_score5,
];
foreach ($range_score_fields as $label => $value) {
    if ($value !== "" && (!is_numeric($value) || $value < 0 || $value > 5))
        $errors[] = "$label must be a number between 0 and 5";
}

if ($avg_risk_score !== "" && (!is_numeric($avg_risk_score) || $avg_risk_score < 0 || $avg_risk_score > 25))
    $errors[] = "Average Risk Score must be a number between 0 and 25";

if ($impact_score1 === "" || $freq_score1 === "")
    $errors[] = "Risk Score row 1 (Impact and Frequency) is required";

$text_fields = [
    'Incident description' => $incident_description,
    'Root cause' => $root_cause,
    'Immediate correction' => $immediate_correction,
    'Corrective action' => $corrective_action,
    'Preventive action' => $preventive_action,
];
foreach ($text_fields as $label => $value) {
    if (strlen($value) > 5000)
        $errors[] = "$label is too long (max 5000 characters)";
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'error' => implode(", ", $errors)]);
    pg_close($conn);
    exit;
}

// ---- Convert empty strings to NULL for numeric PostgreSQL columns ----
function toNullable($value)
{
    return ($value === "" || $value === null) ? null : $value;
}

// ---- INSERT using pg_query_params (fully parameterized, SQL injection safe) ----
$sql = "INSERT INTO feedback_complaint_data 
        (form_no, Operation, Given_by, Date_of_submission, Depatment_section, 
         Incident_description, Main_Error_category, Sub_Error_categor,
         active_error, latent_error, cognitive_error, non_cognitive_error,
         Root_cause, avg_impact_score, avg_freq_score, avg_risk_score,
         immediate_correction, corrective_action, preventive_action, patient_consequences,
         Risk_discription1, impact_score1, freq_score1,
         Risk_discription2, impact_score2, freq_score2,
         Risk_discription3, impact_score3, freq_score3,
         Risk_discription4, impact_score4, freq_score4,
         Risk_discription5, impact_score5, freq_score5)
        VALUES
        ($1,$2,$3,$4,$5,
         $6,$7,$8,
         $9,$10,$11,$12,
         $13,$14,$15,$16,
         $17,$18,$19,$20,
         $21,$22,$23,
         $24,$25,$26,
         $27,$28,$29,
         $30,$31,$32,
         $33,$34,$35)";

$params = [
    $form_no,
    $Operation,
    $Given_by,
    $Date_of_submission,
    $Depatment_Section,
    $incident_description,
    $main_category,
    $sub_error,
    $active_error,
    $latent_error,
    $cognitive_error,
    $non_cognitive_error,
    $root_cause,
    toNullable($avg_impact_score),
    toNullable($avg_freq_score),
    toNullable($avg_risk_score),
    $immediate_correction,
    $corrective_action,
    toNullable($preventive_action),
    toNullable($patient_consequences),
    $Risk_discription1,
    toNullable($impact_score1),
    toNullable($freq_score1),
    $Risk_discription2,
    toNullable($impact_score2),
    toNullable($freq_score2),
    $Risk_discription3,
    toNullable($impact_score3),
    toNullable($freq_score3),
    $Risk_discription4,
    toNullable($impact_score4),
    toNullable($freq_score4),
    $Risk_discription5,
    toNullable($impact_score5),
    toNullable($freq_score5),
];

$result = pg_query_params($conn, $sql, $params);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    error_log("SQL Error: " . pg_last_error($conn));
    echo json_encode(['success' => false, 'error' => 'Something went wrong. Please try again.']);
}

pg_close($conn);
