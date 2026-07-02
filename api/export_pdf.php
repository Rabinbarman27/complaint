<?php

session_start();
if (!isset($_SESSION['employee_id']) && !isset($_SESSION['admin_id'])) {
    http_response_code(401);
    die("Unauthorized. Please log in.");
}
include '../connection.php';

$from = trim($_GET['from_date'] ?? '');
$to   = trim($_GET['to_date'] ?? '');

// ---- Validate dates ----
if (empty($from) || empty($to)) {
    die("Invalid date range.");
}

$from_dt = DateTime::createFromFormat('Y-m-d', $from);
$to_dt   = DateTime::createFromFormat('Y-m-d', $to);

if (!$from_dt || $from_dt->format('Y-m-d') !== $from ||
    !$to_dt   || $to_dt->format('Y-m-d')   !== $to) {
    die("Invalid date format.");
}

if ($from_dt > $to_dt) {
    die("From date cannot be after To date.");
}

// ---- Query using pg_query_params (SQL injection safe) ----
$sql = "SELECT * FROM feedback_complaint_data 
        WHERE Date_of_submission BETWEEN $1 AND $2
        ORDER BY Date_of_submission ASC";

$result = pg_query_params($conn, $sql, [$from, $to]);

if (!$result) {
    error_log("Export PDF query failed: " . pg_last_error($conn));
    die("Something went wrong. Please try again.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback Report <?= htmlspecialchars($from) ?> to <?= htmlspecialchars($to) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
            color: #2d3748;
        }
        h2 {
            text-align: center;
            color: #1a365d;
            margin-bottom: 5px;
        }
        p.range {
            text-align: center;
            color: #4a5568;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            background-color: #2b6cb0;
            color: white;
            padding: 6px 8px;
            text-align: left;
            font-size: 10px;
        }
        td {
            border: 1px solid #cbd5e0;
            padding: 5px 8px;
            vertical-align: top;
        }
        tr:nth-child(even) td {
            background-color: #f7fafc;
        }
        .entry {
            page-break-inside: avoid;
            margin-bottom: 30px;
            border: 1px solid #bee3f8;
            border-radius: 6px;
            padding: 15px;
        }
        .entry h3 {
            color: #2b6cb0;
            margin-bottom: 10px;
            border-bottom: 1px solid #bee3f8;
            padding-bottom: 5px;
        }
        .entry table {
            margin-bottom: 10px;
        }
        .label {
            font-weight: bold;
            color: #4a5568;
            width: 200px;
            white-space: nowrap;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align:center; margin-bottom:20px;">
    <button onclick="window.print()" style="padding:10px 30px; background:#2b6cb0; color:white; border:none; border-radius:5px; font-size:14px; cursor:pointer;">
        Print / Save as PDF
    </button>
</div>

<h2>Complaint & Feedback Report</h2>
<p class="range">Date Range: <?= htmlspecialchars($from) ?> to <?= htmlspecialchars($to) ?></p>

<?php
$count = 0;
while ($row = pg_fetch_assoc($result)):
    $count++;
?>
<div class="entry">
    <h3>Entry #<?= $count ?> — <?= htmlspecialchars($row['date_of_submission'] ?? '') ?></h3>
    <table>
        <tr><td class="label">Form No.</td><td><?= htmlspecialchars($row['form_no'] ?? '') ?></td></tr>
        <tr><td class="label">Operation</td><td><?= htmlspecialchars($row['operation'] ?? '') ?></td></tr>
        <tr><td class="label">Given By</td><td><?= htmlspecialchars($row['given_by'] ?? '') ?></td></tr>
        <tr><td class="label">Date</td><td><?= htmlspecialchars($row['date_of_submission'] ?? '') ?></td></tr>
        <tr><td class="label">Department / Section</td><td><?= htmlspecialchars($row['depatment_section'] ?? '') ?></td></tr>
        <tr><td class="label">Incident Description</td><td><?= htmlspecialchars($row['incident_description'] ?? '') ?></td></tr>
        <tr><td class="label">Main Error Category</td><td><?= htmlspecialchars($row['main_error_category'] ?? '') ?></td></tr>
        <tr><td class="label">Sub Error Category</td><td><?= htmlspecialchars($row['sub_error_categor'] ?? '') ?></td></tr>
        <tr><td class="label">Active Error</td><td><?= htmlspecialchars($row['active_error'] ?? '') ?></td></tr>
        <tr><td class="label">Latent Error</td><td><?= htmlspecialchars($row['latent_error'] ?? '') ?></td></tr>
        <tr><td class="label">Cognitive Error</td><td><?= htmlspecialchars($row['cognitive_error'] ?? '') ?></td></tr>
        <tr><td class="label">Non-Cognitive Error</td><td><?= htmlspecialchars($row['non_cognitive_error'] ?? '') ?></td></tr>
        <tr><td class="label">Root Cause</td><td><?= htmlspecialchars($row['root_cause'] ?? '') ?></td></tr>
        <tr><td class="label">Avg Impact Score</td><td><?= htmlspecialchars($row['avg_impact_score'] ?? '') ?></td></tr>
        <tr><td class="label">Avg Frequency Score</td><td><?= htmlspecialchars($row['avg_freq_score'] ?? '') ?></td></tr>
        <tr><td class="label">Avg Risk Score</td><td><?= htmlspecialchars($row['avg_risk_score'] ?? '') ?></td></tr>
        <tr><td class="label">Immediate Correction</td><td><?= htmlspecialchars($row['immediate_correction'] ?? '') ?></td></tr>
        <tr><td class="label">Corrective Action</td><td><?= htmlspecialchars($row['corrective_action'] ?? '') ?></td></tr>
        <tr><td class="label">Preventive Action</td><td><?= htmlspecialchars($row['preventive_action'] ?? '') ?></td></tr>
        <tr><td class="label">Patient Consequences</td><td><?= htmlspecialchars($row['patient_consequences'] ?? '') ?></td></tr>
    </table>

    <strong>Risk Scores:</strong>
    <table>
        <tr>
            <th>Risk Description</th>
            <th>Impact Score</th>
            <th>Freq Score</th>
        </tr>
        <?php for ($i = 1; $i <= 5; $i++): ?>
        <?php if (!empty($row["risk_discription$i"])): ?>
        <tr>
            <td><?= htmlspecialchars($row["risk_discription$i"] ?? '') ?></td>
            <td><?= htmlspecialchars($row["impact_score$i"] ?? '') ?></td>
            <td><?= htmlspecialchars($row["freq_score$i"] ?? '') ?></td>
        </tr>
        <?php endif; ?>
        <?php endfor; ?>
    </table>
</div>
<?php endwhile; ?>

<?php if ($count === 0): ?>
    <p style="text-align:center; color:#e53e3e;">No records found for the selected date range.</p>
<?php endif; ?>

<?php pg_close($conn); ?>
</body>
</html>