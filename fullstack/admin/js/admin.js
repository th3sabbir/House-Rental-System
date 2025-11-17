// Mobile Sidebar Toggle
function toggleMobileSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('mobile-active');
    overlay.classList.toggle('active');
}

function closeMobileSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.remove('mobile-active');
    overlay.classList.remove('active');
}

// Close sidebar when clicking on a nav link (mobile)
document.querySelectorAll('.admin-nav a').forEach(link => {
    link.addEventListener('click', function() {
        if (window.innerWidth <= 992) {
            closeMobileSidebar();
        }
    });
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load users if on users page
    if (window.location.search.includes('page=users')) {
        loadUsers();
    }
    
    // Load settings if on settings page
    if (window.location.search.includes('page=settings')) {
        loadSettings();
    }
});

// Global variables for user management
let currentPage = 1;
let currentSearch = '';
let currentRoleFilter = 'all';
let isLoadingUsers = false; // Prevent concurrent requests

// Load users function
async function loadUsers(page = 1) {
    // Prevent concurrent requests
    if (isLoadingUsers) {
        console.log('Already loading users, ignoring request');
        return;
    }
    
    isLoadingUsers = true;
    currentPage = page;
    const usersTableBody = document.getElementById('usersTableBody');
    const userCount = document.getElementById('userCount');
    const paginationContainer = document.getElementById('paginationContainer');
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationButtons = document.getElementById('paginationButtons');

    // Show loading state
    usersTableBody.innerHTML = `
        <tr>
            <td colspan="6" style="text-align: center; padding: 40px;">
                <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: var(--secondary-color);"></i>
                <p style="margin-top: 10px; color: var(--text-medium);">Loading users...</p>
            </td>
        </tr>
    `;

    try {
        const params = new URLSearchParams({
            page: page,
            limit: 10,
            search: currentSearch,
            role: currentRoleFilter
        });

        // Use relative path
        const apiUrl = '../api/list_users.php?' + params.toString();
        console.log('Fetching from:', apiUrl);
        
        // Create abort controller with 10 second timeout
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000);

        const response = await fetch(apiUrl, {
            method: 'GET',
            credentials: 'same-origin',
            signal: controller.signal
        });
        
        clearTimeout(timeoutId);
        
        console.log('Response status:', response.status);
        
        // Parse JSON regardless of status code
        const result = await response.json();
        
        console.log('Response data:', result);
        
        // Check for authentication errors first
        if (!response.ok && (response.status === 401 || response.status === 403)) {
            if (result.redirect) {
                console.log('Redirecting to:', result.redirect);
                window.location.href = result.redirect;
                return;
            }
        }
        
        // Check if response is ok for other errors
        if (!response.ok) {
            throw new Error(result.message || `HTTP error! status: ${response.status}`);
        }

        if (result.success) {
            // Update user count
            userCount.textContent = result.pagination.total_count;

            // Render users table
            if (result.users.length === 0) {
                usersTableBody.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px;">
                            <i class="fas fa-users" style="font-size: 48px; color: var(--text-medium); opacity: 0.5;"></i>
                            <p style="margin-top: 15px; color: var(--text-medium); font-size: 1.1rem;">No users found</p>
                            <p style="color: var(--text-medium);">Try adjusting your search or filters</p>
                        </td>
                    </tr>
                `;
            } else {
                usersTableBody.innerHTML = result.users.map(user => `
                    <tr>
                        <td>
                            <div class="user-cell">
                                <img src="${user.profile_image}" alt="User" class="user-avatar" onerror="this.src='https://ui-avatars.com/api/?name='+encodeURIComponent(user.full_name)+'&background=1abc9c&color=ffffff&size=40'">
                                <div>
                                    <div class="user-name-cell">${user.full_name}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-medium);">@${user.username}</div>
                                </div>
                            </div>
                        </td>
                        <td>${user.email}</td>
                        <td><span class="badge ${user.role}">${user.role.charAt(0).toUpperCase() + user.role.slice(1)}</span></td>
                        <td>${user.created_at}</td>
                        <td>${user.last_login}</td>
                        <td>
                            <div class="action-btns">
                                <div class="tooltip-wrapper">
                                    <button class="btn btn-warning btn-sm" onclick="editUser(${user.id})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <span class="tooltip-text">Edit User</span>
                                </div>
                                <div class="tooltip-wrapper">
                                    <button class="btn btn-danger btn-sm" onclick="deleteUser(${user.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <span class="tooltip-text">Delete User</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                `).join('');
            }

            // Update pagination
            if (result.pagination.total_pages > 1) {
                paginationContainer.style.display = 'block';
                paginationInfo.textContent = `Page ${result.pagination.current_page} of ${result.pagination.total_pages} (${result.pagination.total_count} total users)`;

                let buttonsHtml = '';

                // Previous button
                if (result.pagination.current_page > 1) {
                    buttonsHtml += `<button class="btn btn-sm" onclick="loadUsers(${result.pagination.current_page - 1})"><i class="fas fa-chevron-left"></i> Previous</button>`;
                }

                // Page numbers
                const startPage = Math.max(1, result.pagination.current_page - 2);
                const endPage = Math.min(result.pagination.total_pages, result.pagination.current_page + 2);

                for (let i = startPage; i <= endPage; i++) {
                    if (i === result.pagination.current_page) {
                        buttonsHtml += `<button class="btn btn-primary btn-sm" disabled>${i}</button>`;
                    } else {
                        buttonsHtml += `<button class="btn btn-sm" onclick="loadUsers(${i})">${i}</button>`;
                    }
                }

                // Next button
                if (result.pagination.current_page < result.pagination.total_pages) {
                    buttonsHtml += `<button class="btn btn-sm" onclick="loadUsers(${result.pagination.current_page + 1})">Next <i class="fas fa-chevron-right"></i></button>`;
                }

                paginationButtons.innerHTML = buttonsHtml;
            } else {
                paginationContainer.style.display = 'none';
            }
        } else {
            // Check if it's an authentication error
            if (result.redirect) {
                // Redirect to login page
                window.location.href = result.redirect;
                return;
            }
            
            usersTableBody.innerHTML = `
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: var(--danger-color);"></i>
                        <p style="margin-top: 15px; color: var(--danger-color); font-size: 1.1rem;">Error loading users</p>
                        <p style="color: var(--text-medium);">${result.message || 'Unknown error occurred'}</p>
                        ${result.error ? `<p style="color: var(--text-medium); font-size: 0.9rem; margin-top: 10px;">Details: ${result.error}</p>` : ''}
                    </td>
                </tr>
            `;
        }
    } catch (error) {
        console.error('Error loading users:', error);
        let errorMessage = error.message;
        if (error.name === 'AbortError') {
            errorMessage = 'Request timeout - server is not responding';
        }
        usersTableBody.innerHTML = `
            <tr>
                <td colspan="6" style="text-align: center; padding: 40px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: var(--danger-color);"></i>
                    <p style="margin-top: 15px; color: var(--danger-color); font-size: 1.1rem;">Connection Error</p>
                    <p style="color: var(--text-medium);">Unable to load users. Please try again.</p>
                    <p style="color: var(--text-medium); font-size: 0.9rem; margin-top: 10px;">${errorMessage}</p>
                    <button class="btn btn-primary btn-sm" onclick="loadUsers()" style="margin-top: 15px;">
                        <i class="fas fa-sync"></i> Retry
                    </button>
                </td>
            </tr>
        `;
    } finally {
        isLoadingUsers = false;
    }
}

