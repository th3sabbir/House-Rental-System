<?php
// Check if user is already logged in
require_once 'includes/auth.php';

$auth = new Auth();

// Check remember me token first
$auth->checkRememberToken();

// If already logged in, redirect to appropriate dashboard
if ($auth->isLoggedIn()) {
    $redirect_url = 'index.php';
    
    switch ($_SESSION['role']) {
        case 'admin':
            $redirect_url = 'admin/index.php';
            break;
        case 'landlord':
            $redirect_url = 'landlord/index.php';
            break;
        case 'tenant':
            $redirect_url = 'tenant/index.php';
            break;
    }
    
    header('Location: ' . $redirect_url);
    exit();
}

// Handle form submission (fallback for when JavaScript is disabled)
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_submitted'])) {
    // Get POST data
    $username = sanitize_input($_POST['username'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = sanitize_input($_POST['full_name'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $role = sanitize_input($_POST['role'] ?? 'tenant');

    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        $message = '<div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 15px;">Please fill all required fields</div>';
    } elseif (!validate_email($email)) {
        $message = '<div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 15px;">Invalid email format</div>';
    } elseif ($password !== $confirm_password) {
        $message = '<div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 15px;">Passwords do not match</div>';
    } elseif (strlen($password) < 6) {
        $message = '<div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 15px;">Password must be at least 6 characters long</div>';
    } else {
        // Attempt registration
        $result = $auth->register($username, $email, $password, $full_name, $phone, $role);
        
        if ($result['success']) {
            header('Location: login.php?registered=1');
            exit();
        } else {
            $message = '<div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 15px;">' . $result['message'] . '</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - AmarThikana</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(120deg, #16a085 0%, #2980b9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }
        .signup-container {
            display: flex;
            justify-content: center;
            align-items: center;
            
            padding: 60px 20px;
            min-height: 100vh;
        }
        .signup-card {
            background: var(--background-white);
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-medium);
            width: 100%;
            max-width: 550px;
            /* Add these two properties */
            position: relative;
            overflow: hidden;
        }
        
        .signup-card::before {
            content: "";
            position: absolute;
            top: -60px;
            right: -60px;
            width: 140px;
            height: 140px;
            background: linear-gradient(135deg, #16a085 60%, #2980b9 100%);
            opacity: 0.13;
            border-radius: 50%;
            z-index: 1;
        }
        .signup-card h2 {
            text-align: center;
            font-size: 2.2rem;
            margin-bottom: 2rem;
            color: var(--primary-color);
            /* Add z-index to be above the new corner */
            position: relative;
            z-index: 2;
        }
        
        .signup-card form, .signup-card .login-link {
            position: relative;
            z-index: 2;
        }
        .role-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border: 1px solid var(--border-color);
            border-radius: 50px;
            margin-bottom: 2rem;
            overflow: hidden;
        }
        .role-selector input[type="radio"] {
            display: none;
        }
        .role-selector label {
            padding: 14px;
            text-align: center;
            cursor: pointer;
            font-weight: 600;
            color: var(--text-medium);
            transition: background-color 0.3s, color 0.3s;
        }
        .role-selector input[type="radio"]:checked + label {
            background-color: var(--secondary-color);
            color: white;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        .form-group input {
            width: 100%;
            padding: 14px 14px 14px 45px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(22, 160, 133, 0.1);
        }
        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(25%);
            color: var(--text-medium);
        }
        .terms-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 1.5rem 0;
            font-size: 0.9rem;
        }
        .signup-card .btn {
            width: 100%;
            padding: 16px;
            font-size: 1.1rem;
        }
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-medium);
        }

            
        .auth-logo {
            font-size: 2.1rem;
            font-weight: 700;
            color: #16a085;
            margin-bottom: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            z-index: 2;
            position: relative;
        }
        
    </style>
</head>
<body>



<main class="signup-container">
    <div class="signup-card">
      
        <div class="auth-logo"><i class="fa-solid fa-house-chimney-window"></i> AmarThikana</div>
        <h2>Create Your Account</h2>
        <?php if ($message): ?>
            <?php echo $message; ?>
        <?php else: ?>
            <div id="message"></div>
        <?php endif; ?>
        <form id="signupForm" action="signup.php" method="post">
            <input type="hidden" name="form_submitted" value="1">
            <?php $csrf_token = $auth->generateCSRFToken(); ?>
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <div class="role-selector">
                <input type="radio" id="role-tenant" name="role" value="tenant" checked>
                <label for="role-tenant">I am a Tenant</label>
                <input type="radio" id="role-landlord" name="role" value="landlord">
                <label for="role-landlord">I am a Landlord</label>
            </div>

            <div class="form-group">
                <label for="fullname">Full Name</label>
                <i class="fas fa-user"></i>
                <input type="text" name="full_name" id="fullname" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <i class="fas fa-user-circle"></i>
                <input type="text" name="username" id="username" placeholder="Choose a username" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <i class="fas fa-phone"></i>
                <input type="tel" name="phone" id="phone" placeholder="Enter your phone number" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="Create a password" required>
            </div>

            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" id="confirm-password" placeholder="Confirm your password" required>
            </div>

            <div class="terms-group">
                <input type="checkbox" id="terms" required>
                <label for="terms">I agree to the <a href="terms-of-service.php">Terms and Conditions</a></label>
            </div>

            <button type="submit" class="btn btn-primary" id="signupBtn">Create Account</button>
        </form>
        <p class="login-link">Already have an account? <a href="login.php">Log In</a></p>
    </div>
</main>

<script>
    document.getElementById('signupForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        console.log('Form submitted'); // Debug log
        
        const btn = document.getElementById('signupBtn');
        const messageDiv = document.getElementById('message');
        const formData = new FormData(this);

        console.log('Form data:', Array.from(formData.entries())); // Debug log

        // Validate password
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;

        if (password.length < 6) {
            messageDiv.innerHTML = '<div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 15px;">Password must be at least 6 characters long</div>';
            return;
        }

        if (password !== confirmPassword) {
            messageDiv.innerHTML = '<div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 15px;">Passwords do not match</div>';
            return;
        }
        
        btn.disabled = true;
        btn.textContent = 'Creating Account...';
        messageDiv.innerHTML = '';
        
        console.log('Sending request to api/signup_handler.php'); // Debug log
        
        try {
            const response = await fetch('api/signup_handler.php', {
                method: 'POST',
                body: formData
            });
            
            console.log('Response status:', response.status); // Debug log
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            console.log('Response result:', result); // Debug log
            
            if (result.success) {
                messageDiv.innerHTML = '<div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 15px;"><i class="fas fa-check-circle"></i> ' + result.message + '</div>';
                setTimeout(() => {
                    window.location.href = result.redirect;
                }, 1500);
            } else {
                messageDiv.innerHTML = '<div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 15px;">' + result.message + '</div>';
                btn.disabled = false;
                btn.textContent = 'Create Account';
            }
        } catch (error) {
            console.error('Signup error:', error); // Debug log
            messageDiv.innerHTML = '<div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 15px;">An error occurred: ' + error.message + '. Please try again.</div>';
            btn.disabled = false;
            btn.textContent = 'Create Account';
        }
    });
</script>

</body>
</html>




