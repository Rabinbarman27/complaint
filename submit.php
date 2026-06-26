<?php 
include 'connection.php';
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't leak errors

// pg_escape_string instead of mysqli_real_escape_string
$Operation            = pg_escape_string($conn, $_POST["operation"] ?? "");
$form_no              = pg_escape_string($conn, $_POST["form_no"] ?? "");
$Given_by             = pg_escape_string($conn, $_POST["given_by"] ?? "");
$Date_of_submission   = pg_escape_string($conn, $_POST["date"] ?? "");
$Depatment_Section    = pg_escape_string($conn, $_POST["department_section"] ?? "");
$incident_description = pg_escape_string($conn, $_POST["incident_description"] ?? "");
$main_category        = pg_escape_string($conn, $_POST["main_category"] ?? "");
$active_error         = isset($_POST["active_error"]) ? "yes" : "no";
$latent_error         = isset($_POST["latent_error"]) ? "yes" : "no";
$cognitive_error      = isset($_POST["cognitive_error"]) ? "yes" : "no";
$non_cognitive_error  = isset($_POST["non_cognitive_error"]) ? "yes" : "no";
$root_cause           = pg_escape_string($conn, $_POST["root_cause"] ?? "");
$immediate_correction = pg_escape_string($conn, $_POST["immediate_correction"] ?? "");
$corrective_action    = pg_escape_string($conn, $_POST["corrective_action"] ?? "");
$preventive_action    = pg_escape_string($conn, $_POST["preventive_action"] ?? "");
$patient_consequences = pg_escape_string($conn, $_POST["patient_consequences"] ?? "");

$avg_impact_score     = pg_escape_string($conn, $_POST["impact_score"] ?? "");
$avg_freq_score       = pg_escape_string($conn, $_POST["freq_score"] ?? "");
$avg_risk_score       = pg_escape_string($conn, $_POST["risk_score"] ?? "");

$Risk_discription1    = pg_escape_string($conn, $_POST["Risk_discription1"] ?? "");
$Risk_discription2    = pg_escape_string($conn, $_POST["Risk_discription2"] ?? "");
$Risk_discription3    = pg_escape_string($conn, $_POST["Risk_discription3"] ?? "");
$Risk_discription4    = pg_escape_string($conn, $_POST["Risk_discription4"] ?? "");
$Risk_discription5    = pg_escape_string($conn, $_POST["Risk_discription5"] ?? "");
$impact_score1        = pg_escape_string($conn, $_POST["impact_score1"] ?? "");
$impact_score2        = pg_escape_string($conn, $_POST["impact_score2"] ?? "");
$impact_score3        = pg_escape_string($conn, $_POST["impact_score3"] ?? "");
$impact_score4        = pg_escape_string($conn, $_POST["impact_score4"] ?? "");
$impact_score5        = pg_escape_string($conn, $_POST["impact_score5"] ?? "");
$freq_score1          = pg_escape_string($conn, $_POST["freq_score1"] ?? "");
$freq_score2          = pg_escape_string($conn, $_POST["freq_score2"] ?? "");
$freq_score3          = pg_escape_string($conn, $_POST["freq_score3"] ?? "");
$freq_score4          = pg_escape_string($conn, $_POST["freq_score4"] ?? "");
$freq_score5          = pg_escape_string($conn, $_POST["freq_score5"] ?? "");

$sub_error = "";
if ($main_category === "pre") {
    $sub_error = pg_escape_string($conn, $_POST["pre_analytic_error"] ?? "");
} elseif ($main_category === "analytic") {
    $sub_error = pg_escape_string($conn, $_POST["analytic_error"] ?? "");
} elseif ($main_category === "post") {
    $sub_error = pg_escape_string($conn, $_POST["post_analytic_error"] ?? "");
} elseif ($main_category === "others") {
    $sub_error = pg_escape_string($conn, $_POST["no_lab_error"] ?? "");
}

// ---- VALIDATION ----
// Matches the current client-side rules in index.js:
//   Required: operation, given_by, date, department_section,
//             incident_description, main_category (+ sub_error),
//             root_cause, immediate_correction, corrective_action,
//             at least Risk row 1 (impact_score1 + freq_score1)
//   Optional: preventive_action, patient_consequences
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
// preventive_action and patient_consequences are optional — no checks here

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

// Only validate patient_consequences value IF it was actually provided,
// since it's now optional.
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
    'Impact Score 1' => $impact_score1, 'Freq Score 1' => $freq_score1,
    'Impact Score 2' => $impact_score2, 'Freq Score 2' => $freq_score2,
    'Impact Score 3' => $impact_score3, 'Freq Score 3' => $freq_score3,
    'Impact Score 4' => $impact_score4, 'Freq Score 4' => $freq_score4,
    'Impact Score 5' => $impact_score5, 'Freq Score 5' => $freq_score5,
];
foreach ($range_score_fields as $label => $value) {
    if ($value !== "" && (!is_numeric($value) || $value < 0 || $value > 5))
        $errors[] = "$label must be a number between 0 and 5";
}

// avg_risk_score is impact x freq, so its range is 0-25, not 0-5
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

// ---- Convert empty numeric strings to NULL for PostgreSQL ----
// PostgreSQL (unlike MySQL) rejects '' for integer columns, so any
// blank risk-score field must be inserted as the literal NULL keyword
// instead of an empty quoted string.
function toNullableInt($value) {
    return ($value === "" || $value === null) ? "NULL" : "'" . $value . "'";
}

$avg_impact_score_sql = toNullableInt($avg_impact_score);
$avg_freq_score_sql   = toNullableInt($avg_freq_score);
$avg_risk_score_sql   = toNullableInt($avg_risk_score);
$impact_score1_sql    = toNullableInt($impact_score1);
$impact_score2_sql    = toNullableInt($impact_score2);
$impact_score3_sql    = toNullableInt($impact_score3);
$impact_score4_sql    = toNullableInt($impact_score4);
$impact_score5_sql    = toNullableInt($impact_score5);
$freq_score1_sql      = toNullableInt($freq_score1);
$freq_score2_sql      = toNullableInt($freq_score2);
$freq_score3_sql      = toNullableInt($freq_score3);
$freq_score4_sql      = toNullableInt($freq_score4);
$freq_score5_sql      = toNullableInt($freq_score5);

// ---- INSERT (pg_query instead of mysqli_query) ----
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
        ('$form_no','$Operation','$Given_by','$Date_of_submission','$Depatment_Section',
         '$incident_description','$main_category','$sub_error',
         '$active_error','$latent_error','$cognitive_error','$non_cognitive_error',
         '$root_cause',$avg_impact_score_sql,$avg_freq_score_sql,$avg_risk_score_sql,
         '$immediate_correction','$corrective_action','$preventive_action','$patient_consequences',
         '$Risk_discription1',$impact_score1_sql,$freq_score1_sql,
         '$Risk_discription2',$impact_score2_sql,$freq_score2_sql,
         '$Risk_discription3',$impact_score3_sql,$freq_score3_sql,
         '$Risk_discription4',$impact_score4_sql,$freq_score4_sql,
         '$Risk_discription5',$impact_score5_sql,$freq_score5_sql)";

$result = pg_query($conn, $sql);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    // Production-safe: log the real Postgres error server-side only,
    // never expose raw DB errors to the client.
    error_log("SQL Error: " . pg_last_error($conn));
    echo json_encode(['success' => false, 'error' => 'Something went wrong. Please try again.']);
}

pg_close($conn);
?>