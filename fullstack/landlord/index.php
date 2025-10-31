<?php
session_start();

// Prevent browser caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /house_rental/login.php');
    exit();
}

// Check if user is landlord
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'landlord') {
    header('Location: /house_rental/index.php');
    exit();
}

// Include database connection
require_once '../config/database.php';
require_once '../includes/auth.php';

// Initialize database connection
$db = new Database();
$pdo = $db->connect();
global $pdo; // Make $pdo available globally for helper functions

$auth = new Auth();
$conn = $pdo; // Use $pdo for consistency

// Get user info
$user_id = $_SESSION['user_id'];
$user = getUserById($user_id);
$landlord_name = $user['full_name'] ?? 'Landlord';
$landlord_email = $_SESSION['email'] ?? '';
$landlord_username = $_SESSION['username'] ?? '';

// Get user data for form population
$user_email = $user['email'] ?? '';
$user_phone = $user['phone'] ?? '';
$user_date_of_birth = $user['date_of_birth'] ?? '';
$user_address = $user['address'] ?? '';
$user_city = $user['city'] ?? '';
$user_postal_code = $user['postal_code'] ?? '';

// Parse name into first and last name
$name_parts = explode(' ', $landlord_name, 2);
$first_name = $name_parts[0] ?? '';
$last_name = $name_parts[1] ?? '';

// Generate profile image URL
if (!empty($user['profile_image']) && file_exists('../uploads/' . $user['profile_image'])) {
    $profile_image_url = '../uploads/' . $user['profile_image'];
} else {
    // Use UI Avatars as fallback
    $profile_image_url = 'https://ui-avatars.com/api/?name=' . urlencode($landlord_name) . '&background=1abc9c&color=fff&size=160';
}

// Get dashboard statistics
try {
    // Active listings count
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM properties WHERE landlord_id = ? AND status = 'available'");
    $stmt->execute([$user_id]);
    $active_listings = $stmt->fetch()['count'];

    // Total bookings count
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count FROM bookings b 
        JOIN properties p ON b.property_id = p.property_id 
        WHERE p.landlord_id = ?
    ");
    $stmt->execute([$user_id]);
    $total_bookings = $stmt->fetch()['count'];

    // Pending requests count
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count FROM bookings b 
        JOIN properties p ON b.property_id = p.property_id 
        WHERE p.landlord_id = ? AND b.status = 'pending'
    ");
    $stmt->execute([$user_id]);
    $pending_requests = $stmt->fetch()['count'];

    // Average rating
    $stmt = $conn->prepare("
        SELECT AVG(r.rating) as avg_rating FROM reviews r 
        JOIN properties p ON r.property_id = p.property_id 
        WHERE p.landlord_id = ?
    ");
    $stmt->execute([$user_id]);
    $avg_rating = round($stmt->fetch()['avg_rating'] ?? 0, 1);

    // Get recent properties for dashboard
    $stmt = $conn->prepare("
        SELECT p.*, 
               (SELECT image_path FROM property_images WHERE property_id = p.property_id AND is_primary = 1 LIMIT 1) as main_image,
               (SELECT COUNT(*) FROM bookings WHERE property_id = p.property_id) as booking_count,
               (SELECT AVG(rating) FROM reviews WHERE property_id = p.property_id) as rating
        FROM properties p 
        WHERE p.landlord_id = ? 
        ORDER BY p.created_at DESC 
        LIMIT 6
    ");
    $stmt->execute([$user_id]);
    $properties = $stmt->fetchAll();

    // Get recent bookings
    $stmt = $conn->prepare("
        SELECT b.*, p.title as property_title, p.address, u.full_name as tenant_name, u.email as tenant_email, u.phone as tenant_phone,
               (SELECT image_path FROM property_images WHERE property_id = p.property_id AND is_primary = 1 LIMIT 1) as property_image,
               u.profile_image as tenant_image
        FROM bookings b 
        JOIN properties p ON b.property_id = p.property_id 
        JOIN users u ON b.tenant_id = u.user_id 
        WHERE p.landlord_id = ? 
        ORDER BY b.created_at DESC 
        LIMIT 10
    ");
    $stmt->execute([$user_id]);
    $recent_bookings = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Dashboard data loading error: " . $e->getMessage());
    $active_listings = 0;
    $total_bookings = 0;
    $pending_requests = 0;
    $avg_rating = 0;
    $properties = [];
    $recent_bookings = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AmarThikana</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
:root {
    --primary-color: #2c3e50;
    --secondary-color: #1abc9c;
    --background-light: #f5f7fa;
    --background-white: #ffffff;
    --text-dark: #34495e;
    --text-medium: #7f8c8d;
    --border-color: #e0e6ed;
    --font-family-body: 'Lato', sans-serif;
    --font-family-heading: 'Poppins', sans-serif;
    --shadow-soft: 0 2px 8px rgba(0,0,0,0.08);
    --shadow-medium: 0 4px 15px rgba(0,0,0,0.1);
    --border-radius: 12px;
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: var(--font-family-body);
    color: var(--text-dark);
    background-color: #f5f5f5;
    line-height: 1.6;
}

/* Dashboard Layout */
.dashboard-wrapper {
    display: flex;
    min-height: 100vh;
    position: relative;
}

.dashboard-layout {
    display: flex;
    width: 100%;
    position: relative;
}

/* Sidebar Styles */
.sidebar {
    width: 280px;
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    padding: 30px 0;
    height: 100vh;
    position: fixed;
    overflow-y: auto;
}

/* Sidebar Scrollbar */
.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.sidebar::-webkit-scrollbar-thumb {
    background: #1abc9c;
    border-radius: 10px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: #3498db;
}

.sidebar-profile {
    text-align: center;
    padding: 0 20px 30px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-profile img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    border: 4px solid #1abc9c;
    margin-bottom: 15px;
    object-fit: cover;
}

.sidebar-profile h3 {
    font-size: 1.3rem;
    margin-bottom: 5px;
    color: #ffffff;
}

.sidebar-profile p {
    color: #ecf0f1;
    font-size: 0.9rem;
}

.sidebar-nav {
    list-style: none;
    padding: 30px 0;
}

.sidebar-nav li {
    margin-bottom: 5px;
}

.sidebar-nav li a {
    display: flex;
    align-items: center;
    padding: 15px 30px;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
}

.sidebar-nav li a:hover {
    background-color: rgba(26, 188, 156, 0.2);
    border-left: 4px solid #1abc9c;
    padding-left: 26px;
}

.sidebar-nav li a.active {
    background-color: rgba(26, 188, 156, 0.3);
    border-left: 4px solid #1abc9c;
    padding-left: 26px;
}

.sidebar-nav li a i {
    margin-right: 15px;
    width: 20px;
    text-align: center;
    font-size: 1.1rem;
}

/* Main Content */
.dashboard-content {
    margin-left: 280px;
    flex: 1;
    padding: 40px;
    background-color: #f8f9fa;
}

.content-section {
    display: none;
    animation: fadeIn 0.4s ease;
}

.content-section.active {
    display: block;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.dashboard-header {
    margin-bottom: 40px;
}

.dashboard-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 8px;
    color: var(--text-dark);
}

.welcome-message {
    font-size: 1.1rem;
    color: var(--text-medium);
    margin: 0;
}

/* Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 24px;
    margin-bottom: 48px;
}

.stat-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    padding: 28px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-soft);
    transition: all 0.3s ease;
    border: 1px solid var(--border-color);
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--secondary-color);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-medium);
}

.stat-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
}

.stat-icon.primary { background: rgba(26, 188, 156, 0.1); color: var(--secondary-color); }
.stat-icon.warning { background: rgba(241, 196, 15, 0.1); color: #f1c40f; }
.stat-icon.success { background: rgba(46, 204, 113, 0.1); color: #2ecc71; }
.stat-icon.info { background: rgba(52, 152, 219, 0.1); color: #3498db; }
.stat-icon.danger { background: rgba(231, 76, 60, 0.1); color: #e74c3c; }

.stat-value {
    font-size: 2.2rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 4px;
}

.stat-label {
    font-size: 0.95rem;
    color: var(--text-medium);
    font-weight: 500;
}

.stat-change {
    font-size: 0.85rem;
    font-weight: 600;
    margin-top: 8px;
}

.stat-change.positive { color: #2ecc71; }
.stat-change.negative { color: #e74c3c; }

/* Section Header */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.section-header h2 {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--text-dark);
    margin: 0;
}



/* Property Cards Grid */
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 28px;
    margin-bottom: 48px;
}

.property-card {
    background: var(--background-white);
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--shadow-soft);
    transition: all 0.3s ease;
    border: 1px solid var(--border-color);
    position: relative;
}

.property-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-medium);
}

.card-image {
    position: relative;
    overflow: hidden;
    height: 220px;
}

.card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.property-card:hover .card-image img {
    transform: scale(1.1);
}

.card-badge {
    position: absolute;
    top: 16px;
    left: 16px;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    background: rgba(255, 255, 255, 0.95);
    color: var(--text-dark);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.card-badge.active {
    background: rgba(46, 204, 113, 0.95);
    color: white;
}

.card-badge.inactive {
    background: rgba(149, 165, 166, 0.95);
    color: white;
}

.card-content {
    padding: 24px;
}

.card-content h3 {
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 12px;
    color: var(--text-dark);
}

.card-stats {
    display: flex;
    gap: 20px;
    margin-bottom: 16px;
}

.card-stat {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    color: var(--text-medium);
}

.card-stat i {
    color: var(--secondary-color);
}

.card-actions {
    display: flex;
    gap: 12px;
    padding-top: 16px;
    border-top: 1px solid var(--border-color);
}

.card-actions .btn {
    flex: 1;
    padding: 10px 16px;
    font-size: 0.9rem;
    justify-content: center;
}

/* Table Styles */
.table-container {
    background: var(--background-white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-soft);
    overflow: hidden;
    border: 1px solid var(--border-color);
    overflow-x: auto;
}

/* Table Container Scrollbar */
.table-container::-webkit-scrollbar {
    height: 6px;
}

.table-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.table-container::-webkit-scrollbar-thumb {
    background: #16a085;
    border-radius: 10px;
}

.table-container::-webkit-scrollbar-thumb:hover {
    background: #2980b9;
}

.info-table {
    width: 100%;
    border-collapse: collapse;
}

.info-table thead th {
    text-align: left;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    color: var(--text-dark);
    font-weight: 700;
    padding: 20px 24px;
    font-size: 0.9rem;
    border-bottom: 2px solid var(--border-color);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-table tbody td {
    padding: 20px 24px;
    vertical-align: middle;
    border-bottom: 1px solid var(--border-color);
    font-size: 0.95rem;
}

.info-table tbody tr:last-child td {
    border-bottom: none;
}

.info-table tbody tr {
    transition: background-color 0.3s ease;
}

.info-table tbody tr:hover {
    background-color: #f8f9fa;
}

.guest-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.guest-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--border-color);
}

.guest-name {
    font-weight: 600;
    color: var(--text-dark);
}

