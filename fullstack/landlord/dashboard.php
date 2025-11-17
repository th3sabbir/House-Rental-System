<?php
// Dashboard Page - Main Overview
// This file should be included in index.php or loaded separately

// Handle variable scoping when included
if (!isset($user_id)) {
    $user_id = $GLOBALS['user_id'] ?? null;
}
if (!isset($conn)) {
    $conn = $GLOBALS['conn'] ?? null;
}

if (empty($user_id) || empty($conn)) {
    error_log("Dashboard access error: missing user_id or db connection");
    ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        Dashboard cannot be displayed. Please log in again.
    </div>
    <?php
    return;
}

try {
    // Active listings count
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM properties WHERE landlord_id = ? AND status = 'available'");
    $stmt->execute([$user_id]);
    $active_listings = $stmt->fetch()['count'];

    // Total tours count
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count FROM tours b 
        JOIN properties p ON b.property_id = p.property_id 
        WHERE p.landlord_id = ?
    ");
    $stmt->execute([$user_id]);
    $total_tours = $stmt->fetch()['count'];

    // Pending requests count
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count FROM tours b 
        JOIN properties p ON b.property_id = p.property_id 
        WHERE p.landlord_id = ? AND b.status = 'pending'
    ");
    $stmt->execute([$user_id]);
    $pending_requests = $stmt->fetch()['count'];

    // Unread messages count
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND is_read = 0");
    $stmt->execute([$user_id]);
    $unread_messages = $stmt->fetch()['count'];

} catch (PDOException $e) {
    error_log("Dashboard data loading error: " . $e->getMessage());
    $active_listings = 0;
    $total_tours = 0;
    $pending_requests = 0;
    $unread_messages = 0;
}
?>

<div class="dashboard-header">
    <div>
        <h1>Dashboard</h1>
        <p class="welcome-message">Welcome back, <?php echo htmlspecialchars($landlord_name); ?>! Here's what's happening with your properties.</p>
    </div>
    <div>
        <a href="?page=add-property" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Property
        </a>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <a href="?page=my-properties" class="stat-card stat-card-link">
        <div class="stat-card-header">
            <div>
                <div class="stat-value"><?php echo $active_listings; ?></div>
                <div class="stat-label">Properties</div>
            </div>
            <div class="stat-icon primary">
                <i class="fas fa-building"></i>
            </div>
        </div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> Keep it up!
        </div>
    </a>

    <a href="?page=tours" class="stat-card stat-card-link">
        <div class="stat-card-header">
            <div>
                <div class="stat-value"><?php echo $total_tours; ?></div>
                <div class="stat-label">Total Tours</div>
            </div>
            <div class="stat-icon success">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> Great performance
        </div>
    </a>

    <a href="?page=tours" class="stat-card stat-card-link">
        <div class="stat-card-header">
            <div>
                <div class="stat-value"><?php echo $pending_requests; ?></div>
                <div class="stat-label">Tour Requests</div>
            </div>
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="stat-change">
            <?php echo $pending_requests > 0 ? 'Needs your attention' : 'All caught up'; ?>
        </div>
    </a>

    <a href="../messages.php" class="stat-card stat-card-link">
        <div class="stat-card-header">
            <div>
                <div class="stat-value"><?php echo $unread_messages; ?></div>
                <div class="stat-label">Unread Messages</div>
            </div>
            <div class="stat-icon info">
                <i class="fas fa-envelope"></i>
            </div>
        </div>
        <div class="stat-change">
            <?php echo $unread_messages > 0 ? 'Check your inbox' : 'All caught up'; ?>
        </div>
    </a>
</div>

<style>
.stat-card-link {
    text-decoration: none;
    color: inherit;
    display: block;
}
</style>

