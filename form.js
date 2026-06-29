// ── Accordion ─────────────────────────────────────────────
function toggleSection(index) {
    const sections = document.querySelectorAll(".accordion-section");
    sections.forEach((section, i) => {
        const isTarget = i === index;
        const alreadyOpen = section.classList.contains("open");
        if (isTarget) {
            // Toggle the clicked one; opening it closes all others
            section.classList.toggle("open", !alreadyOpen);
        } else {
            section.classList.remove("open");
        }
        const icon = section.querySelector(".accordion-icon");
        if (icon) icon.textContent = section.classList.contains("open") ? "−" : "+";
    });
}

function openSection(index) {
    const sections = document.querySelectorAll(".accordion-section");
    sections.forEach((section, i) => {
        section.classList.toggle("open", i === index);
        const icon = section.querySelector(".accordion-icon");
        if (icon) icon.textContent = section.classList.contains("open") ? "−" : "+";
    });
    // Scroll the opened section into view
    sections[index]?.scrollIntoView({ behavior: "smooth", block: "start" });
}

// ── Interdependent dropdown ──────────────────────────────
function updateSub() {
    const rows = ['pre_row', 'analytic_row', 'post_row', 'others_row'];
    rows.forEach(id => {
        document.getElementById(id).style.display = 'none';
    });
    const val = document.getElementById('main_category').value;
    if (val) {
        document.getElementById(val + '_row').style.removeProperty('display');
    }
}

// ── Risk Score calculator ────────────────────────────────
function calcRisk(impactId, freqId, resultId) {
    const impact = parseFloat(document.getElementById(impactId).value) || 0;
    const freq = parseFloat(document.getElementById(freqId).value) || 0;
    const result = impact * freq;
    document.getElementById(resultId).innerHTML = result;
    calcAverage();
}

function calcAverage() {
    let sumImpact = 0;
    let sumFreq = 0;
    let sumRisk = 0;
    let count = 0;

    for (let i = 1; i <= 5; i++) {
        const impact = parseFloat(document.getElementById('impact_score' + i).value) || 0;
        const freq = parseFloat(document.getElementById('freq_score' + i).value) || 0;
        const risk = parseFloat(document.getElementById('risk_score' + i).innerHTML) || 0;

        if (impact > 0 || freq > 0) {
            sumImpact += impact;
            sumFreq += freq;
            sumRisk += risk;
            count++;
        }
    }

    if (count > 0) {
        document.getElementById('impact_score').value = (sumImpact / count).toFixed(2);
        document.getElementById('freq_score').value = (sumFreq / count).toFixed(2);
        const avgRisk = (sumRisk / count).toFixed(2);
        document.getElementById('risk_score').innerHTML = avgRisk;
        const hiddenRisk = document.getElementById('risk_score_hidden');
        if (hiddenRisk) hiddenRisk.value = avgRisk;
    } else {
        document.getElementById('impact_score').value = '';
        document.getElementById('freq_score').value = '';
        document.getElementById('risk_score').innerHTML = '0';
        const hiddenRisk = document.getElementById('risk_score_hidden');
        if (hiddenRisk) hiddenRisk.value = '0';
    }
}

// ── Form number with prefix ──────────────────────────────
function updateFormNumber() {
    fetch('get_form_number.php')
        .then(res => res.text())
        .then(number => {
            const el = document.getElementById('formno');
            const hiddenEl = document.getElementById('formno_hidden');
            if (!el) return;
            const selected = document.querySelector('input[name="operation"]:checked');
            const year = new Date().getFullYear();
            let formattedNumber;
            if (selected && selected.value.toLowerCase() === 'complaint') {
                formattedNumber = `COMP-${year}-${number}`;
            } else if (selected && selected.value.toLowerCase() === 'feedback') {
                formattedNumber = `FB-${year}-${number}`;
            } else {
                formattedNumber = `FORM-${year}-${number}`;
            }
            el.textContent = formattedNumber;
            if (hiddenEl) hiddenEl.value = formattedNumber;
        })
        .catch(err => console.error('Could not load form number:', err));
}

