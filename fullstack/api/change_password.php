<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Start output buffering to catch any stray output
ob_start();

try {
    require_once '../config/database.php';
    require_once '../includes/auth.php';
    require_once '../includes/functions.php';

    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Unauthorized. Please log in first.'
        ]);
        exit();
    }

    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request method'
        ]);
        exit();
    }

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Invalid JSON input'
        ]);
        exit();
    }

    // Validate input
    $current_password = $input['current_password'] ?? '';
    $new_password = $input['new_password'] ?? '';
    $confirm_password = $input['confirm_password'] ?? '';

    // Validation
    if (empty($current_password)) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Current password is required'
        ]);
        exit();
    }

    if (empty($new_password)) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'New password is required'
        ]);
        exit();
    }

    if (strlen($new_password) < 8) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'New password must be at least 8 characters long'
        ]);
        exit();
    }

    // Check password strength - must contain at least one number
    if (!preg_match('/[0-9]/', $new_password)) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Password must contain at least one number'
        ]);
        exit();
    }

    if ($new_password !== $confirm_password) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'New passwords do not match'
        ]);
        exit();
    }

    if ($current_password === $new_password) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'New password must be different from current password'
        ]);
        exit();
    }

    // Change password using Auth class
    $auth = new Auth();
    $user_id = $_SESSION['user_id'];
    
    $result = $auth->changePassword($user_id, $current_password, $new_password);

    // Clean output buffer and send response
    ob_clean();
    
    if ($result['success']) {
        // Log the activity
        if (isset($_SESSION['user_id'])) {
            log_activity($_SESSION['user_id'], 'password_change', 'Password changed successfully');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Password changed successfully! Please log in again with your new password.'
        ]);
    } else {
        echo json_encode($result);
    }

} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while changing password',
        'debug' => $e->getMessage()
    ]);
}

ob_end_flush();
?>




