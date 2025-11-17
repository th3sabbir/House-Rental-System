// Dashboard JavaScript Functions

function toggleSidebar() {
    const sidebar = document.getElementById('landlordSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const icon = document.querySelector('.mobile-menu-toggle i');
    const isOpen = sidebar.classList.contains('active');

    if (isOpen) {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.classList.remove('menu-open');
        if (icon) { icon.classList.remove('fa-times'); icon.classList.add('fa-bars'); }
    } else {
        sidebar.classList.add('active');
        overlay.classList.add('active');
        document.body.classList.add('menu-open');
        if (icon) { icon.classList.remove('fa-bars'); icon.classList.add('fa-times'); }
    }
}

function closeSidebar() {
    const sidebar = document.getElementById('landlordSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.remove('active');
    overlay.classList.remove('active');
    document.body.classList.remove('menu-open');
    const icon = document.querySelector('.mobile-menu-toggle i');
    if (icon) { icon.classList.remove('fa-times'); icon.classList.add('fa-bars'); }
}

function logout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = '../api/logout.php';
    }
}

// Close sidebar when clicking a nav link on mobile
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.sidebar-nav a').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 992) {
                closeSidebar();
            }
        });
    });
    // Close sidebar when clicking outside (not on the sidebar or toggle) on mobile
    document.addEventListener('click', function(e) {
        const sidebar = document.getElementById('landlordSidebar');
        const toggle = document.querySelector('.mobile-menu-toggle');
        if (sidebar && sidebar.classList.contains('active')) {
            if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                closeSidebar();
            }
        }
    });
});
