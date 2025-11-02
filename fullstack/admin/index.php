<?php
session_start();

// Include path helpers
require_once '../includes/paths.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    redirect('login.php');
}

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    redirect('index.php');
}

// Get user info
$admin_name = $_SESSION['full_name'] ?? 'Admin';
$admin_email = $_SESSION['email'] ?? '';
$admin_username = $_SESSION['username'] ?? '';

// Get current page
$page = $_GET['page'] ?? 'dashboard';

// Validate page
$valid_pages = ['dashboard', 'users', 'properties', 'bookings', 'reviews', 'reports', 'settings'];
if (!in_array($page, $valid_pages)) {
    $page = 'dashboard';
}

// Set page file path
$page_file = __DIR__ . '/' . $page . '.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - AmarThikana</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeMobileSidebar()"></div>

    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <h2>AmarThikana</h2>
                <p>Admin Panel</p>
            </div>
            <ul class="admin-nav">
                <li><a href="?page=dashboard" class="nav-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-th-large"></i> Dashboard
                </a></li>
                <li><a href="?page=users" class="nav-link <?php echo $page === 'users' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Users Management
                    <span class="nav-badge">156</span>
                </a></li>
                <li><a href="?page=properties" class="nav-link <?php echo $page === 'properties' ? 'active' : ''; ?>">
                    <i class="fas fa-building"></i> Properties
                    <span class="nav-badge">42</span>
                </a></li>
                <li><a href="?page=bookings" class="nav-link <?php echo $page === 'bookings' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-check"></i> Bookings
                    <span class="nav-badge">28</span>
                </a></li>
                <li><a href="?page=reviews" class="nav-link <?php echo $page === 'reviews' ? 'active' : ''; ?>">
                    <i class="fas fa-star"></i> Reviews
                </a></li>
                <li><a href="?page=reports" class="nav-link <?php echo $page === 'reports' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line"></i> Reports & Analytics
                </a></li>
                <li><a href="?page=settings" class="nav-link <?php echo $page === 'settings' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i> Settings
                </a></li>
                <li><a href="../api/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="admin-content">
            <?php
            // Include the requested page
            if (file_exists($page_file)) {
                include $page_file;
            } else {
                echo '<div class="content-header"><div class="header-title"><h1>Page Not Found</h1><p>The requested page could not be found.</p></div></div>';
            }
            ?>
        </main>

        <!-- Mobile Menu Toggle Button -->
        <button class="mobile-menu-toggle" onclick="toggleMobileSidebar()">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3><i class="fas fa-user-plus"></i> Add New User</h3>
                <button class="modal-close" onclick="closeAddUserModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Full Name *</label>
                        <input type="text" class="form-control" id="addFullName" required placeholder="Enter full name">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-at"></i> Username *</label>
                        <input type="text" class="form-control" id="addUsername" required placeholder="Enter username">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address *</label>
                        <input type="email" class="form-control" id="addEmail" required placeholder="Enter email address">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Phone Number</label>
                        <input type="tel" class="form-control" id="addPhone" placeholder="Enter phone number">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-user-tag"></i> User Role *</label>
                        <select class="form-control" id="addRole" required>
                            <option value="">Select Role</option>
                            <option value="tenant">Tenant</option>
                            <option value="landlord">Landlord</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Password *</label>
                        <input type="password" class="form-control" id="addPassword" required placeholder="Enter password" minlength="6">
                        <small style="color: var(--text-medium); font-size: 0.85rem;">
                            Password must be at least 6 characters long
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" onclick="closeAddUserModal()">Cancel</button>
                <button class="btn btn-primary" id="addUserBtn" onclick="submitAddUser()">
                    <i class="fas fa-save"></i> Add User
                </button>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3><i class="fas fa-user-edit"></i> Edit User</h3>
                <button class="modal-close" onclick="closeEditUserModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="editUserId">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Full Name *</label>
                        <input type="text" class="form-control" id="editFullName" required placeholder="Enter full name">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-at"></i> Username *</label>
                        <input type="text" class="form-control" id="editUsername" required placeholder="Enter username">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address *</label>
                        <input type="email" class="form-control" id="editEmail" required placeholder="Enter email address">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Phone Number</label>
                        <input type="tel" class="form-control" id="editPhone" placeholder="Enter phone number">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-user-tag"></i> User Role *</label>
                        <select class="form-control" id="editRole" required>
                            <option value="">Select Role</option>
                            <option value="tenant">Tenant</option>
                            <option value="landlord">Landlord</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-toggle-on"></i> Account Status *</label>
                        <select class="form-control" id="editStatus" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" id="changePassword" style="width: auto; margin-right: 10px;">
                            <span><i class="fas fa-key"></i> Change Password</span>
                        </label>
                    </div>
                    <div class="form-group" id="newPasswordGroup" style="display: none;">
                        <label><i class="fas fa-lock"></i> New Password *</label>
                        <input type="password" class="form-control" id="editNewPassword" placeholder="Enter new password" minlength="6">
                        <small style="color: var(--text-medium); font-size: 0.85rem;">
                            Password must be at least 6 characters long
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" onclick="closeEditUserModal()">Cancel</button>
                <button class="btn btn-primary" id="editUserBtn" onclick="submitEditUser()">
                    <i class="fas fa-save"></i> Update User
                </button>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div id="changePasswordModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3><i class="fas fa-key"></i> Change Admin Password</h3>
                <button class="modal-close" onclick="closeChangePasswordModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm">
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Current Password *</label>
                        <input type="password" class="form-control" id="currentPassword" required placeholder="Enter current password">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-key"></i> New Password *</label>
                        <input type="password" class="form-control" id="newPassword" required placeholder="Enter new password" minlength="8">
                        <small style="color: var(--text-medium); font-size: 0.85rem;">
                            Password must be at least 8 characters long
                        </small>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-check-circle"></i> Confirm New Password *</label>
                        <input type="password" class="form-control" id="confirmPassword" required placeholder="Re-enter new password">
                    </div>
                    <div style="background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107; margin-top: 20px;">
                        <strong><i class="fas fa-info-circle"></i> Password Requirements:</strong>
                        <ul style="margin: 10px 0 0 20px; font-size: 0.9rem;">
                            <li>Minimum 8 characters</li>
                            <li>At least one number</li>
                        </ul>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" onclick="closeChangePasswordModal()">Cancel</button>
                <button class="btn btn-primary" id="changePasswordBtn" onclick="submitChangePassword()">
                    <i class="fas fa-save"></i> Change Password
                </button>
            </div>
        </div>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
