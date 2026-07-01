(function () {
    // Hide page immediately until session confirmed
    document.documentElement.style.visibility = 'hidden';

    fetch('check_session.php')
        .then(res => res.json())
        .then(result => {
            if (!result.loggedIn) {
                window.location.href = 'index.html';
            } else {
                document.documentElement.style.visibility = 'visible';
                const welcomeEl = document.getElementById('welcome_user');
                if (welcomeEl && result.employee_id) {
                    welcomeEl.textContent = `Logged in as: ${result.employee_id}`;
                }
            }
        })
        .catch(err => {
            console.error('Session check failed:', err);
            window.location.href = 'index.html';
        });
})();

document.addEventListener('DOMContentLoaded', function () {
    const logoutLink = document.getElementById('logout_link');
    if (logoutLink) {
        logoutLink.addEventListener('click', function (e) {
            e.preventDefault();
            fetch('logout.php')
                .then(() => {
                    window.location.href = 'index.html';
                })
                .catch(err => console.error('Logout failed:', err));
        });
    }
});
window.addEventListener('pageshow', function (e) {
    if (e.persisted) {
        // Page loaded from cache (back button) — recheck session
        fetch('check_session.php')
            .then(res => res.json())
            .then(result => {
                if (!result.loggedIn) {
                    window.location.href = 'index.html';
                } else {
                    document.documentElement.style.visibility = 'visible';
                }
            })
            .catch(() => { window.location.href = 'index.html'; });
    }
});