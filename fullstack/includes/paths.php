<?php
/**
 * Dynamic Path Helper Functions
 * Use these functions instead of hardcoded paths like /house_rental/
 */

if (!function_exists('getBasePath')) {
    /**
     * Get the base path of the application
     * Returns: /amarthikana or /house_rental or whatever the folder name is
     */
    function getBasePath() {
        $script = $_SERVER['SCRIPT_NAME'];
        $parts = explode('/', trim($script, '/'));
        return '/' . $parts[0];
    }
}

if (!function_exists('url')) {
    /**
     * Generate a full URL path
     * Usage: url('login.php'), url('admin/index.php'), url('api/logout_handler.php')
     */
    function url($path = '') {
        $basePath = getBasePath();
        $path = ltrim($path, '/');
        return $basePath . ($path ? '/' . $path : '');
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to a URL within the application
     * Usage: redirect('login.php'), redirect('index.php')
     */
    function redirect($path) {
        $redirectUrl = url($path);
        header('Location: ' . $redirectUrl);
        exit();
    }
}
?>
