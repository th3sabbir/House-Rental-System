<!-- Properties Section -->
<?php
require_once __DIR__ . '/../config/database.php';

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM properties WHERE property_id = ?");
    $stmt->bind_param('i', $delete_id);
    $stmt->execute();
    $stmt->close();
    // Redirect to avoid resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Build query
$query = "SELECT p.property_id, p.title, p.property_type, p.price_per_month, p.status, p.created_at, u.full_name AS owner_name
          FROM properties p
          LEFT JOIN users u ON p.landlord_id = u.user_id ORDER BY p.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$properties = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Count total properties
$count_query = "SELECT COUNT(*) as total FROM properties";
$count_stmt = $conn->prepare($count_query);
$count_stmt->execute();
$total_count = $count_stmt->get_result()->fetch_assoc()['total'];
$count_stmt->close();

// Function to get status badge
function getStatusBadge($status) {
    $classes = [
        'available' => 'active',
        'pending' => 'pending',
        'inactive' => 'inactive'
    ];
    $class = isset($classes[$status]) ? $classes[$status] : 'inactive';
    return "<span class='badge $class'>" . ucfirst($status) . "</span>";
}
?>
<div class="content-header">
    <div class="header-title">
        <h1>Properties Management</h1>
        <p>Manage all listed properties</p>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h3>All Properties (<?php echo $total_count; ?>)</h3>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Property Name</th>
                <th>Owner</th>
                <th>Type</th>
                <th>Price/Month</th>
                <th>Status</th>
                <th>Listed Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($properties)): ?>
            <tr>
                <td colspan="7" style="text-align: center;">No properties found.</td>
            </tr>
            <?php else: ?>
                <?php foreach ($properties as $property): ?>
                <tr>
                    <td class="user-name-cell"><?php echo htmlspecialchars($property['title']); ?></td>
                    <td><?php echo htmlspecialchars($property['owner_name'] ?? 'Unknown'); ?></td>
                    <td><?php echo ucfirst(htmlspecialchars($property['property_type'])); ?></td>
                    <td>৳<?php echo number_format($property['price_per_month']); ?></td>
                    <td><?php echo getStatusBadge($property['status']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($property['created_at'])); ?></td>
                    <td>
                        <div class="action-btns">
                            <div class="tooltip-wrapper">
                                <a href="../property-details.php?id=<?php echo $property['property_id']; ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <span class="tooltip-text">View Property</span>
                            </div>
                            <div class="tooltip-wrapper">
                                <a href="index.php?page=edit-property&id=<?php echo $property['property_id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <span class="tooltip-text">Edit Property</span>
                            </div>
                            <div class="tooltip-wrapper">
                                <form method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this property?');">
                                    <input type="hidden" name="delete_id" value="<?php echo $property['property_id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <span class="tooltip-text">Delete Property</span>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>


