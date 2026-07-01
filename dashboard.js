// ── Export Modal ──────────────────────────────────────────
function openExportModal() {
    document.getElementById('exportModal').style.display = 'block';
}

function closeExportModal() {
    document.getElementById('exportModal').style.display = 'none';
}

function exportData() {
    const from = document.getElementById('export_from').value;
    const to = document.getElementById('export_to').value;
    const format = document.getElementById('export_format').value;

    if (!from || !to) {
        alert('Please select both From and To dates.');
        return;
    }
    if (new Date(from) > new Date(to)) {
        alert('From date cannot be after To date.');
        return;
    }

    if (format === 'excel') {
        window.location.href = `export_excel.php?from_date=${from}&to_date=${to}`;
    } else if (format === 'csv') {
        window.location.href = `export_csv.php?from_date=${from}&to_date=${to}`;
    } else if (format === 'pdf') {
        window.location.href = `export_pdf.php?from_date=${from}&to_date=${to}`;
    }

    closeExportModal();
}

// Close modal if user clicks outside it
window.onclick = function (e) {
    const modal = document.getElementById('exportModal');
    if (e.target === modal) closeExportModal();
}
function handleLogout() {
    fetch('logoutdashboardemployee.php', { method: 'POST' })
        .then(() => {
            window.location.href = 'index.html';
        })
        .catch(() => {
            window.location.href = 'index.html';
        });
}