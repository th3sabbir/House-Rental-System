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

// Get tour statistics
try {
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_tours,
            COUNT(CASE WHEN status IN ('confirmed', 'completed') THEN 1 END) as approved_tours,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_tours,
            COUNT(CASE WHEN status IN ('rejected', 'cancelled') THEN 1 END) as rejected_tours
        FROM tours 
        WHERE tenant_id = ?
    ");
    $stmt->execute([$user_id]);
    $stats = $stmt->fetch();
    
    $total_tours = $stats['total_tours'] ?? 0;
    $approved_tours = $stats['approved_tours'] ?? 0;
    $pending_tours = $stats['pending_tours'] ?? 0;
    $rejected_tours = $stats['rejected_tours'] ?? 0;
    
    // Get favorites count
    $fav_stmt = $pdo->prepare("SELECT COUNT(*) as total_favorites FROM favorites WHERE user_id = ?");
    $fav_stmt->execute([$user_id]);
    $fav_stats = $fav_stmt->fetch();
    $total_favorites = $fav_stats['total_favorites'] ?? 0;
} catch (PDOException $e) {
    error_log("Tenant dashboard stats error: " . $e->getMessage());
    $total_tours = 0;
    $approved_tours = 0;
    $pending_tours = 0;
    $rejected_tours = 0;
    $total_favorites = 0;
}
?>

<div class="dashboard-header">
    <h1>Dashboard</h1>
    <p class="welcome-message">Welcome back, <?php echo htmlspecialchars($tenant_name ?? 'Tenant'); ?>! Here's your rental activity.</p>
</div>

<!-- Stats Grid -->
<div class="stats-grid tenant-stats">
    <a href="?page=bookings" class="stat-card stat-card-link">
        <div class="stat-card-header">
            <div>
                <div class="stat-value"><?php echo $total_tours; ?></div>
                <div class="stat-label">Total Tours</div>
            </div>
            <div class="stat-icon info">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
        <div class="stat-change">All time</div>
    </a>

    <a href="?page=bookings" class="stat-card stat-card-link">
        <div class="stat-card-header">
            <div>
                <div class="stat-value"><?php echo $approved_tours; ?></div>
                <div class="stat-label">Approved Tours</div>
            </div>
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-change positive">Ready to move in</div>
    </a>

    <a href="?page=bookings" class="stat-card stat-card-link">
        <div class="stat-card-header">
            <div>
                <div class="stat-value"><?php echo $pending_tours; ?></div>
                <div class="stat-label">Pending Requests</div>
            </div>
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="stat-change">Awaiting approval</div>
    </a>

    <a href="?page=favorites" class="stat-card stat-card-link">
        <div class="stat-card-header">
            <div>
                <div class="stat-value"><?php echo $total_favorites; ?></div>
                <div class="stat-label">Saved Properties</div>
            </div>
            <div class="stat-icon favorite">
                <i class="fas fa-heart"></i>
            </div>
        </div>
        <div class="stat-change">Your favorites</div>
    </a>
</div>

<style>
.stat-card-link {
    text-decoration: none;
    color: inherit;
    display: block;
}

.stat-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
}

.stat-icon.info {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
}

.stat-icon.success {
    background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);
    color: white;
}

.stat-icon.warning {
    background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
    color: white;
}

.stat-icon.danger {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
}

.stat-icon.favorite {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
}

.stat-value {
    font-size: 2.2rem;
    font-weight: 700;
    color: var(--primary-color, #2c3e50);
    line-height: 1;
}

.stat-label {
    color: var(--text-medium, #7f8c8d);
    font-size: 0.95rem;
    margin-top: 8px;
    font-weight: 500;
}

.stat-change {
    font-size: 0.85rem;
    color: var(--text-medium, #7f8c8d);
}

.stat-change.positive {
    color: #27ae60;
}
</style>
