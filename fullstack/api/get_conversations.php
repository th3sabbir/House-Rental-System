<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$current_user_id = $_SESSION['user_id'];

try {
    // Get list of unique conversations with last message info
    $sql = "
        SELECT 
            u.user_id,
            u.full_name,
            CASE WHEN u.profile_image IS NOT NULL AND u.profile_image != '' THEN CONCAT('uploads/', u.profile_image) ELSE 'img/default-avatar.svg' END as profile_image,
            m.last_message,
            m.last_message_time,
            m.sender_id,
            COALESCE(unread_counts.unread_count, 0) as unread_count
        FROM (
            SELECT
                IF(sender_id = ?, receiver_id, sender_id) AS other_user_id,
                MAX(message_id) AS last_message_id
            FROM messages
            WHERE sender_id = ? OR receiver_id = ?
            GROUP BY other_user_id
        ) AS conv
        JOIN (
            SELECT 
                message_id,
                message AS last_message,
                created_at AS last_message_time,
                sender_id
            FROM messages
        ) AS m ON conv.last_message_id = m.message_id
        JOIN users u ON conv.other_user_id = u.user_id
        LEFT JOIN (
            SELECT 
                sender_id, 
                COUNT(*) as unread_count 
            FROM messages 
            WHERE receiver_id = ? AND is_read = 0 
            GROUP BY sender_id
        ) AS unread_counts ON conv.other_user_id = unread_counts.sender_id
        ORDER BY m.last_message_time DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiii', $current_user_id, $current_user_id, $current_user_id, $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $conversations = [];
    while ($row = $result->fetch_assoc()) {
        $conversations[] = $row;
    }

    echo json_encode(['success' => true, 'conversations' => $conversations]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>