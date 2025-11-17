<?php
// Update Property Status API - Handle property status updates
session_start();
header('Content-Type: application/json');

require_once '../config/database.php';
require_once '../includes/auth.php';

// Check if user is logged in and is a landlord
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Please log in to continue']);
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'landlord') {
    echo json_encode(['success' => false, 'message' => 'Only landlords can update property status']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $db = new Database();
    $conn = $db->connect();

    // Get form data
    $property_id = $_POST['property_id'] ?? null;
    $status = $_POST['status'] ?? null;

    if (!$property_id) {
        echo json_encode(['success' => false, 'message' => 'Property ID is required']);
        exit();
    }

    if (!$status || !in_array($status, ['available', 'rented'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit();
    }

    // Verify property belongs to this landlord
    $stmt = $conn->prepare("SELECT landlord_id FROM properties WHERE property_id = ?");
    $stmt->execute([$property_id]);
    $property = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$property) {
        echo json_encode(['success' => false, 'message' => 'Property not found']);
        exit();
    }

    if ($property['landlord_id'] != $user_id) {
        echo json_encode(['success' => false, 'message' => 'You do not have permission to update this property']);
        exit();
    }

    // Update property status
    $stmt = $conn->prepare("UPDATE properties SET status = ?, updated_at = NOW() WHERE property_id = ?");
    $stmt->execute([$status, $property_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Property status updated successfully',
        'property_id' => $property_id,
        'status' => $status
    ]);

} catch (PDOException $e) {
    error_log("Update Property Status Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Update Property Status Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>