// Search and filter functionality
let searchTimeout;
document.getElementById('userSearch')?.addEventListener('input', function() {
    currentSearch = this.value.trim();
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadUsers(1);
    }, 300); // Debounce search
});

document.getElementById('roleFilter')?.addEventListener('change', function() {
    currentRoleFilter = this.value;
    loadUsers(1);
});

// Modal Functions
function openAddUserModal() {
    document.getElementById('addUserModal').classList.add('active');
    document.getElementById('addUserForm').reset();
}

function closeAddUserModal() {
    document.getElementById('addUserModal').classList.remove('active');
    document.getElementById('addUserForm').reset();
}

function openEditUserModal() {
    document.getElementById('editUserModal').classList.add('active');
}

function closeEditUserModal() {
    document.getElementById('editUserModal').classList.remove('active');
    document.getElementById('editUserForm').reset();
    document.getElementById('newPasswordGroup').style.display = 'none';
    document.getElementById('changePassword').checked = false;
}

async function submitAddUser() {
    const form = document.getElementById('addUserForm');
    const btn = document.getElementById('addUserBtn');

    // Basic validation
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // Collect form data
    const formData = new FormData();
    formData.append('full_name', document.getElementById('addFullName').value.trim());
    formData.append('username', document.getElementById('addUsername').value.trim());
    formData.append('email', document.getElementById('addEmail').value.trim());
    formData.append('phone', document.getElementById('addPhone').value.trim());
    formData.append('role', document.getElementById('addRole').value);
    formData.append('password', document.getElementById('addPassword').value);

    // Disable button
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

    try {
        const response = await fetch('../api/add_user.php', {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert('User added successfully! ✓');
            closeAddUserModal();
            loadUsers(); // Refresh the users list
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while adding the user. Please try again.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Add User';
    }
}

async function submitEditUser() {
    const form = document.getElementById('editUserForm');
    const btn = document.getElementById('editUserBtn');

    // Basic validation
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // Collect form data
    const formData = new FormData();
    formData.append('user_id', document.getElementById('editUserId').value);
    formData.append('full_name', document.getElementById('editFullName').value.trim());
    formData.append('username', document.getElementById('editUsername').value.trim());
    formData.append('email', document.getElementById('editEmail').value.trim());
    formData.append('phone', document.getElementById('editPhone').value.trim());
    formData.append('role', document.getElementById('editRole').value);

    const changePassword = document.getElementById('changePassword').checked;
    if (changePassword) {
        const newPassword = document.getElementById('editNewPassword').value;
        if (!newPassword || newPassword.length < 6) {
            alert('New password must be at least 6 characters long');
            return;
        }
        formData.append('change_password', '1');
        formData.append('new_password', newPassword);
    }

    // Disable button
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';

    try {
        const response = await fetch('../api/edit_user.php', {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert('User updated successfully! ✓');
            closeEditUserModal();
            loadUsers(); // Refresh the users list
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while updating the user. Please try again.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Update User';
    }
}

// User Management Functions
async function editUser(userId) {
    try {
        // Fetch user data
        const response = await fetch(`../api/list_users.php?page=1&limit=1&search=${userId}`, {
            credentials: 'same-origin'
        });
        const result = await response.json();

        if (result.success && result.users.length > 0) {
            const user = result.users[0];

            // Populate edit form
            document.getElementById('editUserId').value = user.id;
            document.getElementById('editFullName').value = user.full_name;
            document.getElementById('editUsername').value = user.username;
            document.getElementById('editEmail').value = user.email;
            document.getElementById('editPhone').value = user.phone || '';
            document.getElementById('editRole').value = user.role;

            openEditUserModal();
        } else {
            alert('Error: Could not load user data');
        }
    } catch (error) {
        console.error('Error loading user for edit:', error);
        alert('An error occurred while loading user data. Please try again.');
    }
}

async function deleteUser(userId) {
    if (!confirm('Are you sure you want to deactivate this user? This action cannot be undone.')) {
        return;
    }

    try {
        const formData = new FormData();
        formData.append('user_id', userId);

        const response = await fetch('../api/delete_user.php', {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert(result.message);
            loadUsers(); // Refresh the users list
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while deleting the user. Please try again.');
    }
}

// Password change toggle
document.getElementById('changePassword')?.addEventListener('change', function() {
    const passwordGroup = document.getElementById('newPasswordGroup');
    const passwordInput = document.getElementById('editNewPassword');

    if (this.checked) {
        passwordGroup.style.display = 'block';
        passwordInput.required = true;
    } else {
        passwordGroup.style.display = 'none';
        passwordInput.required = false;
        passwordInput.value = '';
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddUserModal();
        closeEditUserModal();
        closeChangePasswordModal();
    }
});

// Close modal when clicking outside
document.getElementById('addUserModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddUserModal();
    }
});

document.getElementById('editUserModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditUserModal();
    }
});

// Settings Tab Switching
function showSettingsTab(tabName, clickedCard) {
    // Remove active class from all cards and tabs
    const cards = document.querySelectorAll('.stat-card.clickable');
    const tabs = document.querySelectorAll('.settings-tab');

    cards.forEach(card => card.classList.remove('active'));
    tabs.forEach(tab => tab.classList.remove('active'));

    // Add active class to clicked card
    if (clickedCard) {
        clickedCard.classList.add('active');
    }

    // Show selected tab
    const selectedTab = document.getElementById(tabName + 'Settings');
    if (selectedTab) {
        selectedTab.classList.add('active');
    }
}

async function loadSettings() {
    try {
        const response = await fetch('../api/load_settings.php', {
            credentials: 'same-origin'
        });
        const result = await response.json();

        if (result.success) {
            const settings = result.settings;

            // General settings
            if (settings.admin_email) document.getElementById('adminEmail').value = settings.admin_email;
            if (settings.contact_phone) document.getElementById('contactPhone').value = settings.contact_phone;
            if (settings.business_address) document.getElementById('businessAddress').value = settings.business_address;
            if (settings.timezone) document.getElementById('timezone').value = settings.timezone;
            if (settings.default_language) document.getElementById('defaultLanguage').value = settings.default_language;
            if (settings.currency) document.getElementById('currency').value = settings.currency;

            // Security settings
            if (settings.verification_method) document.getElementById('verificationMethod').value = settings.verification_method;
            if (settings.min_password_length) document.getElementById('minPasswordLength').value = settings.min_password_length;
            if (settings.require_special_chars) document.getElementById('requireSpecialChars').checked = settings.require_special_chars == '1';
            if (settings.require_numbers) document.getElementById('requireNumbers').checked = settings.require_numbers == '1';
            if (settings.enable_ip_tracking) document.getElementById('enableIpTracking').checked = settings.enable_ip_tracking == '1';
            if (settings.send_security_alerts) document.getElementById('sendSecurityAlerts').checked = settings.send_security_alerts == '1';

            // Notification settings
            if (settings.email_new_user) document.getElementById('emailNewUser').checked = settings.email_new_user == '1';
            if (settings.email_new_property) document.getElementById('emailNewProperty').checked = settings.email_new_property == '1';
            if (settings.email_booking_confirm) document.getElementById('emailBookingConfirm').checked = settings.email_booking_confirm == '1';
            if (settings.email_payment_trans) document.getElementById('emailPaymentTrans').checked = settings.email_payment_trans == '1';
            if (settings.email_system_updates) document.getElementById('emailSystemUpdates').checked = settings.email_system_updates == '1';
            if (settings.sms_critical_alerts) document.getElementById('smsCriticalAlerts').checked = settings.sms_critical_alerts == '1';
            if (settings.sms_booking_notif) document.getElementById('smsBookingNotif').checked = settings.sms_booking_notif == '1';
            if (settings.sms_payment_confirm) document.getElementById('smsPaymentConfirm').checked = settings.sms_payment_confirm == '1';
            if (settings.smtp_server) document.getElementById('smtpServer').value = settings.smtp_server;
            if (settings.smtp_username) document.getElementById('smtpUsername').value = settings.smtp_username;
            if (settings.smtp_password) document.getElementById('smtpPassword').value = settings.smtp_password;
            if (settings.smtp_port) document.getElementById('smtpPort').value = settings.smtp_port;
            if (settings.smtp_encryption) document.getElementById('smtpEncryption').value = settings.smtp_encryption;
        }
    } catch (error) {
        console.error('Error loading settings:', error);
    }
}

// Settings Save Functions
async function saveGeneralSettings() {
    const button = document.getElementById('saveGeneralSettings');
    const originalText = button.innerHTML;
    
    // Collect form data
    const settings = {
        adminEmail: document.getElementById('adminEmail').value,
        contactPhone: document.getElementById('contactPhone').value,
        businessAddress: document.getElementById('businessAddress').value,
        timezone: document.getElementById('timezone').value,
        defaultLanguage: document.getElementById('defaultLanguage').value,
        currency: document.getElementById('currency').value
    };

    // Basic validation
    if (!settings.adminEmail || !settings.contactPhone) {
        alert('Please fill in all required fields!');
        return;
    }

    // Disable button
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    try {
        const response = await fetch('../api/save_settings.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                type: 'general',
                settings: settings
            })
        });

        const result = await response.json();

        if (result.success) {
            alert('General settings saved successfully! ✓');
        } else {
            alert('Error saving settings: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while saving settings. Please try again.');
    } finally {
        button.disabled = false;
        button.innerHTML = originalText;
    }
}

