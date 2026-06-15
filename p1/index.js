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
document.getElementById('impact_score').addEventListener('input', calcRisk);
document.getElementById('freq_score').addEventListener('input', calcRisk);

function calcRisk() {
  const impact = parseFloat(document.getElementById('impact_score').value) || 0;
  const freq = parseFloat(document.getElementById('freq_score').value) || 0;
  document.getElementById('risk_score').textContent = impact * freq;
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
  const impact_score = document.getElementById("impact_score").value;
  const freq_score = document.getElementById("freq_score").value;
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

  operation_empty.textContent = "";
  given_by_empty.textContent = "";
  formDate_empty.textContent = "";
  department_section_empty.textContent = "";
  incident_description_empty.textContent = "";
  main_category_empty.textContent = "";
  root_cause_empty.textContent = "";
  immediate_correction_empty.textContent = "";
  corrective_action_empty.textContent = "";
  preventive_action_empty.textContent = "";
  patient_consequences_empty.textContent = "";
  risk_score_evaluation.textContent = "";
  if (operation.length === 0) {
    operation_empty.textContent = "Choose an operation!";

    valid = false;
  }
  if (!given_by) {
    given_by_empty.textContent = "Choose an option!";

    valid = false;
  }
  if (!formDate) {
    formDate_empty.textContent = "Fill date!";

    valid = false;
  }
  if (!department_section) {
    department_section_empty.textContent = "Choose an option!";

    valid = false;
  }
  if (!incident_description.trim()) {
    incident_description_empty.textContent = "Cannot be left empty!";

    valid = false;
  }
  if (!main_category) {
    main_category_empty.textContent = "Choose an option!";

    valid = false;
  }
  if (!root_cause.trim()) {
    root_cause_empty.textContent = "Cannot be left empty!";

    valid = false;
  }
  if (!impact_score || !freq_score) {
    risk_score_evaluation.textContent = "Cannot be left empty!";

    valid = false;
  }
  if (!patient_consequences) {
    patient_consequences_empty.textContent = "Choose an option!";

    valid = false;
  }

  return valid;
}