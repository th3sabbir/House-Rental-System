<?php
// Edit Property API - Handle property updates
session_start();
header('Content-Type: application/json');

require_once '../config/database.php';
require_once '../includes/auth.php';

// Check if user is logged in and is a landlord or admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Please log in to continue']);
    exit();
}

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['landlord', 'admin'])) {
    echo json_encode(['success' => false, 'message' => 'Only landlords and admins can edit properties']);
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['role'] === 'admin';

try {
    $db = new Database();
    $conn = $db->connect();

    // Get form data
    $property_id = $_POST['property_id'] ?? null;
    if (!$property_id) {
        echo json_encode(['success' => false, 'message' => 'Property ID is required']);
        exit();
    }

    // Verify property belongs to this landlord (skip for admin)
    if (!$is_admin) {
        $stmt = $conn->prepare("SELECT landlord_id FROM properties WHERE property_id = ?");
        $stmt->execute([$property_id]);
        $property = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$property) {
            echo json_encode(['success' => false, 'message' => 'Property not found']);
            exit();
        }

        if ($property['landlord_id'] != $user_id) {
            echo json_encode(['success' => false, 'message' => 'You do not have permission to edit this property']);
            exit();
        }
    }

    // Get form data
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = $_POST['city'] ?? '';
    $property_type = $_POST['property_type'] ?? '';
    $renter_type = $_POST['renter_type'] ?? '';
    $price_per_month = floatval($_POST['price_per_month'] ?? 0);
    $bedrooms = intval($_POST['bedrooms'] ?? 0);
    $bathrooms = intval($_POST['bathrooms'] ?? 0);
    $balconies = intval($_POST['balconies'] ?? 0);
    $area_sqft = intval($_POST['area_sqft'] ?? 0);
    $floor_number = trim($_POST['floor_number'] ?? '');
    $facing = trim($_POST['facing'] ?? '');
    $status = $_POST['status'] ?? 'available';
    
    // Validate status
    if (!in_array($status, ['available', 'rented'])) {
        $status = 'available';
    }
    $available_from = $_POST['available_from'] ?? null;
    $map_url = isset($_POST['map_url']) ? trim($_POST['map_url']) : null;
    $amenities = json_decode($_POST['amenities'] ?? '[]', true);

    // Validate required fields
    if (empty($title) || empty($description) || empty($address) || empty($city)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
        exit();
    }

    if ($price_per_month <= 0 || $bedrooms <= 0 || $bathrooms <= 0 || $area_sqft <= 0) {
        echo json_encode(['success' => false, 'message' => 'Please enter valid numbers for price, bedrooms, bathrooms, and area']);
        exit();
    }

    // Begin transaction
    $conn->beginTransaction();

    // Update property
    $stmt = $conn->prepare("
        UPDATE properties SET 
            title = ?,
            description = ?,
            address = ?,
            city = ?,
            property_type = ?,
            renter_type = ?,
            price_per_month = ?,
            bedrooms = ?,
            bathrooms = ?,
            balconies = ?,
            area_sqft = ?,
            floor_number = ?,
            facing = ?,
            status = ?,
            available_from = ?,
            map_url = ?,
            updated_at = NOW()
        WHERE property_id = ?
    ");

    $stmt->execute([
        $title,
        $description,
        $address,
        $city,
        $property_type,
        $renter_type,
        $price_per_month,
        $bedrooms,
        $bathrooms,
        $balconies,
        $area_sqft,
        $floor_number,
        $facing,
        $status,
        $available_from,
        $map_url,
        $property_id
    ]);

    // Update amenities
    // First, delete existing amenities
    $stmt = $conn->prepare("DELETE FROM property_amenities WHERE property_id = ?");
    $stmt->execute([$property_id]);

    // Then insert new amenities
    if (!empty($amenities)) {
        $stmt = $conn->prepare("INSERT INTO property_amenities (property_id, amenity) VALUES (?, ?)");
        foreach ($amenities as $amenity) {
            $stmt->execute([$property_id, $amenity]);
        }
    }

    // Handle image deletions
    $images_to_delete = json_decode($_POST['images_to_delete'] ?? '[]', true);
    if (!empty($images_to_delete)) {
        $placeholders = implode(',', array_fill(0, count($images_to_delete), '?'));
        $stmt = $conn->prepare("SELECT image_path FROM property_images WHERE image_id IN ($placeholders)");
        $stmt->execute($images_to_delete);
        $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Delete files
        foreach ($images as $image_path) {
            $file_path = '../' . $image_path;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        // Delete from database
        $stmt = $conn->prepare("DELETE FROM property_images WHERE image_id IN ($placeholders)");
        $stmt->execute($images_to_delete);
    }

    // Handle new image uploads
    $new_images_count = 0;
    if (isset($_FILES['new_property_images']) && !empty($_FILES['new_property_images']['name'][0])) {
        $upload_dir = '../uploads/properties/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $new_main_image_index = $_POST['new_main_image_index'] ?? null;
        $uploaded_images = [];

        foreach ($_FILES['new_property_images']['tmp_name'] as $index => $tmp_name) {
            if ($_FILES['new_property_images']['error'][$index] === UPLOAD_ERR_OK) {
                $file_extension = strtolower(pathinfo($_FILES['new_property_images']['name'][$index], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (!in_array($file_extension, $allowed_extensions)) {
                    continue;
                }

                $new_filename = uniqid('property_' . $property_id . '_') . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;

                if (move_uploaded_file($tmp_name, $upload_path)) {
                    $uploaded_images[$index] = 'uploads/properties/' . $new_filename;
                    $new_images_count++;
                }
            }
        }

        // If new main image is set, unset all existing main images
        if ($new_main_image_index !== null) {
            $stmt = $conn->prepare("UPDATE property_images SET is_primary = 0 WHERE property_id = ?");
            $stmt->execute([$property_id]);
        }

        // Insert new images
        foreach ($uploaded_images as $index => $image_path) {
            $is_primary = ($new_main_image_index !== null && $index == $new_main_image_index) ? 1 : 0;
            $stmt = $conn->prepare("INSERT INTO property_images (property_id, image_path, is_primary) VALUES (?, ?, ?)");
            $stmt->execute([$property_id, $image_path, $is_primary]);
        }
    }

    // Validate minimum image requirement (2 images)
    $stmt = $conn->prepare("SELECT COUNT(*) FROM property_images WHERE property_id = ?");
    $stmt->execute([$property_id]);
    $total_images = $stmt->fetchColumn();

    if ($total_images < 2) {
        echo json_encode(['success' => false, 'message' => 'Property must have at least 2 images']);
        exit();
    }

    // Handle main image change for existing images
    $main_image_type = $_POST['main_image_type'] ?? 'existing';
    if ($main_image_type === 'existing') {
        $main_image_id = $_POST['main_image_id'] ?? null;
        if ($main_image_id) {
            // Unset all main images
            $stmt = $conn->prepare("UPDATE property_images SET is_primary = 0 WHERE property_id = ?");
            $stmt->execute([$property_id]);

            // Set new main image
            $stmt = $conn->prepare("UPDATE property_images SET is_primary = 1 WHERE image_id = ? AND property_id = ?");
            $stmt->execute([$main_image_id, $property_id]);
        }
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Property updated successfully',
        'property_id' => $property_id
    ]);

} catch (PDOException $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    error_log("Edit Property Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    error_log("Edit Property Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
