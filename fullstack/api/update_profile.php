<?php
session_start();
require_once '../config/database.php';

// Initialize database connection
$db = new Database();
$pdo = $db->connect();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$user_id = $_SESSION['user_id'];
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$city = trim($_POST['city'] ?? '');
$postal_code = trim($_POST['postal_code'] ?? '');
$date_of_birth = trim($_POST['date_of_birth'] ?? '');

// Combine first and last name into full_name
$full_name = trim($first_name . ' ' . $last_name);

// Validate input
if (empty($first_name) || empty($last_name) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'First name, last name, and email are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

try {
    // Check if user exists
    $stmt = $pdo->prepare("SELECT user_id, full_name, email FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user_check = $stmt->fetch();
    if (!$user_check) {
        error_log("User not found for user_id: $user_id");
        echo json_encode(['success' => false, 'message' => 'User not found in database']);
        exit;
    }
    error_log("User found: " . json_encode($user_check));

    // Check if email is already taken by another user
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
    $stmt->execute([$email, $user_id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email already in use']);
        exit;
    }

    error_log("Updating profile for user_id: $user_id, full_name: $full_name, email: $email, phone: $phone, address: $address, city: $city, postal_code: $postal_code, date_of_birth: $date_of_birth");

    // Update user profile
    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, address = ?, city = ?, postal_code = ?, date_of_birth = ? WHERE user_id = ?");
    $stmt->execute([$full_name, $email, $phone, $address, $city, $postal_code, $date_of_birth, $user_id]);

    $rowCount = $stmt->rowCount();
    error_log("Update executed, affected rows: $rowCount");

    // Update session data
    $_SESSION['first_name'] = $first_name;
    $_SESSION['last_name'] = $last_name;
    $_SESSION['email'] = $email;
    $_SESSION['phone'] = $phone;
    $_SESSION['address'] = $address;
    $_SESSION['city'] = $city;
    $_SESSION['postal_code'] = $postal_code;
    $_SESSION['date_of_birth'] = $date_of_birth;

    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);

} catch (PDOException $e) {
    error_log("Profile update error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>
