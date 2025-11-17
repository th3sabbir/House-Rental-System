<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'unread_count' => 0]);
    exit;
}

$current_user_id = $_SESSION['user_id'];

try {
    $sql = "SELECT COUNT(*) as unread_count FROM messages WHERE receiver_id = ? AND is_read = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    echo json_encode(['success' => true, 'unread_count' => $row['unread_count']]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'unread_count' => 0, 'message' => $e->getMessage()]);
}

$conn->close();
?>
