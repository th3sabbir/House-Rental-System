<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | AmarThikana</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(120deg, #16a085 0%, #2980b9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }
        .auth-main {
            width: 100%;
            max-width: 410px;
            margin: 0 auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.18);
            padding: 2.5rem 2.2rem 2rem 2.2rem;
            position: relative;
            overflow: hidden;
        }
        .auth-main:before {
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
        .auth-main h2 {
            font-size: 1.7rem;
            margin-bottom: 1.2rem;
            font-weight: 600;
            color: #222;
            z-index: 2;
            position: relative;
        }
        .auth-main form {
            display: flex;
            flex-direction: column;
            gap: 1.1rem;
            z-index: 2;
            position: relative;
        }
        .auth-main .input-group {
            display: flex;
            align-items: center;
            background: #f4f8fb;
            border-radius: 8px;
            padding: 0.7rem 1rem;
            border: 1.5px solid #e0e0e0;
            transition: border 0.2s;
        }
        .auth-main .input-group:focus-within {
            border: 1.5px solid #16a085;
        }
        .auth-main .input-group i {
            color: #16a085;
            margin-right: 10px;
            font-size: 1.1rem;
        }
        .auth-main input[type="email"],
        .auth-main input[type="password"] {
            border: none;
            outline: none;
            background: transparent;
            font-size: 1rem;
            width: 100%;
            font-family: inherit;
        }
        .auth-main .btn {
            background: linear-gradient(90deg, #16a085 60%, #2980b9 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.8rem 0;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(44,62,80,0.07);
            margin-top: 0.5rem;
        }
        .auth-main .btn:hover {
            background: linear-gradient(90deg, #2980b9 0%, #16a085 100%);
            box-shadow: 0 4px 16px rgba(44,62,80,0.13);
        }
        .auth-main .auth-links {
            margin-top: 1.2rem;
            font-size: 0.97rem;
            z-index: 2;
            position: relative;
        }
        .auth-main .auth-links a {
            color: #16a085;
            text-decoration: none;
            font-weight: 500;
            margin-left: 5px;
        }
        .auth-main .auth-links a:hover {
            text-decoration: underline;
        }
        .auth-main .divider {
            margin: 1.5rem 0 1.2rem 0;
            text-align: center;
            color: #aaa;
            font-size: 0.95rem;
            position: relative;
            z-index: 2;
        }
        .auth-main .divider:before,
        .auth-main .divider:after {
            content: "";
            display: inline-block;
            width: 20%;
            height: 1px;
            background: #e0e0e0;
            vertical-align: middle;
            margin: 0 8px;
        }
        .auth-main .social-login {
            display: flex;
            gap: 1rem;
            justify-content: center;
            z-index: 2;
            position: relative;
        }
        .auth-main .social-login button {
            border: none;
            background: #f4f8fb;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: #2980b9;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            box-shadow: 0 2px 8px rgba(44,62,80,0.07);
        }
        .auth-main .social-login button:hover {
            background: #16a085;
            color: #fff;
        }
        .auth-main .forgot-link {
            display: block;
            text-align: right;
            margin-top: 0.2rem;
            font-size: 0.97rem;
            color: #2980b9;
            text-decoration: none;
            z-index: 2;
            position: relative;
        }
        .auth-main .forgot-link:hover {
            text-decoration: underline;
            color: #16a085;
        }
        @media (max-width: 480px) {
            .auth-main {
                padding: 1.5rem 0.7rem 1.2rem 0.7rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-main">
        <div class="auth-logo"><i class="fa-solid fa-house-chimney-window"></i> AmarThikana</div>
        <h2>Sign in to your account</h2>
        <form>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" placeholder="Email Address" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" placeholder="Password" required>
            </div>
            <a href="forget-pass.php" class="forgot-link">Forgot password?</a>
            <button type="submit" class="btn">Login</button>
        </form>
        <div class="auth-links">
            Don't have an account?
            <a href="signup.php">Sign Up</a>
        </div>
        <div class="divider">or login with</div>
        <div class="social-login">
            <button title="Login with Google"><i class="fab fa-google"></i></button>
            <button title="Login with Facebook"><i class="fab fa-facebook-f"></i></button>
        </div>
    </div>
</body>
</html>
