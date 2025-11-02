// Settings Page JavaScript Functions

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
            showAlert('success', 'Profile updated successfully!');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', 'Error: ' + (data.message || 'Could not update profile'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while updating profile');
    });
}

function handlePasswordChange(e) {
    e.preventDefault();
    
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (newPassword !== confirmPassword) {
        showAlert('error', 'New passwords do not match!');
        return;
    }
    
    if (newPassword.length < 8) {
        showAlert('error', 'Password must be at least 8 characters long!');
        return;
    }
    
    fetch('../api/change_password.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            current_password: currentPassword,
            new_password: newPassword,
            confirm_password: confirmPassword
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Password changed successfully! You will be logged out.');
            setTimeout(() => {
                window.location.href = '../api/logout.php';
            }, 1500);
        } else {
            showAlert('error', 'Error: ' + (data.message || 'Could not change password'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while changing password');
    });
}

function handleImageUpload(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file size (max 5MB)
        const maxSize = 5 * 1024 * 1024;
        if (file.size > maxSize) {
            showAlert('error', 'File size must be less than 5MB');
            return;
        }

        // Validate file type
        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            showAlert('error', 'Only JPG, PNG, and GIF files are allowed');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('profilePreview').src = event.target.result;
            
            // Auto-upload
            const formData = new FormData();
            formData.append('profile_image', file);
            
            fetch('../api/upload_profile_image.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Profile image uploaded successfully');
                } else {
                    showAlert('error', 'Error uploading image: ' + (data.message || 'Unknown error'));
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'An error occurred while uploading image');
                location.reload();
            });
        };
        reader.readAsDataURL(file);
    }
}

function showAlert(type, message) {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${message}
    `;
    
    // Insert at top of main content
    const mainContent = document.querySelector('.dashboard-content');
    mainContent.insertBefore(alertDiv, mainContent.firstChild);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', handleProfileSubmit);
    }
    
    const passwordForm = document.getElementById('passwordForm');
    if (passwordForm) {
        passwordForm.addEventListener('submit', handlePasswordChange);
    }
    
    const profileUpload = document.getElementById('profileUpload');
    if (profileUpload) {
        profileUpload.addEventListener('change', handleImageUpload);
    }
});
