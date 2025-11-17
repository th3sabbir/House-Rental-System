<?php
session_start();
header('Content-Type: application/json');

// Include database connection
require_once '../config/database.php';

// Check if user is logged in and is a landlord
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'landlord') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['tour_id']) || !isset($input['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$tour_id = intval($input['tour_id']);
$status = $input['status'];
$landlord_id = $_SESSION['user_id'];

// Validate status
$valid_statuses = ['pending', 'confirmed', 'rejected', 'cancelled'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

try {
    // Initialize database connection
    $db = new Database();
    $conn = $db->connect();
    
    // Verify that this tour belongs to a property owned by this landlord
    $stmt = $conn->prepare("
        SELECT b.*, p.landlord_id, p.title as property_title, u.full_name as tenant_name, u.email as tenant_email
        FROM tours b
        JOIN properties p ON b.property_id = p.property_id
        JOIN users u ON b.tenant_id = u.user_id
        WHERE b.tour_id = ?
    ");
    $stmt->execute([$tour_id]);
    $tour = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$tour) {
        echo json_encode(['success' => false, 'message' => 'Tour not found']);
        exit;
    }
    
    if ($tour['landlord_id'] != $landlord_id) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized: This tour does not belong to you']);
        exit;
    }
    
    // Update tour status
    $stmt = $conn->prepare("
        UPDATE tours 
        SET status = ?, updated_at = NOW() 
        WHERE tour_id = ?
    ");
    $stmt->execute([$status, $tour_id]);
    
    // If accepted, you might want to update property status or send notifications
    if ($status === 'confirmed') {
        // Optional: Update property status to rented
        // $stmt = $conn->prepare("UPDATE properties SET status = 'rented' WHERE property_id = ?");
        // $stmt->execute([$tour['property_id']]);
        
        // TODO: Send email notification to tenant
        // You can add email notification logic here
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Tour status updated successfully',
        'tour_id' => $tour_id,
        'new_status' => $status
    ]);
    
} catch (PDOException $e) {
    error_log("Booking status update error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>
