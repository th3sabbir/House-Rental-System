<?php
// Fetch real statistics from database
require_once '../config/database.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Set MySQL timezone to match system
$conn->query("SET time_zone = '+05:45'");

// Get current database time to ensure sync
$db_time_result = $conn->query("SELECT NOW() as db_time");
$current_db_time = $db_time_result->fetch_assoc()['db_time'];

// Get total users count
$total_users_result = $conn->query("SELECT COUNT(*) as total FROM users");
$total_users = $total_users_result->fetch_assoc()['total'] ?? 0;

// Get total properties count
$total_properties_result = $conn->query("SELECT COUNT(*) as total FROM properties");
$total_properties = $total_properties_result->fetch_assoc()['total'] ?? 0;

// Get active tours count
$active_tours_result = $conn->query("SELECT COUNT(*) as total FROM tours WHERE status IN ('pending', 'confirmed')");
$active_tours = $active_tours_result->fetch_assoc()['total'] ?? 0;

// Get landlord count
$landlord_count_result = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'landlord'");
$landlord_count = $landlord_count_result->fetch_assoc()['total'] ?? 0;

// Get tenant count
$tenant_count_result = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'tenant'");
$tenant_count = $tenant_count_result->fetch_assoc()['total'] ?? 0;

// Get activity counts for the last 24 hours
$today_start = date('Y-m-d H:i:s', strtotime('-24 hours'));

$today_properties = $conn->query("SELECT COUNT(*) as count FROM properties WHERE created_at >= '$today_start'")->fetch_assoc()['count'];
$today_tours = $conn->query("SELECT COUNT(*) as count FROM tours WHERE created_at >= '$today_start'")->fetch_assoc()['count'];
$today_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE created_at >= '$today_start' AND user_type != 'admin'")->fetch_assoc()['count'];
$today_reviews = $conn->query("SELECT COUNT(*) as count FROM reviews WHERE created_at >= '$today_start'")->fetch_assoc()['count'];
$today_messages = $conn->query("SELECT COUNT(*) as count FROM messages WHERE created_at >= '$today_start'")->fetch_assoc()['count'];
$recent_activity = [];

