<?php
// Test database connection and verify tables exist
session_start();

header('Content-Type: application/json');

// Include database configuration
require_once '../config/database.php';

$response = [
    'success' => false,
    'session' => [
        'logged_in' => isset($_SESSION['logged_in']) ? $_SESSION['logged_in'] : false,
        'role' => isset($_SESSION['role']) ? $_SESSION['role'] : 'none',
        'user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'none'
    ],
    'database' => [],
    'tables' => []
];

try {
    // Test database connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }

    $response['database']['connected'] = true;
    $response['database']['host'] = DB_HOST;
    $response['database']['database'] = DB_NAME;

    // Check if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    $response['tables']['users_exists'] = ($result->num_rows > 0);

    if ($result->num_rows > 0) {
        // Get table structure
        $structure = $conn->query("DESCRIBE users");
        $columns = [];
        while ($row = $structure->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
        $response['tables']['users_columns'] = $columns;

        // Get user count
        $count = $conn->query("SELECT COUNT(*) as total FROM users");
        $response['tables']['users_count'] = $count->fetch_assoc()['total'];
    }

    $response['success'] = true;
    $conn->close();

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
