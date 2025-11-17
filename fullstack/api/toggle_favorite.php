<?php
/**
 * Toggle Favorite API
 * Adds or removes a property from user's favorites
 */

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php';
require_once '../includes/auth.php';

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please log in to manage favorites']);
    exit;
}

// Get user ID
$user_id = $_SESSION['user_id'];

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get property ID
$property_id = isset($_POST['property_id']) ? (int)$_POST['property_id'] : 0;

if ($property_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid property ID']);
    exit;
}

// Check if property exists
$check_property = $conn->prepare("SELECT property_id FROM properties WHERE property_id = ?");
$check_property->bind_param('i', $property_id);
$check_property->execute();
$result = $check_property->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Property not found']);
    exit;
}
$check_property->close();

// Check if already favorited
$check_fav = $conn->prepare("SELECT favorite_id FROM favorites WHERE user_id = ? AND property_id = ?");
$check_fav->bind_param('ii', $user_id, $property_id);
$check_fav->execute();
$fav_result = $check_fav->get_result();

if ($fav_result->num_rows > 0) {
    // Remove from favorites
    $delete_stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND property_id = ?");
    $delete_stmt->bind_param('ii', $user_id, $property_id);
    
    if ($delete_stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'action' => 'removed',
            'message' => 'Property removed from favorites',
            'is_favorited' => false
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove from favorites']);
    }
    $delete_stmt->close();
} else {
    // Add to favorites
    $insert_stmt = $conn->prepare("INSERT INTO favorites (user_id, property_id) VALUES (?, ?)");
    $insert_stmt->bind_param('ii', $user_id, $property_id);
    
    if ($insert_stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'action' => 'added',
            'message' => 'Property added to favorites',
            'is_favorited' => true
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add to favorites']);
    }
    $insert_stmt->close();
}

$check_fav->close();
$conn->close();
?>
