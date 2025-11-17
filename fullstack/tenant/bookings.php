<?php
// Tours Page - View and manage tenant tours

// Handle variable scoping when included
if (!isset($user_id)) {
    $user_id = $GLOBALS['user_id'] ?? null;
}
if (!isset($pdo)) {
    $pdo = $GLOBALS['pdo'] ?? null;
}

if (empty($user_id) || empty($pdo)) {
    error_log("Tenant tours access error: missing user_id or db connection");
    ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        Tours cannot be displayed. Please log in again.
    </div>
    <?php
    return;
}

// Get tenant's tours
try {
    $stmt = $pdo->prepare("
        SELECT
            b.tour_id,
            b.start_date,
            b.end_date,
            b.total_amount,
            b.status,
            b.message,
            b.created_at,
            p.title as property_title,
            p.address,
            p.main_image as property_main_image,
            pi.image_path as main_image,
            u.full_name as landlord_name,
            u.email as landlord_email,
            u.phone as landlord_phone
        FROM tours b
        JOIN properties p ON b.property_id = p.property_id
        JOIN users u ON p.landlord_id = u.user_id
        LEFT JOIN property_images pi ON pi.property_id = p.property_id 
            AND pi.is_primary = 1
        WHERE b.tenant_id = ?
        ORDER BY b.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $tours = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Tenant tours data error: " . $e->getMessage());
    $tours = [];
}
?>

<div class="dashboard-header">
    <h1>My Tours</h1>
    <p class="welcome-message">Track all your rental requests and tours.</p>
</div>

<div class="section-header">
    <h2>All My Tours</h2>
</div>

<div class="table-container">
    <table class="info-table">
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
            <?php if (empty($tours)): ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 40px;">
                    <i class="fas fa-calendar-times" style="font-size: 2rem; color: #ccc; margin-bottom: 10px;"></i>
                    <p style="color: #999; margin: 10px 0;">No tours yet</p>
                    <a href="../properties.php" class="btn btn-primary" style="margin-top: 10px;">
                        <i class="fas fa-search"></i> Browse Properties
                    </a>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($tours as $tour): ?>
            <tr>
                <td>
                    <div class="property-info-cell">
                        <?php 
                        $propertyImage = '';
                        if (!empty($tour['main_image'])) {
                            // Check if path already contains 'uploads/'
                            if (strpos($tour['main_image'], 'uploads/') === 0) {
                                $propertyImage = '../' . $tour['main_image'];
                            } else {
                                $propertyImage = '../uploads/' . $tour['main_image'];
                            }
                        } elseif (!empty($tour['property_main_image'])) {
                            // Check if path already contains 'uploads/'
                            if (strpos($tour['property_main_image'], 'uploads/') === 0) {
                                $propertyImage = '../' . $tour['property_main_image'];
                            } else {
                                $propertyImage = '../uploads/' . $tour['property_main_image'];
                            }
                        } else {
                            $propertyImage = 'https://via.placeholder.com/60x45?text=No+Image';
                        }
                        ?>
                        <img src="<?php echo htmlspecialchars($propertyImage); ?>" 
                             alt="Property" class="property-thumbnail">
                        <div>
                            <div class="property-name"><?php echo htmlspecialchars($tour['property_title']); ?></div>
                            <small style="color: #999;"><?php echo htmlspecialchars(strlen($tour['address']) > 30 ? substr($tour['address'], 0, 30) . '...' : $tour['address']); ?></small>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="landlord-name"><?php echo htmlspecialchars($tour['landlord_name']); ?></div>
                    <small style="color: #999;"><?php echo htmlspecialchars($tour['landlord_email']); ?></small>
                </td>
                <td>৳<?php echo number_format($tour['total_amount'], 2); ?></td>
                <td><?php echo date('M d, Y', strtotime($tour['created_at'])); ?></td>
                <td>
                    <span class="status-badge status-<?php echo strtolower($tour['status'] ?: 'pending'); ?>" style="
                        padding: 6px 16px;
                        border-radius: 50px;
                        font-size: 0.85rem;
                        font-weight: 600;
                        display: inline-block;
                        <?php 
                        $status = strtolower($tour['status'] ?: 'pending');
                        if ($status === 'confirmed' || $status === 'completed') {
                            echo 'background-color: #d4edda; color: #155724;';
                        } elseif ($status === 'pending') {
                            echo 'background-color: #fff3cd; color: #856404;';
                        } elseif ($status === 'cancelled' || $status === 'rejected') {
                            echo 'background-color: #f8d7da; color: #721c24;';
                        } elseif ($status === 'active') {
                            echo 'background-color: #d4edda; color: #155724;';
                        } else {
                            echo 'background-color: #fff3cd; color: #856404;';
                        }
                        ?>
                    ">
                        <?php 
                        $status = $tour['status'] ?: 'pending';
                        echo ucfirst($status); 
                        ?>
                    </span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn view" onclick="openBookingModal(<?php echo htmlspecialchars(json_encode($tour)); ?>)">
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

<!-- Tour Details Modal -->
<div id="tourModal" class="booking-modal-overlay" style="display: none;">
    <div class="booking-modal">
        <div class="booking-modal-header">
            <h2><i class="fas fa-file-invoice-dollar"></i> Tour Details</h2>
            <button class="booking-modal-close" onclick="closeTourModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="booking-modal-body" id="tourDetails">
            <!-- Tour details will be loaded here -->
        </div>
    </div>
</div>

<style>
/* Property Info Cell */
.property-info-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.property-thumbnail {
    width: 60px;
    height: 45px;
    border-radius: 6px;
    object-fit: cover;
    border: 1px solid #e0e6ed;
    flex-shrink: 0;
}

.property-name {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 2px;
}

.action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.action-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
}

