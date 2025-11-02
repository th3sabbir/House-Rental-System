<?php
// Reviews Page - Tenant's reviews

// Handle variable scoping when included
if (!isset($user_id)) {
    $user_id = $GLOBALS['user_id'] ?? null;
}
if (!isset($pdo)) {
    $pdo = $GLOBALS['pdo'] ?? null;
}

if (empty($user_id) || empty($pdo)) {
    error_log("Tenant reviews access error: missing user_id or db connection");
    ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        Reviews cannot be displayed. Please log in again.
    </div>
    <?php
    return;
}

// Get tenant's reviews
try {
    $stmt = $pdo->prepare("
        SELECT 
            r.review_id,
            r.rating,
            r.comment,
            r.created_at,
            p.title as property_title,
            p.address,
            (SELECT image_path FROM property_images WHERE property_id = p.property_id AND is_primary = 1 LIMIT 1) as property_image
        FROM reviews r
        JOIN properties p ON r.property_id = p.property_id
        WHERE r.user_id = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $reviews = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Tenant reviews data error: " . $e->getMessage());
    $reviews = [];
}
?>

<div class="dashboard-header">
    <h1>My Reviews</h1>
    <p class="welcome-message">Reviews you've written for properties</p>
</div>

<?php if (empty($reviews)): ?>
<div class="empty-state">
    <i class="fas fa-star-half-alt"></i>
    <h3>No Reviews Yet</h3>
    <p>You haven't written any reviews yet. Complete a booking to share your experience!</p>
    <a href="../properties.php" class="btn btn-primary">
        <i class="fas fa-search"></i> Browse Properties
    </a>
</div>
<?php else: ?>
<?php foreach ($reviews as $review): ?>
<div class="review-card">
    <div class="review-header">
        <div class="review-property">
            <img src="<?php echo $review['property_image'] ? '../uploads/' . htmlspecialchars($review['property_image']) : 'https://via.placeholder.com/60x60?text=No+Image'; ?>" 
                 alt="Property">
            <div>
                <h4><?php echo htmlspecialchars($review['property_title']); ?></h4>
                <p><?php echo htmlspecialchars($review['address']); ?></p>
            </div>
        </div>
        <div class="review-rating">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <i class="fas fa-star<?php echo $i <= $review['rating'] ? '' : '-o'; ?>"></i>
            <?php endfor; ?>
        </div>
    </div>
    <div class="review-content">
        <?php echo htmlspecialchars($review['comment']); ?>
    </div>
    <div class="review-date">
        Posted on <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>
