<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include path helpers
require_once __DIR__ . '/includes/paths.php';
require_once 'config/database.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$user_name = $is_logged_in ? ($_SESSION['full_name'] ?? $_SESSION['username']) : '';
$user_role = $is_logged_in ? $_SESSION['role'] : '';

// Get unread messages count
$unread_count = 0;
if ($is_logged_in && isset($_SESSION['user_id'])) {
    $unread_query = "SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND is_read = 0";
    $unread_stmt = $conn->prepare($unread_query);
    $unread_stmt->bind_param('i', $_SESSION['user_id']);
    $unread_stmt->execute();
    $unread_result = $unread_stmt->get_result();
    $unread_count = $unread_result->fetch_assoc()['count'];
}

// Profile image handling: check if file exists in uploads/profiles, otherwise fall back to letter avatar
$user_image = null;
if ($is_logged_in && !empty($_SESSION['profile_image'])) {
    $raw = $_SESSION['profile_image'];

    // Accept absolute URLs as-is
    if (filter_var($raw, FILTER_VALIDATE_URL)) {
        $user_image = $raw;
    } else {
        // Remove any leading "profiles/" or "uploads/profiles/" since we'll add it
        $filename = basename($raw);

        // Construct path within amarthikana folder using DOCUMENT_ROOT
        $relativePath = 'uploads/profiles/' . $filename;
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . getBasePath() . '/' . $relativePath;

        if (file_exists($fullPath)) {
            $user_image = getBasePath() . '/' . $relativePath;
        }
    }
}

