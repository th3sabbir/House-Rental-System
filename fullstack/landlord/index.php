<?php
session_start();

// Include path helpers
require_once '../includes/paths.php';

// Prevent browser caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    redirect('login.php');
}

// Check if user is landlord
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'landlord') {
    redirect('index.php');
}

// Include database connection
require_once '../config/database.php';
require_once '../includes/auth.php';

// Initialize database connection
$db = new Database();
$pdo = $db->connect();
global $pdo;

$auth = new Auth();
$conn = $pdo;

// Make $conn and $user_id globally available to included pages
$GLOBALS['conn'] = $conn;
$user_id = $_SESSION['user_id'];
$GLOBALS['user_id'] = $user_id;

// Get user info
$user = getUserById($user_id);
$landlord_name = $user['full_name'] ?? 'Landlord';

// Get unread messages count
$unread_count = 0;
if ($user_id) {
    $unread_query = "SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND is_read = 0";
    $unread_stmt = $conn->prepare($unread_query);
    $unread_stmt->execute([$user_id]);
    $unread_count = $unread_stmt->fetch()['count'];
}

// Generate profile image URL
if (!empty($user['profile_image']) && file_exists('../uploads/' . $user['profile_image'])) {
    $profile_image_url = '../uploads/' . $user['profile_image'];
} else {
    $profile_image_url = 'https://ui-avatars.com/api/?name=' . urlencode($landlord_name) . '&background=1abc9c&color=fff&size=160';
}

// Determine which page to display
$page = $_GET['page'] ?? 'dashboard';
$page = preg_replace('/[^a-z0-9\-_]/', '', strtolower($page));

// Valid pages
$valid_pages = ['dashboard', 'my-properties', 'tours', 'settings', 'add-property', 'edit-property'];
if (!in_array($page, $valid_pages)) {
    $page = 'dashboard';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($page); ?> - AmarThikana</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()" role="button" aria-label="Close sidebar"></div>

    <div class="dashboard-wrapper">
        <div class="dashboard-layout">
            <!-- Sidebar -->
            <aside class="sidebar" id="landlordSidebar">
                <div class="sidebar-profile">
                    <img src="<?php echo htmlspecialchars($profile_image_url); ?>" alt="<?php echo htmlspecialchars($landlord_name); ?>">
                    <h3><?php echo htmlspecialchars($landlord_name); ?></h3>
                    <p>Property Owner</p>
                </div>
                <ul class="sidebar-nav">
                    <li><a href="?page=dashboard" class="nav-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-th-large"></i> Dashboard</a></li>
                    <li><a href="?page=my-properties" class="nav-link <?php echo $page === 'my-properties' ? 'active' : ''; ?>"><i class="fas fa-building"></i> My Properties</a></li>
                    <li><a href="?page=tours" class="nav-link <?php echo $page === 'tours' ? 'active' : ''; ?>"><i class="fas fa-calendar-check"></i> Tour Requests</a></li>
                    <li><a href="../messages.php"><i class="fas fa-comments"></i> Messages
                        <?php if ($unread_count > 0): ?>
                            <span style="background: #e74c3c; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.75rem; margin-left: auto;"><?php echo $unread_count; ?></span>
                        <?php endif; ?>
                    </a></li>
                    <li><a href="?page=settings" class="nav-link <?php echo $page === 'settings' ? 'active' : ''; ?>"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </aside>

            <!-- Main Content -->
            <main class="dashboard-content">
                <?php
                    // Load the appropriate page file
                    $page_file = __DIR__ . '/' . $page . '.php';
                    error_log("LANDLORD INDEX - About to include page: $page, file: $page_file, exists: " . (file_exists($page_file) ? 'YES' : 'NO'));
                    if (file_exists($page_file)) {
                        include $page_file;
                        error_log("LANDLORD INDEX - Successfully included: $page_file");
                    } else {
                        echo '<div class="error-message">Page not found</div>';
                        error_log("LANDLORD INDEX - Page file not found: $page_file");
                    }
                ?>
            </main>

            <!-- Mobile Menu Toggle Button -->
            <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>

    <script src="../js/script.js"></script>
    <script src="js/dashboard.js"></script>
    <script src="js/settings.js"></script>
</body>
</html>
