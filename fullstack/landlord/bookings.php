<?php
// Bookings Page - View and manage bookings

if (!isset($user_id) || !isset($conn)) {
    die('Invalid access');
}

try {
    // Get recent bookings
    $stmt = $conn->prepare("
        SELECT b.*, p.title as property_title, p.address, u.full_name as tenant_name, u.email as tenant_email, u.phone as tenant_phone,
               (SELECT image_path FROM property_images WHERE property_id = p.property_id AND is_primary = 1 LIMIT 1) as property_image,
               u.profile_image as tenant_image
        FROM bookings b 
        JOIN properties p ON b.property_id = p.property_id 
        JOIN users u ON b.tenant_id = u.user_id 
        WHERE p.landlord_id = ? 
        ORDER BY b.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $bookings = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Bookings data loading error: " . $e->getMessage());
    $bookings = [];
}
?>

<div class="dashboard-header">
    <h1>My Bookings</h1>
    <p class="welcome-message">View and manage all rental booking requests.</p>
</div>

<div class="section-header">
    <h2>All Booking Requests</h2>
</div>

<div class="table-container">
    <table class="info-table">
        <thead>
            <tr>
                <th>Tenant</th>
                <th>Property</th>
                <th>Total Amount</th>
                <th>Created Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($bookings)): ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 40px;">
                    <i class="fas fa-inbox" style="font-size: 2rem; color: #ccc; margin-bottom: 10px;"></i>
                    <p style="color: #999; margin: 10px 0;">No bookings yet</p>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($bookings as $booking): ?>
            <tr>
                <td>
                    <div class="tenant-info">
                        <img src="<?php echo $booking['tenant_image'] ? '../uploads/' . $booking['tenant_image'] : 'https://ui-avatars.com/api/?name=' . urlencode($booking['tenant_name']); ?>" 
                             alt="Tenant" class="tenant-avatar">
                        <div>
                            <div class="tenant-name"><?php echo htmlspecialchars($booking['tenant_name']); ?></div>
                            <small style="color: #999;"><?php echo htmlspecialchars($booking['tenant_email']); ?></small>
                        </div>
                    </div>
                </td>
                <td><?php echo htmlspecialchars($booking['property_title']); ?></td>
                <td>$<?php echo number_format($booking['total_amount'], 2); ?></td>
                <td><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></td>
                <td>
                    <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                        <?php echo ucfirst($booking['status']); ?>
                    </span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn view" onclick="openBookingModal(<?php echo htmlspecialchars(json_encode($booking)); ?>)">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
