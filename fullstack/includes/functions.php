<?php
// General utility functions

// Sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Validate email
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validate phone
function validate_phone($phone) {
    return preg_match('/^[0-9]{10,15}$/', $phone);
}

// Generate random string
function generate_random_string($length = 10) {
    return bin2hex(random_bytes($length / 2));
}

// Format currency
function format_currency($amount) {
    return '$' . number_format($amount, 2);
}

// Format date
function format_date($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

// Time ago function
function time_ago($datetime) {
    $timestamp = strtotime($datetime);
    $difference = time() - $timestamp;
    
    $periods = [
        'year' => 31536000,
        'month' => 2592000,
        'week' => 604800,
        'day' => 86400,
        'hour' => 3600,
        'minute' => 60,
        'second' => 1
    ];
    
    foreach ($periods as $key => $value) {
        if ($difference >= $value) {
            $time = floor($difference / $value);
            return $time . ' ' . $key . ($time > 1 ? 's' : '') . ' ago';
        }
    }
    
    return 'Just now';
}

// Get file extension
function get_file_extension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

// Validate image file
function validate_image($file) {
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    $extension = get_file_extension($file['name']);
    
    if (!in_array($extension, $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, JPEG, PNG, and GIF allowed.'];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'File too large. Maximum size is 5MB.'];
    }
    
    return ['success' => true];
}

// Upload image
function upload_image($file, $directory = 'uploads/') {
    $validation = validate_image($file);
    
    if (!$validation['success']) {
        return $validation;
    }
    
    // Create directory if it doesn't exist
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }
    
    // Generate unique filename
    $extension = get_file_extension($file['name']);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $directory . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filepath' => $filepath];
    }
    
    return ['success' => false, 'message' => 'Failed to upload file'];
}

// Paginate results
function paginate($total_records, $records_per_page, $current_page) {
    $total_pages = ceil($total_records / $records_per_page);
    $offset = ($current_page - 1) * $records_per_page;
    
    return [
        'total_pages' => $total_pages,
        'current_page' => $current_page,
        'offset' => $offset,
        'limit' => $records_per_page
    ];
}

// Generate breadcrumb
function generate_breadcrumb($items) {
    $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    
    foreach ($items as $item) {
        if (isset($item['active']) && $item['active']) {
            $html .= '<li class="breadcrumb-item active">' . $item['text'] . '</li>';
        } else {
            $html .= '<li class="breadcrumb-item"><a href="' . $item['url'] . '">' . $item['text'] . '</a></li>';
        }
    }
    
    $html .= '</ol></nav>';
    return $html;
}

// Send JSON response
function send_json_response($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

// Redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Get user IP address
function get_user_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    return $_SERVER['REMOTE_ADDR'];
}

// Log activity
function log_activity($user_id, $action, $details = '') {
    try {
        // Implement activity logging
        $log_dir = __DIR__ . '/../logs';
        $log_file = $log_dir . '/activity.log';
        
        // Create logs directory if it doesn't exist
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        $log_entry = date('Y-m-d H:i:s') . " - User: $user_id - Action: $action - Details: $details - IP: " . get_user_ip() . "\n";
        file_put_contents($log_file, $log_entry, FILE_APPEND);
    } catch (Exception $e) {
        // Silently fail if logging doesn't work
        error_log('Activity logging failed: ' . $e->getMessage());
    }
}

// Rate limiting
function check_rate_limit($identifier, $max_attempts = 5, $time_window = 300) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $key = 'rate_limit_' . $identifier;
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 1, 'time' => time()];
        return true;
    }
    
    $data = $_SESSION[$key];
    
    if (time() - $data['time'] > $time_window) {
        $_SESSION[$key] = ['count' => 1, 'time' => time()];
        return true;
    }
    
    if ($data['count'] >= $max_attempts) {
        return false;
    }
    
    $_SESSION[$key]['count']++;
    return true;
}
?>




