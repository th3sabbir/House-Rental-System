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
        // Known subdirectories that are not the base path
        $known_subdirs = ['api', 'css', 'js', 'images', 'uploads', 'includes', 'admin', 'landlord', 'tenant', 'config', 'assets', 'vendor', 'node_modules', 'logs', 'tmp', 'cache'];
        
        $script = $_SERVER['SCRIPT_NAME'];
        $request_uri = $_SERVER['REQUEST_URI'];

        // If REQUEST_URI contains the script name, extract the base path
        if (strpos($request_uri, $script) !== false) {
            $parts = explode('/', trim(dirname($script), '/'));
            if (!empty($parts[0]) && !in_array($parts[0], $known_subdirs)) {
                return '/' . $parts[0];
            }
        }

        // Fallback: use SCRIPT_NAME
        $parts = explode('/', trim($script, '/'));
        if (count($parts) > 1 && !in_array($parts[0], $known_subdirs)) {
            return '/' . $parts[0];
        }

        // If in root directory or known subdirectory, return empty string
        return '';
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