async function saveSecuritySettings() {
    const button = document.getElementById('saveSecuritySettings');
    const originalText = button.innerHTML;
    
    // Collect form data
    const settings = {
        verificationMethod: document.getElementById('verificationMethod').value,
        minPasswordLength: document.getElementById('minPasswordLength').value,
        requireSpecialChars: document.getElementById('requireSpecialChars').checked,
        requireNumbers: document.getElementById('requireNumbers').checked,
        enableIpTracking: document.getElementById('enableIpTracking').checked,
        sendSecurityAlerts: document.getElementById('sendSecurityAlerts').checked
    };

    // Disable button
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    try {
        const response = await fetch('../api/save_settings.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                type: 'security',
                settings: settings
            })
        });

        const result = await response.json();

        if (result.success) {
            alert('Security settings saved successfully! ✓');
        } else {
            alert('Error saving settings: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while saving settings. Please try again.');
    } finally {
        button.disabled = false;
        button.innerHTML = originalText;
    }
}

async function saveNotificationSettings() {
    const button = document.getElementById('saveNotificationSettings');
    const originalText = button.innerHTML;
    
    // Collect form data
    const settings = {
        emailNotifications: {
            newUser: document.getElementById('emailNewUser').checked,
            newProperty: document.getElementById('emailNewProperty').checked,
            bookingConfirm: document.getElementById('emailBookingConfirm').checked,
            paymentTrans: document.getElementById('emailPaymentTrans').checked,
            systemUpdates: document.getElementById('emailSystemUpdates').checked
        },
        smsNotifications: {
            criticalAlerts: document.getElementById('smsCriticalAlerts').checked,
            bookingNotif: document.getElementById('smsBookingNotif').checked,
            paymentConfirm: document.getElementById('smsPaymentConfirm').checked
        },
        smtpSettings: {
            server: document.getElementById('smtpServer').value,
            username: document.getElementById('smtpUsername').value,
            password: document.getElementById('smtpPassword').value,
            port: document.getElementById('smtpPort').value,
            encryption: document.getElementById('smtpEncryption').value
        }
    };

    // Basic validation
    if (!settings.smtpSettings.server || !settings.smtpSettings.username) {
        alert('Please fill in SMTP server and username!');
        return;
    }

    // Disable button
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    try {
        const response = await fetch('../api/save_settings.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                type: 'notifications',
                settings: settings
            })
        });

        const result = await response.json();

        if (result.success) {
            alert('Notification settings saved successfully! ✓');
        } else {
            alert('Error saving settings: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while saving settings. Please try again.');
    } finally {
        button.disabled = false;
        button.innerHTML = originalText;
    }
}