.status-badge {
    padding: 6px 16px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-accepted {
    background: #d1fae5;
    color: #065f46;
}

.status-cancelled {
    background: #fee2e2;
    color: #991b1b;
}

.status-rejected {
    background: #fee2e2;
    color: #991b1b;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.action-btn {
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    color: white;
}

.action-btn.view {
    background: var(--secondary-color);
}

.action-btn.view:hover {
    background: #16a085;
}

.action-btn.delete {
    background: #e74c3c;
}

.action-btn.delete:hover {
    background: #c0392b;
}

.action-btn.edit {
    background: #3498db;
}

.action-btn.edit:hover {
    background: #2980b9;
}


.btn {
            padding: 12px 28px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }


.btn-primary {
    background-color: #1abc9c;
    color: white;
}

.btn-primary:hover {
    background-color: #16a085;
    transform: translateY(-2px);
}

.btn-secondary {
    background-color: #3498db;
    color: white;
}

.btn-secondary:hover {
    background-color: #2980b9;
    transform: translateY(-2px);
}

.btn-danger {
    background-color: #e74c3c;
    color: white;
}

.btn-danger:hover {
    background-color: #c0392b;
    transform: translateY(-2px);
}

.btn-success {
    background-color: #27ae60;
    color: white;
}

.btn-success:hover {
    background-color: #229954;
    transform: translateY(-2px);
}

/* Settings Form Styles */
.settings-form {
    background: var(--background-white);
    padding: 32px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-soft);
    margin-bottom: 24px;
}

.settings-container {
    background: white;
    border-radius: 12px;
    padding: 40px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    max-width: 800px;
}

.profile-picture-upload {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 30px;
}

.profile-picture-upload img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #1abc9c;
}

.upload-btn-wrapper {
    position: relative;
    overflow: hidden;
    display: inline-block;
}

.upload-btn-wrapper input[type=file] {
    font-size: 100px;
    position: absolute;
    left: 0;
    top: 0;
    opacity: 0;
    cursor: pointer;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.settings-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

/* Settings Page Buttons - Custom Styling */
.settings-btn {
    padding: 15px 16px;
    border: none;
    border-radius: 50px;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.settings-btn-primary {
    background-color: #1abc9c;
    color: white;
}

.settings-btn-primary:hover {
    background-color: #16a085;
    transform: translateY(-2px);
}

.settings-btn-secondary {
    background-color: #3498db;
    color: white;
}

.settings-btn-secondary:hover {
    background-color: #2980b9;
    transform: translateY(-2px);
}

.form-group {
    margin-bottom: 24px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text-dark);
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 18px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    font-size: 0.95rem;
    font-family: var(--font-family-body);
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #1abc9c;
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 24px;
    background: var(--background-white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-soft);
}

.empty-state i {
    font-size: 4rem;
    color: var(--border-color);
    margin-bottom: 24px;
}

.empty-state h3 {
    font-size: 1.5rem;
    margin-bottom: 12px;
    color: var(--text-dark);
}

.empty-state p {
    color: var(--text-medium);
    margin-bottom: 24px;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .dashboard-layout {
        grid-template-columns: 260px 1fr;
    }
}

@media (max-width: 768px) {
    .dashboard-header h1 {
        font-size: 2rem;
    }

    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .dashboard-grid {
        grid-template-columns: 1fr;
    }

    .info-table {
        font-size: 0.85rem;
        min-width: 800px; /* Force horizontal scroll on small screens */
    }

    .info-table thead th,
    .info-table tbody td {
        padding: 16px;
    }

    .action-buttons {
        flex-direction: column;
    }

    .guest-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
}

@media (max-width: 576px) {
    .dashboard-content {
        padding: 24px 16px;
    }

    .stat-card {
        padding: 20px;
    }

    .card-content {
        padding: 20px;
    }
}

/* Modal/Popup Styles */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 9998;
    animation: fadeIn 0.3s ease;
}

.modal-overlay.active {
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal {
    background: var(--background-white);
    border-radius: var(--border-radius);
    max-width: 700px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    position: relative;
    animation: slideUp 0.3s ease;
}

/* Modal Scrollbar */
.modal::-webkit-scrollbar {
    width: 6px;
}

.modal::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.modal::-webkit-scrollbar-thumb {
    background: #16a085;
    border-radius: 10px;
}

.modal::-webkit-scrollbar-thumb:hover {
    background: #2980b9;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-header {
    padding: 28px 32px;
    border-bottom: 2px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}

.modal-header h2 {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--text-dark);
    margin: 0;
}

.modal-close {
    background: transparent;
    border: none;
    font-size: 1.8rem;
    color: var(--text-medium);
    cursor: pointer;
    transition: all 0.3s ease;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.modal-close:hover {
    background: var(--border-color);
    color: var(--text-dark);
    transform: rotate(90deg);
}

.modal-body {
    padding: 32px;
}

.booking-detail-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
    margin-bottom: 24px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.detail-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--text-medium);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    font-size: 1.05rem;
    font-weight: 500;
    color: var(--text-dark);
}

.guest-detail {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: var(--border-radius);
    margin-bottom: 24px;
}

.guest-detail img {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--secondary-color);
}

.guest-detail-info h3 {
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 4px;
    color: var(--text-dark);
}

.guest-detail-info p {
    font-size: 0.95rem;
    color: var(--text-medium);
    margin: 2px 0;
}

.booking-notes {
    background: #fff8e1;
    padding: 20px;
    border-radius: var(--border-radius);
    border-left: 4px solid #f1c40f;
    margin-bottom: 24px;
}

.booking-notes h4 {
    font-size: 1rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 8px;
}

.booking-notes p {
    font-size: 0.95rem;
    color: var(--text-dark);
    line-height: 1.6;
    margin: 0;
}

.modal-footer {
    padding: 24px 32px;
    border-top: 2px solid var(--border-color);
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}

.modal-footer .btn {
    min-width: 150px;
    padding: 12px 24px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.95rem;
}

.btn-success {
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(46, 204, 113, 0.3);
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(46, 204, 113, 0.4);
}

.btn-danger {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(231, 76, 60, 0.4);
}

/* Property View Modal Styles */
.property-view-image {
    width: 100%;
    height: 300px;
    margin-bottom: 24px;
    border-radius: var(--border-radius);
    overflow: hidden;
}

.property-view-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.property-view-info {
    margin-bottom: 24px;
}

.property-view-info h3 {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 8px;
    color: var(--text-dark);
}

.property-address {
    font-size: 1rem;
    color: var(--text-medium);
    display: flex;
    align-items: center;
    gap: 8px;
}

.property-address::before {
    content: '\f3c5';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
}

.amenities-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}

.amenity-tag {
    background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    color: #2e7d32;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
}

.amenity-tag i {
    font-size: 0.85rem;
}

/* Form Styles */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 8px;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    font-size: 1rem;
    font-family: var(--font-family-body);
    color: var(--text-dark);
    transition: all 0.3s ease;
    background: var(--background-white);
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--secondary-color);
    box-shadow: 0 0 0 3px rgba(26, 188, 156, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.form-row-three {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.amenities-checkboxes {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-top: 10px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-size: 0.95rem;
    color: var(--text-dark);
    padding: 10px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    transition: all 0.3s ease;
}

.checkbox-label:hover {
    background: #f8f9fa;
    border-color: var(--secondary-color);
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: var(--secondary-color);
}

.checkbox-label input[type="checkbox"]:checked + i {
    color: var(--secondary-color);
}

.checkbox-label i {
    color: var(--text-medium);
    transition: color 0.3s ease;
}

/* Form Sections */
.form-section {
    margin-bottom: 32px;
    padding-bottom: 24px;
    border-bottom: 1px solid var(--border-color);
}

.form-section:last-child {
    border-bottom: none;
}

.form-section-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-section-title i {
    color: var(--secondary-color);
}

.form-hint {
    display: block;
    font-size: 0.85rem;
    color: var(--text-medium);
    margin-top: 6px;
}

/* Review Info Display Styles */
.review-info-display {
    background: #f8f9fa;
    padding: 20px;
    border-radius: var(--border-radius);
    margin-bottom: 24px;
    border: 1px solid var(--border-color);
}

.review-info-display h4 {
    font-size: 1rem;
    color: var(--text-dark);
    margin-bottom: 16px;
    font-weight: 600;
}

.review-display-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 16px;
}

.review-comment-display {
    padding-top: 16px;
    border-top: 1px solid var(--border-color);
}

.review-comment-display .detail-label {
    display: block;
    margin-bottom: 8px;
}

.review-comment-display p {
    background: white;
    padding: 12px;
    border-radius: 6px;
    font-style: italic;
    color: var(--text-dark);
    line-height: 1.6;
}

/* Image Upload Styles */
.image-upload-container {
    background: #f8f9fa;
    padding: 24px;
    border-radius: var(--border-radius);
    border: 2px dashed var(--border-color);
}

.main-image-preview {
    position: relative;
    width: 100%;
    height: 300px;
    margin-bottom: 20px;
    border-radius: var(--border-radius);
    overflow: hidden;
    background: var(--background-white);
    border: 2px solid var(--secondary-color);
}

.main-image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.main-image-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: var(--secondary-color);
    color: white;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.thumbnail-preview-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}

.thumbnail-preview-item {
    position: relative;
    aspect-ratio: 16/10;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid var(--border-color);
    background: var(--background-white);
    transition: all 0.3s ease;
}

.thumbnail-preview-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.thumbnail-preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.thumbnail-remove-btn {
    position: absolute;
    top: 6px;
    right: 6px;
    background: rgba(231, 76, 60, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    opacity: 0;
}

.thumbnail-preview-item:hover .thumbnail-remove-btn {
    opacity: 1;
}

.thumbnail-remove-btn:hover {
    background: #c0392b;
    transform: scale(1.1);
}

.thumbnail-set-main-btn {
    position: absolute;
    bottom: 6px;
    left: 6px;
    right: 6px;
    background: rgba(22, 160, 133, 0.9);
    color: white;
    border: none;
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    opacity: 0;
}

.thumbnail-preview-item:hover .thumbnail-set-main-btn {
    opacity: 1;
}

.thumbnail-set-main-btn:hover {
    background: #16a085;
}

.image-upload-actions {
    text-align: center;
}

.upload-hint {
    margin-top: 10px;
    font-size: 0.9rem;
    color: var(--text-medium);
}

.detail-section {
    margin-top: 20px;
}

.property-description {
    margin-top: 10px;
    line-height: 1.6;
}

@media (max-width: 768px) {
    .modal {
        width: 95%;
        max-height: 95vh;
    }

    .modal-header,
    .modal-body,
    .modal-footer {
        padding: 20px;
    }

    .modal-header h2 {
        font-size: 1.4rem;
    }

    .booking-detail-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .guest-detail {
        flex-direction: column;
        text-align: center;
    }

    .guest-detail img {
        width: 80px;
        height: 80px;
    }

    .modal-footer {
        flex-direction: column;
    }

    .modal-footer .btn {
        width: 100%;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .form-row-three {
        grid-template-columns: 1fr;
    }

    .amenities-checkboxes {
        grid-template-columns: repeat(2, 1fr);
    }

    .property-view-image {
        height: 200px;
    }

    .thumbnail-preview-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .main-image-preview {
        height: 200px;
    }
}

@media (max-width: 480px) {
    .modal {
        width: 100%;
        height: 100vh;
        max-height: 100vh;
        border-radius: 0;
    }

    .modal-header h2 {
        font-size: 1.2rem;
    }

    .detail-value {
        font-size: 0.95rem;
    }

    .action-btn.view {
        padding: 8px 12px;
        font-size: 0.85rem;
    }

    .amenities-checkboxes {
        grid-template-columns: 1fr;
    }

    .form-section-title {
        font-size: 1.1rem;
    }

    .thumbnail-preview-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .review-display-grid {
        grid-template-columns: 1fr;
    }
}
.main-header {
    background-color: #2c3e50 !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.06) !important;
}

/* Mobile Menu Toggle Button */
.mobile-menu-toggle {
    display: none;
}

/* Sidebar Overlay */
.sidebar-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 998;
}

.sidebar-overlay.active {
    display: block;
}

/* Responsive Styles for Mobile */
@media (max-width: 992px) {
    .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        z-index: 999;
    }

    .sidebar.mobile-active {
        transform: translateX(0);
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3);
    }

    .dashboard-content {
        margin-left: 0 !important;
        width: 100% !important;
    }

    .mobile-menu-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #2c3e50, #1abc9c);
        border: none;
        border-radius: 50%;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(26, 188, 156, 0.4);
        z-index: 1000;
        transition: all 0.3s ease;
    }

    .mobile-menu-toggle:hover {
        transform: scale(1.1);
    }

    .mobile-menu-toggle:active {
        transform: scale(0.95);
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }

    .properties-grid {
        grid-template-columns: 1fr !important;
    }

    .table-container {
        overflow-x: auto;
    }

    .data-table {
        font-size: 0.85rem;
    }

    .data-table th,
    .data-table td {
        padding: 8px;
        white-space: nowrap;
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr !important;
    }

    .content-header {
        flex-direction: column;
        gap: 15px;
    }

    .header-actions {
        width: 100%;
    }

    .header-actions .btn {
        width: 100%;
    }

    /* Form Responsiveness */
    .settings-container {
        padding: 25px;
        max-width: none;
        margin: 0 15px;
    }

    .profile-picture-upload {
        gap: 20px;
    }

    .profile-picture-upload img {
        width: 110px;
        height: 110px;
    }

    /* Modal Responsiveness */
    .modal {
        width: 95%;
        max-height: 90vh;
    }

    .modal-header,
    .modal-body,
    .modal-footer {
        padding: 20px;
    }

    .modal-header h2 {
        font-size: 1.4rem;
    }

    /* Dashboard Layout */
    .dashboard-content {
        padding: 20px;
    }

    .content-header h1 {
        font-size: 1.8rem;
    }

    /* Property Cards */
    .property-card {
        margin-bottom: 20px;
    }

    .property-card-content {
        padding: 18px;
    }
}

