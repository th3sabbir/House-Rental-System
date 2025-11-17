<?php
/**
 * Database Configuration File
 * House Rental System - AmarThikana
 */

// Set timezone to UTC for consistent timestamp handling
date_default_timezone_set('UTC');

// Database credentials - Use environment variables for hosting
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'amarthikana');

// MySQLi connection (backward compatibility)
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for proper character support
$conn->set_charset("utf8mb4");

// Set MySQL session timezone to UTC
$conn->query("SET time_zone = '+00:00';");

// PDO Database class for modern usage
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    private $conn;
    private $error;

    // Database connection
    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->dbname,
                $this->user,
                $this->pass
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            // Set MySQL session timezone to UTC
            $this->conn->exec("SET time_zone = '+00:00';");
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            echo "Connection Error: " . $this->error;
        }

        return $this->conn;
    }

    public function getError() {
        return $this->error;
    }
}

?>




