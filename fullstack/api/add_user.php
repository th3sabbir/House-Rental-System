<?php
// Add user API for admin panel
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

header('Content-Type: application/json');

// Include required files
require_once '../config/database.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

try {
    // Connect to database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        throw new Exception('Database connection failed');
    }

    // Set charset
    $conn->set_charset("utf8mb4");

    // Get POST data
    $username = sanitize_input($_POST['username'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $full_name = sanitize_input($_POST['full_name'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $role = sanitize_input($_POST['role'] ?? 'tenant');

    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
        exit();
    }

    // Validate email
    if (!validate_email($email)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit();
    }

    // Validate phone if provided
    if (!empty($phone) && !validate_phone($phone)) {
        echo json_encode(['success' => false, 'message' => 'Invalid phone number']);
        exit();
    }

    // Check password strength
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long']);
        exit();
    }

    // Validate role
    $allowed_roles = ['tenant', 'landlord', 'admin'];
    if (!in_array($role, $allowed_roles)) {
        $role = 'tenant';
    }

    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username or email already exists']);
        exit();
    }
    $stmt->close();

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $conn->prepare("
        INSERT INTO users (username, email, password, full_name, phone, user_type, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, 'active', NOW())
    ");

    $stmt->bind_param('ssssss', $username, $email, $hashed_password, $full_name, $phone, $role);

    if ($stmt->execute()) {
        $new_user_id = $conn->insert_id;

        // Get the newly created user data
        $stmt->close();
        $stmt = $conn->prepare("
            SELECT user_id, username, email, full_name, phone, user_type, status, profile_image, created_at
            FROM users WHERE user_id = ?
        ");
        $stmt->bind_param('i', $new_user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        echo json_encode([
            'success' => true,
            'message' => 'User added successfully',
            'user' => [
                'id' => $user['user_id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'full_name' => $user['full_name'],
                'phone' => $user['phone'],
                'role' => $user['user_type'],
                'status' => $user['status'],
                'profile_image' => $user['profile_image'] ?: 'https://via.placeholder.com/40x40/cccccc/666666?text=' . substr($user['full_name'], 0, 1),
                'created_at' => date('M d, Y', strtotime($user['created_at'])),
                'last_login' => 'Never'
            ]
        ]);
    } else {
        throw new Exception('Failed to create user');
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log('Add user error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to add user: ' . $e->getMessage()
    ]);
}
?>