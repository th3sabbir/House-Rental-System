<?php
// Test database connection
require_once 'config/database.php';
require_once 'includes/paths.php';

echo "<h1>Database Connection Test</h1>";

try {
    $db = new Database();
    $conn = $db->connect();

    if ($conn) {
        echo "<p style='color: green;'>✅ Database connection successful!</p>";

        // Test a simple query
        $stmt = $conn->query("SELECT COUNT(*) as total FROM users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Total users: " . $result['total'] . "</p>";

        $stmt = $conn->query("SELECT COUNT(*) as total FROM properties");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Total properties: " . $result['total'] . "</p>";

    } else {
        echo "<p style='color: red;'>❌ Database connection failed!</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Current Directory: " . __DIR__ . "</p>";
echo "<p>Base Path: " . getBasePath() . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Script Name: " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";

echo "<h2>Environment Variables</h2>";
echo "<p>DB_HOST: " . (getenv('DB_HOST') ?: 'Not set (using localhost)') . "</p>";
echo "<p>DB_USER: " . (getenv('DB_USER') ?: 'Not set (using root)') . "</p>";
echo "<p>DB_PASS: " . (getenv('DB_PASS') ? 'Set' : 'Not set (using empty)') . "</p>";
echo "<p>DB_NAME: " . (getenv('DB_NAME') ?: 'Not set (using amarthikana)') . "</p>";

echo "<h2>File Permissions Test</h2>";
$test_dirs = ['uploads', 'uploads/profiles', 'logs'];
foreach ($test_dirs as $dir) {
    $full_path = __DIR__ . '/' . $dir;
    $writable = is_writable($full_path);
    $exists = file_exists($full_path);
    echo "<p>$dir: " . ($exists ? 'Exists' : 'Missing') . " - " . ($writable ? 'Writable' : 'Not writable') . "</p>";
}
?>