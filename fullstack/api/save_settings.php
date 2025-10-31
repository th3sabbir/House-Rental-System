<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

// Include database configuration
require_once '../config/database.php';

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        exit();
    }

    $type = $input['type'] ?? '';
    $settings = $input['settings'] ?? [];

    if (empty($type) || empty($settings)) {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
        exit();
    }

    // Connect to database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit();
    }

    switch ($type) {
        case 'general':
            saveGeneralSettings($conn, $settings);
            break;
        case 'security':
            saveSecuritySettings($conn, $settings);
            break;
        case 'notifications':
            saveNotificationSettings($conn, $settings);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid settings type']);
            exit();
    }

    $conn->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

function saveGeneralSettings($conn, $settings) {
    // Sanitize inputs
    $adminEmail = $conn->real_escape_string($settings['adminEmail'] ?? '');
    $contactPhone = $conn->real_escape_string($settings['contactPhone'] ?? '');
    $businessAddress = $conn->real_escape_string($settings['businessAddress'] ?? '');
    $timezone = $conn->real_escape_string($settings['timezone'] ?? '');
    $defaultLanguage = $conn->real_escape_string($settings['defaultLanguage'] ?? '');
    $currency = $conn->real_escape_string($settings['currency'] ?? '');

    // Check if settings table exists, create if not
    $conn->query("CREATE TABLE IF NOT EXISTS system_settings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        setting_key VARCHAR(255) UNIQUE NOT NULL,
        setting_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Update or insert settings
    $settings_data = [
        'admin_email' => $adminEmail,
        'contact_phone' => $contactPhone,
        'business_address' => $businessAddress,
        'timezone' => $timezone,
        'default_language' => $defaultLanguage,
        'currency' => $currency
    ];

    foreach ($settings_data as $key => $value) {
        $stmt = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->bind_param("sss", $key, $value, $value);
        $stmt->execute();
        $stmt->close();
    }

    echo json_encode(['success' => true, 'message' => 'General settings saved successfully']);
}

function saveSecuritySettings($conn, $settings) {
    // Sanitize inputs
    $verificationMethod = $conn->real_escape_string($settings['verificationMethod'] ?? '');
    $minPasswordLength = (int)($settings['minPasswordLength'] ?? 8);
    $requireSpecialChars = isset($settings['requireSpecialChars']) ? 1 : 0;
    $requireNumbers = isset($settings['requireNumbers']) ? 1 : 0;
    $enableIpTracking = isset($settings['enableIpTracking']) ? 1 : 0;
    $sendSecurityAlerts = isset($settings['sendSecurityAlerts']) ? 1 : 0;

    // Check if settings table exists, create if not
    $conn->query("CREATE TABLE IF NOT EXISTS system_settings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        setting_key VARCHAR(255) UNIQUE NOT NULL,
        setting_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Update or insert settings
    $settings_data = [
        'verification_method' => $verificationMethod,
        'min_password_length' => $minPasswordLength,
        'require_special_chars' => $requireSpecialChars,
        'require_numbers' => $requireNumbers,
        'enable_ip_tracking' => $enableIpTracking,
        'send_security_alerts' => $sendSecurityAlerts
    ];

    foreach ($settings_data as $key => $value) {
        $stmt = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->bind_param("sss", $key, $value, $value);
        $stmt->execute();
        $stmt->close();
    }

    echo json_encode(['success' => true, 'message' => 'Security settings saved successfully']);
}

function saveNotificationSettings($conn, $settings) {
    // Sanitize inputs
    $emailNotifications = $settings['emailNotifications'] ?? [];
    $smsNotifications = $settings['smsNotifications'] ?? [];
    $smtpSettings = $settings['smtpSettings'] ?? [];

    // Check if settings table exists, create if not
    $conn->query("CREATE TABLE IF NOT EXISTS system_settings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        setting_key VARCHAR(255) UNIQUE NOT NULL,
        setting_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Email notifications
    $emailSettings = [
        'email_new_user' => isset($emailNotifications['newUser']) ? 1 : 0,
        'email_new_property' => isset($emailNotifications['newProperty']) ? 1 : 0,
        'email_booking_confirm' => isset($emailNotifications['bookingConfirm']) ? 1 : 0,
        'email_payment_trans' => isset($emailNotifications['paymentTrans']) ? 1 : 0,
        'email_system_updates' => isset($emailNotifications['systemUpdates']) ? 1 : 0
    ];

    // SMS notifications
    $smsSettings = [
        'sms_critical_alerts' => isset($smsNotifications['criticalAlerts']) ? 1 : 0,
        'sms_booking_notif' => isset($smsNotifications['bookingNotif']) ? 1 : 0,
        'sms_payment_confirm' => isset($smsNotifications['paymentConfirm']) ? 1 : 0
    ];

    // SMTP settings
    $smtpSettingsData = [
        'smtp_server' => $conn->real_escape_string($smtpSettings['server'] ?? ''),
        'smtp_username' => $conn->real_escape_string($smtpSettings['username'] ?? ''),
        'smtp_password' => $conn->real_escape_string($smtpSettings['password'] ?? ''),
        'smtp_port' => (int)($smtpSettings['port'] ?? 587),
        'smtp_encryption' => $conn->real_escape_string($smtpSettings['encryption'] ?? 'TLS')
    ];

    // Combine all settings
    $allSettings = array_merge($emailSettings, $smsSettings, $smtpSettingsData);

    foreach ($allSettings as $key => $value) {
        $stmt = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->bind_param("sss", $key, $value, $value);
        $stmt->execute();
        $stmt->close();
    }

    echo json_encode(['success' => true, 'message' => 'Notification settings saved successfully']);
}
?>