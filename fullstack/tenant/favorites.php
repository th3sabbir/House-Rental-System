<?php
// Favorites Page - Tenant's favorite properties

// Handle variable scoping when included
if (!isset($user_id)) {
    $user_id = $GLOBALS['user_id'] ?? null;
}
if (!isset($pdo)) {
    $pdo = $GLOBALS['pdo'] ?? null;
}

if (empty($user_id) || empty($pdo)) {
    error_log("Tenant favorites access error: missing user_id or db connection");
    ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        Favorites cannot be displayed. Please log in again.
    </div>
    <?php
    return;
}

// Get tenant's favorite properties
try {
    $stmt = $pdo->prepare("
        SELECT 
            p.*,
            (SELECT image_path FROM property_images WHERE property_id = p.property_id AND is_primary = 1 LIMIT 1) as main_image,
            (SELECT AVG(rating) FROM reviews WHERE property_id = p.property_id) as rating,
            f.created_at as favorited_at
        FROM favorites f
        JOIN properties p ON f.property_id = p.property_id
        WHERE f.user_id = ?
        ORDER BY f.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $favorites = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Tenant favorites data error: " . $e->getMessage());
    $favorites = [];
}
?>

<div class="dashboard-header">
    <h1>Favorite Properties</h1>
    <p class="welcome-message">Properties you've saved for later</p>
</div>

<?php if (empty($favorites)): ?>
<div class="empty-state">
    <i class="fas fa-heart-broken"></i>
    <h3>No Favorites Yet</h3>
    <p>You haven't added any properties to your favorites. Browse available properties and save the ones you like!</p>
    <a href="../properties.php" class="btn btn-primary">
        <i class="fas fa-search"></i> Browse Properties
    </a>
</div>
<?php else: ?>
<div class="property-grid">
    <?php foreach ($favorites as $property): ?>
    <div class="property-card">
        <button class="favorite-btn" onclick="removeFavorite(<?php echo $property['property_id']; ?>)">
            <i class="fas fa-heart"></i>
        </button>
        <img src="<?php echo $property['main_image'] ? '../uploads/' . htmlspecialchars($property['main_image']) : 'https://via.placeholder.com/400x220?text=No+Image'; ?>" 
             alt="<?php echo htmlspecialchars($property['title']); ?>" class="property-card-img">
        <div class="property-card-content">
            <h3 class="property-card-title"><?php echo htmlspecialchars($property['title']); ?></h3>
            <p class="property-card-location">
                <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($property['city']); ?>
            </p>
            <div class="property-card-price">
                $<?php echo number_format($property['price_per_month']); ?>/mo
            </div>
            <div class="property-card-features">
                <span><i class="fas fa-bed"></i> <?php echo $property['bedrooms']; ?> Beds</span>
                <span><i class="fas fa-bath"></i> <?php echo $property['bathrooms']; ?> Baths</span>
                <span><i class="fas fa-star"></i> <?php echo $property['rating'] ? number_format($property['rating'], 1) : 'N/A'; ?></span>
            </div>
            <div class="property-card-actions">
                <button class="btn btn-secondary" onclick="viewProperty(<?php echo $property['property_id']; ?>)">
                    <i class="fas fa-eye"></i> View
                </button>
                <button class="btn btn-primary" onclick="bookProperty(<?php echo $property['property_id']; ?>)">
                    <i class="fas fa-calendar-check"></i> Book
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
