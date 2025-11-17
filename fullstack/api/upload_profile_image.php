<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

ob_start();

try {
    require_once '../config/database.php';

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }

    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit();
    }

    // Check if file was uploaded
    if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] === UPLOAD_ERR_NO_FILE) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'No file uploaded']);
        exit();
    }

    $file = $_FILES['profile_image'];

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'File upload error']);
        exit();
    }

    // Validate file type
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (!in_array($file_extension, $allowed_extensions)) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid file type']);
        exit();
    }

    // Validate file size (max 5MB)
    $max_size = 5 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'File too large. Maximum 5MB.']);
        exit();
    }

    // Create uploads directory if it doesn't exist
    $upload_dir = '../uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Generate unique filename
    $user_id = $_SESSION['user_id'];
    $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Failed to save file']);
        exit();
    }

    // Update database
    $db = new Database();
    $conn = $db->connect();
    
    // Get old profile image to delete it
    $stmt = $conn->prepare("SELECT profile_image FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $old_image = $stmt->fetchColumn();

    // Update user's profile image in database
    $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE user_id = ?");
    $stmt->execute([$new_filename, $user_id]);

    // Delete old profile image if it exists
    if ($old_image && file_exists($upload_dir . $old_image)) {
        unlink($upload_dir . $old_image);
    }

    // Update session
    $_SESSION['profile_image'] = $new_filename;

    ob_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Profile image uploaded successfully',
        'image_url' => '../uploads/' . $new_filename
    ]);

} catch (Exception $e) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

ob_end_flush();
?>
