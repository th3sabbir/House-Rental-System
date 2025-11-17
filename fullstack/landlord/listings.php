<?php
// Listings Page - Manage all properties

// Handle variable scoping when included
if (!isset($user_id)) {
    $user_id = $GLOBALS['user_id'] ?? null;
}
if (!isset($conn)) {
    $conn = $GLOBALS['conn'] ?? null;
}

if (empty($user_id) || empty($conn)) {
    error_log("Listings access error: missing user_id or db connection");
    ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        Properties cannot be displayed. Please log in again.
    </div>
    <?php
    return;
}

try {
    // Get all properties for this landlord
    $stmt = $conn->prepare("
        SELECT p.*, 
               (SELECT image_path FROM property_images WHERE property_id = p.property_id AND is_primary = 1 LIMIT 1) as main_image,
               (SELECT COUNT(*) FROM tours WHERE property_id = p.property_id) as tour_count,
               (SELECT AVG(rating) FROM reviews WHERE property_id = p.property_id) as rating
        FROM properties p 
        WHERE p.landlord_id = ? 
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $properties = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Listings data loading error: " . $e->getMessage());
    $properties = [];
}
?>

<div class="dashboard-header">
    <h1>My Listings</h1>
    <p class="welcome-message">Manage all your property listings here.</p>
</div>

<div class="section-header">
    <h2>All Properties</h2>
    <button class="btn btn-primary" onclick="openAddListingModal()">
        <i class="fas fa-plus"></i> Add New Listing
    </button>
</div>

<div class="dashboard-grid">
    <?php if (empty($properties)): ?>
    <div class="empty-state">
        <i class="fas fa-building"></i>
        <h3>No Properties Yet</h3>
        <p>You haven't added any properties yet. Start by adding your first listing!</p>
        <button class="btn btn-primary" onclick="openAddListingModal()">
            <i class="fas fa-plus"></i> Add Your First Property
        </button>
    </div>
    <?php else: ?>
    <?php foreach ($properties as $property): ?>
    <div class="property-card">
        <div class="card-image">
            <img src="<?php echo $property['main_image'] ? '../uploads/' . $property['main_image'] : 'https://via.placeholder.com/400x250?text=No+Image'; ?>" 
                 alt="<?php echo htmlspecialchars($property['title']); ?>">
            <span class="card-badge <?php echo $property['status'] === 'available' ? 'active' : 'inactive'; ?>">
                <?php echo ucfirst($property['status']); ?>
            </span>
        </div>
        <div class="card-content">
            <h3><?php echo htmlspecialchars($property['title']); ?></h3>
            <div class="card-stats">
                <span class="card-stat">
                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars(substr($property['address'], 0, 30)); ?>...
                </span>
                <span class="card-stat">
                    <i class="fas fa-calendar-check"></i> <?php echo $property['tour_count']; ?> Tours
                </span>
            </div>
            <div class="card-stats">
                <span class="card-stat">
                    <i class="fas fa-star"></i> <?php echo $property['rating'] ? number_format($property['rating'], 1) : 'N/A'; ?>
                </span>
                <span class="card-stat">
                    <i class="fas fa-dollar-sign"></i> $<?php echo number_format($property['price_per_month']); ?>/mo
                </span>
            </div>
            <div class="card-actions">
                <button class="btn btn-secondary" onclick="editProperty(<?php echo htmlspecialchars(json_encode($property)); ?>)">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-primary" onclick="viewProperty(<?php echo htmlspecialchars(json_encode($property)); ?>)">
                    <i class="fas fa-eye"></i> View
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>
