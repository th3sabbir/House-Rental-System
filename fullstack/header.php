<style>
    /* --- Header & Navigation --- */
    .main-header {
        background-color: transparent;
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
        padding: 20px 0;
        transition: background-color 0.4s ease, box-shadow 0.4s ease, padding 0.4s ease;
    }
    
    .main-header.scrolled {
        background-color: #2c3e50;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        padding: 15px 0;
    }
    
    .main-header .logo,
    .main-header .nav-links a,
    .main-header .login-btn,
    .main-header .menu-toggle {
        color: #ffffff;
        transition: color 0.4s ease;
    }
    
    .main-header.scrolled .logo,
    .main-header.scrolled .nav-links a,
    .main-header.scrolled .login-btn,
    .main-header.scrolled .menu-toggle {
        color: #ffffff;
    }
    
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .logo {
        font-family: 'Poppins', sans-serif;
        font-size: 1.75rem;
        font-weight: 700;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .logo i {
        color: #1abc9c;
    }
    
    .nav-links {
        list-style: none;
        display: flex;
        gap: 40px;
        margin: 0;
        padding: 0;
    }
    
    .nav-links a {
        font-weight: 500;
        position: relative;
        padding: 5px 0;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .nav-links a::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background-color: #1abc9c;
        transition: width 0.3s ease;
    }
    
    .nav-links a:hover::after,
    .nav-links a.active::after {
        width: 100%;
    }
    
    .nav-links a:hover,
    .nav-links a.active {
        color: #1abc9c;
    }
    
    .nav-actions {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .login-btn {
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .login-btn:hover {
        color: #1abc9c !important;
        transform: translateY(-2px);
    }
    
    .btn-primary {
        background-color: #1abc9c;
        color: #ffffff;
        display: inline-block;
        padding: 14px 32px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
        border: 1px solid transparent;
        text-align: center;
        text-decoration: none;
    }
    
    .btn-primary:hover {
        color: white !important;
        background-color: #16a085;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(44, 62, 80, 0.1);
    }
    
    .menu-toggle {
        display: none;
        font-size: 1.5rem;
        cursor: pointer;
    }
    
    @media (max-width: 768px) {
        .nav-links {
            position: fixed;
            left: -100%;
            top: 70px;
            flex-direction: column;
            background-color: #2c3e50;
            width: 100%;
            text-align: center;
            transition: 0.3s;
            box-shadow: 0 10px 27px rgba(0, 0, 0, 0.05);
            padding: 30px 0;
            gap: 20px;
        }
        
        .nav-links.active {
            left: 0;
        }
        
        .nav-actions {
            position: fixed;
            left: -100%;
            top: calc(70px + 180px);
            flex-direction: column;
            background-color: #2c3e50;
            width: 100%;
            text-align: center;
            transition: 0.3s;
            padding: 20px 0;
            gap: 15px;
        }
        
        .nav-actions.active {
            left: 0;
        }
        
        .menu-toggle {
            display: block;
        }
    }
</style>

<header class="main-header">
    <nav class="navbar container">
        <a href="index.php" class="logo">
            <i class="fa-solid fa-house-chimney-window"></i> 
            AmarThikana
        </a>
        <ul class="nav-links">
            <li><a href="index.php" id="nav-rent">Rent</a></li>
            <li><a href="properties.php" id="nav-properties">Properties</a></li>
            <li><a href="about-us.php" id="nav-about">About Us</a></li>
        </ul>
        <div class="nav-actions">
            <a href="login.php" class="login-btn">Login</a>
            <a href="signup.php" class="btn btn-primary">Sign Up</a>
        </div>
        <div class="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </nav>
</header>

<script>
    // Dynamic Header on Scroll
    window.addEventListener('scroll', function() {
        const header = document.querySelector('.main-header');
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
    
    // Mobile Menu Toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    const navActions = document.querySelector('.nav-actions');

    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            navActions.classList.toggle('active');
            
            const icon = menuToggle.querySelector('i');
            if (navLinks.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }
</script>