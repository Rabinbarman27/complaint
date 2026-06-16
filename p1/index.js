let currentStep = 0;

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
        document.getElementById('risk_score').innerHTML = (sumRisk / count).toFixed(2);
    } else {
        document.getElementById('impact_score').value = '';
        document.getElementById('freq_score').value = '';
        document.getElementById('risk_score').innerHTML = '0';
    }
}

// ── Form validation ──────────────────────────────────────
function validateform() {
    const operation = [...document.querySelectorAll('input[name="operation"]:checked')]
        .map(cb => cb.value);
    const given_by = document.getElementById("given_by").value;
    const formDate = document.getElementById("date").value;
    const department_section = document.getElementById("department_section").value;
    const incident_description = document.getElementById("incident_description").value;
    const main_category = document.getElementById("main_category").value;
    const root_cause = document.getElementById("root_cause").value;
    const immediate_correction = document.getElementById("immediate_correction").value;
    const corrective_action = document.getElementById("corrective_action").value;
    const preventive_action = document.getElementById("preventive_action").value;
    const impact_score = document.getElementById("impact_score1").value;
    const freq_score = document.getElementById("freq_score1").value;
    const patient_consequences = document.querySelector('input[name="patient_consequences"]:checked');

    let operation_empty = document.getElementById("operation_empty");
    let given_by_empty = document.getElementById("given_by_empty");
    let formDate_empty = document.getElementById("formDate_empty");
    let department_section_empty = document.getElementById("department_section_empty");
    let incident_description_empty = document.getElementById("incident_description_empty");
    let main_category_empty = document.getElementById("main_category_empty");
    let root_cause_empty = document.getElementById("root_cause_empty");
    let immediate_correction_empty = document.getElementById("immediate_correction_empty");
    let corrective_action_empty = document.getElementById("corrective_action_empty");
    let preventive_action_empty = document.getElementById("preventive_action_empty");
    let patient_consequences_empty = document.getElementById("patient_consequences_empty");
    let risk_score_evaluation = document.getElementById("risk_score_evaluation");
    let valid = true;
    let missingFields = [];
    let firstErrorStep = null;

    // Clear all errors
    [operation_empty, given_by_empty, formDate_empty, department_section_empty,
     incident_description_empty, main_category_empty, root_cause_empty,
     immediate_correction_empty, corrective_action_empty, preventive_action_empty,
     patient_consequences_empty, risk_score_evaluation
    ].forEach(el => el.textContent = "");

    // Step 1 fields
    if (operation.length === 0) {
        operation_empty.textContent = "Choose an operation!";
        missingFields.push("Operation");
        valid = false;
        if (firstErrorStep === null) firstErrorStep = 0;
    }
    if (!given_by) {
        given_by_empty.textContent = "Choose an option!";
        missingFields.push("Given by");
        valid = false;
        if (firstErrorStep === null) firstErrorStep = 0;
    }
    if (!formDate) {
        formDate_empty.textContent = "Fill date!";
        missingFields.push("Date");
        valid = false;
        if (firstErrorStep === null) firstErrorStep = 0;
    }
    if (!department_section) {
        department_section_empty.textContent = "Choose an option!";
        missingFields.push("Department/Section");
        valid = false;
        if (firstErrorStep === null) firstErrorStep = 0;
    }
    if (!incident_description.trim()) {
        incident_description_empty.textContent = "Cannot be left empty!";
        missingFields.push("Incident Description");
        valid = false;
        if (firstErrorStep === null) firstErrorStep = 0;
    }
    if (!main_category) {
        main_category_empty.textContent = "Choose an option!";
        missingFields.push("Error Category");
        valid = false;
        if (firstErrorStep === null) firstErrorStep = 0;
    }
    if (!root_cause.trim()) {
        root_cause_empty.textContent = "Cannot be left empty!";
        missingFields.push("Root Cause");
        valid = false;
        if (firstErrorStep === null) firstErrorStep = 0;
    }

    // Step 2 fields
    if (!impact_score || !freq_score) {
        risk_score_evaluation.textContent = "Fill at least row 1!";
        missingFields.push("Risk Score (Row 1)");
        valid = false;
        if (firstErrorStep === null) firstErrorStep = 1;
    }
    if (!immediate_correction.trim()) {
        immediate_correction_empty.textContent = "Cannot be left empty!";
        missingFields.push("Immediate Correction");
        valid = false;
        if (firstErrorStep === null) firstErrorStep = 1;
    }
    if (!corrective_action.trim()) {
        corrective_action_empty.textContent = "Cannot be left empty!";
        missingFields.push("Corrective Action");
        valid = false;
        if (firstErrorStep === null) firstErrorStep = 1;
    }
    if (!preventive_action.trim()) {
        preventive_action_empty.textContent = "Cannot be left empty!";
        missingFields.push("Preventive Action");
        valid = false;
        if (firstErrorStep === null) firstErrorStep = 1;
    }
    if (!patient_consequences) {
        patient_consequences_empty.textContent = "Choose an option!";
        missingFields.push("Patient Consequences");
        valid = false;
        if (firstErrorStep === null) firstErrorStep = 1;
    }

    // If invalid — show alert and jump to the step with the first error
    if (!valid) {
        alert("Please fill the following required fields:\n\n• " + missingFields.join("\n• "));
        currentStep = firstErrorStep;
        showStep(currentStep);
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
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Form submitted successfully!');
            document.querySelector('form').reset();
            currentStep = 0;
            showStep(currentStep);
            fetch('get_form_number.php')
                .then(res => res.text())
                .then(number => {
                    document.getElementById('formno.').textContent = number;
                });
        } else {
            alert('Error: ' + data.error);
        }
        submitBtn.disabled = false;
        submitBtn.textContent = "Submit";
    })
    .catch(() => {
        alert('Network error, please try again.');
        submitBtn.disabled = false;
        submitBtn.textContent = "Submit";
    });

    return false;
}

// ── Steps ────────────────────────────────────────────────
const steps = document.querySelectorAll(".step");

function showStep(index) {
    steps.forEach((step, i) => step.classList.toggle("active", i === index));
}

function nextStep() {
    if (currentStep < steps.length - 1) {
        currentStep++;
        showStep(currentStep);
    }
}

function prevStep() {
    if (currentStep > 0) {
        currentStep--;
        showStep(currentStep);
    }
}

// ── Init ─────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    showStep(currentStep);

    for (let i = 1; i <= 5; i++) {
        const impactEl = document.getElementById('impact_score' + i);
        const freqEl = document.getElementById('freq_score' + i);
        if (impactEl && freqEl) {
            impactEl.addEventListener('input', function() {
                calcRisk('impact_score' + i, 'freq_score' + i, 'risk_score' + i);
            });
            freqEl.addEventListener('input', function() {
                calcRisk('impact_score' + i, 'freq_score' + i, 'risk_score' + i);
            });
        }
    }

    // Get form number on page load
    fetch('get_form_number.php')
        .then(res => res.text())
        .then(number => {
            document.getElementById('formno.').textContent = number;
        });
});
function exportExcel() {
    const from = document.getElementById('export_from').value;
    const to = document.getElementById('export_to').value;

    if (!from || !to) {
        alert("Please select both From and To dates!");
        return;
    }

    window.location.href = `export_excel.php?from_date=${from}&to_date=${to}`;
}