@media (max-width: 576px) {
    .mobile-menu-toggle {
        width: 50px;
        height: 50px;
        font-size: 20px;
        bottom: 15px;
        right: 15px;
    }

    .stats-card h3 {
        font-size: 1.5rem;
    }

    .content-header h1 {
        font-size: 1.5rem;
    }

    .btn {
        padding: 8px 12px;
        font-size: 0.85rem;
    }

    .property-card {
        padding: 15px;
    }

    /* Form Responsiveness */
    .form-group input,
    .form-group select,
    .form-group textarea {
        font-size: 16px; /* Prevents zoom on iOS */
        padding: 12px 15px;
    }

    .form-section {
        margin-bottom: 25px;
    }

    .form-section-title {
        font-size: 1.1rem;
        margin-bottom: 15px;
    }

    /* Profile Settings */
    .settings-container {
        padding: 20px;
        margin: 0 10px;
    }

    .profile-picture-upload {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }

    .profile-picture-upload img {
        width: 100px;
        height: 100px;
    }

    /* Modal Improvements */
    .modal {
        width: 100%;
        height: 100vh;
        max-height: 100vh;
        border-radius: 0;
        margin: 0;
    }

    .modal-header {
        padding: 15px 20px;
    }

    .modal-header h2 {
        font-size: 1.2rem;
    }

    .modal-body {
        padding: 15px 20px;
        overflow-y: auto;
        max-height: calc(100vh - 140px);
    }

    .modal-footer {
        padding: 15px 20px;
        flex-direction: column;
        gap: 10px;
    }

    .modal-footer .btn {
        width: 100%;
        min-width: auto;
    }

    /* Dashboard Content */
    .dashboard-content {
        padding: 15px;
    }

    .content-header h1 {
        font-size: 1.4rem;
    }

    /* Property Cards */
    .property-card-content {
        padding: 15px;
    }

    .property-card-title {
        font-size: 1.1rem;
    }

    /* Table Responsiveness */
    .data-table {
        font-size: 0.8rem;
    }

    .data-table th,
    .data-table td {
        padding: 8px 6px;
    }
}