.action-btn.view {
    background: #3498db;
    color: white;
}

.action-btn.view:hover {
    background: #2980b9;
    transform: translateY(-1px);
}

.action-btn.contact {
    background: #1abc9c;
    color: white;
}

.action-btn.contact:hover {
    background: #16a085;
    transform: translateY(-1px);
}

/* Booking Modal Styles */
.booking-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(0.5px);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.booking-modal-overlay.show {
    opacity: 1;
    visibility: visible;
}

.booking-modal {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    max-width: 900px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    transform: scale(0.9) translateY(20px);
    transition: all 0.3s ease;
}

.booking-modal-overlay.show .booking-modal {
    transform: scale(1) translateY(0);
}

.booking-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px 32px;
    border-bottom: 1px solid #e1e8ed;
    background: #fff;
    color: #2c3e50;
    border-radius: 16px 16px 0 0;
}

.booking-modal-header h2 {
    margin: 0;
    font-size: 1.8rem;
    display: flex;
    align-items: center;
    gap: 12px;
}

.booking-modal-header h2 i {
    color: #1abc9c;
}

.booking-modal-close {
    background: #6c757d;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.3s ease;
}

.booking-modal-close:hover {
    background: #5a6268;
}

.booking-modal-body {
    padding: 32px;
}

.booking-section {
    margin-bottom: 32px;
    background: #f8f9ff;
    border-radius: 12px;
    padding: 24px;
    border-left: 4px solid #1abc9c;
}

.booking-section h3 {
    font-size: 1.3rem;
    color: #333;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.booking-section h3 i {
    color: #1abc9c;
}

.tenant-profile-card {
    display: flex;
    align-items: center;
    gap: 20px;
    background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #e1e8ed;
}

.tenant-profile-img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 3px solid #1abc9c;
    object-fit: cover;
}

.tenant-profile-info h4 {
    margin: 0 0 8px 0;
    font-size: 1.4rem;
    color: #333;
}

.tenant-profile-info p {
    margin: 6px 0;
    color: #666;
    display: flex;
    align-items: center;
    gap: 8px;
}