// Get recent properties
$recent_props = $conn->query("
    SELECT p.title, u.full_name, p.created_at 
    FROM properties p 
    JOIN users u ON p.landlord_id = u.user_id 
    ORDER BY p.created_at DESC 
    LIMIT 3
");

// Get recent tours
$recent_tours = $conn->query("
    SELECT tours.tour_id, tours.status, tours.created_at, p.title, u.full_name
    FROM tours
    JOIN properties p ON tours.property_id = p.property_id
    JOIN users u ON tours.tenant_id = u.user_id
    ORDER BY tours.created_at DESC
    LIMIT 3
");

// Get recent user registrations
$recent_users = $conn->query("
    SELECT full_name, user_type, created_at 
    FROM users 
    WHERE user_type != 'admin'
    ORDER BY created_at DESC 
    LIMIT 3
");

// Get recent reviews
$recent_reviews = $conn->query("
    SELECT r.rating, r.comment, r.created_at, p.title, u.full_name
    FROM reviews r
    JOIN properties p ON r.property_id = p.property_id
    JOIN users u ON r.user_id = u.user_id
    ORDER BY r.created_at DESC
    LIMIT 2
");

// Get recent messages
$recent_messages = $conn->query("
    SELECT m.message, m.created_at, sender.full_name as sender_name, receiver.full_name as receiver_name
    FROM messages m
    JOIN users sender ON m.sender_id = sender.user_id
    JOIN users receiver ON m.receiver_id = receiver.user_id
    ORDER BY m.created_at DESC
    LIMIT 2
");

// Combine all activities
while ($prop = $recent_props->fetch_assoc()) {
    $recent_activity[] = [
        'type' => 'property',
        'icon' => 'fas fa-building',
        'color' => '#3498db',
        'title' => 'New Property Listed',
        'description' => $prop['title'] . ' by ' . $prop['full_name'],
        'time' => $prop['created_at']
    ];
}

while ($tour = $recent_tours->fetch_assoc()) {
    $status_text = ucfirst($tour['status']);
    $status_color = match($tour['status']) {
        'pending' => '#f39c12',
        'confirmed' => '#27ae60',
        'rejected' => '#e74c3c',
        'cancelled' => '#95a5a6',
        'completed' => '#9b59b6',
        default => '#7f8c8d'
    };
    $recent_activity[] = [
        'type' => 'tour',
        'icon' => 'fas fa-calendar-check',
        'color' => $status_color,
        'title' => 'Tour ' . $status_text,
        'description' => $tour['full_name'] . ' booked ' . $tour['title'],
        'time' => $tour['created_at']
    ];
}

while ($review = $recent_reviews->fetch_assoc()) {
    $stars = str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']);
    $recent_activity[] = [
        'type' => 'review',
        'icon' => 'fas fa-star',
        'color' => '#f1c40f',
        'title' => 'New Review',
        'description' => $stars . ' for ' . $review['title'] . ' by ' . $review['full_name'],
        'time' => $review['created_at']
    ];
}

while ($message = $recent_messages->fetch_assoc()) {
    $recent_activity[] = [
        'type' => 'message',
        'icon' => 'fas fa-envelope',
        'color' => '#e67e22',
        'title' => 'New Message',
        'description' => $message['sender_name'] . ' → ' . $message['receiver_name'],
        'time' => $message['created_at']
    ];
}

while ($user = $recent_users->fetch_assoc()) {
    $user_icon = match($user['user_type']) {
        'landlord' => 'fas fa-user-tie',
        'tenant' => 'fas fa-user',
        default => 'fas fa-user-plus'
    };
    $user_color = match($user['user_type']) {
        'landlord' => '#2ecc71',
        'tenant' => '#3498db',
        default => '#95a5a6'
    };
    $recent_activity[] = [
        'type' => 'user',
        'icon' => $user_icon,
        'color' => $user_color,
        'title' => 'New User Registered',
        'description' => $user['full_name'] . ' joined as a ' . ucfirst($user['user_type']),
        'time' => $user['created_at']
    ];
}

// Sort by time
usort($recent_activity, function($a, $b) {
    return strtotime($b['time']) - strtotime($a['time']);
});

// Get only last 10
$recent_activity = array_slice($recent_activity, 0, 10);

// Get activity counts for the last 24 hours
$today_start = date('Y-m-d H:i:s', strtotime('-24 hours'));

$today_properties = $conn->query("SELECT COUNT(*) as count FROM properties WHERE created_at >= '$today_start'")->fetch_assoc()['count'];
$today_tours = $conn->query("SELECT COUNT(*) as count FROM tours WHERE created_at >= '$today_start'")->fetch_assoc()['count'];
$today_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE created_at >= '$today_start' AND user_type != 'admin'")->fetch_assoc()['count'];
$today_reviews = $conn->query("SELECT COUNT(*) as count FROM reviews WHERE created_at >= '$today_start'")->fetch_assoc()['count'];
$today_messages = $conn->query("SELECT COUNT(*) as count FROM messages WHERE created_at >= '$today_start'")->fetch_assoc()['count'];

// Helper function to format time ago
function time_ago($timestamp, $current_db_time) {
    $time_ago = strtotime($timestamp);
    $current_time = strtotime($current_db_time);
    $time_difference = $current_time - $time_ago;
    $seconds = abs($time_difference);

    $minutes = round($seconds / 60);
    $hours = round($seconds / 3600);
    $days = round($seconds / 86400);
    $weeks = round($seconds / 604800);
    $months = round($seconds / 2629440);
    $years = round($seconds / 31553280);

    if ($seconds <= 60) {
        return "Just now";
    } else if ($minutes <= 60) {
        return ($minutes == 1) ? "1 minute ago" : "$minutes minutes ago";
    } else if ($hours <= 24) {
        return ($hours == 1) ? "1 hour ago" : "$hours hours ago";
    } else if ($days <= 7) {
        return ($days == 1) ? "Yesterday" : "$days days ago";
    } else if ($weeks <= 4.3) {
        return ($weeks == 1) ? "1 week ago" : "$weeks weeks ago";
    } else if ($months <= 12) {
        return ($months == 1) ? "1 month ago" : "$months months ago";
    } else {
        return ($years == 1) ? "1 year ago" : "$years years ago";
    }
}
?>

<!-- Dashboard Section -->
<div class="content-header">
    <div class="header-title">
        <h1>Dashboard Overview</h1>
        <p>Welcome back, <?php echo htmlspecialchars($admin_name); ?>! Here's what's happening today.</p>
    </div>
    <div class="header-actions">
        <div class="user-profile">
            <div class="admin-icon">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($admin_name); ?></span>
                <span class="user-role">Administrator</span>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <a href="?page=users" class="stat-card-link">
        <div class="stat-card primary">
            <div class="stat-card-header">
                <div class="stat-icon primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $total_users; ?></div>
                    <div class="stat-label">Total Users</div>
                    <div class="stat-change positive" style="font-size: 0.85rem; color: #7f8c8d;">
                        <?php echo $tenant_count; ?> Tenants, <?php echo $landlord_count; ?> Landlords
                    </div>
                </div>
            </div>
        </div>
    </a>

    <a href="?page=properties" class="stat-card-link">
        <div class="stat-card warning">
            <div class="stat-card-header">
                <div class="stat-icon warning">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $total_properties; ?></div>
                    <div class="stat-label">Total Properties</div>
                    <div class="stat-change positive" style="font-size: 0.85rem; color: #7f8c8d;">
                        Listed on platform
                    </div>
                </div>
            </div>
        </div>
    </a>

    <a href="?page=tours" class="stat-card-link">
        <div class="stat-card info">
            <div class="stat-card-header">
                <div class="stat-icon info">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $active_tours; ?></div>
                    <div class="stat-label">Active Tours</div>
                    <div class="stat-change positive" style="font-size: 0.85rem; color: #7f8c8d;">
                        Pending & Confirmed
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>

<!-- Recent Activity -->
<div class="table-container">
    <div class="table-header">
        <div>
            <h3><i class="fas fa-history"></i> Recent Activity</h3>
            <div class="activity-summary" style="font-size: 0.85rem; color: #7f8c8d; margin-top: 4px;">
                Today: <?php echo $today_properties; ?> properties, <?php echo $today_tours; ?> tours, <?php echo $today_users; ?> users, <?php echo $today_reviews; ?> reviews, <?php echo $today_messages; ?> messages
            </div>
        </div>
    <div class="header-actions">
        <button class="btn btn-outline btn-sm" onclick="refreshActivity()" id="refreshBtn">
            <i class="fas fa-sync"></i> Refresh
        </button>
        <select class="form-select form-select-sm" id="activityFilter" onchange="filterActivity()">
            <option value="all">All Activities</option>
            <option value="property">Properties</option>
            <option value="tour">Tours</option>
            <option value="user">Users</option>
            <option value="review">Reviews</option>
            <option value="message">Messages</option>
        </select>
    </div>
    </div>
    <div id="activityContainer">
        <ul class="activity-list" style="padding: 0; margin: 0; list-style: none;">
            <?php if (count($recent_activity) > 0): ?>
                <?php foreach ($recent_activity as $index => $activity): ?>
                    <li class="activity-item <?php echo $activity['type']; ?> <?php echo $index === 0 ? 'new' : ''; ?>" style="position: relative; padding: 15px 20px; border-bottom: 1px solid #ecf0f1; display: flex; align-items: flex-start; gap: 12px; transition: background-color 0.2s ease;">
                        <div class="activity-icon" style="width: 40px; height: 40px; border-radius: 50%; background: <?php echo $activity['color']; ?>; display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <i class="<?php echo $activity['icon']; ?>"></i>
                        </div>
                        <div class="activity-content" style="flex: 1;">
                            <div class="activity-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">
                                <span class="activity-title" style="font-weight: 600; color: #2c3e50;"><?php echo htmlspecialchars($activity['title']); ?></span>
                                <span class="activity-time" style="font-size: 0.85rem; color: #7f8c8d; white-space: nowrap;"><?php echo time_ago($activity['time'], $current_db_time); ?></span>
                            </div>
                            <p class="activity-desc" style="margin: 0; color: #34495e; line-height: 1.4;"><?php echo htmlspecialchars($activity['description']); ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="activity-item empty-state" style="text-align: center; padding: 60px 20px;">
                    <div style="color: #bdc3c7;">
                        <i class="fas fa-inbox" style="font-size: 4rem; display: block; margin-bottom: 20px;"></i>
                        <h4 style="margin: 0 0 10px 0; color: #7f8c8d;">No Recent Activity</h4>
                        <p style="margin: 0; color: #95a5a6; max-width: 300px; margin: 0 auto;">Activity will appear here as users interact with the platform</p>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<?php
// Close database connection
$conn->close();
?>

<style>
/* Activity Section Styles */
.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #ecf0f1;
}

.table-header h3 {
    margin: 0;
    color: #2c3e50;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.header-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.btn-outline {
    background: transparent;
    border: 1px solid #bdc3c7;
    color: #7f8c8d;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.85rem;
}

.btn-outline:hover {
    background: #ecf0f1;
    border-color: #95a5a6;
    color: #34495e;
}

.btn-outline:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.form-select {
    padding: 6px 12px;
    border: 1px solid #bdc3c7;
    border-radius: 4px;
    background: white;
    color: #34495e;
    font-size: 0.85rem;
    cursor: pointer;
}

.form-select:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

.activity-list {
    margin: 0;
    padding: 0;
    list-style: none;
}

.activity-item {
    transition: all 0.2s ease;
}

.activity-item:hover {
    background-color: #f8f9fa !important;
}

.activity-item.new {
    border-left: 3px solid #3498db;
}

.activity-icon {
    transition: transform 0.2s ease;
}

.activity-item:hover .activity-icon {
    transform: scale(1.1);
}

.activity-title {
    transition: color 0.2s ease;
}

.activity-item:hover .activity-title {
    color: #3498db;
}

/* Responsive design */
@media (max-width: 768px) {
    .table-header {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }

    .header-actions {
        justify-content: space-between;
    }

    .activity-item {
        padding: 12px 15px;
    }

    .activity-header {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 4px;
    }

    .activity-time {
        font-size: 0.8rem !important;
    }
}
</style>

<script>
// Activity refresh functionality
function refreshActivity() {
    const refreshBtn = document.getElementById('refreshBtn');
    const originalHTML = refreshBtn.innerHTML;

    // Show loading state
    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
    refreshBtn.disabled = true;

    // Reload the page to refresh data
    setTimeout(() => {
        location.reload();
    }, 500);
}

// Activity filtering functionality
function filterActivity() {
    const filter = document.getElementById('activityFilter').value;
    const activities = document.querySelectorAll('.activity-item');

    activities.forEach(activity => {
        if (filter === 'all' || activity.classList.contains(filter)) {
            activity.style.display = 'flex';
        } else {
            activity.style.display = 'none';
        }
    });

    // Check if any activities are visible
    const visibleActivities = document.querySelectorAll('.activity-item[style*="display: flex"]');
    const emptyState = document.querySelector('.empty-state');

    if (visibleActivities.length === 0 && !emptyState) {
        // Show no results message
        const container = document.getElementById('activityContainer');
        const noResults = document.createElement('li');
        noResults.className = 'activity-item no-results';
        noResults.style.cssText = 'text-align: center; padding: 40px 20px;';
        noResults.innerHTML = `
            <div style="color: #bdc3c7;">
                <i class="fas fa-search" style="font-size: 3rem; display: block; margin-bottom: 15px;"></i>
                <h4 style="margin: 0 0 5px 0; color: #7f8c8d;">No matching activities</h4>
                <p style="margin: 0; color: #95a5a6;">Try selecting a different filter</p>
            </div>
        `;
        container.querySelector('.activity-list').appendChild(noResults);
    } else if (visibleActivities.length > 0) {
        // Remove no results message if it exists
        const noResults = document.querySelector('.no-results');
        if (noResults) {
            noResults.remove();
        }
    }
}

// Add hover effects
document.addEventListener('DOMContentLoaded', function() {
    const activityItems = document.querySelectorAll('.activity-item');

    activityItems.forEach(item => {
        if (!item.classList.contains('empty-state')) {
            item.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f8f9fa';
            });

            item.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        }
    });

    // Auto-refresh every 5 minutes
    setInterval(() => {
        // Only auto-refresh if page is visible
        if (!document.hidden) {
            // Optional: Add a subtle indicator that data is fresh
            const refreshBtn = document.getElementById('refreshBtn');
            if (refreshBtn) {
                refreshBtn.style.color = '#27ae60';
                setTimeout(() => {
                    refreshBtn.style.color = '';
                }, 2000);
            }
        }
    }, 300000); // 5 minutes
});
</script>
