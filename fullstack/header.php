<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$user_name = $is_logged_in ? ($_SESSION['full_name'] ?? $_SESSION['username']) : '';
$user_role = $is_logged_in ? $_SESSION['role'] : '';

// Profile image handling: session may store a relative path like "uploads/profiles/.."
// Normalize to absolute web path and verify file exists on disk. If missing, fall back to null.
$user_image = null;
if ($is_logged_in && !empty($_SESSION['profile_image'])) {
    $raw = $_SESSION['profile_image'];

    // If it's already an absolute URL, use as-is
    if (preg_match('#^https?://#i', $raw)) {
        $user_image = $raw;
    } else {
        // Build an absolute web path from document root
        $webPath = '/' . ltrim($raw, '/');
        $filePath = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . ltrim($raw, '/');

        // If the file exists on disk, use the absolute web path; otherwise ignore
        if (file_exists($filePath)) {
            $user_image = $webPath;
        } else {
            // If not found, try checking one level up (in case scripts run from subfolders)
            $altPath = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/..' . '/' . ltrim($raw, '/');
            if (file_exists($altPath)) {
                $user_image = $webPath; // still use web path; file likely served
            } else {
                $user_image = null;
            }
        }
    }
}

// Determine dashboard URL based on role
$dashboard_url = '';
if ($is_logged_in) {
    switch ($user_role) {
        case 'admin':
            $dashboard_url = 'admin/index.php';
            break;
        case 'landlord':
            $dashboard_url = 'landlord/index.php';
            break;
        case 'tenant':
            $dashboard_url = 'tenant/index.php';
            break;
        default:
            $dashboard_url = 'index.php';
    }
}
?>

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

    /* User Profile Dropdown */
    .user-profile {
        position: relative;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        padding: 8px 15px;
        border-radius: 50px;
        transition: background-color 0.3s ease;
    }

    .user-profile:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #1abc9c;
    }

    .user-avatar-placeholder {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1abc9c, #16a085);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.1rem;
        border: 2px solid #1abc9c;
    }

    .user-info {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .user-name {
        font-weight: 600;
        font-size: 0.95rem;
        color: #ffffff;
        line-height: 1.2;
    }

    .user-role {
        font-size: 0.75rem;
        color: #1abc9c;
        text-transform: capitalize;
        line-height: 1.2;
    }

    .dropdown-arrow {
        margin-left: 5px;
        font-size: 0.75rem;
        transition: transform 0.3s ease;
    }

    .user-profile.active .dropdown-arrow {
        transform: rotate(180deg);
    }

    .user-dropdown {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        min-width: 200px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .user-dropdown.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .user-dropdown a,
    .user-dropdown button {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        color: #2c3e50;
        text-decoration: none;
        transition: background-color 0.3s ease;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        font-size: 0.95rem;
        cursor: pointer;
        font-family: inherit;
    }

    .user-dropdown a:hover,
    .user-dropdown button:hover {
        background-color: #f8f9fa;
        color: #1abc9c;
    }

    .user-dropdown a i,
    .user-dropdown button i {
        width: 20px;
        font-size: 1rem;
    }

    .user-dropdown .dropdown-divider {
        height: 1px;
        background-color: #e9ecef;
        margin: 5px 0;
    }

    .dashboard-link {
        background-color: #1abc9c;
        color: #ffffff !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
        text-decoration: none;
    }

    .dashboard-link:hover {
        background-color: #16a085;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(26, 188, 156, 0.3);
    }

    .dashboard-link i {
        font-size: 1rem;
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

        .user-info {
            display: none;
        }

        .user-dropdown {
            position: fixed;
            top: 70px;
            right: 0;
            left: 0;
            width: 100%;
            border-radius: 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .dashboard-link {
            width: 100%;
            justify-content: center;
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
            <?php if ($is_logged_in): ?>
                <!-- Logged In User -->
                <a href="<?php echo $dashboard_url; ?>" class="dashboard-link">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
                <div class="user-profile" id="userProfile">
                    <?php if ($user_image): ?>
                        <img src="<?php echo htmlspecialchars($user_image); ?>" alt="Profile" class="user-avatar">
                    <?php else: ?>
                        <div class="user-avatar-placeholder">
                            <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <div class="user-info">
                        <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
                        <span class="user-role"><?php echo htmlspecialchars($user_role); ?></span>
                    </div>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                    
                    <!-- Dropdown Menu -->
                    <div class="user-dropdown" id="userDropdown">
                        <a href="<?php echo $dashboard_url; ?>">
                            <i class="fas fa-th-large"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="profile.php">
                            <i class="fas fa-user"></i>
                            <span>My Profile</span>
                        </a>
                        <?php if ($user_role === 'tenant'): ?>
                        <a href="tenant/index.php#favorites">
                            <i class="fas fa-heart"></i>
                            <span>My Favorites</span>
                        </a>
                        <a href="tenant/index.php#bookings">
                            <i class="fas fa-calendar-check"></i>
                            <span>My Bookings</span>
                        </a>
                        <?php elseif ($user_role === 'landlord'): ?>
                        <a href="landlord/index.php#properties">
                            <i class="fas fa-home"></i>
                            <span>My Properties</span>
                        </a>
                        <a href="landlord/index.php#bookings">
                            <i class="fas fa-calendar-check"></i>
                            <span>Bookings</span>
                        </a>
                        <?php endif; ?>
                        <a href="messages.php">
                            <i class="fas fa-envelope"></i>
                            <span>Messages</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="settings.php">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                        <button onclick="logout()">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <!-- Not Logged In -->
                <a href="login.php" class="login-btn">Login</a>
                <a href="signup.php" class="btn btn-primary">Sign Up</a>
            <?php endif; ?>
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

    // User Profile Dropdown
    const userProfile = document.getElementById('userProfile');
    const userDropdown = document.getElementById('userDropdown');

    if (userProfile && userDropdown) {
        userProfile.addEventListener('click', function(e) {
            e.stopPropagation();
            userProfile.classList.toggle('active');
            userDropdown.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userProfile.contains(e.target)) {
                userProfile.classList.remove('active');
                userDropdown.classList.remove('active');
            }
        });

        // Prevent dropdown from closing when clicking inside it
        userDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Logout Function
    function logout() {
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = 'api/logout_handler.php';
        }
    }
</script>



