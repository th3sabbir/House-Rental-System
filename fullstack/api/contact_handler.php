<?php
// Contact form handler
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(['success' => false, 'message' => 'Invalid request method'], 405);
}

// Get POST data
$name = sanitize_input($_POST['name'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$phone = sanitize_input($_POST['phone'] ?? '');
$subject = sanitize_input($_POST['subject'] ?? '');
$message = sanitize_input($_POST['message'] ?? '');

// Validate inputs
if (empty($name) || empty($email) || empty($message)) {
    send_json_response(['success' => false, 'message' => 'Please fill all required fields']);
}

if (!validate_email($email)) {
    send_json_response(['success' => false, 'message' => 'Invalid email format']);
}

try {
    $db = new Database();
    $conn = $db->connect();
    
    $stmt = $conn->prepare("
        INSERT INTO contact_requests (name, email, phone, subject, message)
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([$name, $email, $phone, $subject, $message]);
    
    // TODO: Send email notification to admin
    
    send_json_response(['success' => true, 'message' => 'Your message has been sent successfully. We will contact you soon.']);
} catch (PDOException $e) {
    send_json_response(['success' => false, 'message' => 'Failed to send message. Please try again later.'], 500);
}
?>




