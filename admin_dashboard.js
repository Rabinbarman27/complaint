// ── Auth guard ───────────────────────────────────────────
(function () {
    document.documentElement.style.visibility = 'hidden';
    fetch('admin_check_session.php')
        .then(res => res.json())
        .then(result => {
            if (!result.loggedIn) {
                window.location.href = 'admin_login.html';
            } else {
                document.documentElement.style.visibility = 'visible';
                const el = document.getElementById('admin_welcome');
                if (el) el.textContent = 'Logged in as: ' + result.admin_id;
            }
        })
        .catch(() => { window.location.href = 'admin_login.html'; });
})();

// ── Logout ───────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('admin_logout').addEventListener('click', function (e) {
        e.preventDefault();
        fetch('admin_logout.php')
            .then(() => { window.location.href = 'admin_login.html'; })
            .catch(() => { window.location.href = 'admin_login.html'; });
    });

    loadEmployees();
    loadSubmissions();
});

// ── Tab switching ────────────────────────────────────────
function switchTab(tab) {
    document.querySelectorAll('.admin-tab').forEach((t, i) => {
        t.classList.toggle('active', (tab === 'employees' && i === 0) || (tab === 'submissions' && i === 1));
    });
    document.getElementById('tab_employees').classList.toggle('active', tab === 'employees');
    document.getElementById('tab_submissions').classList.toggle('active', tab === 'submissions');
}

// ── Helpers ──────────────────────────────────────────────
function escapeHtml(val) {
    if (val === null || val === undefined) return '';
    return String(val)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function showStatus(elId, message, type) {
    const el = document.getElementById(elId);
    el.textContent = message;
    el.className = 'status-msg ' + type;
    setTimeout(() => { el.className = 'status-msg'; el.textContent = ''; }, 4000);
}

// ── Employees ────────────────────────────────────────────
function loadEmployees() {
    fetch('admin_get_employees.php')
        .then(res => {
            if (res.status === 401) { window.location.href = 'admin_login.html'; return; }
            return res.json();
        })
        .then(result => {
            if (!result) return;
            const tbody = document.getElementById('emp_tbody');
            if (!result.success) {
                tbody.innerHTML = `<tr><td colspan="3">${escapeHtml(result.error)}</td></tr>`;
                return;
            }
            if (!result.data || result.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3">No employees found.</td></tr>';
                return;
            }
            tbody.innerHTML = result.data.map((emp, i) => `
                <tr>
                    <td>${i + 1}</td>
                    <td>${escapeHtml(emp.employee_id)}</td>
                    <td>
                        <button class="btn-delete" data-empid="${escapeHtml(emp.employee_id)}" onclick="deleteEmployee(this.dataset.empid)">Delete</button>
                    </td>
                </tr>
            `).join('');
        })
        .catch(err => {
            console.error('Load employees error:', err);
            document.getElementById('emp_tbody').innerHTML = '<tr><td colspan="3">Failed to load employees.</td></tr>';
        });
}

function addEmployee() {
    const empId = document.getElementById('new_emp_id').value.trim();
    const empPass = document.getElementById('new_emp_pass').value;
    const statusEl = document.getElementById('add_emp_status');

    if (!empId || !empPass) {
        statusEl.style.color = 'red';
        statusEl.textContent = 'Please enter both Employee ID and Password.';
        return;
    }

    const formData = new FormData();
    formData.append('employee_id', empId);
    formData.append('password', empPass);

    fetch('admin_add_employee.php', { method: 'POST', body: formData })
        .then(res => {
            if (res.status === 401) { window.location.href = 'admin_login.html'; return; }
            return res.json();
        })
        .then(result => {
            if (!result) return;
            if (result.success) {
                statusEl.style.color = 'green';
                statusEl.textContent = `Employee "${empId}" added successfully.`;
                document.getElementById('new_emp_id').value = '';
                document.getElementById('new_emp_pass').value = '';
                document.getElementById('new_emp_pass').type = 'password';
                document.getElementById('eye_icon').src = 'icons8-eye-50.png';
                loadEmployees();
            } else {
                statusEl.style.color = 'red';
                statusEl.textContent = result.error || 'Failed to add employee.';
            }
        })
        .catch(err => {
            console.error('Add employee error:', err);
            statusEl.style.color = 'red';
            statusEl.textContent = 'Network error.';
        });
}

function deleteEmployee(employeeId) {
    if (!confirm(`Delete employee "${employeeId}"? This cannot be undone.`)) return;

    const formData = new FormData();
    formData.append('employee_id', employeeId);

    fetch('admin_delete_employee.php', { method: 'POST', body: formData })
        .then(res => {
            if (res.status === 401) { window.location.href = 'admin_login.html'; return; }
            return res.json();
        })
        .then(result => {
            if (!result) return;
            if (result.success) {
                showStatus('emp_status', `Employee "${employeeId}" deleted.`, 'success');
                loadEmployees();
            } else {
                showStatus('emp_status', result.error || 'Failed to delete.', 'error');
            }
        })
        .catch(err => {
            console.error('Delete employee error:', err);
            showStatus('emp_status', 'Network error.', 'error');
        });
}

// ── Submissions ──────────────────────────────────────────
function loadSubmissions() {
    fetch('admin_get_submissions.php')
        .then(res => {
            if (res.status === 401) { window.location.href = 'admin_login.html'; return; }
            return res.json();
        })
        .then(result => {
            if (!result) return;
            const tbody = document.getElementById('sub_tbody');
            if (!result.success) {
                tbody.innerHTML = `<tr><td colspan="15">${escapeHtml(result.error)}</td></tr>`;
                return;
            }
            if (!result.data || result.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="15">No submissions yet.</td></tr>';
                return;
            }
            tbody.innerHTML = result.data.map(row => `
                <tr data-id="${escapeHtml(row.id)}">
                    <td>${escapeHtml(row.id)}</td>
                    <td>${escapeHtml(row.form_no)}</td>
                    <td >${escapeHtml(row.operation)}</td>
                    <td >${escapeHtml(row.given_by)}</td>
                    <td>${escapeHtml(row.date_of_submission)}</td>
                    <td class="editable" data-field="depatment_section">${escapeHtml(row.depatment_section)}</td>
                    <td class="editable" data-field="incident_description">${escapeHtml(row.incident_description)}</td>
                    <td class="editable" data-field="main_error_category">${escapeHtml(row.main_error_category)}</td>
                    <td class="editable" data-field="root_cause">${escapeHtml(row.root_cause)}</td>
                    <td class="editable" data-field="immediate_correction">${escapeHtml(row.immediate_correction)}</td>
                    <td class="editable" data-field="corrective_action">${escapeHtml(row.corrective_action)}</td>
                    <td class="editable" data-field="preventive_action">${escapeHtml(row.preventive_action)}</td>
                    <td class="editable" data-field="patient_consequences">${escapeHtml(row.patient_consequences)}</td>
                    <td>
                        <button class="btn-edit" onclick="toggleEditRow(this)" title="Edit row">✏️</button>
                    </td>
                    <td>
                        <button class="btn-delete" onclick="deleteSubmission(${escapeHtml(row.id)})">Delete</button>
                    </td>
                </tr>
            `).join('');
        })
        .catch(err => {
            console.error('Load submissions error:', err);
            document.getElementById('sub_tbody').innerHTML = '<tr><td colspan="15">Failed to load submissions.</td></tr>';
        });
}

function saveEdit(id, field, value, td, original) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('field', field);
    formData.append('value', value);

    fetch('admin_edit_submission.php', { method: 'POST', body: formData })
        .then(res => {
            if (res.status === 401) { window.location.href = 'admin_login.html'; return; }
            return res.json();
        })
        .then(result => {
            if (!result) return;
            if (result.success) {
                td.textContent = value;
                showStatus('sub_status', 'Record updated successfully.', 'success');
            } else {
                td.textContent = original;
                showStatus('sub_status', result.error || 'Failed to update.', 'error');
            }
        })
        .catch(err => {
            console.error('Edit error:', err);
            td.textContent = original;
            showStatus('sub_status', 'Network error.', 'error');
        });
}

