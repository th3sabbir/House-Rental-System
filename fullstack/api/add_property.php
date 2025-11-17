<?php
session_start();
header('Content-Type: application/json');

// Include database configuration
require_once('../config/database.php');

// Get PDO connection
$db = new Database();
$conn = $db->connect();

// Check if user is logged in and is a landlord
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'landlord') {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access. Please login as landlord.'
    ]);
    exit();
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
    exit();
}

try {
    $landlord_id = $_SESSION['user_id'];
    
    // DEBUG: Log the landlord_id being used
    error_log("ADD_PROPERTY API - Adding property for landlord_id: " . $landlord_id);
    error_log("ADD_PROPERTY API - Session user_id: " . $_SESSION['user_id']);
    error_log("ADD_PROPERTY API - Session role: " . $_SESSION['role']);
    
    // Validate required fields
    $required_fields = ['title', 'address', 'city', 'property_type', 'renter_type', 'price_per_month', 'bedrooms', 'bathrooms', 'area_sqft', 'description'];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            echo json_encode([
                'success' => false,
                'message' => "Missing required field: $field"
            ]);
            exit();
        }
    }
    
    // Validate images
    if (!isset($_FILES['property_images']) || count(array_filter($_FILES['property_images']['name'])) < 2) {
        echo json_encode([
            'success' => false,
            'message' => 'At least 2 property images are required.'
        ]);
        exit();
    }
    
    // Sanitize input data
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $property_type = strtolower(trim($_POST['property_type'])); // Convert to lowercase for enum
    $address = trim($_POST['address']);
    $city = strtolower(trim($_POST['city'])); // Convert to lowercase for consistency
    $renter_type = trim($_POST['renter_type']);
    $status = 'available'; // Always set to available for new properties
    $price_per_month = floatval($_POST['price_per_month']);
    $bedrooms = intval($_POST['bedrooms']);
    $bathrooms = intval($_POST['bathrooms']);
    $balconies = isset($_POST['balconies']) ? intval($_POST['balconies']) : 0;
    $area_sqft = intval($_POST['area_sqft']);
    $floor_number = isset($_POST['floor_number']) && !empty($_POST['floor_number']) ? trim($_POST['floor_number']) : '';
    $facing_direction = isset($_POST['facing']) && !empty($_POST['facing']) ? trim($_POST['facing']) : '';
    $available_from = !empty($_POST['available_from']) ? $_POST['available_from'] : '';
    $map_url = isset($_POST['map_url']) && !empty($_POST['map_url']) ? trim($_POST['map_url']) : '';
    
    // Parse amenities
    $amenities = [];
    if (!empty($_POST['amenities'])) {
        $amenities = json_decode($_POST['amenities'], true);
        if (!is_array($amenities)) {
            $amenities = [];
        }
    }
    
    // Start transaction
    $conn->beginTransaction();
    
    // Insert property
    $stmt = $conn->prepare("INSERT INTO properties (
        landlord_id, title, description, property_type, address, city,
        price_per_month, bedrooms, bathrooms, balconies, area_sqft, 
        floor_number, facing, renter_type, available_from, status, map_url
    ) VALUES (
        :landlord_id, :title, :description, :property_type, :address, :city,
        :price_per_month, :bedrooms, :bathrooms, :balconies, :area_sqft,
        :floor_number, :facing, :renter_type, :available_from, :status, :map_url
    )");
    
    $stmt->execute([
        ':landlord_id' => $landlord_id,
        ':title' => $title,
        ':description' => $description,
        ':property_type' => $property_type,
        ':address' => $address,
        ':city' => $city,
        ':price_per_month' => $price_per_month,
        ':bedrooms' => $bedrooms,
        ':bathrooms' => $bathrooms,
        ':balconies' => $balconies,
        ':area_sqft' => $area_sqft,
        ':floor_number' => $floor_number,
        ':facing' => $facing_direction,
        ':renter_type' => $renter_type,
        ':available_from' => $available_from,
        ':status' => $status,
        ':map_url' => $map_url
    ]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception("Failed to insert property");
    }
    
    $property_id = $conn->lastInsertId();
    error_log("ADD_PROPERTY API - Property inserted successfully. Property ID: $property_id, Landlord ID: $landlord_id");
    
    // Handle image uploads
    $upload_dir = '../uploads/properties/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $main_image_index = isset($_POST['main_image_index']) ? intval($_POST['main_image_index']) : 0;
    $main_image_path = null;
    
    $upload_errors = [];
    $uploaded_images = [];
    
    foreach ($_FILES['property_images']['tmp_name'] as $index => $tmp_name) {
        if (empty($tmp_name)) {
            continue;
        }
        
        // Validate image
        $file_size = $_FILES['property_images']['size'][$index];
        $file_error = $_FILES['property_images']['error'][$index];
        
        if ($file_error !== UPLOAD_ERR_OK) {
            $upload_errors[] = "Upload error for image " . ($index + 1);
            continue;
        }
        
        if ($file_size > 5 * 1024 * 1024) { // 5MB max
            $upload_errors[] = "Image " . ($index + 1) . " is too large (max 5MB)";
            continue;
        }
        
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = $_FILES['property_images']['type'][$index];
        
        if (!in_array($file_type, $allowed_types)) {
            $upload_errors[] = "Invalid file type for image " . ($index + 1);
            continue;
        }
        
        // Generate unique filename
        $extension = pathinfo($_FILES['property_images']['name'][$index], PATHINFO_EXTENSION);
        $filename = 'property_' . $property_id . '_' . time() . '_' . $index . '.' . $extension;
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($tmp_name, $filepath)) {
            $is_primary = ($index === $main_image_index) ? 1 : 0;
            
            // Insert image record
            $img_stmt = $conn->prepare("INSERT INTO property_images (property_id, image_path, is_primary) VALUES (?, ?, ?)");
            $img_path = 'uploads/properties/' . $filename;
            $img_stmt->execute([$property_id, $img_path, $is_primary]);
            
            $uploaded_images[] = $img_path;
            
            if ($is_primary) {
                $main_image_path = $img_path;
            }
        } else {
            $upload_errors[] = "Failed to move uploaded file " . ($index + 1);
        }
    }
    
    // Update main_image in properties table
    if ($main_image_path) {
        $update_stmt = $conn->prepare("UPDATE properties SET main_image = ? WHERE property_id = ?");
        $update_stmt->execute([$main_image_path, $property_id]);
    } else if (!empty($uploaded_images)) {
        // If no main image was set, use the first uploaded image
        $first_image = $uploaded_images[0];
        $update_stmt = $conn->prepare("UPDATE properties SET main_image = ? WHERE property_id = ?");
        $update_stmt->execute([$first_image, $property_id]);
    }
    
    // Insert amenities
    if (!empty($amenities)) {
        $amenity_stmt = $conn->prepare("INSERT INTO property_amenities (property_id, amenity) VALUES (?, ?)");
        foreach ($amenities as $amenity) {
            $amenity = trim($amenity);
            if (!empty($amenity)) {
                $amenity_stmt->execute([$property_id, $amenity]);
            }
        }
    }
    
    // Commit transaction
    $conn->commit();
    
    // Prepare response
    $response = [
        'success' => true,
        'message' => 'Property added successfully!',
        'property_id' => $property_id,
        'uploaded_images' => count($uploaded_images)
    ];
    
    if (!empty($upload_errors)) {
        $response['warnings'] = $upload_errors;
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    // Rollback on error
    if ($conn) {
        $conn->rollBack();
    }
    
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
