<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Log the activity before destroying session
if (isset($_SESSION['user_id'])) {
    try {
        log_activity($_SESSION['user_id'], 'logout', 'User logged out');
    } catch (Exception $e) {
        // Continue with logout even if logging fails
    }
}

// Logout user
$auth = new Auth();
$auth->deleteRememberToken(); // Clear remember me token
$auth->logout();

// Redirect to login page
header('Location: /house_rental/login.php?logout=success');
exit();
?>