/* Extra Small Devices */
@media (max-width: 480px) {
    .modal {
        width: 100%;
        height: 100vh;
        max-height: 100vh;
        border-radius: 0;
        margin: 0;
    }

    .modal-header {
        padding: 15px;
    }

    .modal-header h2 {
        font-size: 1.2rem;
    }

    .modal-body {
        padding: 15px;
        max-height: calc(100vh - 120px);
    }

    .modal-footer {
        padding: 15px;
    }

    .dashboard-content {
        padding: 10px;
    }

    .content-header h1 {
        font-size: 1.3rem;
    }

    .settings-container {
        padding: 15px;
        margin: 0 5px;
    }

    .stat-card {
        padding: 15px;
    }

    .btn {
        padding: 10px 15px;
        font-size: 0.85rem;
    }

    .mobile-menu-toggle {
        width: 45px !important;
        height: 45px !important;
        font-size: 18px !important;
        bottom: 10px !important;
        right: 10px !important;
    }
}
    </style>
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <div class="dashboard-wrapper">
        <div class="dashboard-layout">
            <!-- Sidebar -->
            <aside class="sidebar" id="landlordSidebar">
                <div class="sidebar-profile">
                    <img src="<?php echo htmlspecialchars($profile_image_url); ?>" alt="<?php echo htmlspecialchars($landlord_name); ?>">
                    <h3><?php echo htmlspecialchars($landlord_name); ?></h3>
                    <p>Property Owner</p>
                </div>
                <ul class="sidebar-nav">
                    <li><a href="#" class="nav-link active" data-section="dashboard"><i class="fas fa-th-large"></i> Dashboard</a></li>
                    <li><a href="#" class="nav-link" data-section="listings"><i class="fas fa-building"></i> My Listings</a></li>
                    <li><a href="#" class="nav-link" data-section="bookings"><i class="fas fa-calendar-check"></i> Bookings</a></li>
                    <li><a href="#" class="nav-link" data-section="reviews"><i class="fas fa-star"></i> Reviews</a></li>
                    <li><a href="../messages.php"><i class="fas fa-comments"></i> Messages
                        <span style="background: #e74c3c; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.75rem; margin-left: auto;">5</span>
                    </a></li>
                    <li><a href="#" class="nav-link" data-section="settings"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </aside>

            <!-- Main Content -->
            <main class="dashboard-content">
                
                <!-- Dashboard Section -->
                <section id="dashboard" class="content-section active">
                    <div class="dashboard-header">
                        <h1>Dashboard</h1>
                        <p class="welcome-message">Welcome back, Sophia! Here's what's happening with your properties.</p>
                    </div>

                    <!-- Stats Grid -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div>
                                    <div class="stat-value"><?php echo $active_listings; ?></div>
                                    <div class="stat-label">Active Listings</div>
                                </div>
                                <div class="stat-icon primary">
                                    <i class="fas fa-building"></i>
                                </div>
                            </div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> Keep it up!
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div>
                                    <div class="stat-value"><?php echo $total_bookings; ?></div>
                                    <div class="stat-label">Total Bookings</div>
                                </div>
                                <div class="stat-icon success">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                            </div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> Great performance
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div>
                                    <div class="stat-value"><?php echo $pending_requests; ?></div>
                                    <div class="stat-label">Pending Requests</div>
                                </div>
                                <div class="stat-icon warning">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                            <div class="stat-change">
                                <?php echo $pending_requests > 0 ? 'Needs your attention' : 'All caught up'; ?>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div>
                                    <div class="stat-value"><?php echo $avg_rating > 0 ? $avg_rating : 'N/A'; ?></div>
                                    <div class="stat-label">Average Rating</div>
                                </div>
                                <div class="stat-icon info">
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> <?php echo $avg_rating >= 4.5 ? 'Excellent' : 'Good'; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="section-header">
                        <h2>Quick Overview</h2>
                    </div>

                    <div class="dashboard-grid">
                        <?php if (empty($properties)): ?>
                        <div class="empty-state">
                            <i class="fas fa-building"></i>
                            <h3>No Properties Yet</h3>
                            <p>You haven't added any properties yet. Start by adding your first listing!</p>
                            <a href="#" class="btn btn-primary" onclick="openAddListingModal(); return false;">
                                <i class="fas fa-plus"></i> Add Your First Property
                            </a>
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
                                        <i class="fas fa-calendar-check"></i> <?php echo $property['booking_count']; ?> Bookings
                                    </span>
                                    <span class="card-stat">
                                        <i class="fas fa-star"></i> <?php echo $property['rating'] ? number_format($property['rating'], 1) : 'N/A'; ?>
                                    </span>
                                </div>
                                <div class="card-actions">
                                    <button class="btn btn-secondary" onclick="openPropertyEditModal({
                                        id: <?php echo $property['property_id']; ?>,
                                        name: '<?php echo addslashes(htmlspecialchars($property['title'])); ?>',
                                        address: '<?php echo addslashes(htmlspecialchars($property['address'])); ?>',
                                        type: '<?php echo addslashes(htmlspecialchars($property['property_type'])); ?>',
                                        status: '<?php echo addslashes(htmlspecialchars($property['status'])); ?>',
                                        price: <?php echo $property['price_per_month']; ?>,
                                        bedrooms: <?php echo $property['bedrooms'] ?? 0; ?>,
                                        bathrooms: <?php echo $property['bathrooms'] ?? 0; ?>,
                                        guests: <?php echo $property['area_sqft'] ?? 0; ?>,
                                        description: '<?php echo addslashes(htmlspecialchars($property['description'])); ?>'
                                    })">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-primary" onclick="openPropertyViewModal({
                                        image: '<?php echo $property['main_image'] ? '../uploads/' . $property['main_image'] : 'https://via.placeholder.com/400x250?text=No+Image'; ?>',
                                        name: '<?php echo addslashes(htmlspecialchars($property['title'])); ?>',
                                        address: '<?php echo addslashes(htmlspecialchars($property['address'])); ?>',
                                        type: '<?php echo addslashes(htmlspecialchars($property['property_type'])); ?>',
                                        status: '<?php echo addslashes(htmlspecialchars($property['status'])); ?>',
                                        price: '$<?php echo number_format($property['price_per_month']); ?>',
                                        rating: '<?php echo $property['rating'] ? number_format($property['rating'], 1) . ' ⭐' : 'N/A'; ?>',
                                        bedrooms: <?php echo $property['bedrooms'] ?? 0; ?>,
                                        bathrooms: <?php echo $property['bathrooms'] ?? 0; ?>,
                                        guests: <?php echo $property['area_sqft'] ?? 0; ?>,
                                        bookings: <?php echo $property['booking_count']; ?>,
                                        description: '<?php echo addslashes(htmlspecialchars($property['description'])); ?>'
                                    })">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Listings Section -->
                <section id="listings" class="content-section">
                    <div class="dashboard-header">
                        <h1>My Listings</h1>
                        <p class="welcome-message">Manage all your property listings here.</p>
                    </div>

                    <div class="section-header">
                        <h2>All Properties</h2>
                        <a href="#" class="btn btn-primary" onclick="openAddListingModal(); return false;">
                            <i class="fas fa-plus"></i> Add New Listing
                        </a>
                    </div>

                    <div class="dashboard-grid">
                        <?php if (empty($properties)): ?>
                        <div class="empty-state">
                            <i class="fas fa-building"></i>
                            <h3>No Properties Yet</h3>
                            <p>You haven't added any properties yet. Start by adding your first listing!</p>
                            <a href="#" class="btn btn-primary" onclick="openAddListingModal(); return false;">
                                <i class="fas fa-plus"></i> Add Your First Property
                            </a>
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
                                        <i class="fas fa-calendar-check"></i> <?php echo $property['booking_count']; ?> Bookings
                                    </span>
                                    <span class="card-stat">
                                        <i class="fas fa-star"></i> <?php echo $property['rating'] ? number_format($property['rating'], 1) : 'N/A'; ?>
                                    </span>
                                </div>
                                <div class="card-actions">
                                    <button class="btn btn-secondary" onclick="openPropertyEditModal({
                                        id: <?php echo $property['property_id']; ?>,
                                        name: '<?php echo addslashes(htmlspecialchars($property['title'])); ?>',
                                        address: '<?php echo addslashes(htmlspecialchars($property['address'])); ?>',
                                        type: '<?php echo addslashes(htmlspecialchars($property['property_type'])); ?>',
                                        status: '<?php echo addslashes(htmlspecialchars($property['status'])); ?>',
                                        price: <?php echo $property['price_per_month']; ?>,
                                        bedrooms: <?php echo $property['bedrooms'] ?? 0; ?>,
                                        bathrooms: <?php echo $property['bathrooms'] ?? 0; ?>,
                                        guests: <?php echo $property['area_sqft'] ?? 0; ?>,
                                        description: '<?php echo addslashes(htmlspecialchars($property['description'])); ?>'
                                    })">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-primary" onclick="openPropertyViewModal({
                                        image: '<?php echo $property['main_image'] ? '../uploads/' . $property['main_image'] : 'https://via.placeholder.com/400x250?text=No+Image'; ?>',
                                        name: '<?php echo addslashes(htmlspecialchars($property['title'])); ?>',
                                        address: '<?php echo addslashes(htmlspecialchars($property['address'])); ?>',
                                        type: '<?php echo addslashes(htmlspecialchars($property['property_type'])); ?>',
                                        status: '<?php echo addslashes(htmlspecialchars($property['status'])); ?>',
                                        price: '$<?php echo number_format($property['price_per_month']); ?>',
                                        rating: '<?php echo $property['rating'] ? number_format($property['rating'], 1) . ' ⭐' : 'N/A'; ?>',
                                        bedrooms: <?php echo $property['bedrooms'] ?? 0; ?>,
                                        bathrooms: <?php echo $property['bathrooms'] ?? 0; ?>,
                                        guests: <?php echo $property['area_sqft'] ?? 0; ?>,
                                        bookings: <?php echo $property['booking_count']; ?>,
                                        description: '<?php echo addslashes(htmlspecialchars($property['description'])); ?>'
                                    })">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Bookings Section -->
                <section id="bookings" class="content-section">
                    <div class="dashboard-header">
                        <h1>Bookings</h1>
                        <p class="welcome-message">View and manage all booking requests.</p>
                    </div>

                    <div class="section-header">
                        <h2>All Booking Requests</h2>
                        
                    </div>

                    <div class="table-container">
                        <table class="info-table">
                            <thead>
                                <tr>
                                    <th>Guest</th>
                                    <th>Property</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($bookings)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px;">
                                        <div class="empty-state">
                                            <i class="fas fa-calendar-times"></i>
                                            <h3>No Booking Requests Yet</h3>
                                            <p>When tenants request to book your properties, they'll appear here.</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td>
                                        <div class="guest-info">
                                            <img src="<?php echo htmlspecialchars($booking['tenant_image'] ?? 'https://via.placeholder.com/40x40?text=User'); ?>" 
                                                 alt="<?php echo htmlspecialchars($booking['tenant_name']); ?>" class="guest-avatar">
                                            <span class="guest-name"><?php echo htmlspecialchars($booking['tenant_name']); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($booking['property_title']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?></td>
                                    <td><span class="status-badge status-<?php echo strtolower($booking['status']); ?>"><?php echo ucfirst($booking['status']); ?></span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn view" onclick="openBookingModal({
                                                guestName: '<?php echo addslashes(htmlspecialchars($booking['tenant_name'])); ?>',
                                                guestEmail: '<?php echo addslashes(htmlspecialchars($booking['tenant_email'])); ?>',
                                                guestPhone: '<?php echo addslashes(htmlspecialchars($booking['tenant_phone'])); ?>',
                                                guestAvatar: '<?php echo htmlspecialchars($booking['tenant_image'] ?? 'https://via.placeholder.com/40x40?text=User'); ?>',
                                                property: '<?php echo addslashes(htmlspecialchars($booking['property_title'])); ?>',
                                                checkin: '<?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?>',
                                                checkout: '<?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?>',
                                                status: '<?php echo ucfirst($booking['status']); ?>',
                                                nights: '<?php echo $booking['nights']; ?>',
                                                price: '$<?php echo number_format($booking['total_price'], 2); ?>',
                                                guests: '<?php echo $booking['guests']; ?>',
                                                bookingDate: '<?php echo date('M d, Y', strtotime($booking['created_at'])); ?>',
                                                notes: '<?php echo addslashes(htmlspecialchars($booking['message'])); ?>',
                                                bookingId: <?php echo $booking['booking_id']; ?>
                                            })">View</button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Reviews Section -->
                <section id="reviews" class="content-section">
                    <div class="dashboard-header">
                        <h1>Reviews</h1>
                        <p class="welcome-message">See what guests are saying about your properties.</p>
                    </div>

                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div>
                                    <div class="stat-value">4.8</div>
                                    <div class="stat-label">Overall Rating</div>
                                </div>
                                <div class="stat-icon info">
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <div class="stat-change positive">
                                Based on 156 reviews
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div>
                                    <div class="stat-value">142</div>
                                    <div class="stat-label">Total Reviews</div>
                                </div>
                                <div class="stat-icon success">
                                    <i class="fas fa-comments"></i>
                                </div>
                            </div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> 12 this month
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div>
                                    <div class="stat-value">8</div>
                                    <div class="stat-label">New Reviews</div>
                                </div>
                                <div class="stat-icon warning">
                                    <i class="fas fa-bell"></i>
                                </div>
                            </div>
                            <div class="stat-change">
                                Awaiting response
                            </div>
                        </div>
                    </div>

                    <div class="section-header">
                        <h2>Recent Reviews</h2>
                        <!-- <a href="#" class="btn btn-secondary">View All</a> -->
                    </div>

                    <div class="table-container">
                        <table class="info-table">
                            <thead>
                                <tr>
                                    <th>Guest</th>
                                    <th>Property</th>
                                    <th>Rating</th>
                                    <th>Comment</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="guest-info">
                                            <img src="https://images.pexels.com/photos/1222271/pexels-photo-1222271.jpeg?auto=compress&cs=tinysrgb&w=400" alt="Emma Wilson" class="guest-avatar">
                                            <span class="guest-name">Emma Wilson</span>
                                        </div>
                                    </td>
                                    <td>Cozy Apartment</td>
                                    <td>
                                        <div style="color: #f1c40f;">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                    </td>
                                    <td>Amazing place! Very clean and comfortable.</td>
                                    <td>Oct 8, 2025</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn view" onclick="openReplyModal({guest: 'Sarah Johnson', property: 'Cozy Apartment', rating: 5, comment: 'Amazing place! Very clean and comfortable.', date: 'Oct 8, 2025'})">Reply</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="guest-info">
                                            <img src="https://images.pexels.com/photos/91227/pexels-photo-91227.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Noah Foster" class="guest-avatar">
                                            <span class="guest-name">Noah Foster</span>
                                        </div>
                                    </td>
                                    <td>Beachfront Villa</td>
                                    <td>
                                        <div style="color: #f1c40f;">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                        </div>
                                    </td>
                                    <td>Great location and stunning views!</td>
                                    <td>Oct 5, 2025</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn view" onclick="openReplyModal({guest: 'Noah Foster', property: 'Beachfront Villa', rating: 4, comment: 'Great location and stunning views!', date: 'Oct 5, 2025'})">Reply</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Settings Section -->
                <section id="settings" class="content-section">
                    <div class="dashboard-header">
                        <h1>Profile Settings</h1>
                        <p class="welcome-message">Manage your account information</p>
                    </div>

                    <div class="settings-container">
                        <div class="profile-picture-upload">
                            <img src="<?php echo htmlspecialchars($profile_image_url); ?>" alt="Profile Picture" id="profileImage">
                            <div class="upload-btn-wrapper">
                                <button class="btn btn-primary" onclick="document.getElementById('profileImageInput').click()">
                                    <i class="fas fa-camera"></i> Select Photo
                                </button>
                                <button class="btn btn-success" id="uploadPhotoBtn" style="display: none;" onclick="uploadPhoto()">
                                    <i class="fas fa-upload"></i> Upload Photo
                                </button>
                                <input type="file" id="profileImageInput" accept="image/*" style="display: none;" onchange="previewPhoto(event)">
                            </div>
                        </div>

                        <form id="settingsForm" onsubmit="saveSettings(event)">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>First Name *</label>
                                    <input type="text" id="firstName" value="<?php echo htmlspecialchars($first_name); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Last Name *</label>
                                    <input type="text" id="lastName" value="<?php echo htmlspecialchars($last_name); ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Email Address *</label>
                                <input type="email" id="email" value="<?php echo htmlspecialchars($user_email); ?>" required>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Phone Number *</label>
                                    <input type="tel" id="phone" value="<?php echo htmlspecialchars($user_phone); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Date of Birth</label>
                                    <input type="date" id="dateOfBirth" value="<?php echo htmlspecialchars($user_date_of_birth); ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" id="address" value="<?php echo htmlspecialchars($user_address); ?>">
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" id="city" value="<?php echo htmlspecialchars($user_city); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Postal Code</label>
                                    <input type="text" id="postalCode" value="<?php echo htmlspecialchars($user_postal_code); ?>">
                                </div>
                            </div>

                            <div class="settings-actions">
                                <button type="submit" class="settings-btn settings-btn-primary">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                                <button type="button" class="settings-btn settings-btn-secondary" onclick="openPasswordModal()">
                                    <i class="fas fa-key"></i> Change Password
                                </button>
                            </div>
                        </form>
                    </div>
                </section>

            </main>

            <!-- Mobile Menu Toggle Button -->
            <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>

    <!-- Footer Placeholder -->
    <div id="footer-placeholder"></div>

    <!-- Booking Details Modal -->
    <div id="bookingModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2>Booking Details</h2>
                <button class="modal-close" onclick="closeBookingModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="guest-detail">
                    <img id="modalGuestAvatar" src="" alt="Guest Avatar">
                    <div class="guest-detail-info">
                        <h3 id="modalGuestName"></h3>
                        <p><i class="fas fa-envelope"></i> <span id="modalGuestEmail"></span></p>
                        <p><i class="fas fa-phone"></i> <span id="modalGuestPhone"></span></p>
                    </div>
                </div>

                <div class="booking-detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Property</span>
                        <span class="detail-value" id="modalProperty"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Booking Status</span>
                        <span class="detail-value" id="modalStatus"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Check-in Date</span>
                        <span class="detail-value" id="modalCheckin"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Check-out Date</span>
                        <span class="detail-value" id="modalCheckout"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Total Nights</span>
                        <span class="detail-value" id="modalNights"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Total Price</span>
                        <span class="detail-value" id="modalPrice"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Number of Guests</span>
                        <span class="detail-value" id="modalGuests"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Booking Date</span>
                        <span class="detail-value" id="modalBookingDate"></span>
                    </div>
                </div>

                <div class="booking-notes">
                    <h4><i class="fas fa-sticky-note"></i> Special Requests</h4>
                    <p id="modalNotes"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" onclick="closeBookingModal()">
                    <i class="fas fa-times-circle"></i> Close
                </button>
                <button class="btn btn-success" id="modalActionBtn">
                    <i class="fas fa-check-circle"></i> Accept Booking
                </button>
            </div>
        </div>
    </div>

    <!-- Property View Modal -->
    <div id="propertyViewModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2>Property Details</h2>
                <button class="modal-close" onclick="closePropertyViewModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="property-view-image">
                    <img id="viewPropertyImage" src="" alt="Property Image">
                </div>

                <div class="property-view-info">
                    <h3 id="viewPropertyName"></h3>
                    <p id="viewPropertyAddress" class="property-address"></p>
                </div>

                <div class="booking-detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Property Type</span>
                        <span class="detail-value" id="viewPropertyType"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Status</span>
                        <span class="detail-value" id="viewPropertyStatus"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Price per Night</span>
                        <span class="detail-value" id="viewPropertyPrice"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Rating</span>
                        <span class="detail-value" id="viewPropertyRating"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Bedrooms</span>
                        <span class="detail-value" id="viewPropertyBedrooms"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Bathrooms</span>
                        <span class="detail-value" id="viewPropertyBathrooms"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Max Guests</span>
                        <span class="detail-value" id="viewPropertyGuests"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Total Bookings</span>
                        <span class="detail-value" id="viewPropertyBookings"></span>
                    </div>
                </div>

                <div class="detail-item detail-section">
                    <span class="detail-label">Description</span>
                    <p id="viewPropertyDescription" class="property-description"></p>
                </div>

                <div class="detail-item detail-section">
                    <span class="detail-label">Amenities</span>
                    <div id="viewPropertyAmenities" class="amenities-list"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" onclick="closePropertyViewModal()">
                    <i class="fas fa-times"></i> Close
                </button>
                <button class="btn btn-success" id="viewEditBtn">
                    <i class="fas fa-edit"></i> Edit Property
                </button>
            </div>
        </div>
    </div>

    <!-- Property Edit Modal -->
    <div id="propertyEditModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2>Edit Property</h2>
                <button class="modal-close" onclick="closePropertyEditModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="editPropertyForm">
                    <!-- Property Images Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-images"></i> Property Images
                        </h3>
                        
                        <div class="image-upload-container">
                            <div class="main-image-preview">
                                <img id="editMainImage" src="" alt="Main Property Image">
                                <div class="main-image-badge">Main Image</div>
                            </div>
                            
                            <div class="thumbnail-preview-grid" id="editThumbnailGrid">
                                <!-- Thumbnails will be added here dynamically -->
                            </div>
                            
                            <div class="image-upload-actions">
                                <label for="imageUploadInput" class="btn btn-secondary">
                                    <i class="fas fa-cloud-upload-alt"></i> Upload New Images
                                </label>
                                <input type="file" id="imageUploadInput" accept="image/*" multiple style="display: none;">
                                <p class="upload-hint">You can upload up to 5 images. First image will be the main image.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Basic Information Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-info-circle"></i> Basic Information
                        </h3>
                        
                        <div class="form-group">
                            <label for="editPropertyName">Property Title *</label>
                            <input type="text" id="editPropertyName" placeholder="e.g., Modern Family Flat in Gulshan" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="editPropertyAddress">Full Address *</label>
                            <input type="text" id="editPropertyAddress" placeholder="e.g., Gulshan, Dhaka" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="editPropertyType">Property Type *</label>
                                <select id="editPropertyType" required>
                                    <option value="">Select Type</option>
                                    <option value="Flat">Flat</option>
                                    <option value="Apartment">Apartment</option>
                                    <option value="House">House</option>
                                    <option value="Villa">Villa</option>
                                    <option value="Cabin">Cabin</option>
                                    <option value="Studio">Studio</option>
                                    <option value="Room">Single Room</option>
                                    <option value="Duplex">Duplex</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="editRenterType">Suitable For *</label>
                                <select id="editRenterType" required>
                                    <option value="">Select Renter Type</option>
                                    <option value="Family">Family</option>
                                    <option value="Bachelor">Bachelor</option>
                                    <option value="Student">Student</option>
                                    <option value="Professional">Professional</option>
                                    <option value="Any">Any</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="editPropertyStatus">Status *</label>
                                <select id="editPropertyStatus" required>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="editPropertyPrice">Rent per Month (৳) *</label>
                                <input type="number" id="editPropertyPrice" placeholder="e.g., 85000" required>
                            </div>
                        </div>
                    </div>

                    <!-- Property Details Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-home"></i> Property Details
                        </h3>
                        
                        <div class="form-row-three">
                            <div class="form-group">
                                <label for="editPropertyBedrooms">Bedrooms *</label>
                                <input type="number" id="editPropertyBedrooms" min="1" placeholder="e.g., 3" required>
                            </div>

                            <div class="form-group">
                                <label for="editPropertyBathrooms">Bathrooms *</label>
                                <input type="number" id="editPropertyBathrooms" min="1" placeholder="e.g., 3" required>
                            </div>

                            <div class="form-group">
                                <label for="editPropertyBalcony">Balconies</label>
                                <input type="number" id="editPropertyBalcony" min="0" placeholder="e.g., 2">
                            </div>
                        </div>

                        <div class="form-row-three">
                            <div class="form-group">
                                <label for="editPropertySize">Size (sqft) *</label>
                                <input type="number" id="editPropertySize" placeholder="e.g., 1800" required>
                            </div>

                            <div class="form-group">
                                <label for="editPropertyFloor">Floor Number</label>
                                <input type="text" id="editPropertyFloor" placeholder="e.g., 5th Floor">
                            </div>

                            <div class="form-group">
                                <label for="editPropertyFacing">Facing Direction</label>
                                <select id="editPropertyFacing">
                                    <option value="">Select Facing</option>
                                    <option value="North">North</option>
                                    <option value="South">South</option>
                                    <option value="East">East</option>
                                    <option value="West">West</option>
                                    <option value="North-East">North-East</option>
                                    <option value="North-West">North-West</option>
                                    <option value="South-East">South-East</option>
                                    <option value="South-West">South-West</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="editAvailableFrom">Available From</label>
                                <input type="date" id="editAvailableFrom">
                            </div>

                            <div class="form-group">
                                <label for="editPropertyGuests">Maximum Guests</label>
                                <input type="number" id="editPropertyGuests" min="1" placeholder="e.g., 6">
                            </div>
                        </div>
                    </div>

                    <!-- Description Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-align-left"></i> Description
                        </h3>
                        
                        <div class="form-group">
                            <label for="editPropertyDescription">Property Description *</label>
                            <textarea id="editPropertyDescription" rows="5" placeholder="Describe your property in detail..." required></textarea>
                            <small class="form-hint">Provide a detailed description of your property, including its features and nearby facilities.</small>
                        </div>
                    </div>

                    <!-- Amenities Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-check-circle"></i> Amenities & Features
                        </h3>
                        
                        <div class="amenities-checkboxes">
                            <label class="checkbox-label">
                                <input type="checkbox" value="High-speed Wifi" class="amenity-checkbox">
                                <i class="fas fa-wifi"></i> High-speed WiFi
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" value="Smart TV" class="amenity-checkbox">
                                <i class="fas fa-tv"></i> Smart TV
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" value="Modern Kitchen" class="amenity-checkbox">
                                <i class="fas fa-utensils"></i> Modern Kitchen
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" value="Air Conditioning" class="amenity-checkbox">
                                <i class="fas fa-fan"></i> Air Conditioning
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" value="Reserved Parking" class="amenity-checkbox">
                                <i class="fas fa-car"></i> Reserved Parking
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" value="24/7 Security" class="amenity-checkbox">
                                <i class="fas fa-shield-alt"></i> 24/7 Security
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" value="Elevator" class="amenity-checkbox">
                                <i class="fas fa-elevator"></i> Elevator
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" value="Generator Backup" class="amenity-checkbox">
                                <i class="fas fa-bolt"></i> Generator Backup
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" value="Hot & Cold Water" class="amenity-checkbox">
                                <i class="fas fa-water"></i> Hot & Cold Water
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" value="Rooftop Garden" class="amenity-checkbox">
                                <i class="fas fa-tree"></i> Rooftop Garden
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" value="Gym" class="amenity-checkbox">
                                <i class="fas fa-dumbbell"></i> Gym
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" value="Swimming Pool" class="amenity-checkbox">
                                <i class="fas fa-swimming-pool"></i> Swimming Pool
                            </label>
                        </div>
                    </div>

                    <!-- Location Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-map-marker-alt"></i> Location Details
                        </h3>
                        
                        <div class="form-group">
                            <label for="editMapUrl">Google Maps Embed URL (Optional)</label>
                            <input type="url" id="editMapUrl" placeholder="Paste Google Maps embed URL">
                            <small class="form-hint">Go to Google Maps, search your location, click Share → Embed a map, and copy the URL.</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" onclick="closePropertyEditModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button class="btn btn-success" onclick="savePropertyChanges()">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </div>
    </div>

    <!-- Add New Listing Modal -->
    <div id="addListingModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2><i class="fas fa-plus-circle"></i> Add New Property Listing</h2>
                <button class="modal-close" onclick="closeAddListingModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="addListingForm">
                    <!-- Property Images Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-images"></i> Property Images
                        </h3>
                        
                        <div class="image-upload-container">
                            <div class="main-image-preview" id="addMainImagePreview" style="display: none;">
                                <img id="addMainImage" src="" alt="Main Property Image">
                                <div class="main-image-badge">Main Image</div>
                            </div>
                            
                            <div class="thumbnail-preview-grid" id="addThumbnailGrid">
                                <!-- Thumbnails will be added here dynamically -->
                            </div>
                            
                            <div class="image-upload-actions">
                                <label for="addImageUploadInput" class="btn btn-secondary">
                                    <i class="fas fa-cloud-upload-alt"></i> Upload Property Images
                                </label>
                                <input type="file" id="addImageUploadInput" accept="image/*" multiple style="display: none;">
                                <p class="upload-hint">Upload 1-5 images. First image will be the main image.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Basic Information Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-info-circle"></i> Basic Information
                        </h3>
                        
                        <div class="form-group">
                            <label for="addPropertyName">Property Title *</label>
                            <input type="text" id="addPropertyName" placeholder="e.g., Modern Family Flat in Gulshan" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="addPropertyAddress">Full Address *</label>
                            <input type="text" id="addPropertyAddress" placeholder="e.g., Gulshan, Dhaka" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="addPropertyType">Property Type *</label>
                                <select id="addPropertyType" required>
                                    <option value="">Select Type</option>
                                    <option value="Flat">Flat</option>
                                    <option value="Apartment">Apartment</option>
                                    <option value="House">House</option>
                                    <option value="Villa">Villa</option>
                                    <option value="Cabin">Cabin</option>
                                    <option value="Studio">Studio</option>
                                    <option value="Room">Single Room</option>
                                    <option value="Duplex">Duplex</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="addRenterType">Suitable For *</label>
                                <select id="addRenterType" required>
                                    <option value="">Select Renter Type</option>
                                    <option value="Family">Family</option>
                                    <option value="Bachelor">Bachelor</option>
                                    <option value="Student">Student</option>
                                    <option value="Professional">Professional</option>
                                    <option value="Any">Any</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="addPropertyStatus">Status *</label>
                                <select id="addPropertyStatus" required>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="addPropertyPrice">Rent per Month (৳) *</label>
                                <input type="number" id="addPropertyPrice" placeholder="e.g., 85000" required>
                            </div>
                        </div>
                    </div>

                    <!-- Property Details Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-home"></i> Property Details
                        </h3>
                        
                        <div class="form-row-three">
                            <div class="form-group">
                                <label for="addPropertyBedrooms">Bedrooms *</label>
                                <input type="number" id="addPropertyBedrooms" min="1" placeholder="e.g., 3" required>
                            </div>

                            <div class="form-group">
                                <label for="addPropertyBathrooms">Bathrooms *</label>
                                <input type="number" id="addPropertyBathrooms" min="1" placeholder="e.g., 3" required>
                            </div>

                            <div class="form-group">
                                <label for="addPropertyBalcony">Balconies</label>
                                <input type="number" id="addPropertyBalcony" min="0" placeholder="e.g., 2">
                            </div>
                        </div>

                        <div class="form-row-three">
                            <div class="form-group">
                                <label for="addPropertySize">Size (sqft) *</label>
                                <input type="number" id="addPropertySize" placeholder="e.g., 1800" required>
                            </div>

                            <div class="form-group">
                                <label for="addPropertyFloor">Floor Number</label>
                                <input type="text" id="addPropertyFloor" placeholder="e.g., 5th Floor">
                            </div>

                            <div class="form-group">
                                <label for="addPropertyFacing">Facing Direction</label>
                                <select id="addPropertyFacing">
                                    <option value="">Select Facing</option>
                                    <option value="North">North</option>
                                    <option value="South">South</option>
                                    <option value="East">East</option>
                                    <option value="West">West</option>
                                    <option value="North-East">North-East</option>
                                    <option value="North-West">North-West</option>
                                    <option value="South-East">South-East</option>
                                    <option value="South-West">South-West</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="addAvailableFrom">Available From</label>
                                <input type="date" id="addAvailableFrom">
                            </div>

                            <div class="form-group">
                                <label for="addPropertyGuests">Maximum Guests</label>
                                <input type="number" id="addPropertyGuests" min="1" placeholder="e.g., 6">
                            </div>
                        </div>
                    </div>

                    <!-- Description Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-align-left"></i> Description
                        </h3>
                        
                        <div class="form-group">
                            <label for="addPropertyDescription">Property Description *</label>
                            <textarea id="addPropertyDescription" rows="5" placeholder="Describe your property in detail..." required></textarea>
                            <small class="form-hint">Provide a detailed description of your property, including its features and nearby facilities.</small>
                        </div>
                    </div>

                    <!-- Amenities Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-check-circle"></i> Amenities & Features
                        </h3>
                        
                        <div class="amenities-checkboxes">
                            <label class="checkbox-label">
                                <input type="checkbox" value="High-speed Wifi" class="add-amenity-checkbox">
                                <i class="fas fa-wifi"></i> High-speed WiFi
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" value="Smart TV" class="add-amenity-checkbox">
                                <i class="fas fa-tv"></i> Smart TV
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" value="Modern Kitchen" class="add-amenity-checkbox">
                                <i class="fas fa-utensils"></i> Modern Kitchen
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" value="Air Conditioning" class="add-amenity-checkbox">
                                <i class="fas fa-fan"></i> Air Conditioning
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" value="Reserved Parking" class="add-amenity-checkbox">
                                <i class="fas fa-car"></i> Reserved Parking
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" value="24/7 Security" class="add-amenity-checkbox">
                                <i class="fas fa-shield-alt"></i> 24/7 Security
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" value="Elevator" class="add-amenity-checkbox">
                                <i class="fas fa-elevator"></i> Elevator
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" value="Generator Backup" class="add-amenity-checkbox">
                                <i class="fas fa-bolt"></i> Generator Backup
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" value="Hot & Cold Water" class="add-amenity-checkbox">
                                <i class="fas fa-water"></i> Hot & Cold Water
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" value="Rooftop Garden" class="add-amenity-checkbox">
                                <i class="fas fa-tree"></i> Rooftop Garden
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" value="Gym" class="add-amenity-checkbox">
                                <i class="fas fa-dumbbell"></i> Gym
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" value="Swimming Pool" class="add-amenity-checkbox">
                                <i class="fas fa-swimming-pool"></i> Swimming Pool
                            </label>
                        </div>
                    </div>

                    <!-- Location Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-map-marker-alt"></i> Location Details
                        </h3>
                        
                        <div class="form-group">
                            <label for="addMapUrl">Google Maps Embed URL (Optional)</label>
                            <input type="url" id="addMapUrl" placeholder="Paste Google Maps embed URL">
                            <small class="form-hint">Go to Google Maps, search your location, click Share → Embed a map, and copy the URL.</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" onclick="closeAddListingModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button class="btn btn-success" onclick="submitNewListing()">
                    <i class="fas fa-plus-circle"></i> Add Property
                </button>
            </div>
        </div>
    </div>

    <!-- Review Reply Modal -->
    <div id="replyModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2>Reply to Review</h2>
                <button class="modal-close" onclick="closeReplyModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="review-info-display">
                    <h4>Review Details</h4>
                    <div class="review-display-grid">
                        <div class="detail-item">
                            <span class="detail-label">Guest</span>
                            <span class="detail-value" id="replyGuestName"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Property</span>
                            <span class="detail-value" id="replyPropertyName"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Rating</span>
                            <span class="detail-value" id="replyRating"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Date</span>
                            <span class="detail-value" id="replyDate"></span>
                        </div>
                    </div>
                    <div class="review-comment-display">
                        <span class="detail-label">Review Comment</span>
                        <p id="replyComment"></p>
                    </div>
                </div>

                <form id="replyForm">
                    <div class="form-group">
                        <label for="replyMessage">Your Reply *</label>
                        <textarea id="replyMessage" rows="5" placeholder="Write your reply to the guest..." required></textarea>
                        <small class="form-hint">Be professional and courteous in your response.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" onclick="closeReplyModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button class="btn btn-success" onclick="submitReply()">
                    <i class="fas fa-paper-plane"></i> Send Reply
                </button>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div id="passwordModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2>Change Password</h2>
                <button class="modal-close" onclick="closePasswordModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="passwordForm" onsubmit="changePassword(event)">
                    <div class="form-group">
                        <label>Current Password *</label>
                        <input type="password" id="currentPassword" required placeholder="Enter current password">
                    </div>
                    <div class="form-group">
                        <label>New Password *</label>
                        <input type="password" id="newPassword" required placeholder="Enter new password" minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password *</label>
                        <input type="password" id="confirmPassword" required placeholder="Confirm new password" minlength="6">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" onclick="closePasswordModal()">
                    <i class="fas fa-times-circle"></i> Cancel
                </button>
                <button class="btn btn-primary" onclick="document.getElementById('passwordForm').requestSubmit()">
                    <i class="fas fa-check-circle"></i> Update Password
                </button>
            </div>
        </div>
    </div>

    <script src="../js/script.js"></script>
    
    <script>
        // Navigation between sections
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-link');
            const sections = document.querySelectorAll('.content-section');

            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const targetSection = this.getAttribute('data-section');
                    
                    // Remove active class from all links
                    navLinks.forEach(l => l.classList.remove('active'));
                    
                    // Add active class to clicked link
                    this.classList.add('active');
                    
                    // Hide all sections
                    sections.forEach(section => section.classList.remove('active'));
                    
                    // Show target section
                    const target = document.getElementById(targetSection);
                    if (target) {
                        target.classList.add('active');
                        
                        // Scroll to top of content
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });

        // Logout function
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = '../api/logout.php';
            }
        }

        // Booking Modal Functions
        function openBookingModal(booking) {
            const modal = document.getElementById('bookingModal');
            
            // Populate modal with booking data
            document.getElementById('modalGuestAvatar').src = booking.guestAvatar;
            document.getElementById('modalGuestName').textContent = booking.guestName;
            document.getElementById('modalGuestEmail').textContent = booking.guestEmail;
            document.getElementById('modalGuestPhone').textContent = booking.guestPhone;
            document.getElementById('modalProperty').textContent = booking.property;
            document.getElementById('modalCheckin').textContent = booking.checkin;
            document.getElementById('modalCheckout').textContent = booking.checkout;
            document.getElementById('modalNights').textContent = booking.nights + ' nights';
            document.getElementById('modalPrice').textContent = booking.price;
            document.getElementById('modalGuests').textContent = booking.guests;
            document.getElementById('modalBookingDate').textContent = booking.bookingDate;
            document.getElementById('modalNotes').textContent = booking.notes;
            
            // Set status badge with appropriate class
            const statusElement = document.getElementById('modalStatus');
            statusElement.innerHTML = `<span class="status-badge status-${booking.status.toLowerCase()}">${booking.status}</span>`;
            
            // Update action button based on status
            const actionBtn = document.getElementById('modalActionBtn');
            if (booking.status === 'Pending') {
                actionBtn.innerHTML = '<i class="fas fa-check-circle"></i> Accept Booking';
                actionBtn.className = 'btn btn-success';
                actionBtn.style.display = 'inline-flex';
                actionBtn.onclick = function() {
                    acceptBooking(booking);
                };
            } else if (booking.status === 'Accepted') {
                actionBtn.innerHTML = '<i class="fas fa-times-circle"></i> Cancel Booking';
                actionBtn.className = 'btn btn-danger';
                actionBtn.style.display = 'inline-flex';
                actionBtn.onclick = function() {
                    cancelBooking(booking);
                };
            } else if (booking.status === 'Cancelled') {
                actionBtn.style.display = 'none';
            }
            
            // Show modal
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeBookingModal() {
            const modal = document.getElementById('bookingModal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.getElementById('bookingModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeBookingModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeBookingModal();
                closePropertyViewModal();
                closePropertyEditModal();
                closeAddListingModal();
                closeReplyModal();
            }
        });

        // Accept Booking Function
        function acceptBooking(booking) {
            if (confirm(`Accept booking from ${booking.guestName} for ${booking.property}?`)) {
                // Update status in modal
                const statusElement = document.getElementById('modalStatus');
                statusElement.innerHTML = '<span class="status-badge status-accepted">Accepted</span>';
                
                // Update action button
                const actionBtn = document.getElementById('modalActionBtn');
                actionBtn.innerHTML = '<i class="fas fa-times-circle"></i> Cancel Booking';
                actionBtn.className = 'btn btn-danger';
                actionBtn.onclick = function() {
                    cancelBooking(booking);
                };
                
                // Update booking object
                booking.status = 'Accepted';
                
                // Show success message
                alert('Booking has been accepted successfully!');
                
                // Here you would typically send this to your backend
                // updateBookingStatus(booking.id, 'Accepted');
            }
        }

        // Cancel Booking Function
        function cancelBooking(booking) {
            if (confirm(`Are you sure you want to cancel this booking from ${booking.guestName}?`)) {
                // Update status in modal
                const statusElement = document.getElementById('modalStatus');
                statusElement.innerHTML = '<span class="status-badge status-cancelled">Cancelled</span>';
                
                // Hide action button
                const actionBtn = document.getElementById('modalActionBtn');
                actionBtn.style.display = 'none';
                
                // Update booking object
                booking.status = 'Cancelled';
                
                // Show success message
                alert('Booking has been cancelled.');
                
                // Here you would typically send this to your backend
                // updateBookingStatus(booking.id, 'Cancelled');
            }
        }

        // Property View Modal Functions
        function openPropertyViewModal(property) {
            const modal = document.getElementById('propertyViewModal');
            
            // Populate modal with property data
            document.getElementById('viewPropertyImage').src = property.image;
            document.getElementById('viewPropertyName').textContent = property.name;
            document.getElementById('viewPropertyAddress').textContent = property.address;
            document.getElementById('viewPropertyType').textContent = property.type;
            document.getElementById('viewPropertyPrice').textContent = property.price + ' / night';
            document.getElementById('viewPropertyRating').textContent = property.rating;
            document.getElementById('viewPropertyBedrooms').textContent = property.bedrooms;
            document.getElementById('viewPropertyBathrooms').textContent = property.bathrooms;
            document.getElementById('viewPropertyGuests').textContent = property.guests;
            document.getElementById('viewPropertyBookings').textContent = property.bookings;
            document.getElementById('viewPropertyDescription').textContent = property.description;
            
            // Set status badge
            const statusElement = document.getElementById('viewPropertyStatus');
            const statusClass = property.status.toLowerCase() === 'active' ? 'status-accepted' : 'status-pending';
            statusElement.innerHTML = `<span class="status-badge ${statusClass}">${property.status}</span>`;
            
            // Populate amenities
            const amenitiesContainer = document.getElementById('viewPropertyAmenities');
            amenitiesContainer.innerHTML = '';
            property.amenities.forEach(amenity => {
                const amenityTag = document.createElement('span');
                amenityTag.className = 'amenity-tag';
                amenityTag.innerHTML = `<i class="fas fa-check"></i> ${amenity}`;
                amenitiesContainer.appendChild(amenityTag);
            });
            
            // Set Edit button functionality
            document.getElementById('viewEditBtn').onclick = function() {
                closePropertyViewModal();
                openPropertyEditModal(property);
            };
            
            // Show modal
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closePropertyViewModal() {
            const modal = document.getElementById('propertyViewModal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Property Edit Modal Functions
        let currentEditingProperty = null;
        let propertyImages = [];

        function openPropertyEditModal(property) {
            const modal = document.getElementById('propertyEditModal');
            currentEditingProperty = property;
            
            // Initialize property images
            propertyImages = property.images || [
                property.image || 'https://images.pexels.com/photos/186077/pexels-photo-186077.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
                'https://images.pexels.com/photos/271624/pexels-photo-271624.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
                'https://images.pexels.com/photos/5998120/pexels-photo-5998120.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
                'https://images.pexels.com/photos/210617/pexels-photo-210617.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1'
            ];
            
            // Populate basic fields
            document.getElementById('editPropertyName').value = property.name;
            document.getElementById('editPropertyAddress').value = property.address;
            document.getElementById('editPropertyType').value = property.type;
            document.getElementById('editPropertyStatus').value = property.status;
            document.getElementById('editPropertyPrice').value = property.price;
            document.getElementById('editPropertyBedrooms').value = property.bedrooms;
            document.getElementById('editPropertyBathrooms').value = property.bathrooms;
            document.getElementById('editPropertyDescription').value = property.description;
            
            // Populate additional fields (with defaults if not present)
            document.getElementById('editRenterType').value = property.renterType || 'Family';
            document.getElementById('editPropertyGuests').value = property.guests || property.bedrooms * 2;
            document.getElementById('editPropertySize').value = property.size || 1800;
            document.getElementById('editPropertyBalcony').value = property.balcony || 2;
            document.getElementById('editPropertyFloor').value = property.floor || '5th Floor';
            document.getElementById('editPropertyFacing').value = property.facing || 'South';
            document.getElementById('editAvailableFrom').value = property.availableFrom || '';
            document.getElementById('editMapUrl').value = property.mapUrl || '';
            
            // Set amenities checkboxes
            const checkboxes = document.querySelectorAll('.amenity-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = property.amenities.includes(checkbox.value);
            });
            
            // Display images
            displayPropertyImages();
            
            // Show modal
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function displayPropertyImages() {
            // Display main image
            const mainImage = document.getElementById('editMainImage');
            mainImage.src = propertyImages[0] || 'https://via.placeholder.com/800x400?text=No+Image';
            
            // Display thumbnails
            const thumbnailGrid = document.getElementById('editThumbnailGrid');
            thumbnailGrid.innerHTML = '';
            
            propertyImages.slice(1).forEach((imgSrc, index) => {
                const thumbItem = document.createElement('div');
                thumbItem.className = 'thumbnail-preview-item';
                
                const img = document.createElement('img');
                img.src = imgSrc;
                img.alt = `Property Image ${index + 2}`;
                
                const removeBtn = document.createElement('button');
                removeBtn.className = 'thumbnail-remove-btn';
                removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                removeBtn.type = 'button';
                removeBtn.onclick = function() {
                    removePropertyImage(index + 1);
                };
                
                const setMainBtn = document.createElement('button');
                setMainBtn.className = 'thumbnail-set-main-btn';
                setMainBtn.textContent = 'Set as Main';
                setMainBtn.type = 'button';
                setMainBtn.onclick = function() {
                    setAsMainImage(index + 1);
                };
                
                thumbItem.appendChild(img);
                thumbItem.appendChild(removeBtn);
                thumbItem.appendChild(setMainBtn);
                thumbnailGrid.appendChild(thumbItem);
            });
        }

        function removePropertyImage(index) {
            if (propertyImages.length <= 1) {
                alert('You must have at least one image!');
                return;
            }
            
            if (confirm('Are you sure you want to remove this image?')) {
                propertyImages.splice(index, 1);
                displayPropertyImages();
            }
        }

        function setAsMainImage(index) {
            const selectedImage = propertyImages[index];
            propertyImages.splice(index, 1);
            propertyImages.unshift(selectedImage);
            displayPropertyImages();
        }

        // Handle image upload
        document.getElementById('imageUploadInput')?.addEventListener('change', function(e) {
            const files = e.target.files;
            
            if (propertyImages.length + files.length > 5) {
                alert('You can upload a maximum of 5 images!');
                return;
            }
            
            // In a real application, you would upload these to a server
            // For demo purposes, we'll create temporary URLs
            for (let i = 0; i < files.length; i++) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    propertyImages.push(event.target.result);
                    displayPropertyImages();
                };
                reader.readAsDataURL(files[i]);
            }
            
            // Clear the input
            e.target.value = '';
        });

        function closePropertyEditModal() {
            const modal = document.getElementById('propertyEditModal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
            currentEditingProperty = null;
            propertyImages = [];
        }

        function savePropertyChanges() {
            // Get all form values
            const updatedProperty = {
                id: currentEditingProperty.id,
                name: document.getElementById('editPropertyName').value,
                address: document.getElementById('editPropertyAddress').value,
                type: document.getElementById('editPropertyType').value,
                renterType: document.getElementById('editRenterType').value,
                status: document.getElementById('editPropertyStatus').value,
                price: document.getElementById('editPropertyPrice').value,
                bedrooms: document.getElementById('editPropertyBedrooms').value,
                bathrooms: document.getElementById('editPropertyBathrooms').value,
                balcony: document.getElementById('editPropertyBalcony').value,
                size: document.getElementById('editPropertySize').value,
                floor: document.getElementById('editPropertyFloor').value,
                facing: document.getElementById('editPropertyFacing').value,
                guests: document.getElementById('editPropertyGuests').value,
                availableFrom: document.getElementById('editAvailableFrom').value,
                description: document.getElementById('editPropertyDescription').value,
                mapUrl: document.getElementById('editMapUrl').value,
                images: propertyImages
            };
            
            // Get selected amenities
            const selectedAmenities = [];
            document.querySelectorAll('.amenity-checkbox:checked').forEach(checkbox => {
                selectedAmenities.push(checkbox.value);
            });
            updatedProperty.amenities = selectedAmenities;
            
            // Validate required fields
            if (!updatedProperty.name || !updatedProperty.address || !updatedProperty.price || 
                !updatedProperty.bedrooms || !updatedProperty.bathrooms || !updatedProperty.description) {
                alert('Please fill in all required fields!');
                return;
            }
            
            if (propertyImages.length === 0) {
                alert('Please add at least one image!');
                return;
            }
            
            // In a real application, you would send this data to a server
            console.log('Saving property:', updatedProperty);
            
            alert('Property updated successfully! ✓\n\nUpdated Details:\n' +
                  `• ${updatedProperty.name}\n` +
                  `• ${updatedProperty.type} for ${updatedProperty.renterType}\n` +
                  `• ৳${updatedProperty.price}/month\n` +
                  `• ${updatedProperty.bedrooms} Beds, ${updatedProperty.bathrooms} Baths\n` +
                  `• ${updatedProperty.size} sqft\n` +
                  `• ${propertyImages.length} images uploaded`);
            
            closePropertyEditModal();
        }

        // Close modals when clicking outside
        document.getElementById('propertyViewModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closePropertyViewModal();
            }
        });

        document.getElementById('propertyEditModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closePropertyEditModal();
            }
        });

        // Edit button in view modal
        document.getElementById('viewEditBtn')?.addEventListener('click', function() {
            closePropertyViewModal();
            // You can add logic here to open edit modal with the same property data
        });

        // ===== Add New Listing Modal Functions =====
        let addListingImages = [];

        function openAddListingModal() {
            const modal = document.getElementById('addListingModal');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            // Reset form
            document.getElementById('addListingForm').reset();
            addListingImages = [];
            document.getElementById('addMainImagePreview').style.display = 'none';
            document.getElementById('addThumbnailGrid').innerHTML = '';
        }

        function closeAddListingModal() {
            const modal = document.getElementById('addListingModal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
            addListingImages = [];
        }

        // Image upload handling for Add Listing
        document.getElementById('addImageUploadInput')?.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            
            if (addListingImages.length + files.length > 5) {
                alert('You can only upload up to 5 images!');
                return;
            }
            
            files.forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        addListingImages.push(e.target.result);
                        displayAddListingImages();
                    };
                    reader.readAsDataURL(file);
                }
            });
            
            e.target.value = '';
        });

        function displayAddListingImages() {
            const mainPreview = document.getElementById('addMainImagePreview');
            const mainImage = document.getElementById('addMainImage');
            const thumbnailGrid = document.getElementById('addThumbnailGrid');
            
            if (addListingImages.length === 0) {
                mainPreview.style.display = 'none';
                thumbnailGrid.innerHTML = '';
                return;
            }
            
            // Show first image as main
            mainPreview.style.display = 'block';
            mainImage.src = addListingImages[0];
            
            // Display all images as thumbnails
            thumbnailGrid.innerHTML = '';
            addListingImages.forEach((img, index) => {
                const thumbDiv = document.createElement('div');
                thumbDiv.className = 'image-thumbnail';
                if (index === 0) thumbDiv.classList.add('main');
                
                thumbDiv.innerHTML = `
                    <img src="${img}" alt="Property Image ${index + 1}">
                    <button type="button" class="remove-image-btn" onclick="removeAddListingImage(${index})" title="Remove Image">
                        <i class="fas fa-times"></i>
                    </button>
                    ${index === 0 ? '<span class="main-badge">Main</span>' : `
                        <button type="button" class="set-main-btn" onclick="setAddListingMain(${index})" title="Set as Main">
                            <i class="fas fa-star"></i>
                        </button>
                    `}
                `;
                
                thumbnailGrid.appendChild(thumbDiv);
            });
        }

        function removeAddListingImage(index) {
            addListingImages.splice(index, 1);
            displayAddListingImages();
        }

        function setAddListingMain(index) {
            const mainImage = addListingImages[index];
            addListingImages.splice(index, 1);
            addListingImages.unshift(mainImage);
            displayAddListingImages();
        }

        function submitNewListing() {
            // Get all form values
            const newListing = {
                name: document.getElementById('addPropertyName').value,
                address: document.getElementById('addPropertyAddress').value,
                type: document.getElementById('addPropertyType').value,
                renterType: document.getElementById('addRenterType').value,
                status: document.getElementById('addPropertyStatus').value,
                price: document.getElementById('addPropertyPrice').value,
                bedrooms: document.getElementById('addPropertyBedrooms').value,
                bathrooms: document.getElementById('addPropertyBathrooms').value,
                balcony: document.getElementById('addPropertyBalcony').value,
                size: document.getElementById('addPropertySize').value,
                floor: document.getElementById('addPropertyFloor').value,
                facing: document.getElementById('addPropertyFacing').value,
                guests: document.getElementById('addPropertyGuests').value,
                availableFrom: document.getElementById('addAvailableFrom').value,
                description: document.getElementById('addPropertyDescription').value,
                mapUrl: document.getElementById('addMapUrl').value,
                images: addListingImages
            };
            
            // Get selected amenities
            const selectedAmenities = [];
            document.querySelectorAll('.add-amenity-checkbox:checked').forEach(checkbox => {
                selectedAmenities.push(checkbox.value);
            });
            newListing.amenities = selectedAmenities;
            
            // Validate required fields
            if (!newListing.name || !newListing.address || !newListing.type || 
                !newListing.renterType || !newListing.price || !newListing.bedrooms || 
                !newListing.bathrooms || !newListing.size || !newListing.description) {
                alert('Please fill in all required fields marked with *');
                return;
            }
            
            if (addListingImages.length === 0) {
                alert('Please upload at least one image!');
                return;
            }
            
            // In a real application, you would send this data to a server
            console.log('Adding new listing:', newListing);
            
            alert('Property added successfully! ✓\n\nNew Listing:\n' +
                  `• ${newListing.name}\n` +
                  `• ${newListing.type} for ${newListing.renterType}\n` +
                  `• ${newListing.address}\n` +
                  `• ৳${newListing.price}/month\n` +
                  `• ${newListing.bedrooms} Beds, ${newListing.bathrooms} Baths\n` +
                  `• ${newListing.size} sqft\n` +
                  `• ${addListingImages.length} images uploaded\n` +
                  `• Status: ${newListing.status}`);
            
            closeAddListingModal();
        }

        // Close Add Listing modal when clicking outside
        document.getElementById('addListingModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddListingModal();
            }
        });

        // ===== Review Reply Modal Functions =====
        let currentReview = null;

        function openReplyModal(reviewData) {
            const modal = document.getElementById('replyModal');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            currentReview = reviewData;
            
            // Populate review details
            document.getElementById('replyGuestName').textContent = reviewData.guest;
            document.getElementById('replyPropertyName').textContent = reviewData.property;
            document.getElementById('replyDate').textContent = reviewData.date;
            document.getElementById('replyComment').textContent = reviewData.comment;
            
            // Display rating stars
            const ratingHtml = generateStars(reviewData.rating);
            document.getElementById('replyRating').innerHTML = ratingHtml;
            
            // Reset reply textarea
            document.getElementById('replyMessage').value = '';
        }

        function closeReplyModal() {
            const modal = document.getElementById('replyModal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
            currentReview = null;
        }

        function generateStars(rating) {
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= rating) {
                    stars += '<i class="fas fa-star" style="color: #f1c40f;"></i> ';
                } else {
                    stars += '<i class="far fa-star" style="color: #f1c40f;"></i> ';
                }
            }
            return stars;
        }

        function submitReply() {
            const replyMessage = document.getElementById('replyMessage').value.trim();
            
            if (!replyMessage) {
                alert('Please write a reply message!');
                return;
            }
            
            // In a real application, you would send this data to a server
            console.log('Submitting reply:', {
                review: currentReview,
                reply: replyMessage,
                timestamp: new Date().toISOString()
            });
            
            alert('Reply sent successfully! ✓\n\nYour reply to ' + currentReview.guest + 
                  ' has been sent.\n\nReply: "' + replyMessage + '"');
            
            closeReplyModal();
        }

        // Close Reply modal when clicking outside
        document.getElementById('replyModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeReplyModal();
            }
        });

        // Close Property View modal when clicking outside
        document.getElementById('propertyViewModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closePropertyViewModal();
            }
        });

        // Close Property Edit modal when clicking outside
        document.getElementById('propertyEditModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closePropertyEditModal();
            }
        });

        // Close Add Listing modal when clicking outside
        document.getElementById('addListingModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddListingModal();
            }
        });

        // ===== Settings Functions =====
        
        // Save Settings Form
        async function saveSettings(event) {
            event.preventDefault();
            
            const formData = new FormData();
            formData.append('first_name', document.getElementById('firstName').value);
            formData.append('last_name', document.getElementById('lastName').value);
            formData.append('email', document.getElementById('email').value);
            formData.append('phone', document.getElementById('phone').value);
            formData.append('date_of_birth', document.getElementById('dateOfBirth').value);
            formData.append('address', document.getElementById('address').value);
            formData.append('city', document.getElementById('city').value);
            formData.append('postal_code', document.getElementById('postalCode').value);

            try {
                const response = await fetch('../api/update_profile.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert('✓ Profile updated successfully!');
                    // Update the displayed name in sidebar
                    const fullName = document.getElementById('firstName').value + ' ' + document.getElementById('lastName').value;
                    document.querySelector('.sidebar-profile h3').textContent = fullName;
                    
                    // Refresh the page to show updated values
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Update error:', error);
                alert('Failed to update profile. Please try again.');
            }
        }

        // Change Photo Function
        let selectedPhotoFile = null;

        function previewPhoto(event) {
            const file = event.target.files[0];
            if (!file) return;

            selectedPhotoFile = file;

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('Invalid file type. Please select a JPG, PNG, GIF, or WebP image.');
                event.target.value = ''; // Clear the input
                selectedPhotoFile = null;
                return;
            }

            // Validate file size (5MB max)
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                alert('File too large. Maximum size is 5MB.');
                event.target.value = ''; // Clear the input
                selectedPhotoFile = null;
                return;
            }

            // Show preview
            const profileImg = document.getElementById('profileImage');
            const reader = new FileReader();
            reader.onload = function(e) {
                profileImg.src = e.target.result;
            };
            reader.readAsDataURL(file);

            // Show upload button
            document.getElementById('uploadPhotoBtn').style.display = 'inline-block';
        }

        async function uploadPhoto() {
            if (!selectedPhotoFile) {
                alert('Please select a photo first.');
                return;
            }

            const profileImg = document.getElementById('profileImage');
            const sidebarImg = document.querySelector('.sidebar-profile img');
            const originalSrc = profileImg.src;

            // Show loading state
            const uploadBtn = document.getElementById('uploadPhotoBtn');
            const originalText = uploadBtn.innerHTML;
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';

            try {
                const formData = new FormData();
                formData.append('profile_image', selectedPhotoFile);

                const response = await fetch('../api/upload_profile_photo.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert('✓ ' + result.message);
                    // Update with server URL
                    const serverUrl = '../' + result.image_url.substring(1);
                    profileImg.src = serverUrl;
                    if (sidebarImg) {
                        sidebarImg.src = serverUrl;
                    }
                    // Hide upload button
                    uploadBtn.style.display = 'none';
                    selectedPhotoFile = null;
                } else {
                    alert('Error: ' + result.message);
                    // Restore original image on error
                    profileImg.src = originalSrc;
                    if (sidebarImg) {
                        sidebarImg.src = originalSrc;
                    }
                }
            } catch (error) {
                console.error('Upload error:', error);
                alert('Failed to upload photo. Please try again.');
                // Restore original image on error
                profileImg.src = originalSrc;
                if (sidebarImg) {
                    sidebarImg.src = originalSrc;
                }
            } finally {
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = originalText;
            }

            // Clear the input so the same file can be selected again
            document.getElementById('profileImageInput').value = '';
        }

        // Password Modal Functions
        function openPasswordModal() {
            document.getElementById('passwordModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.remove('active');
            document.body.style.overflow = 'auto';
            document.getElementById('passwordForm').reset();
        }

        async function changePassword(event) {
            event.preventDefault();
            
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            // Client-side validation
            if (!currentPassword || !newPassword || !confirmPassword) {
                alert('Please fill in all fields!');
                return;
            }
            
            if (newPassword !== confirmPassword) {
                alert('New passwords do not match!');
                return;
            }
            
            if (newPassword.length < 8) {
                alert('Password must be at least 8 characters long!');
                return;
            }
            
            // Password strength validation - must contain at least one number
            const hasNumber = /[0-9]/.test(newPassword);

            if (!hasNumber) {
                alert('Password must contain at least one number');
                return;
            }
            
            // Show loading state
            const submitBtn = event.target.querySelector('button[type="submit"]');
            if (!submitBtn) {
                // Fallback if button not found
                const modalFooterBtn = document.querySelector('#passwordModal .btn-primary');
                if (modalFooterBtn) {
                    submitBtn = modalFooterBtn;
                }
            }
            
            let originalText = '';
            if (submitBtn) {
                originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Changing Password...';
            }
            
            try {
                const response = await fetch('../api/change_password.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        current_password: currentPassword,
                        new_password: newPassword,
                        confirm_password: confirmPassword
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Password changed successfully! ✓\n\nYou will be logged out for security reasons.');
                    closePasswordModal();
                    
                    // Log out the user
                    setTimeout(() => {
                        window.location.href = '../api/logout.php';
                    }, 1000);
                } else {
                    alert('Error: ' + result.message);
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while changing password. Please try again.');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            }
        }

        // Close password modal when clicking outside
        document.getElementById('passwordModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closePasswordModal();
            }
        });

        // Mobile Menu Toggle Functions
        function toggleSidebar() {
            const sidebar = document.getElementById('landlordSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('mobile-active');
            overlay.classList.toggle('active');
        }

        function closeSidebar() {
            const sidebar = document.getElementById('landlordSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.remove('mobile-active');
            overlay.classList.remove('active');
        }

        // Close sidebar when clicking a nav link on mobile
        document.querySelectorAll('.sidebar-nav a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 992) {
                    closeSidebar();
                }
            });
        });
    </script>
</body>
</html>




