<?php
// Settings Page - Landlord profile and account settings

// Handle variable scoping when included
if (!isset($user_id)) {
    $user_id = $GLOBALS['user_id'] ?? null;
}
if (!isset($pdo)) {
    $pdo = $GLOBALS['pdo'] ?? null;
}

if (empty($user_id) || empty($pdo)) {
    error_log("Landlord settings access error: missing user_id or db connection");
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
        FROM users WHERE user_id = ? AND user_type = 'landlord'
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        error_log("Landlord settings: User not found for user_id=$user_id");
        ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            User profile not found. Please try logging out and logging back in.
        </div>
        <?php
        return;
    }

} catch (PDOException $e) {
    error_log("Landlord settings data loading error: " . $e->getMessage());
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
        <?php
        $profile_image_src = '';
        if (!empty($user['profile_image'])) {
            // Check if file exists
            $img_path = '../uploads/' . $user['profile_image'];
            if (file_exists($img_path)) {
                $profile_image_src = $img_path;
            }
        }
        // Fallback to default avatar with first character
        if (empty($profile_image_src)) {
            $first_char = strtoupper(substr($user['full_name'], 0, 1));
            $profile_image_src = 'https://ui-avatars.com/api/?name=' . urlencode($first_char) . '&background=1abc9c&color=fff&size=120';
        }
        ?>
        <img id="profileImage" 
             src="<?php echo htmlspecialchars($profile_image_src); ?>" 
             alt="Profile">
        <div>
            <div class="upload-btn-wrapper">
                <button class="btn btn-primary" type="button">
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
            <h2><i class="fas fa-lock"></i> Change Password</h2>
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

<script>
async function saveSettings(event) {
    event.preventDefault();
    
    // Get form values
    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const address = document.getElementById('address').value.trim();
    
    // Combine first and last name
    const fullName = firstName + ' ' + lastName;
    
    // Validate
    if (!firstName || !lastName || !email) {
        alert('Please fill in all required fields (Name and Email)');
        return;
    }
    
    const formData = new FormData();
    formData.append('full_name', fullName);
    formData.append('email', email);
    formData.append('phone', phone);
    formData.append('address', address);

    try {
        const response = await fetch('../api/update_profile.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert('✓ Profile updated successfully!');
            // Update the displayed name in sidebar
            const sidebarName = document.querySelector('.sidebar-profile h3');
            if (sidebarName) {
                sidebarName.textContent = fullName;
            }
            
            // Refresh the page to show updated values
            setTimeout(() => {
                location.reload();
            }, 500);
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Update error:', error);
        alert('Failed to update profile. Please try again.');
    }
}

async function changePhoto(event) {
    const file = event.target.files[0];
    if (!file) return;

    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        alert('Invalid file type. Please select a JPG, PNG, GIF, or WebP image.');
        event.target.value = ''; // Clear the input
        return;
    }

    // Validate file size (5MB max)
    const maxSize = 5 * 1024 * 1024; // 5MB
    if (file.size > maxSize) {
        alert('File too large. Maximum size is 5MB.');
        event.target.value = ''; // Clear the input
        return;
    }

    // Show loading state
    const profileImg = document.getElementById('profileImage');
    const sidebarImg = document.querySelector('.sidebar-profile img');
    const originalSrc = profileImg.src;

    // Preview image immediately
    const reader = new FileReader();
    reader.onload = function(e) {
        profileImg.src = e.target.result;
        if (sidebarImg) {
            sidebarImg.src = e.target.result;
        }
    };
    reader.readAsDataURL(file);

    // Upload to server
    try {
        const formData = new FormData();
        formData.append('profile_image', file);

        const response = await fetch('../api/upload_profile_photo.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert('✓ Profile picture updated successfully!');
            // Reload to get the updated image from server
            setTimeout(() => {
                location.reload();
            }, 500);
        } else {
            alert('Error: ' + result.message);
            // Restore original image on error
            profileImg.src = originalSrc;
            if (sidebarImg) {
                sidebarImg.src = originalSrc;
            }
        }
    } catch (error) {
        console.error('Upload error:', error);
        alert('Failed to upload photo. Please try again.');
        // Restore original image on error
        profileImg.src = originalSrc;
        if (sidebarImg) {
            sidebarImg.src = originalSrc;
        }
    }

    // Clear the input so the same file can be selected again
    event.target.value = '';
}

// Password Modal Functions
function openPasswordModal() {
    document.getElementById('passwordModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePasswordModal() {
    document.getElementById('passwordModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    document.getElementById('passwordForm').reset();
}

async function changePassword(event) {
    event.preventDefault();
    
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    // Client-side validation
    if (!currentPassword || !newPassword || !confirmPassword) {
        alert('Please fill in all fields!');
        return;
    }
    
    if (newPassword !== confirmPassword) {
        alert('New passwords do not match!');
        return;
    }
    
    if (newPassword.length < 8) {
        alert('Password must be at least 8 characters long!');
        return;
    }
    
    // Password strength validation - must contain at least one number
    const hasNumber = /[0-9]/.test(newPassword);

    if (!hasNumber) {
        alert('Password must contain at least one number');
        return;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('updatePasswordBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Changing Password...';
    
    try {
        const response = await fetch('../api/change_password.php', {
            method: 'POST',
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
            closePasswordModal();
            
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

// Close password modal when clicking outside or Escape key
document.addEventListener('DOMContentLoaded', function() {
    const passwordModal = document.getElementById('passwordModal');
    if (passwordModal) {
        passwordModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closePasswordModal();
            }
        });
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const passwordModal = document.getElementById('passwordModal');
        if (passwordModal && passwordModal.style.display === 'flex') {
            closePasswordModal();
        }
    }
});
</script>

