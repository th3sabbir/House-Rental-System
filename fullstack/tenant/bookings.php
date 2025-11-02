<?php
// Bookings Page - View and manage tenant bookings

// Handle variable scoping when included
if (!isset($user_id)) {
    $user_id = $GLOBALS['user_id'] ?? null;
}
if (!isset($pdo)) {
    $pdo = $GLOBALS['pdo'] ?? null;
}

if (empty($user_id) || empty($pdo)) {
    error_log("Tenant bookings access error: missing user_id or db connection");
    ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        Bookings cannot be displayed. Please log in again.
    </div>
    <?php
    return;
}

// Get tenant's bookings
try {
    $stmt = $pdo->prepare("
        SELECT
            b.booking_id,
            b.start_date,
            b.end_date,
            b.total_amount,
            b.status,
            b.message,
            b.created_at,
            p.title as property_title,
            p.address,
            (SELECT image_path FROM property_images WHERE property_id = p.property_id AND is_primary = 1 LIMIT 1) as main_image,
            u.full_name as landlord_name,
            u.email as landlord_email,
            u.phone as landlord_phone
        FROM bookings b
        JOIN properties p ON b.property_id = p.property_id
        JOIN users u ON p.landlord_id = u.user_id
        WHERE b.tenant_id = ?
        ORDER BY b.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $bookings = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Tenant bookings data error: " . $e->getMessage());
    $bookings = [];
}
?>

<div class="dashboard-header">
    <h1>My Bookings</h1>
    <p class="welcome-message">Track all your rental requests and bookings.</p>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Property</th>
                <th>Landlord</th>
                <th>Total Amount</th>
                <th>Booking Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($bookings)): ?>
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No Bookings Yet</h3>
                        <p>You haven't made any booking requests yet. Start by browsing available properties!</p>
                        <a href="../properties.php" class="btn btn-primary">
                            <i class="fas fa-search"></i> Browse Properties
                        </a>
                    </div>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($bookings as $booking): ?>
            <tr>
                <td>
                    <div class="property-info">
                        <img src="<?php echo $booking['main_image'] ? '../uploads/' . htmlspecialchars($booking['main_image']) : 'https://via.placeholder.com/80x60?text=No+Image'; ?>" 
                             alt="Property" class="property-img">
                        <div class="property-details">
                            <h4><?php echo htmlspecialchars($booking['property_title']); ?></h4>
                            <p><?php echo htmlspecialchars($booking['address']); ?></p>
                        </div>
                    </div>
                </td>
                <td>
                    <strong><?php echo htmlspecialchars($booking['landlord_name']); ?></strong><br>
                    <small><?php echo htmlspecialchars($booking['landlord_email']); ?></small>
                </td>
                <td>$<?php echo number_format($booking['total_amount'], 2); ?></td>
                <td><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></td>
                <td>
                    <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                        <?php echo ucfirst($booking['status']); ?>
                    </span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-secondary" onclick="viewBooking(<?php echo $booking['booking_id']; ?>)">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <?php if ($booking['status'] === 'confirmed'): ?>
                        <button class="btn btn-primary" onclick="writeReview(<?php echo $booking['booking_id']; ?>)">
                            <i class="fas fa-star"></i> Review
                        </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
