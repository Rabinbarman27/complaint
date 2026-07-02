function handleLogin() {
    const employeeId = document.getElementById('employee_id').value.trim();
    const password = document.getElementById('password').value;
    const errorEl = document.getElementById('login_error');
    const loginBtn = document.getElementById('login_btn');

    errorEl.textContent = "";

    if (!employeeId || !password) {
        errorEl.textContent = "Please enter both Employee ID and Password.";
        return false;
    }

    loginBtn.disabled = true;
    loginBtn.textContent = "Logging in...";

    const formData = new FormData();
    formData.append('employee_id', employeeId);
    formData.append('password', password);

    fetch('login.php', {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'dashboard.php';
            } else {
                errorEl.textContent = data.error || "Invalid Employee ID or Password.";
                loginBtn.disabled = false;
                loginBtn.textContent = "Login";
            }
        })
        .catch(err => {
            console.error('Login failed:', err);
            errorEl.textContent = "Network error. Please try again.";
            loginBtn.disabled = false;
            loginBtn.textContent = "Login";
        });

    return false;
}

// Allow pressing Enter in either field to submit
document.addEventListener('DOMContentLoaded', function () {
    window.addEventListener('pageshow', function () {
        const btn = document.getElementById('login_btn');
        if (btn) {
            btn.disabled = false;
            btn.textContent = 'Login';
        }
    });
    document.getElementById('employee_id').focus();
    document.getElementById('login_form').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleLogin();
        }
    });
});
function toggleEmpPass() {
    const input = document.getElementById('password');
    const icon = document.getElementById('eye_icon');

    if (input.type === 'password') {
        input.type = 'text';
        icon.src = 'icons8-closed-eye-50.png'; // ← fixed
    } else {
        input.type = 'password';
        icon.src = 'icons8-eye-50.png';
    }
}
