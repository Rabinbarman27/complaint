document.addEventListener('DOMContentLoaded', function () {
    const statusEl = document.getElementById('responses_status');
    const table = document.getElementById('responses_table');
    const tbody = document.getElementById('responses_body');

    fetch('view_responses.php')
        .then(res => {
            if (res.status === 401) {
                window.location.href = 'index.html';
                return;
            }
            return res.json();
        })
        .then(result => {
            if (!result) return; // redirect already happened

            if (!result.success) {
                statusEl.textContent = result.error || 'Could not load responses.';
                return;
            }

            const rows = result.data;

            if (!rows || rows.length === 0) {
                statusEl.textContent = 'No responses submitted yet.';
                return;
            }

            statusEl.style.display = 'none';
            table.style.display = 'table';

            rows.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${escapeHtml(row.form_no)}</td>
                    <td>${escapeHtml(row.operation)}</td>
                    <td>${escapeHtml(row.given_by)}</td>
                    <td>${escapeHtml(row.date_of_submission)}</td>
                    <td>${escapeHtml(row.depatment_section)}</td>
                    <td>${escapeHtml(row.incident_description)}</td>
                    <td>${escapeHtml(row.main_error_category)}</td>
                    <td>${escapeHtml(row.sub_error_categor)}</td>
                    <td>${escapeHtml(row.avg_impact_score)}</td>
                    <td>${escapeHtml(row.avg_freq_score)}</td>
                    <td>${escapeHtml(row.avg_risk_score)}</td>
                    <td>${escapeHtml(row.patient_consequences)}</td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(err => {
            console.error('Failed to load responses:', err);
            statusEl.textContent = 'Network error while loading responses.';
        });
});

function escapeHtml(value) {
    if (value === null || value === undefined) return '';
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}