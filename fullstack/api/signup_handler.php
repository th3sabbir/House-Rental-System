<?php
// Handle registration request
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(['success' => false, 'message' => 'Invalid request method'], 405);
}

// Create Auth instance for CSRF validation
$auth = new Auth();

// Validate CSRF token
$csrf_token = $_POST['csrf_token'] ?? '';
if (empty($csrf_token) || !$auth->verifyCSRFToken($csrf_token)) {
    send_json_response(['success' => false, 'message' => 'Invalid or missing security token']);
}

// Get POST data
$username = sanitize_input($_POST['username'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$full_name = sanitize_input($_POST['full_name'] ?? '');
$phone = sanitize_input($_POST['phone'] ?? '');
$role = sanitize_input($_POST['role'] ?? 'tenant');

// Validate inputs
if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
    send_json_response(['success' => false, 'message' => 'Please fill all required fields']);
}

// Validate email
if (!validate_email($email)) {
    send_json_response(['success' => false, 'message' => 'Invalid email format']);
}

// Validate phone if provided
if (!empty($phone) && !validate_phone($phone)) {
    send_json_response(['success' => false, 'message' => 'Invalid phone number']);
}

// Check password match
if ($password !== $confirm_password) {
    send_json_response(['success' => false, 'message' => 'Passwords do not match']);
}

// Check password strength
if (strlen($password) < 6) {
    send_json_response(['success' => false, 'message' => 'Password must be at least 6 characters long']);
}

// Validate role
$allowed_roles = ['tenant', 'landlord'];
if (!in_array($role, $allowed_roles)) {
    $role = 'tenant';
}

// Attempt registration
$auth = new Auth();
$result = $auth->register($username, $email, $password, $full_name, $phone, $role);

if ($result['success']) {
    // Registration successful - redirect to login page with success message
    // Get base path dynamically
    $basePath = '/' . explode('/', trim($_SERVER['SCRIPT_NAME'], '/'))[0];
    $result['redirect'] = $basePath . '/login.php?registered=1';
    $result['message'] = 'Registration successful! Please log in with your credentials.';
}

send_json_response($result);
?>




