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

    // Total bookings count
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count FROM bookings b 
        JOIN properties p ON b.property_id = p.property_id 
        WHERE p.landlord_id = ?
    ");
    $stmt->execute([$user_id]);
    $total_bookings = $stmt->fetch()['count'];

    // Pending requests count
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count FROM bookings b 
        JOIN properties p ON b.property_id = p.property_id 
        WHERE p.landlord_id = ? AND b.status = 'pending'
    ");
    $stmt->execute([$user_id]);
    $pending_requests = $stmt->fetch()['count'];

    // Average rating
    $stmt = $conn->prepare("
        SELECT AVG(r.rating) as avg_rating FROM reviews r 
        JOIN properties p ON r.property_id = p.property_id 
        WHERE p.landlord_id = ?
    ");
    $stmt->execute([$user_id]);
    $avg_rating = round($stmt->fetch()['avg_rating'] ?? 0, 1);

    // Get recent properties
    $stmt = $conn->prepare("
        SELECT p.*, 
               (SELECT image_path FROM property_images WHERE property_id = p.property_id AND is_primary = 1 LIMIT 1) as main_image,
               (SELECT COUNT(*) FROM bookings WHERE property_id = p.property_id) as booking_count,
               (SELECT AVG(rating) FROM reviews WHERE property_id = p.property_id) as rating
        FROM properties p 
        WHERE p.landlord_id = ? 
        ORDER BY p.created_at DESC 
        LIMIT 6
    ");
    $stmt->execute([$user_id]);
    $properties = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Dashboard data loading error: " . $e->getMessage());
    $active_listings = 0;
    $total_bookings = 0;
    $pending_requests = 0;
    $avg_rating = 0;
    $properties = [];
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
    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-value"><?php echo $active_listings; ?></div>
                <div class="stat-label">Active Listings</div>
            </div>
            <div class="stat-icon primary">
                <i class="fas fa-building"></i>
            </div>
        </div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> Keep it up!
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-value"><?php echo $total_bookings; ?></div>
                <div class="stat-label">Total Bookings</div>
            </div>
            <div class="stat-icon success">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> Great performance
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-value"><?php echo $pending_requests; ?></div>
                <div class="stat-label">Pending Requests</div>
            </div>
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="stat-change">
            <?php echo $pending_requests > 0 ? 'Needs your attention' : 'All caught up'; ?>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-value"><?php echo $avg_rating > 0 ? $avg_rating : 'N/A'; ?></div>
                <div class="stat-label">Average Rating</div>
            </div>
            <div class="stat-icon info">
                <i class="fas fa-star"></i>
            </div>
        </div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> <?php echo $avg_rating >= 4.5 ? 'Excellent' : 'Good'; ?>
        </div>
    </div>
</div>