// Determine dashboard URL based on role using dynamic paths
$dashboard_url = '';
$base = getBasePath();
if ($is_logged_in) {
    switch ($user_role) {
        case 'landlord':
            $dashboard_url = $base . '/landlord/index.php';
            break;
        case 'tenant':
            $dashboard_url = $base . '/tenant/index.php';
            break;
        default:
            $dashboard_url = $base . '/index.php';
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

    /* mobile-only link duplicates are hidden on desktop */
    .nav-links .mobile-auth {
        display: none;
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
        background-color: #d5dbdb;
        color: #1abc9c;
        padding: 12px 20px;
        border-radius: 0;
        margin: 0;
        font-weight: 600;
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

    /* Messages Badge */
    .messages-badge {
        position: relative;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.3s ease;
        color: white;
        font-size: 1.2rem;
        text-decoration: none;
    }

    .messages-badge:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: scale(1.1);
        color: #1abc9c;
    }

    .messages-badge .badge-count {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #e74c3c;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        border: 2px solid white;
    }

    /* Mobile Menu Overlay */
    .mobile-menu-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 998;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .mobile-menu-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    /* Body scroll prevention */
    body.menu-open {
        overflow: hidden;
        position: fixed;
        width: 100%;
    }
    
    .mobile-menu {
        display: contents;
    }
    
    @media (max-width: 768px) {
        .navbar {
            padding: 0 15px;
        }
        
        .logo {
            font-size: 1.4rem;
        }
        
        .mobile-menu {
            position: fixed;
            left: -100%;
            top: 70px;
            width: 100%;
            height: calc(100vh - 70px);
            background-color: #2c3e50;
            transition: left 0.3s ease;
            box-shadow: 0 10px 27px rgba(0, 0, 0, 0.15);
            z-index: 999;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        
        .mobile-menu.active {
            left: 0;
        }
        
        .nav-links {
            flex-direction: column;
            background-color: transparent;
            width: 100%;
            text-align: center;
            padding: 40px 0;
            gap: 25px;
            flex: 1;
        }
        /* show mobile-only auth links inside nav for better mobile visibility */
        .nav-links .mobile-auth {
            display: block;
            width: 100%;
        }
        .nav-links .mobile-auth a {
            display: block;
            padding: 15px 20px;
            font-weight: 600;
            text-decoration: none;
            color: #ffffff;
        }
        .nav-links .mobile-auth a.btn-primary {
            width: calc(100% - 40px);
            margin: 10px 20px;
            border-radius: 8px;
        }
        
        .nav-links li {
            width: 100%;
        }
        
        .nav-links a {
            display: block;
            padding: 15px 20px;
            font-size: 1.1rem;
            font-weight: 500;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .nav-links a:last-child {
            border-bottom: none;
        }
        
        .nav-actions {
            flex-direction: column;
            background-color: transparent;
            width: 100%;
            text-align: center;
            padding: 30px 0;
            gap: 15px;
        }
        
        .menu-toggle {
            display: block;
            padding: 10px;
            margin-left: 10px;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }
        
        .menu-toggle:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .user-info {
            display: flex !important;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        
        .user-info .user-name {
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .user-info .user-role {
            font-size: 0.85rem;
        }

        .user-profile {
            width: 100%;
            justify-content: center;
            flex-direction: column;
            padding: 20px;
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            margin-left: 20px;
            margin-right: 20px;
            width: calc(100% - 40px);
        }
        
        .user-profile .dropdown-arrow {
            margin-top: 10px;
            margin-left: 0;
        }

        .user-dropdown {
            position: static;
            opacity: 0;
            visibility: hidden;
            max-height: 0;
            transform: none;
            box-shadow: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            margin-top: 15px;
            margin-left: 20px;
            margin-right: 20px;
            width: calc(100% - 40px);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .user-dropdown.active {
            opacity: 1;
            visibility: visible;
            max-height: 500px;
        }

        .user-dropdown a,
        .user-dropdown button {
            color: #ffffff;
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            justify-content: flex-start;
            gap: 15px;
        }

        .user-dropdown a:hover,
        .user-dropdown button:hover {
            background-color: rgba(26, 188, 156, 0.2);
            color: #1abc9c;
            font-weight: 600;
        }
        
        .user-dropdown a:last-child,
        .user-dropdown button:last-child {
            border-bottom: none;
        }

        .user-dropdown .dropdown-divider {
            background-color: rgba(255, 255, 255, 0.2);
            margin: 10px 0;
        }

        .dashboard-link {
            width: calc(100% - 40px);
            justify-content: center;
            margin-left: 20px;
            margin-right: 20px;
            margin-bottom: 15px;
        }

        .nav-actions .login-btn {
            display: block !important;
            padding: 15px 20px !important;
            color: #ffffff !important;
            text-decoration: none !important;
            font-weight: 500 !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
            transition: all 0.3s ease !important;
            width: 100% !important;
            text-align: center !important;
            background: transparent !important;
            font-size: 1rem !important;
            line-height: 1.5 !important;
            margin-bottom: 10px !important;
        }
        
        .nav-actions .login-btn:hover {
            background: rgba(26, 188, 156, 0.1) !important;
            color: #1abc9c !important;
        }

        .nav-actions .btn-primary {
            display: block !important;
            width: calc(100% - 40px) !important;
            padding: 15px 20px !important;
            margin: 10px 20px 0 20px !important;
            text-align: center !important;
            background-color: #1abc9c !important;
            color: #ffffff !important;
            border: none !important;
            border-radius: 8px !important;
            font-size: 1rem !important;
            font-weight: 600 !important;
            text-decoration: none !important;
            transition: all 0.3s ease !important;
        }

        .nav-actions .btn-primary:hover {
            background-color: #16a085 !important;
            color: white !important;
            transform: translateY(-1px) !important;
        }

        /* Messages badge in mobile menu */
        .messages-badge {
            width: calc(100% - 40px);
            justify-content: center;
            padding: 15px 20px;
            margin: 0 20px 15px 20px;
            background: rgba(26, 188, 156, 0.15);
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .messages-badge:hover {
            background: rgba(26, 188, 156, 0.25);
            transform: scale(1.02);
        }
    }
</style>

<header class="main-header">
    <nav class="navbar container">
        <a href="index.php" class="logo">
            <i class="fa-solid fa-house-chimney-window"></i> 
            AmarThikana
        </a>
        <div class="mobile-menu">
        <ul class="nav-links">
            <li><a href="index.php" id="nav-rent">Rent</a></li>
            <li><a href="properties.php" id="nav-properties">Properties</a></li>
            <li><a href="about-us.php" id="nav-about">About Us</a></li>
            <!-- Mobile-only auth links (duplicate of nav-actions) to ensure visibility when header is loaded via AJAX -->
            <?php if (!isset($is_logged_in) || !$is_logged_in): ?>
                <li class="mobile-auth"><a href="login.php">Login</a></li>
                <li class="mobile-auth"><a href="signup.php" class="btn btn-primary">Sign Up</a></li>
            <?php endif; ?>
        </ul>
        <div class="nav-actions">
            <?php if ($is_logged_in && $user_role !== 'admin'): ?>
                <!-- Messages Quick Access -->
                <a href="messages.php" class="messages-badge">
                    <i class="fas fa-envelope"></i>
                    <?php if ($unread_count > 0): ?>
                        <span class="badge-count"><?php echo $unread_count; ?></span>
                    <?php endif; ?>
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
                        <a href="<?php echo $dashboard_url; ?>?page=settings">
                            <i class="fas fa-user"></i>
                            <span>My Profile</span>
                        </a>
                        <?php if ($user_role === 'tenant'): ?>
                        <a href="<?php echo $dashboard_url; ?>?page=favorites">
                            <i class="fas fa-heart"></i>
                            <span>My Favorites</span>
                        </a>
                        <a href="<?php echo $dashboard_url; ?>?page=tours">
                            <i class="fas fa-calendar-check"></i>
                            <span>My Tours</span>
                        </a>
                        <?php elseif ($user_role === 'landlord'): ?>
                        <a href="<?php echo $dashboard_url; ?>?page=my-properties">
                            <i class="fas fa-home"></i>
                            <span>My Properties</span>
                        </a>
                        <a href="<?php echo $dashboard_url; ?>?page=tours">
                            <i class="fas fa-calendar-check"></i>
                            <span>Tours</span>
                        </a>
                        <?php endif; ?>
                        <a href="messages.php">
                            <i class="fas fa-envelope"></i>
                            <span>Messages</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="<?php echo $dashboard_url; ?>?page=settings">
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
                <!-- Not Logged In or Admin -->
                <a href="login.php" class="login-btn">Login</a>
                <a href="signup.php" class="btn btn-primary">Sign Up</a>
            <?php endif; ?>
        </div>
        </div>
        <div class="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </nav>
</header>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

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
    
    // Mobile menu toggle is handled centrally in js/script.js
    // This header script keeps only profile/dropdown and scroll behavior.

    // Handle user profile click in mobile menu
    const userProfile = document.getElementById('userProfile');
    if (userProfile) {
        userProfile.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // On mobile, toggle the dropdown
            if (window.innerWidth <= 768) {
                userProfile.classList.toggle('active');
                const userDropdown = document.getElementById('userDropdown');
                userDropdown?.classList.toggle('active');
            } else {
                // On desktop, toggle dropdown
                userProfile.classList.toggle('active');
                const userDropdown = document.getElementById('userDropdown');
                userDropdown?.classList.toggle('active');
            }
        });
    }
    
    // Close user dropdown when clicking outside on desktop
    document.addEventListener('click', function(e) {
        if (userProfile && window.innerWidth > 768) {
            if (!userProfile.contains(e.target)) {
                userProfile.classList.remove('active');
                const userDropdown = document.getElementById('userDropdown');
                userDropdown?.classList.remove('active');
            }
        }
    });

    // (No more mobile menu handlers here — see js/script.js)

    // Logout Function
    function logout() {
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = '<?php echo url('api/logout_handler.php'); ?>';
        }
    }
</script>