function deleteSubmission(id) {
    if (!confirm(`Delete submission #${id}? This cannot be undone.`)) return;

    const formData = new FormData();
    formData.append('id', id);

    fetch('admin_delete_submission.php', { method: 'POST', body: formData })
        .then(res => {
            if (res.status === 401) { window.location.href = 'admin_login.html'; return; }
            return res.json();
        })
        .then(result => {
            if (!result) return;
            if (result.success) {
                showStatus('sub_status', `Submission #${id} deleted.`, 'success');
                loadSubmissions();
            } else {
                showStatus('sub_status', result.error || 'Failed to delete.', 'error');
            }
        })
        .catch(err => {
            console.error('Delete submission error:', err);
            showStatus('sub_status', 'Network error.', 'error');
        });
}
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

window.onclick = function (e) {
    const modal = document.getElementById('exportModal');
    if (e.target === modal) closeExportModal();
}
function toggleEmpPass() {
    const input = document.getElementById('new_emp_pass');
    const icon = document.getElementById('eye_icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.src = 'icons8-closed-eye-50.png';
    } else {
        input.type = 'password';
        icon.src = 'icons8-eye-50.png';
    }
} function toggleEditRow(btn) {
    const row = btn.closest('tr');
    const isEditing = row.classList.contains('editing');

    if (isEditing) {
        // Save all inputs and exit edit mode
        row.querySelectorAll('td.editable input').forEach(input => {
            const td = input.parentElement;
            const field = td.dataset.field;
            const newVal = input.value.trim();
            const original = input.dataset.original;
            if (newVal !== original) {
                saveEdit(row.dataset.id, field, newVal, td, original);
            } else {
                td.textContent = newVal;
            }
        });
        row.classList.remove('editing');
        btn.textContent = '✏️';
        btn.title = 'Edit row';
    } else {
        // Enter edit modeF
        row.querySelectorAll('td.editable').forEach(td => {
            const current = td.textContent;
            td.innerHTML = `<input type="text" value="${escapeHtml(current)}" data-original="${escapeHtml(current)}">`;
        });
        row.classList.add('editing');
        btn.textContent = '💾';
        btn.title = 'Save changes';
    }
}