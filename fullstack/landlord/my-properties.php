<?php
// My Properties Page - View and manage all properties

if (!isset($user_id) || !isset($conn)) {
    die('Invalid access');
}

// Handle status filter
$status_filter = $_GET['filter'] ?? 'all';
$valid_filters = ['all', 'available', 'rented'];
if (!in_array($status_filter, $valid_filters)) {
    $status_filter = 'all';
}

try {
    // Build query based on filter
    $query = "
        SELECT p.*, 
               (SELECT image_path FROM property_images WHERE property_id = p.property_id AND is_primary = 1 LIMIT 1) as main_image,
               p.main_image as property_main_image,
               (SELECT COUNT(*) FROM tours WHERE property_id = p.property_id) as tour_count,
               (SELECT COUNT(*) FROM tours WHERE property_id = p.property_id AND status = 'pending') as pending_count,
               (SELECT AVG(rating) FROM reviews WHERE property_id = p.property_id) as avg_rating,
               (SELECT COUNT(*) FROM reviews WHERE property_id = p.property_id) as review_count
        FROM properties p 
        WHERE p.landlord_id = ?
    ";
    
    if ($status_filter !== 'all') {
        $query .= " AND p.status = ?";
    }
    
    $query .= " ORDER BY p.created_at DESC";
    
    error_log("MY-PROPERTIES DEBUG - About to execute query with user_id: $user_id, filter: $status_filter");
    
    $stmt = $conn->prepare($query);
    if ($status_filter !== 'all') {
        $stmt->execute([$user_id, $status_filter]);
    } else {
        $stmt->execute([$user_id]);
    }
    $properties = $stmt->fetchAll();

    // DEBUG: Log the query results
    error_log("MY-PROPERTIES DEBUG - Query executed. Found " . count($properties) . " properties for landlord_id: " . $user_id);
    if (count($properties) > 0) {
        error_log("MY-PROPERTIES DEBUG - First property: " . json_encode($properties[0]));
        error_log("MY-PROPERTIES DEBUG - All property IDs: " . implode(', ', array_column($properties, 'property_id')));
    } else {
        error_log("MY-PROPERTIES DEBUG - No properties found. Check if landlord_id matches user_id in session.");
        // Let's also check what properties DO exist
        $check_stmt = $conn->prepare("SELECT COUNT(*) as total FROM properties");
        $check_stmt->execute();
        $total_props = $check_stmt->fetch()['total'];
        error_log("MY-PROPERTIES DEBUG - Total properties in database: $total_props");

        $check_stmt = $conn->prepare("SELECT landlord_id, COUNT(*) as count FROM properties GROUP BY landlord_id");
        $check_stmt->execute();
        $landlord_counts = $check_stmt->fetchAll();
        error_log("MY-PROPERTIES DEBUG - Properties by landlord: " . json_encode($landlord_counts));
    }

    // Get stats
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM properties WHERE landlord_id = ?");
    $stmt->execute([$user_id]);
    $total_properties = $stmt->fetch()['count'];

    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM properties WHERE landlord_id = ? AND status = 'available'");
    $stmt->execute([$user_id]);
    $available_count = $stmt->fetch()['count'];

    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM properties WHERE landlord_id = ? AND status = 'rented'");
    $stmt->execute([$user_id]);
    $rented_count = $stmt->fetch()['count'];

} catch (PDOException $e) {
    error_log("MY-PROPERTIES DEBUG - PDO Exception: " . $e->getMessage());
    error_log("MY-PROPERTIES DEBUG - Exception trace: " . $e->getTraceAsString());
    $properties = [];
    $total_properties = 0;
    $available_count = 0;
    $rented_count = 0;
}
?>

<div class="dashboard-header">
    <div>
        <h1><i class="fas fa-building"></i> My Properties</h1>
        <p class="welcome-message">Manage all your property listings in one place.</p>
    </div>
    <div>
        <a href="?page=add-property" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Property
        </a>
    </div>
</div>

