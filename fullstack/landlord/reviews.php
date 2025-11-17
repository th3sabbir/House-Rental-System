<?php
// Reviews Page - View and manage guest reviews

if (!isset($user_id) || !isset($conn)) {
    die('Invalid access');
}

try {
    // Get all reviews for landlord's properties
    $stmt = $conn->prepare("
        SELECT r.*, p.title as property_title, p.address, u.full_name as guest_name, u.email as guest_email,
               (SELECT image_path FROM property_images WHERE property_id = p.property_id AND is_primary = 1 LIMIT 1) as property_image,
               u.profile_image as guest_image
        FROM reviews r 
        JOIN properties p ON r.property_id = p.property_id 
        JOIN users u ON r.guest_id = u.user_id 
        WHERE p.landlord_id = ? 
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $reviews = $stmt->fetchAll();

    // Get average rating
    $stmt = $conn->prepare("
        SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews
        FROM reviews r
        JOIN properties p ON r.property_id = p.property_id
        WHERE p.landlord_id = ?
    ");
    $stmt->execute([$user_id]);
    $rating_stats = $stmt->fetch();

} catch (PDOException $e) {
    error_log("Reviews data loading error: " . $e->getMessage());
    $reviews = [];
    $rating_stats = null;
}
?>

<div class="dashboard-header">
    <h1>Guest Reviews</h1>
    <p class="welcome-message">Manage and view feedback from your guests.</p>
</div>

<?php if ($rating_stats && $rating_stats['total_reviews'] > 0): ?>
<div class="stats-grid" style="margin-bottom: 30px;">
    <div class="stat-card">
        <div class="stat-number"><?php echo number_format($rating_stats['avg_rating'], 1); ?> <span style="font-size: 0.8em;">★</span></div>
        <div class="stat-label">Average Rating</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo $rating_stats['total_reviews']; ?></div>
        <div class="stat-label">Total Reviews</div>
    </div>
</div>
<?php endif; ?>

<div class="section-header">
    <h2>All Reviews</h2>
</div>

<div class="reviews-container">
    <?php if (empty($reviews)): ?>
    <div style="text-align: center; padding: 40px;">
        <i class="fas fa-star" style="font-size: 2rem; color: #ccc; margin-bottom: 10px;"></i>
        <p style="color: #999; margin: 10px 0;">No reviews yet</p>
    </div>
    <?php else: ?>
    <?php foreach ($reviews as $review): ?>
    <div class="review-card">
        <div class="review-header">
            <div class="reviewer-info">
                <img src="<?php echo $review['guest_image'] ? '../uploads/' . $review['guest_image'] : 'https://ui-avatars.com/api/?name=' . urlencode($review['guest_name']); ?>" 
                     alt="Guest" class="reviewer-avatar">
                <div>
                    <div class="reviewer-name"><?php echo htmlspecialchars($review['guest_name']); ?></div>
                    <small style="color: #999;">
                        <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                        for <?php echo htmlspecialchars($review['property_title']); ?>
                    </small>
                </div>
            </div>
            <div class="review-rating">
                <?php for ($i = 0; $i < 5; $i++): ?>
                <span style="color: <?php echo $i < $review['rating'] ? '#ffc107' : '#ddd'; ?>;">★</span>
                <?php endfor; ?>
                <span style="margin-left: 5px; color: #666;"><?php echo $review['rating']; ?>/5</span>
            </div>
        </div>
        <div class="review-body">
            <p><?php echo htmlspecialchars($review['comment']); ?></p>
        </div>
        <div class="review-footer">
            <button class="action-btn reply" onclick="openReplyModal(<?php echo htmlspecialchars(json_encode($review)); ?>)">
                <i class="fas fa-reply"></i> Reply
            </button>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.review-card {
    background: white;
    border: 1px solid #eee;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.reviewer-info {
    display: flex;
    gap: 10px;
}

.reviewer-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.reviewer-name {
    font-weight: 600;
    color: #333;
}

.review-rating {
    display: flex;
    align-items: center;
    gap: 2px;
}

.review-body {
    margin-bottom: 15px;
    color: #555;
    line-height: 1.6;
}

.review-footer {
    display: flex;
    justify-content: flex-start;
}
</style>
