<?php
// Handle login request
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors, log them

// Start output buffering to catch any stray output
ob_start();

try {
    require_once __DIR__ . '/../includes/auth.php';
    require_once __DIR__ . '/../includes/functions.php';

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        send_json_response(['success' => false, 'message' => 'Invalid request method'], 405);
    }

    // Rate limiting
    if (!check_rate_limit('login_' . get_user_ip(), 5, 300)) {
        send_json_response(['success' => false, 'message' => 'Too many login attempts. Please try again later.'], 429);
    }

    // Create Auth instance first
    $auth = new Auth();
    
    // Validate CSRF token
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (empty($csrf_token) || !$auth->verifyCSRFToken($csrf_token)) {
        send_json_response(['success' => false, 'message' => 'Invalid or missing security token']);
    }
    
    // Get POST data
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']);

    // Validate inputs
    if (empty($username) || empty($password)) {
        send_json_response(['success' => false, 'message' => 'Please provide username and password']);
    }
    
    error_log("Login attempt: Username = $username");

    try {
        // Attempt login
        $result = $auth->login($username, $password);

        if ($result['success']) {
            // Set remember me token if checked
            if ($remember_me) {
                $auth->createRememberToken($_SESSION['user_id']);
            }
            
            // Determine redirect URL based on role
            $redirect_url = '/house_rental/index.php';
            switch ($result['role']) {
                case 'admin':
                    $redirect_url = '/house_rental/admin/index.php';
                    break;
                case 'landlord':
                    $redirect_url = '/house_rental/landlord/index.php';
                    break;
                case 'tenant':
                    $redirect_url = '/house_rental/tenant/index.php';
                    break;
            }
            
            $result['redirect'] = $redirect_url;
            
            // Only log activity if session has user_id
            if (isset($_SESSION['user_id'])) {
                log_activity($_SESSION['user_id'], 'login', 'User logged in');
            }
        }

        // Clear any output buffer
        ob_clean();
        
        send_json_response($result);
    } catch (PDOException $e) {
        // Database error
        error_log('Database error during login: ' . $e->getMessage());
        send_json_response([
            'success' => false,
            'message' => 'Database connection error. Please try again later.',
            'debug' => $e->getMessage()
        ]);
    }
} catch (Exception $e) {
    // Clear any output buffer
    ob_clean();
    
    // Log the error
    error_log('Login error: ' . $e->getMessage());
    
    // Send user-friendly error
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Login system error. Please try again later.',
        'debug' => $e->getMessage() // Remove this in production
    ]);
    exit();
}
?>




