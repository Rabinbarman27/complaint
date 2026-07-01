function handleAdminLogin() {
    const adminId = document.getElementById('admin_id').value.trim();
    const password = document.getElementById('password').value;
    const errorEl = document.getElementById('admin_login_error');
    const loginBtn = document.getElementById('admin_login_btn');

    errorEl.textContent = '';

    if (!adminId || !password) {
        errorEl.textContent = 'Please enter both Admin ID and Password.';
        return false;
    }

    loginBtn.disabled = true;
    loginBtn.textContent = 'Logging in...';

    const formData = new FormData();
    formData.append('admin_id', adminId);
    formData.append('password', password);

    fetch('admin_login.php', {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'admin_dashboard.html';
            } else {
                errorEl.textContent = data.error || 'Invalid Admin ID or Password.';
                loginBtn.disabled = false;
                loginBtn.textContent = 'Login as Admin';
            }
        })
        .catch(err => {
            console.error('Admin login failed:', err);
            errorEl.textContent = 'Network error. Please try again.';
            loginBtn.disabled = false;
            loginBtn.textContent = 'Login as Admin';
        });

    return false;
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('admin_id').focus();
    document.getElementById('admin_login_form').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleAdminLogin();
        }
    });
    window.addEventListener('pageshow', function () {
        const btn = document.getElementById('admin_login_btn');
        if (btn) {
            btn.disabled = false;
            btn.textContent = 'Login as Admin';
        }
    });
});
function toggleEmpPass() {
    const input = document.getElementById('password');
    const icon = document.getElementById('eye_icon');

    if (input.type === 'password') {
        input.type = 'text';
        icon.src = 'icons8-closed-eye-50.png'; // correct filename
    } else {
        input.type = 'password';
        icon.src = 'icons8-eye-50.png';
    }
}
