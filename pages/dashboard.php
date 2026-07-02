<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header('Location: ../index.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint &amp; Feedback Dashboard</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .logout {
            display: inline-block;
            background: #303655;
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            width: 90px;
            text-align: center;
            box-sizing: border-box;
            line-height: normal;
            vertical-align: middle;
            margin-top: 10px;
        }

        .logout:hover {
            background: #2f5494;
        }
    </style>
</head>

<body>
    <div class="container dashboard-container">
        <img src="../BBH-logo-1-1B.png" alt="" srcset="">
        <h1>Complaint and Feedback Dashboard</h1>

        <div class="dashboard-grid">

            <div class="dashboard-card">
                <h2>File a Complaint/feedback</h2>
                <p>Submit a new complaint or feedback form.</p>
                <a href="form.php" class="dashboard-btn">Go to Form</a>
            </div>

            <div class="dashboard-card">
                <h2>Check submissions</h2>
                <p>See all submitted complaint and feedback records.</p>
                <a href="responses.php" class="dashboard-btn">View Responses</a>
            </div>

            <div class="dashboard-card">
                <h2>Export Data</h2>
                <p>Download submitted records as Excel, CSV, or PDF.</p>
                <button type="button" class="dashboard-btn" onclick="openExportModal()">Export Data</button>
            </div>

        </div>
        <button onclick="handleLogout()" class="logout">Logout</button>
    </div>

    <div id="exportModal"
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
        <div style="background:#fff; margin:10% auto; padding:30px; border-radius:8px; max-width:400px;">
            <h3 style="margin-bottom:20px; color:#1a365d;">Export Feedback Data</h3>
            <label>From:</label><br>
            <input type="date" id="export_from" style="margin-bottom:15px; width:100%;"><br>
            <label>To:</label><br>
            <input type="date" id="export_to" style="margin-bottom:15px; width:100%;"><br>
            <label>Format:</label><br>
            <select id="export_format" style="margin-bottom:20px; width:100%;">
                <option value="excel">Excel (.xlsx)</option>
                <option value="csv">CSV (.csv)</option>
                <option value="pdf">PDF (.pdf)</option>
            </select><br>
            <button type="button" onclick="exportData()">Download</button>
            <button type="button" onclick="closeExportModal()">Cancel</button>
        </div>
    </div>

    <script src="../js/auth-guard.js"></script>
    <script src="../js/dashboard.js"></script>

</body>

</html>