<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_GET['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$current_user_id = $_SESSION['user_id'];
$other_user_id = (int)$_GET['user_id'];

try {
    // Fetch all messages between current user and other user
    $sql = "
        SELECT 
            message_id,
            sender_id,
            receiver_id,
            message,
            created_at as timestamp,
            is_read
        FROM messages
        WHERE 
            (sender_id = ? AND receiver_id = ?) OR
            (sender_id = ? AND receiver_id = ?)
        ORDER BY created_at ASC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiii', $current_user_id, $other_user_id, $other_user_id, $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    // Mark messages from other user as read
    $update_sql = "
        UPDATE messages 
        SET is_read = 1 
        WHERE sender_id = ? AND receiver_id = ? AND is_read = 0
    ";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('ii', $other_user_id, $current_user_id);
    $update_stmt->execute();

    echo json_encode(['success' => true, 'messages' => $messages]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>