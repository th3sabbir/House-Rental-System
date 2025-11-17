<!-- Tours Section -->
<?php
require_once __DIR__ . '/../config/database.php';

// Build query
$query = "SELECT t.tour_id, t.start_date, t.end_date, t.total_amount, t.message, t.status, t.created_at,
          p.title as property_title, p.address,
          tenant.full_name as tenant_name, tenant.email as tenant_email,
          landlord.full_name as landlord_name, landlord.email as landlord_email
          FROM tours t
          LEFT JOIN properties p ON t.property_id = p.property_id
          LEFT JOIN users tenant ON t.tenant_id = tenant.user_id
          LEFT JOIN users landlord ON t.landlord_id = landlord.user_id
          ORDER BY t.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$tours = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Count total tours
$count_query = "SELECT COUNT(*) as total FROM tours";
$count_stmt = $conn->prepare($count_query);
$count_stmt->execute();
$total_count = $count_stmt->get_result()->fetch_assoc()['total'];
$count_stmt->close();

// Function to get status badge
function getStatusBadge($status) {
    $classes = [
        'pending' => 'pending',
        'confirmed' => 'active',
        'rejected' => 'inactive',
        'cancelled' => 'inactive',
        'completed' => 'active'
    ];
    $class = isset($classes[$status]) ? $classes[$status] : 'inactive';
    return "<span class='badge $class'>" . ucfirst($status) . "</span>";
}
?>
<div class="content-header">
    <div class="header-title">
        <h1>Tours Management</h1>
        <p>Manage all tour requests and bookings</p>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h3>All Tours (<?php echo $total_count; ?>)</h3>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Tour ID</th>
                <th>Property</th>
                <th>Tenant</th>
                <th>Landlord</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($tours)): ?>
            <tr>
                <td colspan="7" style="text-align: center;">No tours found.</td>
            </tr>
            <?php else: ?>
                <?php foreach ($tours as $tour): ?>
                <tr>
                    <td>#<?php echo $tour['tour_id']; ?></td>
                    <td class="user-name-cell">
                        <div><?php echo htmlspecialchars($tour['property_title'] ?? 'Unknown Property'); ?></div>
                        <small style="color: #666;"><?php echo htmlspecialchars(substr($tour['address'] ?? '', 0, 30)); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($tour['tenant_name'] ?? 'Unknown'); ?><br>
                        <small style="color: #666;"><?php echo htmlspecialchars($tour['tenant_email'] ?? ''); ?></small></td>
                    <td><?php echo htmlspecialchars($tour['landlord_name'] ?? 'Unknown'); ?><br>
                        <small style="color: #666;"><?php echo htmlspecialchars($tour['landlord_email'] ?? ''); ?></small></td>
                    <td>৳<?php echo number_format($tour['total_amount'], 2); ?></td>
                    <td><?php echo getStatusBadge($tour['status']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($tour['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
