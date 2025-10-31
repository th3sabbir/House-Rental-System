<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Start output buffering
ob_start();

try {
    require_once '../config/database.php';
    require_once '../includes/auth.php';
    require_once '../includes/functions.php';

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Unauthorized. Please log in first.',
            'debug' => 'user_id not set in session'
        ]);
        exit();
    }

    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request method'
        ]);
        exit();
    }

    // Check if file was uploaded
    if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] === UPLOAD_ERR_NO_FILE) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'No file uploaded'
        ]);
        exit();
    }

    $file = $_FILES['profile_image'];

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'File upload error: ' . $file['error']
        ]);
        exit();
    }

    // Validate file type using file extension (more reliable)
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $extension_to_mime = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp'
    ];
    
    if (!in_array($file_extension, $allowed_extensions)) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed.',
            'debug' => 'Extension: ' . $file_extension
        ]);
        exit();
    }
    
    $file_type = $extension_to_mime[$file_extension];

    // Validate file size (max 5MB)
    $max_size = 5 * 1024 * 1024; // 5MB in bytes
    if ($file['size'] > $max_size) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'File too large. Maximum size is 5MB.'
        ]);
        exit();
    }

    // Create uploads directory if it doesn't exist
    $upload_dir = '../uploads/profiles/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Generate unique filename
    $user_id = $_SESSION['user_id'];
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Failed to save uploaded file',
            'debug' => 'move_uploaded_file failed for path: ' . $upload_path
        ]);
        exit();
    }

    // Update database
    try {
        $db = new Database();
        $conn = $db->connect();
        
        // Get old profile image to delete it
        $stmt = $conn->prepare("SELECT profile_image FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $old_image = $stmt->fetchColumn();

        // Update user's profile image in database
        $relative_path = 'profiles/' . $new_filename;
        $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE user_id = ?");
        $result = $stmt->execute([$relative_path, $user_id]);
        
        if (!$result) {
            throw new Exception('Database update failed');
        }
    } catch (PDOException $e) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Database error occurred',
            'debug' => $e->getMessage()
        ]);
        exit();
    }

    // Delete old profile image if it exists and is not the default
    if ($old_image && file_exists('../uploads/' . $old_image) && strpos($old_image, 'profiles/') === 0) {
        unlink('../uploads/' . $old_image);
    }

    // Update session
    $_SESSION['profile_image'] = $relative_path;

    // Log activity
    if (isset($_SESSION['user_id'])) {
        log_activity($_SESSION['user_id'], 'profile_photo_update', 'Profile photo updated');
    }

    ob_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Profile photo updated successfully!',
        'image_url' => '/uploads/' . $relative_path
    ]);

} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while uploading photo',
        'debug' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}

ob_end_flush();
?>




