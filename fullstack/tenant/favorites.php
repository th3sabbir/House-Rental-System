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

// Get tenant's favorite properties with full details
try {
    $stmt = $pdo->prepare("
        SELECT 
            p.*,
            f.created_at as favorited_at,
            u.full_name as landlord_name,
            u.phone as landlord_phone,
            (SELECT image_path FROM property_images WHERE property_id = p.property_id AND is_primary = 1 LIMIT 1) as primary_image,
            (SELECT COUNT(*) FROM property_images WHERE property_id = p.property_id) as total_images
        FROM favorites f
        JOIN properties p ON f.property_id = p.property_id
        JOIN users u ON p.landlord_id = u.user_id
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
    <h1><i class="fas fa-heart"></i> Favorite Properties</h1>
    <p class="welcome-message">Properties you've saved for later</p>
</div>

<?php if (empty($favorites)): ?>
<div class="empty-state">
    <div class="empty-state-icon">
        <i class="fas fa-heart-broken"></i>
    </div>
    <h3>No Favorites Yet</h3>
    <p>You haven't added any properties to your favorites. Browse available properties and save the ones you like!</p>
    <a href="../properties.php" class="btn btn-primary">
        <i class="fas fa-search"></i> Browse Properties
    </a>
</div>
<?php else: ?>
<div class="favorites-count">
    <span class="badge badge-primary">
        <i class="fas fa-heart"></i> <?php echo count($favorites); ?> Properties
    </span>
</div>

<div class="property-grid">
    <?php foreach ($favorites as $property): 
        $image = !empty($property['primary_image']) ? '../' . $property['primary_image'] : '../uploads/properties/default-property.jpg';
        $price = number_format($property['price_per_month']);
        $city = ucfirst(strtolower($property['city']));
    ?>
    <div class="property-card" data-property-id="<?php echo $property['property_id']; ?>">
        <div class="property-image-wrapper">
            <button class="favorite-btn active" 
                    onclick="toggleFavorite(<?php echo $property['property_id']; ?>, this)"
                    title="Remove from favorites">
                <i class="fas fa-heart"></i>
            </button>
            
            <?php if ($property['featured']): ?>
                <span class="badge badge-featured">Featured</span>
            <?php endif; ?>

            <span class="badge badge-status badge-<?php echo $property['status']; ?>">
                <?php echo ucfirst($property['status']); ?>
            </span>
            
            <?php if ($property['status'] === 'available'): ?>
            <a href="../property-details.php?id=<?php echo $property['property_id']; ?>">
                <img src="<?php echo htmlspecialchars($image); ?>" 
                     alt="<?php echo htmlspecialchars($property['title']); ?>" 
                     class="property-card-img"
                     onerror="this.src='../uploads/properties/default-property.jpg'">
            </a>
            <?php else: ?>
            <div class="property-image-overlay">
                <img src="<?php echo htmlspecialchars($image); ?>" 
                     alt="<?php echo htmlspecialchars($property['title']); ?>" 
                     class="property-card-img"
                     onerror="this.src='../uploads/properties/default-property.jpg'">
                <div class="rented-overlay">
                    <i class="fas fa-key"></i>
                    <span><?php echo ucfirst($property['status']); ?></span>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="property-card-content">
            <div class="property-card-price">৳<?php echo $price; ?>/month</div>
            
            <h3 class="property-card-title">
                <?php if ($property['status'] === 'available'): ?>
                <a href="../property-details.php?id=<?php echo $property['property_id']; ?>">
                    <?php echo htmlspecialchars($property['title']); ?>
                </a>
                <?php else: ?>
                <span class="rented-title"><?php echo htmlspecialchars($property['title']); ?></span>
                <?php endif; ?>
            </h3>
            
            <p class="property-card-location">
                <i class="fas fa-map-marker-alt"></i> 
                <?php echo htmlspecialchars($property['address'] . ', ' . $city); ?>
            </p>
            
            <div class="property-card-features">
                <span><i class="fas fa-bed"></i> <?php echo $property['bedrooms']; ?> Beds</span>
                <span><i class="fas fa-bath"></i> <?php echo $property['bathrooms']; ?> Baths</span>
                <span><i class="fas fa-ruler-combined"></i> <?php echo $property['area_sqft']; ?> sqft</span>
            </div>
            
            <div class="property-card-footer">
                <div class="favorited-date">
                    <i class="fas fa-clock"></i> 
                    Added <?php echo date('M j, Y', strtotime($property['favorited_at'])); ?>
                </div>
                <div class="property-card-actions">
                    <?php if ($property['status'] === 'available'): ?>
                    <a href="../property-details.php?id=<?php echo $property['property_id']; ?>" 
                       class="btn btn-sm btn-primary">
                        View Details
                    </a>
                    <?php else: ?>
                    <span class="btn btn-sm btn-disabled">
                        <i class="fas fa-key"></i> <?php echo ucfirst($property['status']); ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<style>
.dashboard-header h1 {
    display: flex;
    align-items: center;
    gap: 10px;
}

.dashboard-header h1 i {
    color: #e74c3c;
}

.favorites-count {
    margin-bottom: 1.5rem;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.badge-primary {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.empty-state-icon {
    font-size: 5rem;
    color: #e0e0e0;
    margin-bottom: 1.5rem;
}

.empty-state h3 {
    color: var(--text-dark);
    margin-bottom: 1rem;
}

.empty-state p {
    color: var(--text-medium);
    margin-bottom: 2rem;
    font-size: 1.05rem;
}

.property-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
}

.property-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.property-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.property-image-wrapper {
    position: relative;
    height: 220px;
    overflow: hidden;
}

.property-card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.property-card:hover .property-card-img {
    transform: scale(1.1);
}

.favorite-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    background: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    z-index: 10;
}

.favorite-btn i {
    font-size: 1.2rem;
    color: #ccc;
    transition: all 0.3s ease;
}

.favorite-btn.active i {
    color: #e74c3c;
}

.favorite-btn:hover {
    transform: scale(1.1);
}

.badge-featured {
    position: absolute;
    top: 12px;
    left: 12px;
    background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-status {
    position: absolute;
    bottom: 12px;
    left: 12px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    color: white;
}

.badge-available {
    background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);
}

.badge-rented {
    background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
}

.property-card-content {
    padding: 1.25rem;
}

.property-card-price {
    color: var(--secondary-color);
    font-size: 1.4rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
}

.property-card-title {
    margin: 0 0 0.75rem 0;
    font-size: 1.1rem;
}

.property-card-title a {
    color: var(--text-dark);
    text-decoration: none;
    transition: color 0.3s ease;
}

.property-card-title a:hover {
    color: var(--secondary-color);
}

.property-card-location {
    color: var(--text-medium);
    font-size: 0.9rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 6px;
}

.property-card-features {
    display: flex;
    gap: 1rem;
    padding: 0.75rem 0;
    border-top: 1px solid var(--border-color);
    border-bottom: 1px solid var(--border-color);
    margin-bottom: 1rem;
}

.property-card-features span {
    color: var(--text-medium);
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 4px;
}

.property-card-features i {
    color: var(--secondary-color);
}

.property-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.favorited-date {
    font-size: 0.8rem;
    color: var(--text-medium);
    display: flex;
    align-items: center;
    gap: 4px;
}

.property-image-overlay {
    position: relative;
}

.rented-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    text-align: center;
    gap: 8px;
}

.rented-overlay i {
    font-size: 2rem;
    opacity: 0.8;
}

.rented-title {
    color: var(--text-dark);
    cursor: not-allowed;
    opacity: 0.7;
}

.btn-disabled {
    background: #95a5a6 !important;
    color: white !important;
    cursor: not-allowed !important;
    opacity: 0.7;
}
</style>

<script>
function toggleFavorite(propertyId, button) {
    const card = button.closest('.property-card');
    
    fetch('../api/toggle_favorite.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'property_id=' + propertyId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.action === 'removed') {
                // Remove card with animation
                card.style.transition = 'all 0.3s ease';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.9)';
                
                setTimeout(() => {
                    card.remove();
                    
                    // Check if no favorites left
                    const remainingCards = document.querySelectorAll('.property-card');
                    if (remainingCards.length === 0) {
                        location.reload();
                    } else {
                        // Update count
                        const countBadge = document.querySelector('.favorites-count .badge');
                        if (countBadge) {
                            const currentCount = parseInt(countBadge.textContent.match(/\d+/)[0]);
                            countBadge.innerHTML = '<i class="fas fa-heart"></i> ' + (currentCount - 1) + ' Properties';
                        }
                    }
                }, 300);
                
                showNotification('Removed from favorites', 'success');
            }
        } else {
            showNotification(data.message || 'Failed to update favorites', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#27ae60' : '#e74c3c'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(400px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(400px); opacity: 0; }
    }
`;
document.head.appendChild(style);
</script>
