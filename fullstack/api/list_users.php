<?php
// List users API for admin panel
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors directly
ini_set('log_errors', 1);

// Set content type first
header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode([
        'success' => false, 
        'message' => 'Session expired or not logged in',
        'redirect' => '../admin/login.php'
    ]);
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode([
        'success' => false, 
        'message' => 'Access denied - Admin privileges required',
        'redirect' => '../admin/login.php'
    ]);
    exit();
}

// Include database configuration
require_once '../config/database.php';

try {
    // Connect to database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        throw new Exception('Database connection failed');
    }

    // Set charset
    $conn->set_charset("utf8mb4");

    // Get search and filter parameters
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $role_filter = isset($_GET['role']) ? trim($_GET['role']) : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;

    // Build WHERE clause
    $where_conditions = [];
    $params = [];
    $types = '';

    if (!empty($search)) {
        if (is_numeric($search)) {
            // If search is numeric, treat as user_id
            $where_conditions[] = "user_id = ?";
            $params[] = (int)$search;
            $types .= 'i';
        } else {
            // Otherwise, search in text fields
            $where_conditions[] = "(username LIKE ? OR email LIKE ? OR full_name LIKE ? OR phone LIKE ?)";
            $search_param = "%$search%";
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
            $types .= 'ssss';
        }
    }

    if (!empty($role_filter) && $role_filter !== 'all') {
        $where_conditions[] = "user_type = ?";
        $params[] = $role_filter;
        $types .= 's';
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    // Get total count for pagination
    $count_sql = "SELECT COUNT(*) as total FROM users $where_clause";
    $count_stmt = $conn->prepare($count_sql);

    if (!empty($params)) {
        $count_stmt->bind_param($types, ...$params);
    }

    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_count = $count_result->fetch_assoc()['total'];
    $count_stmt->close();

    // Get users with pagination
    $sql = "
        SELECT user_id, username, email, full_name, phone, user_type,
               profile_image, created_at, last_login
        FROM users
        $where_clause
        ORDER BY created_at DESC
        LIMIT ? OFFSET ?
    ";

    $stmt = $conn->prepare($sql);

    if (!empty($params)) {
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
        $stmt->bind_param($types, ...$params);
    } else {
        $stmt->bind_param('ii', $limit, $offset);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        // Construct profile image URL
        $profile_image_url = '';
        if (!empty($row['profile_image']) && file_exists(__DIR__ . '/../uploads/' . $row['profile_image'])) {
            $profile_image_url = '../uploads/' . $row['profile_image'];
        } else {
            $profile_image_url = 'https://ui-avatars.com/api/?name=' . urlencode($row['full_name']) . '&background=1abc9c&color=ffffff&size=40';
        }

        // Format the data for frontend
        $users[] = [
            'id' => $row['user_id'],
            'username' => $row['username'],
            'email' => $row['email'],
            'full_name' => $row['full_name'],
            'phone' => $row['phone'],
            'role' => $row['user_type'],
            'profile_image' => $profile_image_url,
            'created_at' => date('M d, Y', strtotime($row['created_at'])),
            'last_login' => $row['last_login'] ? date('M d, Y H:i', strtotime($row['last_login'])) : 'Never'
        ];
    }

    $stmt->close();
    $conn->close();

    // Calculate pagination info
    $total_pages = ceil($total_count / $limit);

    echo json_encode([
        'success' => true,
        'users' => $users,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_count' => $total_count,
            'limit' => $limit
        ]
    ]);

} catch (Exception $e) {
    error_log('List users error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load users',
        'error' => $e->getMessage(),
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}
?>