// Change Password Modal Functions
function openChangePasswordModal() {
    document.getElementById('changePasswordModal').classList.add('active');
}

function closeChangePasswordModal() {
    document.getElementById('changePasswordModal').classList.remove('active');
    document.getElementById('changePasswordForm').reset();
}

async function submitChangePassword() {
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    // Validation
    if (!currentPassword || !newPassword || !confirmPassword) {
        alert('Please fill in all fields!');
        return;
    }

    if (newPassword.length < 8) {
        alert('New password must be at least 8 characters long!');
        return;
    }

    if (newPassword !== confirmPassword) {
        alert('New passwords do not match!');
        return;
    }

    // Password strength validation - must contain at least one number
    const hasNumber = /[0-9]/.test(newPassword);

    if (!hasNumber) {
        alert('Password must contain at least one number');
        return;
    }

    // Disable button to prevent double submission
    const submitBtn = document.getElementById('changePasswordBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Changing Password...';

    try {
        const response = await fetch('../api/change_password.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                current_password: currentPassword,
                new_password: newPassword,
                confirm_password: confirmPassword
            })
        });

        const result = await response.json();

        if (result.success) {
            alert('Password changed successfully! ✓\n\nYou will be logged out for security reasons.');
            closeChangePasswordModal();
            
            // Log out the user
            setTimeout(() => {
                window.location.href = '../api/logout.php';
            }, 1000);
        } else {
            alert('Error: ' + result.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while changing password. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

// Close Change Password modal when clicking outside
document.getElementById('changePasswordModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeChangePasswordModal();
    }
});

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddUserModal();
        closeChangePasswordModal();
    }
});