// ── Form validation ──────────────────────────────────────
function validateform() {
    const operation = [...document.querySelectorAll('input[name="operation"]:checked')]
        .map(cb => cb.value);
    const given_by = document.getElementById("given_by").value;
    const department_section = document.getElementById("department_section").value;
    const incident_description = document.getElementById("incident_description").value;
    const main_category = document.getElementById("main_category").value;
    const root_cause = document.getElementById("root_cause").value;
    const immediate_correction = document.getElementById("immediate_correction").value;
    const corrective_action = document.getElementById("corrective_action").value;

    // Check whether at least one risk row has both impact and freq filled
    let anyRiskRowFilled = false;
    for (let i = 1; i <= 5; i++) {
        const impact = document.getElementById('impact_score' + i).value;
        const freq = document.getElementById('freq_score' + i).value;
        if (impact && freq) {
            anyRiskRowFilled = true;
            break;
        }
    }

    let operation_empty = document.getElementById("operation_empty");
    let given_by_empty = document.getElementById("given_by_empty");
    let department_section_empty = document.getElementById("department_section_empty");
    let incident_description_empty = document.getElementById("incident_description_empty");
    let main_category_empty = document.getElementById("main_category_empty");
    let root_cause_empty = document.getElementById("root_cause_empty");
    let immediate_correction_empty = document.getElementById("immediate_correction_empty");
    let corrective_action_empty = document.getElementById("corrective_action_empty");
    let risk_score_evaluation = document.getElementById("risk_score_evaluation");

    let valid = true;
    let missingFields = [];
    let firstErrorSection = null; // accordion section index (0-5)

    // Clear all errors
    [operation_empty, given_by_empty, department_section_empty,
        incident_description_empty, main_category_empty, root_cause_empty,
        immediate_correction_empty, corrective_action_empty,
        risk_score_evaluation
    ].forEach(el => { if (el) el.textContent = ""; });

    // Section 0 — Basic Information
    if (operation.length === 0) {
        operation_empty.textContent = "Choose an operation!";
        missingFields.push("Operation");
        valid = false;
        if (firstErrorSection === null) firstErrorSection = 0;
    }
    if (!given_by) {
        given_by_empty.textContent = "Choose an option!";
        missingFields.push("Given by");
        valid = false;
        if (firstErrorSection === null) firstErrorSection = 0;
    }

    // Section 1 — Incident Details
    if (!department_section) {
        department_section_empty.textContent = "Choose an option!";
        missingFields.push("Department/Section");
        valid = false;
        if (firstErrorSection === null) firstErrorSection = 1;
    }
    if (!incident_description.trim()) {
        incident_description_empty.textContent = "Cannot be left empty!";
        missingFields.push("Incident Description");
        valid = false;
        if (firstErrorSection === null) firstErrorSection = 1;
    }

    // Section 2 — Error Classification
    if (!main_category) {
        main_category_empty.textContent = "Choose an option!";
        missingFields.push("Error Category");
        valid = false;
        if (firstErrorSection === null) firstErrorSection = 2;
    }

    // Section 3 — Error Analysis
    if (!root_cause.trim()) {
        root_cause_empty.textContent = "Cannot be left empty!";
        missingFields.push("Root Cause");
        valid = false;
        if (firstErrorSection === null) firstErrorSection = 3;
    }

    // Section 4 — Risk Score
    if (!anyRiskRowFilled) {
        risk_score_evaluation.textContent = "Fill at least one risk row!";
        missingFields.push("Risk Score (at least one row)");
        valid = false;
        if (firstErrorSection === null) firstErrorSection = 4;
    }

    // Section 5 — Actions Taken
    if (!immediate_correction.trim()) {
        immediate_correction_empty.textContent = "Cannot be left empty!";
        missingFields.push("Immediate Correction");
        valid = false;
        if (firstErrorSection === null) firstErrorSection = 5;
    }
    if (!corrective_action.trim()) {
        corrective_action_empty.textContent = "Cannot be left empty!";
        missingFields.push("Corrective Action");
        valid = false;
        if (firstErrorSection === null) firstErrorSection = 5;
    }

    // If invalid — show alert and open the section with the first error
    if (!valid) {
        alert("Please fill the following required fields:\n\n• " + missingFields.join("\n• "));
        openSection(firstErrorSection);
        return false;
    }

    // Valid — disable button, then submit via fetch
    const submitBtn = document.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = "Submitting...";

    const formData = new FormData(document.querySelector('form'));

    fetch('submit.php', {
        method: 'POST',
        body: formData
    })
        .then(res => {
            return res.text().then(text => ({ status: res.status, ok: res.ok, text }));
        })
        .then(({ status, ok, text }) => {
            console.log('submit.php status:', status, 'ok:', ok);
            console.log('submit.php raw response:', text);

            let data;
            try {
                data = JSON.parse(text);
            } catch (parseErr) {
                console.error('Could not parse JSON:', parseErr);
                alert('Server returned an unexpected response (check console for details).');
                submitBtn.disabled = false;
                submitBtn.textContent = "Submit";
                return;
            }

            if (data.success) {
                alert('Form submitted successfully!');
                document.querySelector('form').reset();
                openSection(0);
                updateSub(); 
                updateFormNumber();
            } else {
                alert('Error: ' + data.error);
            }
            submitBtn.disabled = false;
            submitBtn.textContent = "Submit";
        })
        .catch(err => {
            console.error('Fetch failed:', err);
            alert('Network error: ' + err.message);
            submitBtn.disabled = false;
            submitBtn.textContent = "Submit";
        });

    return false;
}

// ── Init ─────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    // Set today's date
    const d = new Date();
    const el = document.getElementById("date");
    const hiddenEl = document.getElementById("date_hidden");
    if (el) el.innerHTML = d.toLocaleDateString();
    if (hiddenEl) {
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        hiddenEl.value = `${yyyy}-${mm}-${dd}`;
    }

    for (let i = 1; i <= 5; i++) {
        const impactEl = document.getElementById('impact_score' + i);
        const freqEl = document.getElementById('freq_score' + i);
        if (impactEl && freqEl) {
            impactEl.addEventListener('input', function () {
                calcRisk('impact_score' + i, 'freq_score' + i, 'risk_score' + i);
            });
            freqEl.addEventListener('input', function () {
                calcRisk('impact_score' + i, 'freq_score' + i, 'risk_score' + i);
            });
        }
    }

    // Load form number on page load
    updateFormNumber();

    // Update form number prefix when operation changes
    document.querySelectorAll('input[name="operation"]').forEach(cb => {
        cb.addEventListener('change', updateFormNumber);
    });
});