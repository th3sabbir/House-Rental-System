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

if (!isset($input['property_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing property ID']);
    exit;
}

$property_id = intval($input['property_id']);
$landlord_id = $_SESSION['user_id'];

try {
    // Initialize database connection
    $db = new Database();
    $conn = $db->connect();
    
    // Verify that this property belongs to this landlord
    $stmt = $conn->prepare("SELECT * FROM properties WHERE property_id = ? AND landlord_id = ?");
    $stmt->execute([$property_id, $landlord_id]);
    $property = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$property) {
        echo json_encode(['success' => false, 'message' => 'Property not found or you do not have permission to delete it']);
        exit;
    }
    
    // Begin transaction
    $conn->beginTransaction();
    
    try {
        // Get all images associated with this property
        $stmt = $conn->prepare("SELECT image_path FROM property_images WHERE property_id = ?");
        $stmt->execute([$property_id]);
        $images = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Delete image files from disk
        foreach ($images as $image_path) {
            $file_path = '../uploads/' . $image_path;
            if (file_exists($file_path)) {
                @unlink($file_path);
            }
        }
        
        // Also delete main property image if exists
        if (!empty($property['image'])) {
            $main_image_path = '../uploads/properties/' . $property['image'];
            if (file_exists($main_image_path)) {
                @unlink($main_image_path);
            }
        }
        
        // Delete property images from database (will cascade)
        $stmt = $conn->prepare("DELETE FROM property_images WHERE property_id = ?");
        $stmt->execute([$property_id]);
        
        // Delete reviews (if not set to cascade)
        $stmt = $conn->prepare("DELETE FROM reviews WHERE property_id = ?");
        $stmt->execute([$property_id]);
        
        // Delete tours (if not set to cascade)
        $stmt = $conn->prepare("DELETE FROM tours WHERE property_id = ?");
        $stmt->execute([$property_id]);
        
        // Finally, delete the property
        $stmt = $conn->prepare("DELETE FROM properties WHERE property_id = ?");
        $stmt->execute([$property_id]);
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Property deleted successfully',
            'property_id' => $property_id
        ]);
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollBack();
        throw $e;
    }
    
} catch (PDOException $e) {
    error_log("Property deletion error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>