<!-- Property Stats -->
<div class="property-stats-row">
    <div class="stat-mini">
        <i class="fas fa-home"></i>
        <div>
            <div class="stat-mini-value"><?php echo $total_properties; ?></div>
            <div class="stat-mini-label">Total Properties</div>
        </div>
    </div>
    <div class="stat-mini">
        <i class="fas fa-check-circle" style="color: #27ae60;"></i>
        <div>
            <div class="stat-mini-value"><?php echo $available_count; ?></div>
            <div class="stat-mini-label">Available</div>
        </div>
    </div>
    <div class="stat-mini">
        <i class="fas fa-key" style="color: #e67e22;"></i>
        <div>
            <div class="stat-mini-value"><?php echo $rented_count; ?></div>
            <div class="stat-mini-label">Rented</div>
        </div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="filter-tabs">
    <a href="?page=my-properties&filter=all" class="filter-tab <?php echo $status_filter === 'all' ? 'active' : ''; ?>">
        <i class="fas fa-list"></i> All Properties (<?php echo $total_properties; ?>)
    </a>
    <a href="?page=my-properties&filter=available" class="filter-tab <?php echo $status_filter === 'available' ? 'active' : ''; ?>">
        <i class="fas fa-check-circle"></i> Available (<?php echo $available_count; ?>)
    </a>
    <a href="?page=my-properties&filter=rented" class="filter-tab <?php echo $status_filter === 'rented' ? 'active' : ''; ?>">
        <i class="fas fa-key"></i> Rented (<?php echo $rented_count; ?>)
        <?php if ($status_filter === 'rented'): ?>
        <small style="display: block; font-size: 0.7rem; opacity: 0.8; margin-top: 2px;">Hidden from public listings</small>
        <?php endif; ?>
    </a>
</div>

<!-- Properties Grid -->
<?php if (empty($properties)): ?>
<div class="empty-state">
    <i class="fas fa-building"></i>
    <h3>No Properties Found</h3>
    <p>
        <?php if ($status_filter !== 'all'): ?>
            No <?php echo $status_filter; ?> properties found. Try a different filter.
        <?php else: ?>
            You haven't added any properties yet. Start by adding your first property!
        <?php endif; ?>
    </p>
    

    
    <a href="?page=add-property" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Your First Property
    </a>
</div>
<?php else: ?>
<div class="dashboard-grid">
    <?php foreach ($properties as $property): 
        // Determine property image
        $property_image = '';
        if ($property['main_image']) {
            // main_image comes from property_images table and already contains full path
            if (strpos($property['main_image'], 'uploads/') === 0) {
                $property_image = '../' . $property['main_image'];
            } else {
                $property_image = '../uploads/' . $property['main_image'];
            }
        } elseif ($property['property_main_image']) {
            // property_main_image comes from properties.main_image column
            if (strpos($property['property_main_image'], 'uploads/') !== false) {
                $property_image = '../' . $property['property_main_image'];
            } elseif (strpos($property['property_main_image'], 'http') === 0) {
                // External URL (like Pexels images)
                $property_image = $property['property_main_image'];
            } else {
                $property_image = '../uploads/properties/' . $property['property_main_image'];
            }
        } else {
            $property_image = 'https://via.placeholder.com/400x300?text=No+Image';
        }
    ?>
    <div class="property-card">
        <div class="card-image">
            <img src="<?php echo htmlspecialchars($property_image); ?>" 
                 alt="<?php echo htmlspecialchars($property['title']); ?>"
                 onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'">
            <span class="card-badge <?php echo $property['status'] === 'available' ? 'active' : ($property['status'] === 'rented' ? 'rented' : 'inactive'); ?>">
                <?php echo ucfirst($property['status']); ?>
            </span>
            <?php if ($property['pending_count'] > 0): ?>
            <span class="card-notification">
                <i class="fas fa-bell"></i> <?php echo $property['pending_count']; ?> Pending
            </span>
            <?php endif; ?>
        </div>
        <div class="card-content">
            <h3><?php echo htmlspecialchars($property['title']); ?></h3>
            <p class="property-address">
                <i class="fas fa-map-marker-alt"></i> 
                <?php echo htmlspecialchars(strlen($property['address']) > 40 ? substr($property['address'], 0, 40) . '...' : $property['address']); ?>
            </p>
            
            <div class="property-meta">
                <div class="meta-item">
                    <i class="fas fa-bed"></i> <?php echo $property['bedrooms']; ?> Beds
                </div>
                <div class="meta-item">
                    <i class="fas fa-bath"></i> <?php echo $property['bathrooms']; ?> Baths
                </div>
                <div class="meta-item">
                    <i class="fas fa-ruler-combined"></i> <?php echo number_format($property['area_sqft']); ?> sqft
                </div>
            </div>

            <div class="property-price">
                <span class="price-label">Price:</span>
                <span class="price-value">৳<?php echo number_format($property['price_per_month']); ?>/month</span>
            </div>

            <div class="property-stats-mini">
                <div class="stat-item">
                    <i class="fas fa-calendar-check"></i>
                    <span><?php echo $property['tour_count']; ?> Tours</span>
                </div>
                <?php if ($property['avg_rating']): ?>
                <div class="stat-item">
                    <i class="fas fa-star"></i>
                    <span><?php echo number_format($property['avg_rating'], 1); ?> Rating</span>
                </div>
                <?php endif; ?>
            </div>

            <div class="card-actions">
                <button class="action-btn edit" onclick="editProperty(<?php echo $property['property_id']; ?>)">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="action-btn view" onclick="viewProperty(<?php echo $property['property_id']; ?>)">
                    <i class="fas fa-eye"></i> View
                </button>
                <?php if ($property['status'] !== 'rented'): ?>
                <button class="action-btn rented" onclick="markAsRented(<?php echo $property['property_id']; ?>, '<?php echo htmlspecialchars(addslashes($property['title'])); ?>')">
                    <i class="fas fa-key"></i> Mark Rented
                </button>
                <?php endif; ?>
                <button class="action-btn delete" onclick="deleteProperty(<?php echo $property['property_id']; ?>, '<?php echo htmlspecialchars(addslashes($property['title'])); ?>')">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<style>
