<?php
// Authentication functions
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

class Auth {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    // Register new user
    public function register($username, $email, $password, $full_name, $phone, $role = 'tenant') {
        try {
            // Check if username or email already exists
            $stmt = $this->conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Username or email already exists'];
            }

            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $stmt = $this->conn->prepare("
                INSERT INTO users (username, email, password, full_name, phone, user_type) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([$username, $email, $hashed_password, $full_name, $phone, $role]);
            
            return ['success' => true, 'message' => 'Registration successful'];
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }

    // Login user
    public function login($username, $password) {
        try {
            error_log("[Auth] Login attempt with username: " . $username);
            error_log("[Auth] Session status: " . (session_status() === PHP_SESSION_ACTIVE ? "Active" : "Not active"));
            error_log("[Auth] Session ID: " . session_id());

            // Ensure clean session state
            if (isset($_SESSION['user_id'])) {
                error_log("[Auth] Existing session found for user ID: " . $_SESSION['user_id'] . ", cleaning up");
                $this->logout();
            }

            $stmt = $this->conn->prepare("
                SELECT user_id, username, email, password, full_name, phone, user_type, profile_image, status 
                FROM users 
                WHERE (username = ? OR email = ?) AND status = 'active'
            ");
            
            $stmt->execute([$username, $username]);
            error_log("[Auth] Query executed. Found rows: " . $stmt->rowCount());
            
            if ($stmt->rowCount() === 0) {
                error_log("[Auth] No user found with username/email: " . $username);
                return ['success' => false, 'message' => 'User not found. Please check your username or email.'];
            }

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log("[Auth] User found. ID: " . $user['user_id'] . ", Role: " . $user['user_type']);
            error_log("[Auth] Stored password hash: " . substr($user['password'], 0, 10) . "...");

            // Verify password
            $password_verify_result = password_verify($password, $user['password']);
            error_log("[Auth] Password verify result: " . ($password_verify_result ? "true" : "false"));
            
            if (!$password_verify_result) {
                error_log("[Auth] Password verification failed for user ID: " . $user['user_id']);
                return ['success' => false, 'message' => 'Incorrect password. Please try again or use forgot password link.'];
            }

            error_log("[Auth] Password verified successfully. Setting session for user ID: " . $user['user_id']);

            // Regenerate session ID for security
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['user_type'];
            $_SESSION['profile_image'] = $user['profile_image'];
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();

            // Generate CSRF token
            if (!isset($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            error_log("[Auth] Login successful for user ID: " . $user['user_id'] . ", Role: " . $user['user_type']);

            // Update last login timestamp
            $this->updateLastLogin($user['user_id']);

            return [
                'success' => true, 
                'message' => 'Login successful',
                'role' => $user['user_type'],
                'user' => [
                    'id' => $user['user_id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['user_type']
                ]
            ];
        } catch (PDOException $e) {
            error_log("[Auth] Database error during login: " . $e->getMessage());
            return [
                'success' => false, 
                'message' => 'Login failed: Database error',
                'debug' => $e->getMessage()
            ];
        } catch (Exception $e) {
            error_log("[Auth] General error during login: " . $e->getMessage());
            return [
                'success' => false, 
                'message' => 'Login failed: System error',
                'debug' => $e->getMessage()
            ];
        }
    }

    // Logout user
    public function logout() {
        // Delete remember me token if exists
        $this->deleteRememberToken();
        
        // Unset all session variables
        session_unset();
        
        // Destroy the session
        session_destroy();
        
        // Start new session for flash messages etc
        session_start();
        
        return ['success' => true, 'message' => 'Logged out successfully'];
    }

    // Check if user is logged in
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    // Get current user
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'user_id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'email' => $_SESSION['email'],
                'full_name' => $_SESSION['full_name'],
                'role' => $_SESSION['role'],
                'profile_image' => $_SESSION['profile_image'] ?? null
            ];
        }
        return null;
    }

    // Check user role
    public function hasRole($role) {
        return $this->isLoggedIn() && $_SESSION['role'] === $role;
    }

    // Require login
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: /house_rental/login.php');
            exit();
        }
    }

    // Require specific role
    public function requireRole($role) {
        $this->requireLogin();
        if ($_SESSION['role'] !== $role) {
            header('Location: /house_rental/index.php');
            exit();
        }
    }

