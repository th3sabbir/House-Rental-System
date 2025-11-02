<?php
// Dashboard Page - Tenant Overview

// Handle variable scoping when included
if (!isset($user_id)) {
    $user_id = $GLOBALS['user_id'] ?? null;
}
if (!isset($pdo)) {
    $pdo = $GLOBALS['pdo'] ?? null;
}

if (empty($user_id) || empty($pdo)) {
    error_log("Tenant dashboard access error: missing user_id or db connection");
    ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        Dashboard cannot be displayed. Please log in again.
    </div>
    <?php
    return;
}

// Get booking statistics
try {
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_bookings,
            COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as approved_bookings,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_bookings,
            COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_bookings
        FROM bookings 
        WHERE tenant_id = ?
    ");
    $stmt->execute([$user_id]);
    $stats = $stmt->fetch();
    
    $total_bookings = $stats['total_bookings'] ?? 0;
    $approved_bookings = $stats['approved_bookings'] ?? 0;
    $pending_bookings = $stats['pending_bookings'] ?? 0;
    $rejected_bookings = $stats['rejected_bookings'] ?? 0;
} catch (PDOException $e) {
    error_log("Tenant dashboard stats error: " . $e->getMessage());
    $total_bookings = 0;
    $approved_bookings = 0;
    $pending_bookings = 0;
    $rejected_bookings = 0;
}
?>

<div class="dashboard-header">
    <h1>Dashboard</h1>
    <p class="welcome-message">Welcome back, <?php echo htmlspecialchars($tenant_name ?? 'Tenant'); ?>! Here's your rental activity.</p>
</div>

<!-- Stats Grid -->
<div class="stats-grid tenant-stats">
    <div class="stat-card">
        <i class="fas fa-calendar-check"></i>
        <h3><?php echo $total_bookings; ?></h3>
        <p>Total Bookings</p>
        <div class="stat-change">All time</div>
    </div>

    <div class="stat-card">
        <i class="fas fa-check-circle"></i>
        <h3><?php echo $approved_bookings; ?></h3>
        <p>Approved Bookings</p>
        <div class="stat-change">Ready to move in</div>
    </div>

    <div class="stat-card">
        <i class="fas fa-clock"></i>
        <h3><?php echo $pending_bookings; ?></h3>
        <p>Pending Requests</p>
        <div class="stat-change">Awaiting approval</div>
    </div>

    <div class="stat-card">
        <i class="fas fa-times-circle"></i>
        <h3><?php echo $rejected_bookings; ?></h3>
        <p>Rejected Requests</p>
        <div class="stat-change">Try different properties</div>
    </div>
</div>

<!-- Quick Actions -->
<!-- <div class="section-header">
    <h2>Quick Actions</h2>
</div>

<div class="property-grid">
    <div class="property-card">
        <div class="card-image">
            <img src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=600" alt="Browse Properties">
        </div>
        <div class="card-content">
            <h3>Browse Properties</h3>
            <p>Find your next home from our available listings</p>
            <a href="../properties.php" class="btn btn-primary">
                <i class="fas fa-search"></i> Search Now
            </a>
        </div>
    </div>

    <div class="property-card">
        <div class="card-image">
            <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=600" alt="My Bookings">
        </div>
        <div class="card-content">
            <h3>My Bookings</h3>
            <p>View and manage your booking requests</p>
            <a href="?page=bookings" class="btn btn-primary">
                <i class="fas fa-calendar-check"></i> View Bookings
            </a>
        </div>
    </div>

    <div class="property-card">
        <div class="card-image">
            <img src="https://images.unsplash.com/photo-1507089947368-19c1da9775ae?w=600" alt="Messages">
        </div>
        <div class="card-content">
            <h3>Messages</h3>
            <p>Contact property owners and landlords</p>
            <a href="../messages.php" class="btn btn-primary">
                <i class="fas fa-comments"></i> Open Messages
            </a>
        </div>
    </div> -->
</div>