/* Property Stats Row */
.property-stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-mini {
    background: white;
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #e0e6ed;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.stat-mini:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.stat-mini i {
    font-size: 2rem;
    color: #1abc9c;
}

.stat-mini-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: #2c3e50;
}

.stat-mini-label {
    font-size: 0.9rem;
    color: #7f8c8d;
}

/* Filter Tabs */
.filter-tabs {
    display: flex;
    gap: 12px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.filter-tab {
    padding: 12px 24px;
    background: white;
    border: 2px solid #e0e6ed;
    border-radius: 8px;
    text-decoration: none;
    color: #2c3e50;
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-tab:hover {
    background: #f8f9fa;
    border-color: #1abc9c;
}

.filter-tab.active {
    background: #1abc9c;
    color: white;
    border-color: #1abc9c;
}

.filter-tab i {
    font-size: 1rem;
}

/* Property Card Enhancements */
.property-address {
    color: #7f8c8d;
    font-size: 0.9rem;
    margin: 8px 0 12px;
    display: flex;
    align-items: flex-start;
    gap: 6px;
}

.property-address i {
    margin-top: 2px;
    color: #1abc9c;
}

.property-meta {
    display: flex;
    gap: 12px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 0.85rem;
    color: #7f8c8d;
    background: #f8f9fa;
    padding: 4px 10px;
    border-radius: 6px;
}

.meta-item i {
    color: #1abc9c;
}

.property-price {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: linear-gradient(135deg, #f0fdf4 0%, #f8f9fa 100%);
    border-radius: 8px;
    margin-bottom: 12px;
    border-left: 4px solid #1abc9c;
}

.price-label {
    font-size: 0.85rem;
    color: #7f8c8d;
    font-weight: 600;
}

.price-value {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1abc9c;
}

.property-stats-mini {
    display: flex;
    gap: 16px;
    margin-bottom: 12px;
    padding: 8px 0;
    border-top: 1px solid #e0e6ed;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.85rem;
    color: #7f8c8d;
}

.stat-item i {
    color: #1abc9c;
}

.card-notification {
    position: absolute;
    top: 12px;
    right: 12px;
    background: #e74c3c;
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 4px;
}

.card-badge.rented {
    background: rgba(230, 126, 34, 0.95);
    color: white;
}

.action-btn.edit {
    background: #3498db;
}

.action-btn.edit:hover {
    background: #2980b9;
}

.action-btn.rented {
    background: #e67e22;
}

.action-btn.rented:hover {
    background: #d35400;
}

.action-btn.delete {
    background: #e74c3c;
    padding: 8px 12px;
}

.action-btn.delete:hover {
    background: #c0392b;
}

@media (max-width: 768px) {
    .property-stats-row {
        grid-template-columns: 1fr;
    }

    .filter-tabs {
        flex-direction: column;
    }

    .filter-tab {
        justify-content: center;
    }

    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>

<script>
function viewProperty(propertyId) {
    // Redirect to property details page on the main site
    window.open('../property-details.php?id=' + propertyId, '_blank');
}

function editProperty(propertyId) {
    // Redirect to edit property page
    console.log('Redirecting to edit property:', propertyId);
    window.location.href = '?page=edit-property&id=' + propertyId;
}

function deleteProperty(propertyId, propertyTitle) {
    if (!confirm(`Are you sure you want to delete "${propertyTitle}"?\n\nThis action cannot be undone and will also delete all associated tours and reviews.`)) {
        return;
    }
    
    // Show loading
    const confirmDelete = confirm('This is a permanent action. Click OK to confirm deletion.');
    if (!confirmDelete) {
        return;
    }
    
    // Send delete request
    fetch('../api/delete_property.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            property_id: propertyId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Property deleted successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to delete property'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the property');
    });
}

function markAsRented(propertyId, propertyTitle) {
    if (!confirm(`Are you sure you want to mark "${propertyTitle}" as rented?\n\nThis will remove it from public listings but keep it visible in your "Rented" filter.`)) {
        return;
    }
    
    // Send mark as rented request
    const formData = new FormData();
    formData.append('property_id', propertyId);
    formData.append('status', 'rented');
    
    fetch('../api/update_property_status.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Property marked as rented successfully! It has been removed from public listings.');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to update property status'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the property status');
    });
}
</script>