    // Generate CSRF token
    public function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Verify CSRF token
    public function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    // Update user profile
    public function updateProfile($user_id, $data) {
        try {
            $allowed_fields = ['full_name', 'phone', 'profile_image'];
            $update_fields = [];
            $values = [];

            foreach ($data as $key => $value) {
                if (in_array($key, $allowed_fields)) {
                    $update_fields[] = "$key = ?";
                    $values[] = $value;
                }
            }

            if (empty($update_fields)) {
                return ['success' => false, 'message' => 'No valid fields to update'];
            }

            $values[] = $user_id;
            $sql = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE user_id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($values);

            return ['success' => true, 'message' => 'Profile updated successfully'];
        } catch (PDOException $e) {
            error_log("Profile update error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Update failed: ' . $e->getMessage()];
        }
    }

    // Change password
    public function changePassword($user_id, $old_password, $new_password) {
        try {
            $stmt = $this->conn->prepare("SELECT password FROM users WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();

            if (!password_verify($old_password, $user['password'])) {
                return ['success' => false, 'message' => 'Current password is incorrect'];
            }

            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $stmt->execute([$hashed_password, $user_id]);

            return ['success' => true, 'message' => 'Password changed successfully'];
        } catch (PDOException $e) {
            error_log("Password change error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Password change failed: ' . $e->getMessage()];
        }
    }

    // Create remember me token
    public function createRememberToken($user_id) {
        try {
            // Generate secure random token
            $token = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));

            // Delete old tokens for this user
            $stmt = $this->conn->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
            $stmt->execute([$user_id]);

            // Insert new token
            $stmt = $this->conn->prepare("
                INSERT INTO remember_tokens (user_id, token, expires_at) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$user_id, $token, $expires_at]);

            // Set cookie (30 days)
            setcookie('remember_token', $token, time() + (86400 * 30), "/", "", true, true);

            return true;
        } catch (PDOException $e) {
            error_log('Remember token creation failed: ' . $e->getMessage());
            return false;
        }
    }

    // Check and validate remember me token
    public function checkRememberToken() {
        if (!isset($_COOKIE['remember_token'])) {
            return false;
        }

        try {
            $token = $_COOKIE['remember_token'];
            
            // Find valid token
            $stmt = $this->conn->prepare("
                SELECT rt.user_id, u.username, u.email, u.full_name, u.user_type, u.profile_image
                FROM remember_tokens rt
                JOIN users u ON rt.user_id = u.user_id
                WHERE rt.token = ? AND rt.expires_at > NOW() AND u.status = 'active'
            ");
            $stmt->execute([$token]);

            if ($stmt->rowCount() === 0) {
                // Invalid or expired token, delete cookie
                setcookie('remember_token', '', time() - 3600, "/", "", true, true);
                return false;
            }

            $user = $stmt->fetch();

            // Regenerate session ID for security
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['user_type'];
            $_SESSION['profile_image'] = $user['profile_image'];
            $_SESSION['logged_in'] = true;

            // Generate CSRF token
            if (!isset($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            // Refresh token expiry
            $new_expires = date('Y-m-d H:i:s', strtotime('+30 days'));
            $stmt = $this->conn->prepare("UPDATE remember_tokens SET expires_at = ? WHERE token = ?");
            $stmt->execute([$new_expires, $token]);

            // Refresh cookie
            setcookie('remember_token', $token, time() + (86400 * 30), "/", "", true, true);

            return true;
        } catch (PDOException $e) {
            error_log('Remember token check failed: ' . $e->getMessage());
            return false;
        }
    }

    // Delete remember token
    public function deleteRememberToken() {
        if (isset($_COOKIE['remember_token'])) {
            try {
                $token = $_COOKIE['remember_token'];
                $stmt = $this->conn->prepare("DELETE FROM remember_tokens WHERE token = ?");
                $stmt->execute([$token]);
            } catch (PDOException $e) {
                error_log('Remember token deletion failed: ' . $e->getMessage());
            }
            
            // Delete cookie
            setcookie('remember_token', '', time() - 3600, "/", "", true, true);
        }
    }

    // Update last login timestamp
    private function updateLastLogin($userId) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE users 
                SET last_login = CURRENT_TIMESTAMP 
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
        } catch (Exception $e) {
            error_log("Error updating last login: " . $e->getMessage());
        }
    }
}

// Helper functions for backward compatibility
function isLoggedIn() {
    $auth = new Auth();
    return $auth->isLoggedIn();
}

function getUserById($user_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return null;
    }
}



