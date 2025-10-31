<?php
// List users API for admin panel
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
    $status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;

    // Build WHERE clause
    $where_conditions = [];
    $params = [];
    $types = '';

    if (!empty($search)) {
        $where_conditions[] = "(username LIKE ? OR email LIKE ? OR full_name LIKE ? OR phone LIKE ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= 'ssss';
    }

    if (!empty($role_filter) && $role_filter !== 'all') {
        $where_conditions[] = "user_type = ?";
        $params[] = $role_filter;
        $types .= 's';
    }

    if (!empty($status_filter) && $status_filter !== 'all') {
        $where_conditions[] = "status = ?";
        $params[] = $status_filter;
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
        SELECT user_id, username, email, full_name, phone, user_type, status,
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
        // Format the data for frontend
        $users[] = [
            'id' => $row['user_id'],
            'username' => $row['username'],
            'email' => $row['email'],
            'full_name' => $row['full_name'],
            'phone' => $row['phone'],
            'role' => $row['user_type'],
            'status' => $row['status'],
            'profile_image' => $row['profile_image'] ?: 'https://via.placeholder.com/40x40/cccccc/666666?text=' . substr($row['full_name'], 0, 1),
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
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load users: ' . $e->getMessage()
    ]);
}
?>