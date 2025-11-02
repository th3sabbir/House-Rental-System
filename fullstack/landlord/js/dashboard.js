// Dashboard JavaScript Functions

function toggleSidebar() {
    const sidebar = document.getElementById('landlordSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.add('active');
    overlay.style.display = 'block';
}

function closeSidebar() {
    const sidebar = document.getElementById('landlordSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.remove('active');
    overlay.style.display = 'none';
}

function logout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = '../api/logout.php';
    }
}
