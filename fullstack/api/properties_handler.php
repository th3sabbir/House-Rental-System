<?php
// Properties API - Handle property CRUD operations
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$auth = new Auth();
$db = new Database();
$conn = $db->connect();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'get_all':
        get_all_properties($conn);
        break;
    
    case 'get_by_id':
        get_property_by_id($conn);
        break;
    
    case 'search':
        search_properties($conn);
        break;
    
    case 'create':
        $auth->requireRole('landlord');
        create_property($conn, $auth);
        break;
    
    case 'update':
        $auth->requireLogin();
        update_property($conn, $auth);
        break;
    
    case 'delete':
        $auth->requireLogin();
        delete_property($conn, $auth);
        break;
    
    case 'toggle_favorite':
        $auth->requireLogin();
        toggle_favorite($conn, $auth);
        break;
    
    default:
        send_json_response(['success' => false, 'message' => 'Invalid action'], 400);
}

// Update property
function update_property($conn, $auth) {
    try {
        $user = $auth->getCurrentUser();
        $property_id = (int)($_POST['property_id'] ?? 0);
        
        // Verify ownership
        $check_stmt = $conn->prepare("SELECT landlord_id FROM properties WHERE id = ?");
        $check_stmt->execute([$property_id]);
        $property = $check_stmt->fetch();
        
        if (!$property || ($property['landlord_id'] != $user['id'] && $user['role'] !== 'admin')) {
            send_json_response(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $update_fields = [];
        $values = [];
        
        $allowed_fields = ['title', 'description', 'property_type', 'address', 'city', 'price', 'bedrooms', 'bathrooms', 'area_sqft', 'status'];
        
        foreach ($allowed_fields as $field) {
            if (isset($_POST[$field])) {
                $update_fields[] = "$field = ?";
                $values[] = sanitize_input($_POST[$field]);
            }
        }
        
        if (empty($update_fields)) {
            send_json_response(['success' => false, 'message' => 'No fields to update']);
        }
        
        $values[] = $property_id;
        $sql = "UPDATE properties SET " . implode(', ', $update_fields) . " WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($values);
        
        log_activity($user['id'], 'update_property', "Updated property ID: $property_id");
        
        send_json_response(['success' => true, 'message' => 'Property updated successfully']);
    } catch (PDOException $e) {
        send_json_response(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

// Delete property
function delete_property($conn, $auth) {
    try {
        $user = $auth->getCurrentUser();
        $property_id = (int)($_POST['property_id'] ?? 0);
        
        // Verify ownership
        $check_stmt = $conn->prepare("SELECT landlord_id FROM properties WHERE id = ?");
        $check_stmt->execute([$property_id]);
        $property = $check_stmt->fetch();
        
        if (!$property || ($property['landlord_id'] != $user['id'] && $user['role'] !== 'admin')) {
            send_json_response(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $stmt = $conn->prepare("DELETE FROM properties WHERE id = ?");
        $stmt->execute([$property_id]);
        
        log_activity($user['id'], 'delete_property', "Deleted property ID: $property_id");
        
        send_json_response(['success' => true, 'message' => 'Property deleted successfully']);
    } catch (PDOException $e) {
        send_json_response(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

// Get all properties
function get_all_properties($conn) {
    try {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        // Count total properties
        $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM properties WHERE status = 'available'");
        $count_stmt->execute();
        $total = $count_stmt->fetch()['total'];
        
        // Get properties
        $stmt = $conn->prepare("
            SELECT p.*, u.full_name as landlord_name, u.phone as landlord_phone,
                   AVG(r.rating) as avg_rating, COUNT(r.id) as review_count
            FROM properties p
            LEFT JOIN users u ON p.landlord_id = u.id
            LEFT JOIN reviews r ON p.id = r.property_id
            WHERE p.status = 'available'
            GROUP BY p.id
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute([$limit, $offset]);
        $properties = $stmt->fetchAll();
        
        send_json_response([
            'success' => true,
            'properties' => $properties,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'total_pages' => ceil($total / $limit)
            ]
        ]);
    } catch (PDOException $e) {
        send_json_response(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

// Get property by ID
function get_property_by_id($conn) {
    try {
        $id = $_GET['id'] ?? 0;
        
        $stmt = $conn->prepare("
            SELECT p.*, u.full_name as landlord_name, u.phone as landlord_phone, u.email as landlord_email,
                   AVG(r.rating) as avg_rating, COUNT(r.id) as review_count
            FROM properties p
            LEFT JOIN users u ON p.landlord_id = u.id
            LEFT JOIN reviews r ON p.id = r.property_id
            WHERE p.id = ?
            GROUP BY p.id
        ");
        
        $stmt->execute([$id]);
        $property = $stmt->fetch();
        
        if (!$property) {
            send_json_response(['success' => false, 'message' => 'Property not found'], 404);
        }
        
        // Get images
        $img_stmt = $conn->prepare("SELECT * FROM property_images WHERE property_id = ?");
        $img_stmt->execute([$id]);
        $property['images'] = $img_stmt->fetchAll();
        
        // Get amenities
        $amenity_stmt = $conn->prepare("SELECT amenity FROM property_amenities WHERE property_id = ?");
        $amenity_stmt->execute([$id]);
        $property['amenities'] = $amenity_stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Get reviews
        $review_stmt = $conn->prepare("
            SELECT r.*, u.full_name as tenant_name, u.profile_image
            FROM reviews r
            LEFT JOIN users u ON r.tenant_id = u.id
            WHERE r.property_id = ?
            ORDER BY r.created_at DESC
        ");
        $review_stmt->execute([$id]);
        $property['reviews'] = $review_stmt->fetchAll();
        
        send_json_response(['success' => true, 'property' => $property]);
    } catch (PDOException $e) {
        send_json_response(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

// Search properties
function search_properties($conn) {
    try {
        $city = sanitize_input($_GET['city'] ?? '');
        $property_type = sanitize_input($_GET['type'] ?? '');
        $min_price = (int)($_GET['min_price'] ?? 0);
        $max_price = (int)($_GET['max_price'] ?? 999999);
        $bedrooms = (int)($_GET['bedrooms'] ?? 0);
        
        $sql = "
            SELECT p.*, u.full_name as landlord_name,
                   AVG(r.rating) as avg_rating, COUNT(r.id) as review_count
            FROM properties p
            LEFT JOIN users u ON p.landlord_id = u.id
            LEFT JOIN reviews r ON p.id = r.property_id
            WHERE p.status = 'available'
        ";
        
        $params = [];
        
        if ($city) {
            $sql .= " AND p.city LIKE ?";
            $params[] = "%$city%";
        }
        
        if ($property_type) {
            $sql .= " AND p.property_type = ?";
            $params[] = $property_type;
        }
        
        if ($min_price > 0) {
            $sql .= " AND p.price >= ?";
            $params[] = $min_price;
        }
        
        if ($max_price < 999999) {
            $sql .= " AND p.price <= ?";
            $params[] = $max_price;
        }
        
        if ($bedrooms > 0) {
            $sql .= " AND p.bedrooms >= ?";
            $params[] = $bedrooms;
        }
        
        $sql .= " GROUP BY p.id ORDER BY p.created_at DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $properties = $stmt->fetchAll();
        
        send_json_response(['success' => true, 'properties' => $properties]);
    } catch (PDOException $e) {
        send_json_response(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

// Create property
function create_property($conn, $auth) {
    try {
        $user = $auth->getCurrentUser();
        
        $title = sanitize_input($_POST['title'] ?? '');
        $description = sanitize_input($_POST['description'] ?? '');
        $property_type = sanitize_input($_POST['property_type'] ?? '');
        $address = sanitize_input($_POST['address'] ?? '');
        $city = sanitize_input($_POST['city'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $bedrooms = (int)($_POST['bedrooms'] ?? 0);
        $bathrooms = (int)($_POST['bathrooms'] ?? 0);
        $area_sqft = (int)($_POST['area_sqft'] ?? 0);
        
        if (empty($title) || empty($price) || empty($city)) {
            send_json_response(['success' => false, 'message' => 'Missing required fields']);
        }
        
        $stmt = $conn->prepare("
            INSERT INTO properties (landlord_id, title, description, property_type, address, city, price, bedrooms, bathrooms, area_sqft)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $user['id'], $title, $description, $property_type, $address, $city, $price, $bedrooms, $bathrooms, $area_sqft
        ]);
        
        $property_id = $conn->lastInsertId();
        
        log_activity($user['id'], 'create_property', "Created property ID: $property_id");
        
        send_json_response(['success' => true, 'message' => 'Property created successfully', 'property_id' => $property_id]);
    } catch (PDOException $e) {
        send_json_response(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

// Toggle favorite
function toggle_favorite($conn, $auth) {
    try {
        $user = $auth->getCurrentUser();
        $property_id = (int)($_POST['property_id'] ?? 0);
        
        // Check if already favorited
        $check_stmt = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND property_id = ?");
        $check_stmt->execute([$user['id'], $property_id]);
        
        if ($check_stmt->fetch()) {
            // Remove favorite
            $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND property_id = ?");
            $stmt->execute([$user['id'], $property_id]);
            $message = 'Removed from favorites';
            $favorited = false;
        } else {
            // Add favorite
            $stmt = $conn->prepare("INSERT INTO favorites (user_id, property_id) VALUES (?, ?)");
            $stmt->execute([$user['id'], $property_id]);
            $message = 'Added to favorites';
            $favorited = true;
        }
        
        send_json_response(['success' => true, 'message' => $message, 'favorited' => $favorited]);
    } catch (PDOException $e) {
        send_json_response(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
?>




