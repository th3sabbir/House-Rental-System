<?php
// Settings Page - User profile and account settings

// Try to recover $user_id and $conn when this file is included inside the dashboard
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If $user_id wasn't provided by the includer, try session
if (!isset($user_id)) {
    $user_id = $_SESSION['user_id'] ?? null;
}

// If $conn wasn't provided, try common globals (so includer can set $GLOBALS['conn'])
if (!isset($conn)) {
    $conn = $GLOBALS['conn'] ?? null;
}

// If still missing, show a friendly message instead of dying so the dashboard can render
if (empty($user_id) || empty($conn)) {
    error_log("Settings access error: missing user_id or db connection (user_id=" . var_export($user_id, true) . ")");
    ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        Settings cannot be displayed because the user is not signed in or the database connection is unavailable.
    </div>
    <?php
    // Stop processing this file but do not terminate the whole dashboard
    return;
}

try {
    // Get user profile data
    $stmt = $conn->prepare("
        SELECT user_id, full_name, email, phone, address, 
               profile_image, created_at, updated_at
        FROM users WHERE user_id = ? AND user_type = 'landlord'
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        error_log("Settings: User not found for user_id=$user_id");
        ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            User profile not found. Please try logging out and logging back in.
        </div>
        <?php
        return;
    }

} catch (PDOException $e) {
    error_log("Settings data loading error: " . $e->getMessage());
    ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        Error loading settings: <?php echo htmlspecialchars($e->getMessage()); ?>
    </div>
    <?php
    return;
}

$success_message = '';
$error_message = '';
?>

<div class="dashboard-header">
    <h1>Account Settings</h1>
    <p class="welcome-message">Manage your profile and account preferences.</p>
</div>

<?php if ($success_message): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
</div>
<?php endif; ?>

<?php if ($error_message): ?>
<div class="alert alert-error">
    <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
</div>
<?php endif; ?>

<div class="settings-container">
    <!-- Profile Picture Section -->
    <div class="settings-card">
        <h3><i class="fas fa-image"></i> Profile Picture</h3>
        <div class="profile-upload">
            <div class="profile-pic-container">
                <img id="profilePreview" 
                     src="<?php echo !empty($user['profile_image']) ? '../uploads/' . htmlspecialchars($user['profile_image']) : 'https://ui-avatars.com/api/?name=' . urlencode($user['full_name']) . '&background=1abc9c&color=fff&size=120'; ?>" 
                     alt="Profile" class="profile-pic-large">
            </div>
            <div class="upload-info">
                <label for="profileUpload" class="upload-btn">
                    <i class="fas fa-camera"></i> Upload New Picture
                </label>
                <input type="file" id="profileUpload" name="profile_image" accept="image/jpeg,image/png,image/gif" style="display: none;">
                <p style="font-size: 0.9em; color: #999; margin-top: 10px;">
                    JPG, PNG or GIF. Max 5MB.
                </p>
            </div>
        </div>
    </div>

    <!-- Personal Information Section -->
    <div class="settings-card">
        <h3><i class="fas fa-user"></i> Personal Information</h3>
        <form id="profileForm" method="POST" onsubmit="handleProfileSubmit(event)">
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>

            <button type="submit" class="save-btn">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </form>
    </div>

    <!-- Password Section -->
    <div class="settings-card">
        <h3><i class="fas fa-lock"></i> Change Password</h3>
        <form id="passwordForm" onsubmit="handlePasswordChange(event)">
            <div class="form-group">
                <label for="currentPassword">Current Password</label>
                <input type="password" id="currentPassword" name="current_password" required>
            </div>

            <div class="form-group">
                <label for="newPassword">New Password</label>
                <input type="password" id="newPassword" name="new_password" required>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" id="confirmPassword" name="confirm_password" required>
            </div>

            <button type="submit" class="save-btn">
                <i class="fas fa-key"></i> Update Password
            </button>
        </form>
    </div>

    <!-- Account Information -->
    <div class="settings-card">
        <h3><i class="fas fa-info-circle"></i> Account Information</h3>
        <div class="info-row">
            <span class="info-label">Account Created:</span>
            <span class="info-value"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Last Updated:</span>
            <span class="info-value"><?php echo date('M d, Y \a\t h:i A', strtotime($user['updated_at'])); ?></span>
        </div>
    </div>
</div>

<script>
    function handleProfileSubmit(e) {
        e.preventDefault();
        const formData = new FormData(document.getElementById('profileForm'));
        
        fetch('../api/update_profile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✓ Profile updated successfully!');
                location.reload();
            } else {
                alert('✗ Error: ' + (data.message || 'Could not update profile'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('✗ An error occurred');
        });
    }

    function handlePasswordChange(e) {
        e.preventDefault();
        
        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        
        if (newPassword !== confirmPassword) {
            alert('✗ Passwords do not match!');
            return;
        }
        
        if (newPassword.length < 8) {
            alert('✗ Password must be at least 8 characters!');
            return;
        }
        
        fetch('../api/change_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                current_password: currentPassword,
                new_password: newPassword,
                confirm_password: confirmPassword
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✓ Password changed! Logging out...');
                setTimeout(() => {
                    window.location.href = '../api/logout.php';
                }, 1000);
            } else {
                alert('✗ Error: ' + (data.message || 'Could not change password'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('✗ An error occurred');
        });
    }

    document.getElementById('profileUpload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('✗ File must be less than 5MB');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('profilePreview').src = event.target.result;
                
                const formData = new FormData();
                formData.append('profile_image', file);
                
                fetch('../api/upload_profile_image.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✓ Image uploaded successfully!');
                    } else {
                        alert('✗ Error: ' + (data.message || 'Upload failed'));
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('✗ An error occurred');
                    location.reload();
                });
            };
            reader.readAsDataURL(file);
        }
    });

    // Attach form handlers
    document.addEventListener('DOMContentLoaded', function() {
        const profileForm = document.getElementById('profileForm');
        if (profileForm) {
            profileForm.addEventListener('submit', handleProfileSubmit);
        }
        
        const passwordForm = document.getElementById('passwordForm');
        if (passwordForm) {
            passwordForm.addEventListener('submit', handlePasswordChange);
        }
    });
</script>

