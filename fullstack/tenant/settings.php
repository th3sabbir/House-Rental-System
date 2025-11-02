<?php
// Settings Page - Tenant profile and account settings

// Handle variable scoping when included
if (!isset($user_id)) {
    $user_id = $GLOBALS['user_id'] ?? null;
}
if (!isset($pdo)) {
    $pdo = $GLOBALS['pdo'] ?? null;
}

if (empty($user_id) || empty($pdo)) {
    error_log("Tenant settings access error: missing user_id or db connection");
    ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        Settings cannot be displayed. Please log in again.
    </div>
    <?php
    return;
}

try {
    // Get user profile data
    $stmt = $pdo->prepare("
        SELECT user_id, full_name, email, phone, address, 
               profile_image, created_at, updated_at
        FROM users WHERE user_id = ? AND user_type = 'tenant'
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        error_log("Tenant settings: User not found for user_id=$user_id");
        ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            User profile not found. Please try logging out and logging back in.
        </div>
        <?php
        return;
    }

} catch (PDOException $e) {
    error_log("Tenant settings data loading error: " . $e->getMessage());
    ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        Error loading settings: <?php echo htmlspecialchars($e->getMessage()); ?>
    </div>
    <?php
    return;
}

// Parse name into first and last name
$name_parts = explode(' ', $user['full_name'], 2);
$first_name = $name_parts[0] ?? '';
$last_name = $name_parts[1] ?? '';
?>

<div class="dashboard-header">
    <h1>Profile Settings</h1>
    <p class="welcome-message">Manage your account information</p>
</div>

<div class="settings-container">
    <div class="profile-picture-upload">
        <img id="profileImage" 
             src="<?php echo !empty($user['profile_image']) ? '../uploads/' . htmlspecialchars($user['profile_image']) : 'https://ui-avatars.com/api/?name=' . urlencode($user['full_name']) . '&background=1abc9c&color=fff&size=120'; ?>" 
             alt="Profile">
        <div>
            <div class="upload-btn-wrapper">
                <button class="btn btn-primary">
                    <i class="fas fa-camera"></i> Change Photo
                </button>
                <input type="file" id="photoUpload" name="profile_image" accept="image/*" onchange="changePhoto(event)">
            </div>
            <p style="font-size: 0.9em; color: #999; margin-top: 10px;">
                JPG, PNG or GIF. Max 5MB.
            </p>
        </div>
    </div>

    <form id="settingsForm" onsubmit="saveSettings(event)">
        <div class="form-row">
            <div class="form-group">
                <label for="firstName">First Name *</label>
                <input type="text" id="firstName" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
            </div>
            <div class="form-group">
                <label for="lastName">Last Name *</label>
                <input type="text" id="lastName" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
        </div>

        <div class="settings-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
            <button type="button" class="btn btn-secondary" onclick="openPasswordModal()">
                <i class="fas fa-key"></i> Change Password
            </button>
        </div>
    </form>

    <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #dee2e6;">
        <h3 style="margin-bottom: 15px;">Account Information</h3>
        <p style="color: var(--text-medium); margin-bottom: 10px;">
            <strong>Account Created:</strong> <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
        </p>
        <p style="color: var(--text-medium);">
            <strong>Last Updated:</strong> <?php echo date('M d, Y \a\t h:i A', strtotime($user['updated_at'])); ?>
        </p>
    </div>
</div>

<!-- Change Password Modal -->
<div id="passwordModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h2>Change Password</h2>
            <button class="modal-close" onclick="closePasswordModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="passwordForm">
                <div class="form-group">
                    <label>Current Password *</label>
                    <input type="password" id="currentPassword" required placeholder="Enter current password">
                </div>
                <div class="form-group">
                    <label>New Password *</label>
                    <input type="password" id="newPassword" required placeholder="Enter new password" minlength="8">
                    <small style="color: #7f8c8d; font-size: 0.85rem;">
                        Password must be at least 8 characters long and contain at least one number.
                    </small>
                </div>
                <div class="form-group">
                    <label>Confirm New Password *</label>
                    <input type="password" id="confirmPassword" required placeholder="Confirm new password" minlength="8">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-danger" onclick="closePasswordModal()">
                <i class="fas fa-times-circle"></i> Cancel
            </button>
            <button class="btn btn-primary" id="updatePasswordBtn" type="button" onclick="changePassword(event)">
                <i class="fas fa-check-circle"></i> Update Password
            </button>
        </div>
    </div>
</div>
