<?php
// Handle logout request
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/paths.php';

$auth = new Auth();

if ($auth->isLoggedIn()) {
    log_activity($_SESSION['user_id'], 'logout', 'User logged out');
}

$auth->logout();

// Redirect to home page using dynamic path
header('Location: ' . url('index.php'));
exit();
?>





