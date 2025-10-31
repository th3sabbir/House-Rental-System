<?php
require_once 'includes/session_check.php';
require_once 'config/database.php';

$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'includes/auth.php';
    $auth = new Auth();
    
    if (isset($_POST['update_profile'])) {
        $full_name = $_POST['full_name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        
        $data = [];
        if (!empty($full_name)) $data['full_name'] = $full_name;
        if (!empty($phone)) $data['phone'] = $phone;
        
        $result = $auth->updateProfile($current_user['id'], $data);
        
        if ($result['success']) {
            $_SESSION['full_name'] = $full_name;
            $message = $result['message'];
            $message_type = 'success';
            // Refresh user data
            $current_user['full_name'] = $full_name;
        } else {
            $message = $result['message'];
            $message_type = 'error';
        }
    } elseif (isset($_POST['change_password'])) {
        $old_password = $_POST['old_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if ($new_password !== $confirm_password) {
            $message = 'New passwords do not match';
            $message_type = 'error';
        } else {
            $result = $auth->changePassword($current_user['id'], $old_password, $new_password);
            $message = $result['message'];
            $message_type = $result['success'] ? 'success' : 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | AmarThikana</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .settings-container {
            max-width: 800px;
            margin: 100px auto 50px;
            padding: 20px;
        }
        .settings-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 35px;
            margin-bottom: 25px;
        }
        .settings-card h3 {
            margin: 0 0 25px 0;
            color: #2c3e50;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .settings-card h3 i {
            color: #1abc9c;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-group input:focus {
            outline: none;
            border-color: #1abc9c;
        }
        .btn-save {
            background: #1abc9c;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-save:hover {
            background: #16a085;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 188, 156, 0.3);
        }
        .message {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div id="header-placeholder"></div>

    <div class="settings-container">
        <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>">
            <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <div class="settings-card">
            <h3>
                <i class="fas fa-user-edit"></i>
                Update Profile Information
            </h3>
            <form method="POST">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($current_user['full_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email (Cannot be changed)</label>
                    <input type="email" id="email" value="<?php echo htmlspecialchars($current_user['email']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="Enter phone number">
                </div>
                <button type="submit" name="update_profile" class="btn-save">
                    <i class="fas fa-save"></i>
                    Save Changes
                </button>
            </form>
        </div>

        <div class="settings-card">
            <h3>
                <i class="fas fa-lock"></i>
                Change Password
            </h3>
            <form method="POST">
                <div class="form-group">
                    <label for="old_password">Current Password</label>
                    <input type="password" id="old_password" name="old_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" minlength="6" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" minlength="6" required>
                </div>
                <button type="submit" name="change_password" class="btn-save">
                    <i class="fas fa-key"></i>
                    Change Password
                </button>
            </form>
        </div>

        <div class="settings-card">
            <h3>
                <i class="fas fa-shield-alt"></i>
                Account Information
            </h3>
            <div class="info-grid" style="display: grid; gap: 15px;">
                <div>
                    <strong>Username:</strong> <?php echo htmlspecialchars($current_user['username']); ?>
                </div>
                <div>
                    <strong>Account Type:</strong> 
                    <span style="background: #1abc9c; color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.9rem; text-transform: capitalize;">
                        <?php echo htmlspecialchars($current_user['role']); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div id="footer-placeholder"></div>

    <script src="js/loader.js"></script>
</body>
</html>