.tenant-profile-info i {
    color: #1abc9c;
    width: 16px;
}

.property-card {
    display: flex;
    gap: 20px;
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #e1e8ed;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.property-card-img {
    width: 150px;
    height: 100px;
    border-radius: 8px;
    object-fit: cover;
    flex-shrink: 0;
}

.property-card-info h4 {
    margin: 0 0 8px 0;
    font-size: 1.3rem;
    color: #333;
}

.property-card-info p {
    margin: 6px 0;
    color: #666;
    display: flex;
    align-items: center;
    gap: 8px;
}

.booking-details-layout {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.booking-details-primary {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.booking-details-secondary {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.booking-detail-card {
    background: #fff;
    border: 1px solid #e1e8ed;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
}

.booking-detail-card-large {
    padding: 24px;
    gap: 20px;
}

.booking-detail-card-large .booking-detail-icon {
    width: 60px;
    height: 60px;
    font-size: 1.5rem;
}

.booking-detail-card-large .booking-detail-value {
    font-size: 1.5rem;
}

.booking-detail-icon {
    width: 50px;
    height: 50px;
    background: #f8f9fa;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1abc9c;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.booking-detail-content {
    flex: 1;
    min-width: 0;
}

.booking-detail-label {
    font-size: 0.85rem;
    color: #7f8c8d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.booking-detail-value {
    font-size: 1.2rem;
    color: #2c3e50;
    font-weight: 500;
    line-height: 1.3;
    word-break: break-word;
}

.booking-detail-card-large .booking-detail-value {
    font-size: 1.6rem;
    color: #2c3e50;
}

.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-confirmed { background: #d4edda; color: #155724; }
.status-rejected { background: #f8d7da; color: #721c24; }

.booking-actions {
    display: flex;
    gap: 16px;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #e1e8ed;
    justify-content: center;
}

.booking-btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 140px;
    justify-content: center;
}

.booking-btn-contact {
    background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);
    color: white;
}

.booking-btn-contact:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(26, 188, 156, 0.4);
}

.booking-btn-close {
    background: #9e9e9e;
    color: white;
}

.booking-btn-close:hover {
    background: #757575;
}

.loading-state {
    text-align: center;
    padding: 40px;
}

.loading-state i {
    font-size: 3rem;
    color: #1abc9c;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-state p {
    margin-top: 20px;
    color: #666;
}

@media (max-width: 768px) {
    .booking-modal {
        width: 95%;
        margin: 20px;
    }

    .booking-modal-header {
        padding: 20px;
    }

    .booking-modal-body {
        padding: 20px;
    }

    .tenant-profile-card {
        flex-direction: column;
        text-align: center;
    }

    .property-card {
        flex-direction: column;
    }

    .property-card-img {
        width: 100%;
        height: 200px;
    }

    .booking-actions {
        flex-direction: column;
    }

    .booking-btn {
        width: 100%;
    }
}
</style>

<script>
function openBookingModal(tour) {
    const modal = document.getElementById('tourModal');
    const detailsContainer = document.getElementById('tourDetails');
    
    // Format dates
    const createdDate = new Date(tour.created_at).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    // Get status badge class
    const statusClass = 'status-' + (tour.status || 'pending').toLowerCase();
    
    // Landlord image
    const landlordImage = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(tour.landlord_name);
    
    // Property image - fix the path with better fallback handling
    let propertyImage;
    if (tour.main_image) {
        // Image from property_images table
        if (tour.main_image.includes('uploads/')) {
            propertyImage = '../' + tour.main_image;
        } else {
            propertyImage = '../uploads/properties/' + tour.main_image;
        }
    } else if (tour.property_main_image) {
        // Fallback to main image from properties table
        if (tour.property_main_image.includes('uploads/')) {
            propertyImage = '../' + tour.property_main_image;
        } else {
            propertyImage = '../uploads/properties/' + tour.property_main_image;
        }
    } else {
        propertyImage = 'https://via.placeholder.com/400x300?text=No+Image';
    }
    
    console.log('Property Image Path:', propertyImage); // Debug log
    console.log('Tour data:', tour); // Debug log
    
    let actionsHtml = '';
    if ((tour.status || 'pending') === 'confirmed' || (tour.status || 'pending') === 'completed') {
        actionsHtml = `
            <div class="booking-actions">
                <button class="booking-btn booking-btn-contact" onclick="contactLandlord('${tour.landlord_email}', '${tour.landlord_name}')">
                    <i class="fas fa-envelope"></i> Contact Landlord
                </button>
            </div>
        `;
    } else {
        actionsHtml = `
            <div class="booking-actions">
                <button class="booking-btn booking-btn-close" onclick="closeTourModal()">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        `;
    }
    
    detailsContainer.innerHTML = `
        <div class="booking-section">
            <h3><i class="fas fa-user-circle"></i> Landlord Information</h3>
            <div class="tenant-profile-card">
                <img src="${landlordImage}" alt="${tour.landlord_name}" class="tenant-profile-img">
                <div class="tenant-profile-info">
                    <h4>${tour.landlord_name}</h4>
                    <p><i class="fas fa-envelope"></i> ${tour.landlord_email}</p>
                    <p><i class="fas fa-phone"></i> ${tour.landlord_phone || 'Not provided'}</p>
                </div>
            </div>
        </div>

        <div class="booking-section">
            <h3><i class="fas fa-home"></i> Property Information</h3>
            <div class="property-card">
                <img src="${propertyImage}" alt="${tour.property_title}" class="property-card-img">
                <div class="property-card-info">
                    <h4>${tour.property_title}</h4>
                    <p><i class="fas fa-map-marker-alt"></i> ${tour.address}</p>
                </div>
            </div>
        </div>

        <div class="booking-section">
            <h3><i class="fas fa-info-circle"></i> Tour Details</h3>
            <div class="booking-details-layout">
                <div class="booking-details-primary">
                    <div class="booking-detail-card booking-detail-card-large">
                        <div class="booking-detail-icon">
                            <i class="fas fa-hashtag"></i>
                        </div>
                        <div class="booking-detail-content">
                            <div class="booking-detail-label">Tour ID</div>
                            <div class="booking-detail-value">#${tour.tour_id}</div>
                        </div>
                    </div>
                    <div class="booking-detail-card booking-detail-card-large">
                        <div class="booking-detail-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="booking-detail-content">
                            <div class="booking-detail-label">Total Amount</div>
                            <div class="booking-detail-value">৳${parseFloat(tour.total_amount).toFixed(2)}</div>
                        </div>
                    </div>
                </div>
                <div class="booking-details-secondary">
                    <div class="booking-detail-card">
                        <div class="booking-detail-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="booking-detail-content">
                            <div class="booking-detail-label">Status</div>
                            <div class="booking-detail-value">
                                <span class="status-badge ${statusClass}">${(tour.status || 'pending').charAt(0).toUpperCase() + (tour.status || 'pending').slice(1)}</span>
                            </div>
                        </div>
                    </div>
                    <div class="booking-detail-card">
                        <div class="booking-detail-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="booking-detail-content">
                            <div class="booking-detail-label">Created Date</div>
                            <div class="booking-detail-value">${createdDate}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        ${actionsHtml}
    `;
    
    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('show'), 10);
    document.body.style.overflow = 'hidden';
}

function closeTourModal() {
    const modal = document.getElementById('tourModal');
    modal.classList.remove('show');
    document.body.style.overflow = 'auto';
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

function contactLandlord(email, name) {
    // Close the tour modal first
    closeTourModal();
    
    // Redirect to messages page with contact parameters
    window.location.href = '../messages.php?contact=' + encodeURIComponent(email) + '&name=' + encodeURIComponent(name);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('tourModal');
    if (event.target === modal) {
        closeTourModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeTourModal();
    }
});
</script>
