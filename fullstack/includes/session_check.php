<?php
/**
 * Session Check and Authentication Guard
 * Include this file at the top of protected pages
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // User not logged in, redirect to login page
    header('Location: /house_rental/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

// Get current user info
$current_user = [
    'id' => $_SESSION['user_id'] ?? 0,
    'username' => $_SESSION['username'] ?? '',
    'email' => $_SESSION['email'] ?? '',
    'full_name' => $_SESSION['full_name'] ?? '',
    'role' => $_SESSION['role'] ?? 'tenant',
    'profile_image' => $_SESSION['profile_image'] ?? null
];

/**
 * Check if user has specific role
 * @param string $required_role Role to check ('landlord', 'tenant')
 */
function require_role($required_role) {
    global $current_user;
    
    if ($current_user['role'] !== $required_role) {
        // Unauthorized, redirect based on their actual role
        $redirect_urls = [
            'landlord' => '/house_rental/landlord/index.php',
            'tenant' => '/house_rental/tenant/index.php'
        ];
        
        $redirect = $redirect_urls[$current_user['role']] ?? '/house_rental/index.php';
        header("Location: $redirect");
        exit();
    }
}

/**
 * Get logout URL
 */
function get_logout_url() {
    return '/house_rental/api/logout_handler.php';
}

/**
 * Display user name
 */
function get_user_display_name() {
    global $current_user;
    return $current_user['full_name'] ?: $current_user['username'];
}
?>




