// Navigation
function showSection(sectionId) {
    // Hide all sections
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => section.classList.remove('active'));

    // Show selected section
    document.getElementById(sectionId).classList.add('active');

    // Update active nav item
    const navLinks = document.querySelectorAll('.sidebar-nav a');
    navLinks.forEach(link => link.classList.remove('active'));
    event.target.closest('a').classList.add('active');
}

// Modal Functions
function viewBooking(bookingId) {
    // This function is now handled inline in bookings.php
    // Keeping for compatibility with other pages
}

function writeReview(bookingId) {
    alert('Review form would open here for booking #' + bookingId);
    // Implement review modal
}

function viewProperty(propertyId) {
    // Redirect to property details page
    window.location.href = '../property-details.php?id=' + propertyId;
}

function bookProperty(propertyId) {
    // Redirect to property details page for booking
    window.location.href = '../property-details.php?id=' + propertyId + '&action=book';
}

function removeFavorite(propertyId) {
    if (confirm('Remove this property from favorites?')) {
        alert('Property removed from favorites!');
        // Add remove logic here
    }
}

function contactLandlord() {
    // Redirect to chat page
    window.location.href = '../messages.php';
}

function closeBookingModal() {
    const modal = document.getElementById('bookingModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const bookingModal = document.getElementById('bookingModal');
    if (bookingModal) {
        bookingModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeBookingModal();
            }
        });
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const bookingModal = document.getElementById('bookingModal');
        const passwordModal = document.getElementById('passwordModal');
        
        if (bookingModal && bookingModal.style.display === 'block') {
            closeBookingModal();
        }
        if (passwordModal && passwordModal.classList.contains('active')) {
            closePasswordModal();
        }
    }
});

// Form submission
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

// Change Photo Function
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
            alert('✓ ' + result.message);
            // Update with server URL
            const serverUrl = '../' + result.image_url.substring(1);
            profileImg.src = serverUrl;
            if (sidebarImg) {
                sidebarImg.src = serverUrl;
            }
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
    document.getElementById('passwordModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closePasswordModal() {
    document.getElementById('passwordModal').classList.remove('active');
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

// Close password modal when clicking outside
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

// Mobile Menu Toggle Functions
function toggleSidebar() {
    const sidebar = document.getElementById('tenantSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('mobile-active');
    overlay.classList.toggle('active');
}

function closeSidebar() {
    const sidebar = document.getElementById('tenantSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.remove('mobile-active');
    overlay.classList.remove('active');
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
});
