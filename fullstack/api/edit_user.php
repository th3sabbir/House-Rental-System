<?php
// Edit user API for admin panel
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
    $user_id = (int)($_POST['user_id'] ?? 0);
    $username = sanitize_input($_POST['username'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $full_name = sanitize_input($_POST['full_name'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $role = sanitize_input($_POST['role'] ?? 'tenant');
    $status = sanitize_input($_POST['status'] ?? 'active');
    $change_password = isset($_POST['change_password']) && $_POST['change_password'] === '1';
    $new_password = $_POST['new_password'] ?? '';

    // Validate inputs
    if (empty($user_id) || empty($username) || empty($email) || empty($full_name)) {
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

    // Validate role
    $allowed_roles = ['tenant', 'landlord', 'admin'];
    if (!in_array($role, $allowed_roles)) {
        $role = 'tenant';
    }

    // Validate status
    $allowed_statuses = ['active', 'inactive'];
    if (!in_array($status, $allowed_statuses)) {
        $status = 'active';
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit();
    }
    $stmt->close();

    // Check if username or email already exists (excluding current user)
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE (username = ? OR email = ?) AND user_id != ?");
    $stmt->bind_param('ssi', $username, $email, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username or email already exists']);
        exit();
    }
    $stmt->close();

    // Build update query
    $update_fields = [];
    $params = [];
    $types = '';

    $update_fields[] = "username = ?";
    $params[] = $username;
    $types .= 's';

    $update_fields[] = "email = ?";
    $params[] = $email;
    $types .= 's';

    $update_fields[] = "full_name = ?";
    $params[] = $full_name;
    $types .= 's';

    $update_fields[] = "phone = ?";
    $params[] = $phone;
    $types .= 's';

    $update_fields[] = "user_type = ?";
    $params[] = $role;
    $types .= 's';

    $update_fields[] = "status = ?";
    $params[] = $status;
    $types .= 's';

    // Handle password change
    if ($change_password && !empty($new_password)) {
        if (strlen($new_password) < 6) {
            echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters long']);
            exit();
        }

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_fields[] = "password = ?";
        $params[] = $hashed_password;
        $types .= 's';
    }

    $params[] = $user_id;
    $types .= 'i';

    $sql = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        // Get updated user data
        $stmt->close();
        $stmt = $conn->prepare("
            SELECT user_id, username, email, full_name, phone, user_type, status, profile_image, created_at, last_login
            FROM users WHERE user_id = ?
        ");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        echo json_encode([
            'success' => true,
            'message' => 'User updated successfully',
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
                'last_login' => $user['last_login'] ? date('M d, Y H:i', strtotime($user['last_login'])) : 'Never'
            ]
        ]);
    } else {
        throw new Exception('Failed to update user');
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log('Edit user error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update user: ' . $e->getMessage()
    ]);
}
?>