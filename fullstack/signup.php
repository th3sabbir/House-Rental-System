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
        <form action="#">
            <div class="role-selector">
                <input type="radio" id="role-tenant" name="role" value="tenant" checked>
                <label for="role-tenant">I am a Tenant</label>
                <input type="radio" id="role-landlord" name="role" value="landlord">
                <label for="role-landlord">I am a Landlord</label>
            </div>

            <div class="form-group">
                <label for="fullname">Full Name</label>
                <i class="fas fa-user"></i>
                <input type="text" id="fullname" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <i class="fas fa-phone"></i>
                <input type="tel" id="phone" placeholder="Enter your phone number" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <i class="fas fa-lock"></i>
                <input type="password" id="password" placeholder="Create a password" required>
            </div>

            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <i class="fas fa-lock"></i>
                <input type="password" id="confirm-password" placeholder="Confirm your password" required>
            </div>

            <div class="terms-group">
                <input type="checkbox" id="terms" required>
                <label for="terms">I agree to the <a href="#">Terms and Conditions</a></label>
            </div>

            <button type="submit" class="btn btn-primary">Create Account</button>
        </form>
        <p class="login-link">Already have an account? <a href="login.php">Log In</a></p>
    </div>
</main>

</body>
</html>
