<?php
// Tours Page - View and manage tours

$user_id = $GLOBALS['user_id'] ?? null;
$conn = $GLOBALS['conn'] ?? null;

if (!$user_id || !$conn) {
    die('Invalid access - user_id: ' . ($user_id ? 'set' : 'not set') . ', conn: ' . ($conn ? 'set' : 'not set'));
}

try {
    // Get recent tours
    $stmt = $conn->prepare("
        SELECT b.*, p.title as property_title, p.address, p.main_image as property_main_image,
               u.full_name as tenant_name, u.email as tenant_email, u.phone as tenant_phone,
               (SELECT image_path FROM property_images WHERE property_id = p.property_id AND is_primary = 1 LIMIT 1) as property_image,
               u.profile_image as tenant_image
        FROM tours b 
        JOIN properties p ON b.property_id = p.property_id 
        JOIN users u ON b.tenant_id = u.user_id 
        WHERE p.landlord_id = ? 
        ORDER BY b.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $tours = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Tours data loading error: " . $e->getMessage());
    $tours = [];
}
?>

<div class="dashboard-header">
    <h1>Tour Requests</h1>
    <p class="welcome-message">View and manage all rental tour requests.</p>
</div>

<div class="section-header">
    <h2>All Tour Requests</h2>
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
            <?php if (empty($tours)): ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 40px;">
                    <i class="fas fa-inbox" style="font-size: 2rem; color: #ccc; margin-bottom: 10px;"></i>
                    <p style="color: #999; margin: 10px 0;">No tours yet</p>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($tours as $tour): ?>
            <tr>
                <td>
                    <div class="tenant-info">
                        <img src="<?php echo $tour['tenant_image'] ? '../uploads/' . $tour['tenant_image'] : 'https://ui-avatars.com/api/?name=' . urlencode($tour['tenant_name']); ?>" 
                             alt="Tenant" class="tenant-avatar">
                        <div>
                            <div class="tenant-name"><?php echo htmlspecialchars($tour['tenant_name']); ?></div>
                            <small style="color: #999;"><?php echo htmlspecialchars($tour['tenant_email']); ?></small>
                        </div>
                    </div>
                </td>
                <td><?php echo htmlspecialchars($tour['property_title']); ?></td>
                <td>৳<?php echo number_format($tour['total_amount'], 2); ?></td>
                <td><?php echo date('M d, Y', strtotime($tour['created_at'])); ?></td>
                <td>
                    <span class="status-badge status-<?php echo strtolower($tour['status'] ?: 'pending'); ?>">
                        <?php 
                        $status = $tour['status'] ?: 'pending';
                        echo ucfirst($status); 
                        ?>
                    </span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn view" onclick="openTourModal(<?php echo htmlspecialchars(json_encode($tour)); ?>)">
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
<div id="tourModal" class="tour-modal-overlay" style="display: none;">
    <div class="tour-modal">
        <div class="tour-modal-header">
            <h2><i class="fas fa-file-invoice-dollar"></i> Tour Details</h2>
            <button class="tour-modal-close" onclick="closeTourModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="tour-modal-body" id="tourDetails">
            <!-- Tour details will be loaded here -->
        </div>
    </div>
</div>

<style>
/* Tour Modal Styles */
.tour-modal-overlay {
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

.tour-modal-overlay.show {
    opacity: 1;
    visibility: visible;
}

.tour-modal {
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

.tour-modal-overlay.show .tour-modal {
    transform: scale(1) translateY(0);
}

.tour-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px 32px;
    border-bottom: 1px solid #e1e8ed;
    background: #fff;
    color: #2c3e50;
    border-radius: 16px 16px 0 0;
}

.tour-modal-header h2 {
    margin: 0;
    font-size: 1.8rem;
    display: flex;
    align-items: center;
    gap: 12px;
}

.tour-modal-header h2 i {
    color: #1abc9c;
}

.tour-modal-close {
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

.tour-modal-close:hover {
    background: #5a6268;
}

.tour-modal-body {
    padding: 32px;
}

.tour-section {
    margin-bottom: 32px;
    background: #f8f9ff;
    border-radius: 12px;
    padding: 24px;
    border-left: 4px solid #1abc9c;
}

.tour-section h3 {
    font-size: 1.3rem;
    color: #333;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.tour-section h3 i {
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

.tour-details-layout {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.tour-details-primary {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.tour-details-secondary {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.tour-detail-card {
    background: #fff;
    border: 1px solid #e1e8ed;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
}

.tour-detail-card-large {
    padding: 24px;
    gap: 20px;
}

.tour-detail-card-large .tour-detail-icon {
    width: 60px;
    height: 60px;
    font-size: 1.5rem;
}

.tour-detail-card-large .tour-detail-value {
    font-size: 1.5rem;
}

.tour-detail-icon {
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

.tour-detail-content {
    flex: 1;
    min-width: 0;
}

.tour-detail-label {
    font-size: 0.85rem;
    color: #7f8c8d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.tour-detail-value {
    font-size: 1.2rem;
    color: #2c3e50;
    font-weight: 500;
    line-height: 1.3;
    word-break: break-word;
}

.tour-detail-card-large .tour-detail-value {
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

.tour-actions {
    display: flex;
    gap: 16px;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #e1e8ed;
    justify-content: center;
}

.tour-btn {
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

.tour-btn-accept {
    background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);
    color: white;
}

.tour-btn-accept:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(26, 188, 156, 0.4);
}

.tour-btn-reject {
    background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
    color: white;
}

.tour-btn-reject:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(244, 67, 54, 0.4);
}

.tour-btn-close {
    background: #9e9e9e;
    color: white;
}

.tour-btn-close:hover {
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
    .tour-modal {
        width: 95%;
        margin: 20px;
    }

    .tour-modal-header {
        padding: 20px;
    }

    .tour-modal-body {
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

    .tour-actions {
        flex-direction: column;
    }

    .tour-btn {
        width: 100%;
    }
}
</style>

<script>
function openTourModal(tour) {
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
    
    // Tenant image
    const tenantImage = tour.tenant_image 
        ? '../uploads/' + tour.tenant_image 
        : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(tour.tenant_name);
    
    // Property image - fix the path with better fallback handling
    let propertyImage;
    if (tour.property_image) {
        // Image from property_images table
        if (tour.property_image.includes('uploads/')) {
            propertyImage = '../' + tour.property_image;
        } else {
            propertyImage = '../uploads/properties/' + tour.property_image;
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
    if ((tour.status || 'pending') === 'pending') {
        actionsHtml = `
            <div class="tour-actions">
                <button class="tour-btn tour-btn-accept" onclick="updateTourStatus(${tour.tour_id}, 'confirmed')">
                    <i class="fas fa-check-circle"></i> Accept Tour
                </button>
                <button class="tour-btn tour-btn-reject" onclick="updateTourStatus(${tour.tour_id}, 'rejected')">
                    <i class="fas fa-times-circle"></i> Reject Tour
                </button>
            </div>
        `;
    } else {
        actionsHtml = `
            <div class="tour-actions">
                <button class="tour-btn tour-btn-close" onclick="closeTourModal()">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        `;
    }
    
    detailsContainer.innerHTML = `
        <div class="tour-section">
            <h3><i class="fas fa-user-circle"></i> Tenant Information</h3>
            <div class="tenant-profile-card">
                <img src="${tenantImage}" alt="${tour.tenant_name}" class="tenant-profile-img">
                <div class="tenant-profile-info">
                    <h4>${tour.tenant_name}</h4>
                    <p><i class="fas fa-envelope"></i> ${tour.tenant_email}</p>
                    <p><i class="fas fa-phone"></i> ${tour.tenant_phone || 'Not provided'}</p>
                </div>
            </div>
        </div>

        <div class="tour-section">
            <h3><i class="fas fa-home"></i> Property Information</h3>
            <div class="property-card">
                <img src="${propertyImage}" alt="${tour.property_title}" class="property-card-img">
                <div class="property-card-info">
                    <h4>${tour.property_title}</h4>
                    <p><i class="fas fa-map-marker-alt"></i> ${tour.address}</p>
                </div>
            </div>
        </div>

        <div class="tour-section">
            <h3><i class="fas fa-info-circle"></i> Tour Details</h3>
            <div class="tour-details-layout">
                <div class="tour-details-primary">
                    <div class="tour-detail-card tour-detail-card-large">
                        <div class="tour-detail-icon">
                            <i class="fas fa-hashtag"></i>
                        </div>
                        <div class="tour-detail-content">
                            <div class="tour-detail-label">Tour ID</div>
                            <div class="tour-detail-value">#${tour.tour_id}</div>
                        </div>
                    </div>
                    <div class="tour-detail-card tour-detail-card-large">
                        <div class="tour-detail-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="tour-detail-content">
                            <div class="tour-detail-label">Total Amount</div>
                            <div class="tour-detail-value">৳${parseFloat(tour.total_amount).toFixed(2)}</div>
                        </div>
                    </div>
                </div>
                <div class="tour-details-secondary">
                    <div class="tour-detail-card">
                        <div class="tour-detail-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="tour-detail-content">
                            <div class="tour-detail-label">Status</div>
                            <div class="tour-detail-value">
                                <span class="status-badge ${statusClass}">${(tour.status || 'pending').charAt(0).toUpperCase() + (tour.status || 'pending').slice(1)}</span>
                            </div>
                        </div>
                    </div>
                    <div class="tour-detail-card">
                        <div class="tour-detail-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="tour-detail-content">
                            <div class="tour-detail-label">Created Date</div>
                            <div class="tour-detail-value">${createdDate}</div>
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

function updateTourStatus(tourId, status) {
    const statusText = status === 'confirmed' ? 'accept' : 'reject';
    if (!confirm(`Are you sure you want to ${statusText} this tour?`)) {
        return;
    }
    
    // Show loading state
    const modal = document.getElementById('tourModal');
    const modalBody = document.getElementById('tourDetails');
    const originalContent = modalBody.innerHTML;
    modalBody.innerHTML = `
        <div class="loading-state">
            <i class="fas fa-spinner"></i>
            <p>Updating tour status...</p>
        </div>
    `;
    
    // Send AJAX request
    fetch('../api/update_tour_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            tour_id: tourId,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Tour ${statusText}ed successfully!`);
            closeTourModal();
            location.reload(); // Reload to update the table
        } else {
            alert('Error: ' + (data.message || 'Failed to update tour status'));
            modalBody.innerHTML = originalContent;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the tour status');
        modalBody.innerHTML = originalContent;
    });
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
