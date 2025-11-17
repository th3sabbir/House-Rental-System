<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($_SESSION['user_id']) || !isset($data['receiver_id']) || !isset($data['message'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

$sender_id = $_SESSION['user_id'];
$receiver_id = (int)$data['receiver_id'];
$message = trim($data['message']);
$property_id = isset($data['property_id']) ? (int)$data['property_id'] : null;

if (empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Message cannot be empty.']);
    exit;
}

try {
    $sql = "
        INSERT INTO messages (sender_id, receiver_id, message)
        VALUES (?, ?, ?)
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iis', $sender_id, $receiver_id, $message);
    $stmt->execute();

    $new_message_id = $conn->insert_id;

    // Fetch the newly created message
    $select_sql = "SELECT message_id, sender_id, receiver_id, message, created_at as timestamp, is_read FROM messages WHERE message_id = ?";
    $select_stmt = $conn->prepare($select_sql);
    $select_stmt->bind_param('i', $new_message_id);
    $select_stmt->execute();
    $result = $select_stmt->get_result();
    $new_message = $result->fetch_assoc();

    echo json_encode(['success' => true, 'message' => $new_message]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>