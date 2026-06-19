<?php
include 'connection.php';

$from = pg_escape_string($conn, $_GET['from_date'] ?? '');
$to   = pg_escape_string($conn, $_GET['to_date'] ?? '');

if (empty($from) || empty($to)) {
    die("Invalid date range.");
}

$sql = "SELECT * FROM feedback_complaint_data 
        WHERE Date_of_submission BETWEEN '$from' AND '$to'
        ORDER BY Date_of_submission ASC";

$result = pg_query($conn, $sql);

if (!$result) {
    die("Query failed: " . pg_last_error($conn));
}

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=feedback_data_{$from}_to_{$to}.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Operation</th>
        <th>Given By</th>
        <th>Date</th>
        <th>Department/Section</th>
        <th>Incident Description</th>
        <th>Main Error Category</th>
        <th>Sub Error Category</th>
        <th>Active Error</th>
        <th>Latent Error</th>
        <th>Cognitive Error</th>
        <th>Non-Cognitive Error</th>
        <th>Root Cause</th>
        <th>Avg Impact Score</th>
        <th>Avg Freq Score</th>
        <th>Immediate Correction</th>
        <th>Corrective Action</th>
        <th>Preventive Action</th>
        <th>Patient Consequences</th>
        <th>Risk Description 1</th>
        <th>Impact Score 1</th>
        <th>Freq Score 1</th>
        <th>Risk Description 2</th>
        <th>Impact Score 2</th>
        <th>Freq Score 2</th>
        <th>Risk Description 3</th>
        <th>Impact Score 3</th>
        <th>Freq Score 3</th>
        <th>Risk Description 4</th>
        <th>Impact Score 4</th>
        <th>Freq Score 4</th>
        <th>Risk Description 5</th>
        <th>Impact Score 5</th>
        <th>Freq Score 5</th>
    </tr>
    <?php while ($row = pg_fetch_assoc($result)): ?>
    <tr>
        <td><?= htmlspecialchars($row['id'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['operation'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['given_by'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['date_of_submission'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['depatment_section'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['incident_description'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['main_error_category'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['sub_error_categor'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['active_error'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['latent_error'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['cognitive_error'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['non_cognitive_error'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['root_cause'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['avg_impact_score'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['avg_freq_score'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['immediate_correction'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['corrective_action'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['preventive_action'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['patient_consequences'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['risk_discription1'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['impact_score1'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['freq_score1'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['risk_discription2'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['impact_score2'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['freq_score2'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['risk_discription3'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['impact_score3'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['freq_score3'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['risk_discription4'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['impact_score4'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['freq_score4'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['risk_discription5'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['impact_score5'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['freq_score5'] ?? '') ?></td>
    </tr>
    <?php endwhile; ?>
</table>
<?php pg_close($conn); ?>