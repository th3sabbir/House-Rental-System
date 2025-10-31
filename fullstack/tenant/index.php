<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';

// Prevent browser caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Check if user is logged in and is a tenant
if (!isLoggedIn() || $_SESSION['role'] !== 'tenant') {
    header('Location: ../login.php');
    exit();
}

// Initialize database connection
$db = new Database();
$pdo = $db->connect();
global $pdo; // Make $pdo available globally for helper functions

// Get tenant information
$user_id = $_SESSION['user_id'];
$user = getUserById($user_id);
$tenant_name = $user['full_name'] ?? 'Tenant';

// Parse name into first and last name
$name_parts = explode(' ', $tenant_name, 2);
$first_name = $name_parts[0] ?? '';
$last_name = $name_parts[1] ?? '';

// Get user data for form population
$user_email = $user['email'] ?? '';
$user_phone = $user['phone'] ?? '';
$user_address = $user['address'] ?? '';
$user_city = $user['city'] ?? '';
$user_postal_code = $user['postal_code'] ?? '';
$user_date_of_birth = $user['date_of_birth'] ?? '';

// Get tenant's bookings
try {
    $stmt = $pdo->prepare("
        SELECT
            b.booking_id,
            b.check_in_date,
            b.check_out_date,
            b.guests,
            b.total_price,
            b.status,
            b.message,
            b.created_at,
            p.title as property_title,
            p.address,
            p.main_image,
            u.full_name as landlord_name,
            u.email as landlord_email,
            u.phone as landlord_phone
        FROM bookings b
        JOIN properties p ON b.property_id = p.property_id
        JOIN users u ON p.landlord_id = u.user_id
        WHERE b.tenant_id = ?
        ORDER BY b.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $bookings = $stmt->fetchAll();
} catch (PDOException $e) {
    $bookings = [];
}

// Get booking statistics
try {
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_bookings,
            COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_bookings,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_bookings,
            COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_bookings
        FROM bookings 
        WHERE tenant_id = ?
    ");
    $stmt->execute([$user_id]);
    $stats = $stmt->fetch();
    
    $total_bookings = $stats['total_bookings'] ?? 0;
    $approved_bookings = $stats['approved_bookings'] ?? 0;
    $pending_bookings = $stats['pending_bookings'] ?? 0;
    $rejected_bookings = $stats['rejected_bookings'] ?? 0;
} catch (PDOException $e) {
    $total_bookings = 0;
    $approved_bookings = 0;
    $pending_bookings = 0;
    $rejected_bookings = 0;
}

// Get tenant's reviews
try {
    $stmt = $pdo->prepare("
        SELECT 
            r.review_id,
            r.rating,
            r.review_text,
            r.created_at,
            r.landlord_response,
            p.title as property_title,
            p.address,
            p.main_image as property_image
        FROM reviews r
        JOIN properties p ON r.property_id = p.property_id
        WHERE r.tenant_id = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $reviews = $stmt->fetchAll();
} catch (PDOException $e) {
    $reviews = [];
}

// Get profile image URL
if (!empty($user['profile_image']) && file_exists('../uploads/' . $user['profile_image'])) {
    $profile_image_url = '../uploads/' . $user['profile_image'];
} else {
    // Use UI Avatars as fallback
    $profile_image_url = 'https://ui-avatars.com/api/?name=' . urlencode($tenant_name) . '&background=1abc9c&color=fff&size=160';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Dashboard - House Rental System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1abc9c;
            --secondary-color: #16a085;
            --accent-color: #3498db;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
            --text-dark: #34495e;
            --text-medium: #7f8c8d;
            --background-white: #ffffff;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --success-color: #27ae60;
            --shadow-light: 0 2px 8px rgba(0,0,0,0.1);
            --shadow-medium: 0 4px 16px rgba(0,0,0,0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: var(--text-dark);
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
            background: linear-gradient(135deg, var(--dark-color) 0%, var(--text-dark) 100%);
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
            background: var(--secondary-color);
            border-radius: 10px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: var(--accent-color);
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
            border: 4px solid var(--primary-color);
            margin-bottom: 15px;
            object-fit: cover;
        }

        .sidebar-profile h3 {
            font-size: 1.3rem;
            margin-bottom: 5px;
        }

        .sidebar-profile p {
            color: var(--light-color);
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
            border-left: 4px solid var(--primary-color);
            padding-left: 26px;
        }

        .sidebar-nav li a.active {
            background-color: rgba(26, 188, 156, 0.3);
            border-left: 4px solid var(--primary-color);
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
            animation: fadeIn 0.5s ease-in;
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
            color: var(--dark-color);
            margin-bottom: 10px;
        }

        .welcome-message {
            font-size: 1.1rem;
            color: var(--text-medium);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: var(--shadow-light);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
        }

        .stat-card i {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .stat-card h3 {
            font-size: 2rem;
            color: var(--dark-color);
            margin-bottom: 5px;
        }

        .stat-card p {
            color: var(--text-medium);
            font-size: 0.95rem;
        }

        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow-light);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .table-header {
            padding: 20px 30px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h2 {
            font-size: 1.5rem;
            display: flex;
            align-items: center;
        }

        .table-header h2 i {
            margin-right: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #f8f9fa;
        }

        thead th {
            padding: 18px 20px;
            text-align: left;
            font-weight: 600;
            color: var(--text-dark);
            border-bottom: 2px solid #dee2e6;
        }

        tbody tr {
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.3s ease;
        }

        tbody tr:hover {
            background-color: #f8f9fa;
        }

        tbody td {
            padding: 20px;
        }

        .property-info {
            display: flex;
            align-items: center;
        }

        .property-img {
            width: 80px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 15px;
        }

        .property-details h4 {
            color: var(--dark-color);
            margin-bottom: 5px;
            font-size: 1rem;
        }

        .property-details p {
            color: var(--text-medium);
            font-size: 0.85rem;
        }

        .status-badge {
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-completed {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
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

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: var(--accent-color);
            color: white;
        }

        .btn-secondary:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }

        /* Property Cards Grid */
        .property-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-bottom: 30px;
        }

        .property-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-light);
            transition: all 0.3s ease;
            position: relative;
        }

        .property-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-medium);
        }

        .property-card-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        .property-card-content {
            padding: 20px;
        }

        .property-card-title {
            font-size: 1.2rem;
            color: var(--dark-color);
            margin-bottom: 10px;
        }

        .property-card-location {
            color: var(--text-medium);
            font-size: 0.9rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .property-card-location i {
            margin-right: 5px;
            color: var(--primary-color);
        }

        .property-card-price {
            font-size: 1.5rem;
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 15px;
        }

        .property-card-features {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: var(--text-medium);
        }

        .property-card-features span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .property-card-actions {
            display: flex;
            gap: 10px;
        }

        .favorite-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-light);
        }

        .favorite-btn i {
            color: var(--danger-color);
            font-size: 1.2rem;
        }

        .favorite-btn:hover {
            transform: scale(1.1);
        }

        /* Reviews Section */
        .review-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-light);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .review-property {
            display: flex;
            align-items: center;
        }

        .review-property img {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 15px;
        }

        .review-rating {
            color: #f39c12;
            font-size: 1.2rem;
        }

        .review-content {
            color: var(--text-dark);
            line-height: 1.8;
            margin-bottom: 15px;
        }

        .review-date {
            color: var(--text-medium);
            font-size: 0.85rem;
        }

        /* Messages Section */
        .message-container {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 20px;
            height: 600px;
        }

        .message-list {
            background: white;
            border-radius: 12px;
            overflow-y: auto;
            box-shadow: var(--shadow-light);
        }

        .message-item {
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .message-item:hover {
            background-color: #f8f9fa;
        }

        .message-item.active {
            background-color: #e8f5f1;
            border-left: 4px solid var(--primary-color);
        }

        .message-sender {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .message-sender img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-right: 12px;
        }

        .message-sender-info h4 {
            font-size: 1rem;
            color: var(--dark-color);
            margin-bottom: 3px;
        }

        .message-sender-info p {
            font-size: 0.8rem;
            color: var(--text-medium);
        }

        .message-preview {
            color: var(--text-medium);
            font-size: 0.9rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .message-chat {
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow-light);
            display: flex;
            flex-direction: column;
        }

        .message-chat-header {
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
        }

        .message-chat-header img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .message-chat-body {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .chat-message {
            margin-bottom: 20px;
            display: flex;
        }

        .chat-message.sent {
            justify-content: flex-end;
        }

        .chat-bubble {
            max-width: 70%;
            padding: 12px 18px;
            border-radius: 18px;
            background-color: #e9ecef;
            color: var(--text-dark);
        }

        .chat-message.sent .chat-bubble {
            background-color: var(--primary-color);
            color: white;
        }

        .chat-time {
            font-size: 0.75rem;
            color: var(--text-medium);
            margin-top: 5px;
        }

        .message-chat-footer {
            padding: 20px;
            border-top: 1px solid #e9ecef;
        }

        .message-input-container {
            display: flex;
            gap: 10px;
        }

        .message-input-container input {
            flex: 1;
            padding: 12px 20px;
            border: 1px solid #dee2e6;
            border-radius: 50px;
            font-size: 0.95rem;
        }

        .message-input-container button {
            padding: 12px 24px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .message-input-container button:hover {
            background-color: var(--secondary-color);
        }

        /* Profile Settings */
        .settings-container {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: var(--shadow-light);
            max-width: 800px;
        }

        .form-group {
            margin-bottom: 25px;
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
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
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
            border: 4px solid var(--primary-color);
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

        .settings-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-medium);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--text-dark);
        }

        .empty-state p {
            font-size: 1rem;
            margin-bottom: 20px;
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 700px;
            max-height: 90vh;
            overflow: hidden;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            padding: 25px 30px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            font-size: 1.5rem;
        }

        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background-color 0.3s ease;
        }

        .modal-close:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .modal-body {
            padding: 30px;
            overflow-y: auto;
            max-height: calc(90vh - 160px);
        }

        .modal-body::-webkit-scrollbar {
            width: 6px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: var(--secondary-color);
            border-radius: 10px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover {
            background: var(--accent-color);
        }

        .modal-footer {
            padding: 20px 30px;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .modal-footer .btn {
            min-width: 150px;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.95rem;
        }

        /* Booking Detail in Modal */
        .booking-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .detail-item {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .detail-label {
            display: block;
            font-size: 0.85rem;
            color: var(--text-medium);
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 1.1rem;
            color: var(--text-dark);
            font-weight: 600;
        }

        .property-view-image {
            width: 100%;
            height: 300px;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .property-view-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .property-view-info {
            margin-bottom: 20px;
        }

        .property-view-info h3 {
            font-size: 1.8rem;
            color: var(--dark-color);
            margin-bottom: 10px;
        }

        .property-view-info p {
            color: var(--text-medium);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .property-view-info p i {
            color: var(--primary-color);
        }

        .amenities-list {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .amenity-tag {
            padding: 8px 15px;
            background: linear-gradient(135deg, #e8f5f1, #d4edda);
            border-radius: 50px;
            font-size: 0.85rem;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .amenity-tag i {
            color: var(--primary-color);
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .property-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .booking-detail-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .dashboard-header h1 {
                font-size: 1.8rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .property-grid {
                grid-template-columns: 1fr;
            }

            .table-container {
                overflow-x: auto;
            }

            table {
                min-width: 800px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .amenities-list {
                grid-template-columns: 1fr;
            }

            .modal {
                width: 95%;
                max-width: none;
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

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            /* Message/Chat Responsiveness */
            .message-container {
                grid-template-columns: 1fr;
                height: 550px;
            }

            .message-list {
                max-height: 200px;
            }

            .message-item {
                padding: 15px;
            }

            .message-sender img {
                width: 35px;
                height: 35px;
            }

            /* Settings Form */
            .settings-container {
                padding: 25px;
            }

            .profile-picture-upload img {
                width: 100px;
                height: 100px;
            }
        }

        @media (max-width: 576px) {
            .dashboard-content {
                padding: 15px;
            }

            .dashboard-header h1 {
                font-size: 1.5rem;
            }

            .welcome-message {
                font-size: 0.95rem;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-card h3 {
                font-size: 1.5rem;
            }

            .property-card-actions {
                flex-direction: column;
            }

            .settings-container {
                padding: 20px;
            }

            .settings-actions {
                flex-direction: column;
                gap: 10px;
            }

            .settings-actions .btn {
                width: 100%;
            }

            .profile-picture-upload {
                flex-direction: column;
                text-align: center;
            }

            .modal-body {
                padding: 20px;
            }

            .modal-footer .btn {
                min-width: 100px;
                padding: 10px 16px;
                font-size: 0.85rem;
            }

            .modal-footer {
                flex-direction: column;
                gap: 10px;
            }

            .modal-footer .btn {
                width: 100%;
                min-width: auto;
            }

            /* Message/Chat Responsiveness */
            .message-container {
                grid-template-columns: 1fr;
                height: 500px;
            }

            .message-chat-header {
                padding: 15px;
            }

            .message-chat-header img {
                width: 40px;
                height: 40px;
            }

            .message-chat-body {
                padding: 15px;
            }

            .chat-bubble {
                max-width: 85%;
                padding: 10px 15px;
                font-size: 0.9rem;
            }

            .message-input-container input {
                padding: 10px 15px;
                font-size: 0.9rem;
            }

            .message-input-container button {
                padding: 10px 20px;
            }

            /* Form Responsiveness */
            .form-group input,
            .form-group select,
            .form-group textarea {
                font-size: 16px; /* Prevents zoom on iOS */
            }

            /* Review Cards */
            .review-card {
                padding: 20px;
            }

            .review-property img {
                width: 50px;
                height: 50px;
            }

            /* Mobile Menu */
            .mobile-menu-toggle {
                width: 50px !important;
                height: 50px !important;
                font-size: 20px !important;
                bottom: 15px !important;
                right: 15px !important;
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

            .stat-card {
                padding: 15px;
            }

            .settings-container {
                padding: 15px;
                margin: 0 5px;
            }

            .message-container {
                height: 450px;
            }
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

        /* Responsive Styles */
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
                background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
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
                grid-template-columns: 1fr 1fr !important;
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
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <div class="dashboard-wrapper">
        <div class="dashboard-layout">
            <!-- Sidebar -->
            <aside class="sidebar" id="tenantSidebar">
                <div class="sidebar-profile">
                    <img src="<?php echo htmlspecialchars($profile_image_url); ?>" alt="<?php echo htmlspecialchars($tenant_name); ?>">
                    <h3><?php echo htmlspecialchars($tenant_name); ?></h3>
                    <p>Tenant</p>
                </div>
                <ul class="sidebar-nav">
                    <li><a href="#" class="active" onclick="showSection('dashboard')">
                        <i class="fas fa-th-large"></i> Dashboard
                    </a></li>
                    <li><a href="#" onclick="showSection('bookings')">
                        <i class="fas fa-calendar-check"></i> My Bookings
                    </a></li>
                    <li><a href="#" onclick="showSection('favorites')">
                        <i class="fas fa-heart"></i> Favorite Properties
                    </a></li>
                    <li><a href="#" onclick="showSection('reviews')">
                        <i class="fas fa-star"></i> My Reviews
                    </a></li>
                    <li><a href="../messages.php">
                        <i class="fas fa-envelope"></i> Messages
                        <span style="background: var(--danger-color); color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.75rem; margin-left: auto;">3</span>
                    </a></li>
                    <li><a href="#" onclick="showSection('settings')">
                        <i class="fas fa-cog"></i> Settings
                    </a></li>
                    <li><a href="../api/logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a></li>
                </ul>
            </aside>

            <!-- Main Content -->
            <main class="dashboard-content">
                <!-- Dashboard Section -->
                <section id="dashboard" class="content-section active">
                    <div class="dashboard-header">
                        <h1>Dashboard</h1>
                        <p class="welcome-message">Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>! Here's your rental activity.</p>
                    </div>

                    <!-- Stats Grid -->
                    <div class="stats-grid tenant-stats">
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div>
                                    <div class="stat-value"><?php echo $total_bookings; ?></div>
                                    <div class="stat-label">Total Bookings</div>
                                </div>
                                <div class="stat-icon primary">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                            </div>
                            <div class="stat-change">All time</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div>
                                    <div class="stat-value"><?php echo $approved_bookings; ?></div>
                                    <div class="stat-label">Approved</div>
                                </div>
                                <div class="stat-icon success">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                            <div class="stat-change">Ready to move in</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div>
                                    <div class="stat-value"><?php echo $pending_bookings; ?></div>
                                    <div class="stat-label">Pending</div>
                                </div>
                                <div class="stat-icon warning">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                            <div class="stat-change">Awaiting approval</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div>
                                    <div class="stat-value"><?php echo $rejected_bookings; ?></div>
                                    <div class="stat-label">Rejected</div>
                                </div>
                                <div class="stat-icon danger">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                            </div>
                            <div class="stat-change">Try different properties</div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="section-header">
                        <h2>Quick Actions</h2>
                    </div>

                    <div class="dashboard-grid">
                        <div class="property-card">
                            <div class="card-image">
                                <img src="https://images.pexels.com/photos/1396122/pexels-photo-1396122.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Find Properties">
                            </div>
                            <div class="card-content">
                                <h3>Find Your Perfect Home</h3>
                                <p>Browse thousands of rental properties in your area</p>
                                <div class="card-actions">
                                    <a href="../properties.php" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Browse Properties
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="property-card">
                            <div class="card-image">
                                <img src="https://images.pexels.com/photos/209296/pexels-photo-209296.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="My Bookings">
                            </div>
                            <div class="card-content">
                                <h3>Manage Your Bookings</h3>
                                <p>View and track all your rental requests</p>
                                <div class="card-actions">
                                    <button class="btn btn-secondary" onclick="showSection('bookings')">
                                        <i class="fas fa-calendar-check"></i> View Bookings
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="property-card">
                            <div class="card-image">
                                <img src="https://images.pexels.com/photos/3184430/pexels-photo-3184430.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Favorites">
                            </div>
                            <div class="card-content">
                                <h3>Your Favorites</h3>
                                <p>Properties you've saved for later</p>
                                <div class="card-actions">
                                    <button class="btn btn-secondary" onclick="showSection('favorites')">
                                        <i class="fas fa-heart"></i> View Favorites
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Bookings Section -->
                <section id="bookings" class="content-section">
                    <div class="dashboard-header">
                        <h1>My Bookings</h1>
                        <p class="welcome-message">Track all your rental requests and bookings.</p>
                    </div>

                    <div class="bookings-list">
                        <?php if (empty($bookings)): ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <h3>No Bookings Yet</h3>
                            <p>You haven't made any booking requests yet. Start by browsing available properties!</p>
                            <a href="../properties.php" class="btn btn-primary">
                                <i class="fas fa-search"></i> Find Properties
                            </a>
                        </div>
                        <?php else: ?>
                        <?php foreach ($bookings as $booking): ?>
                        <div class="booking-item">
                            <div class="booking-header">
                                <div class="booking-info">
                                    <h4><?php echo htmlspecialchars($booking['property_title']); ?></h4>
                                    <p><?php echo htmlspecialchars($booking['address']); ?></p>
                                    <p class="booking-meta">
                                        <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($booking['landlord_name']); ?></span>
                                        <span><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($booking['landlord_email']); ?></span>
                                        <span><i class="fas fa-phone"></i> <?php echo htmlspecialchars($booking['landlord_phone']); ?></span>
                                    </p>
                                </div>
                                <div class="booking-status">
                                    <span class="status-badge booking-status-<?php echo strtolower($booking['status']); ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="booking-details">
                                <div class="booking-dates">
                                    <span><i class="fas fa-calendar-alt"></i> Check-in: <?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></span>
                                    <span><i class="fas fa-calendar-check"></i> Check-out: <?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?></span>
                                    <span><i class="fas fa-users"></i> Guests: <?php echo $booking['guests']; ?></span>
                                    <span><i class="fas fa-dollar-sign"></i> Total: $<?php echo number_format($booking['total_price'], 2); ?></span>
                                </div>
                                <div class="booking-message">
                                    <p><strong>Your Message:</strong> <?php echo htmlspecialchars($booking['message']); ?></p>
                                </div>
                            </div>
                            <div class="booking-actions">
                                <button class="btn btn-secondary" onclick="viewBookingDetails(<?php echo $booking['booking_id']; ?>)">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                                <?php if ($booking['status'] === 'approved'): ?>
                                <button class="btn btn-success" onclick="contactLandlord('<?php echo htmlspecialchars($booking['landlord_email']); ?>')">
                                    <i class="fas fa-envelope"></i> Contact Landlord
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Favorite Properties Section -->
                <section id="favorites" class="content-section">
                    <div class="dashboard-header">
                        <h1>Favorite Properties</h1>
                        <p class="welcome-message">Properties you've saved for later</p>
                    </div>

                    <div class="property-grid">
                        <div class="property-card">
                            <button class="favorite-btn" onclick="removeFavorite(1)">
                                <i class="fas fa-heart"></i>
                            </button>
                            <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400" alt="Property" class="property-card-img">
                            <div class="property-card-content">
                                <h3 class="property-card-title">Modern Apartment</h3>
                                <p class="property-card-location">
                                    <i class="fas fa-map-marker-alt"></i> Dhaka, Bangladesh
                                </p>
                                <div class="property-card-price">৳5,000<span style="font-size: 0.9rem; font-weight: normal;">/night</span></div>
                                <div class="property-card-features">
                                    <span><i class="fas fa-bed"></i> 2 Beds</span>
                                    <span><i class="fas fa-bath"></i> 2 Baths</span>
                                    <span><i class="fas fa-users"></i> 4 Guests</span>
                                </div>
                                <div class="property-card-actions">
                                    <button class="btn btn-primary" onclick="viewProperty(1)">
                                        <i class="fas fa-eye"></i> View Details
                                    </button>
                                    <button class="btn btn-secondary" onclick="bookProperty(1)">
                                        <i class="fas fa-calendar-check"></i> Book Now
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="property-card">
                            <button class="favorite-btn" onclick="removeFavorite(2)">
                                <i class="fas fa-heart"></i>
                            </button>
                            <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=400" alt="Property" class="property-card-img">
                            <div class="property-card-content">
                                <h3 class="property-card-title">Cozy Studio</h3>
                                <p class="property-card-location">
                                    <i class="fas fa-map-marker-alt"></i> Chittagong, Bangladesh
                                </p>
                                <div class="property-card-price">৳4,000<span style="font-size: 0.9rem; font-weight: normal;">/night</span></div>
                                <div class="property-card-features">
                                    <span><i class="fas fa-bed"></i> 1 Bed</span>
                                    <span><i class="fas fa-bath"></i> 1 Bath</span>
                                    <span><i class="fas fa-users"></i> 2 Guests</span>
                                </div>
                                <div class="property-card-actions">
                                    <button class="btn btn-primary" onclick="viewProperty(2)">
                                        <i class="fas fa-eye"></i> View Details
                                    </button>
                                    <button class="btn btn-secondary" onclick="bookProperty(2)">
                                        <i class="fas fa-calendar-check"></i> Book Now
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="property-card">
                            <button class="favorite-btn" onclick="removeFavorite(3)">
                                <i class="fas fa-heart"></i>
                            </button>
                            <img src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=400" alt="Property" class="property-card-img">
                            <div class="property-card-content">
                                <h3 class="property-card-title">Luxury Villa</h3>
                                <p class="property-card-location">
                                    <i class="fas fa-map-marker-alt"></i> Cox's Bazar, Bangladesh
                                </p>
                                <div class="property-card-price">৳8,000<span style="font-size: 0.9rem; font-weight: normal;">/night</span></div>
                                <div class="property-card-features">
                                    <span><i class="fas fa-bed"></i> 4 Beds</span>
                                    <span><i class="fas fa-bath"></i> 3 Baths</span>
                                    <span><i class="fas fa-users"></i> 8 Guests</span>
                                </div>
                                <div class="property-card-actions">
                                    <button class="btn btn-primary" onclick="viewProperty(3)">
                                        <i class="fas fa-eye"></i> View Details
                                    </button>
                                    <button class="btn btn-secondary" onclick="bookProperty(3)">
                                        <i class="fas fa-calendar-check"></i> Book Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Reviews Section -->
                <section id="reviews" class="content-section">
                    <div class="dashboard-header">
                        <h1>My Reviews</h1>
                        <p class="welcome-message">Reviews you've written for properties</p>
                    </div>

                    <?php if (empty($reviews)): ?>
                    <div class="empty-state">
                        <i class="fas fa-star-half-alt"></i>
                        <h3>No Reviews Yet</h3>
                        <p>You haven't written any reviews yet. Complete a booking to share your experience!</p>
                        <a href="../properties.php" class="btn btn-primary">
                            <i class="fas fa-search"></i> Find Properties
                        </a>
                    </div>
                    <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                    <div class="review-card">
                        <div class="review-header">
                            <div class="review-property">
                                <img src="<?php echo htmlspecialchars($review['property_image']); ?>" alt="Property">
                                <div>
                                    <h4><?php echo htmlspecialchars($review['property_title']); ?></h4>
                                    <p style="color: var(--text-medium); font-size: 0.9rem;"><?php echo htmlspecialchars($review['address']); ?></p>
                                </div>
                            </div>
                            <div class="review-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?php echo $i <= $review['rating'] ? '' : 'far'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="review-content">
                            "<?php echo htmlspecialchars($review['review_text']); ?>"
                        </div>
                        <div class="review-date">
                            <i class="fas fa-calendar"></i> <?php echo date('F d, Y', strtotime($review['created_at'])); ?>
                        </div>
                        <?php if (!empty($review['landlord_response'])): ?>
                        <div class="landlord-response">
                            <div class="response-header">
                                <i class="fas fa-reply"></i> <strong>Landlord Response:</strong>
                            </div>
                            <p><?php echo htmlspecialchars($review['landlord_response']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </section>

                <!-- Messages Section -->
                <section id="messages" class="content-section">
                    <div class="dashboard-header">
                        <h1>Messages</h1>
                        <p class="welcome-message">Communicate with property owners</p>
                    </div>

                    <div class="message-container">
                        <div class="message-list">
                            <div class="message-item active">
                                <div class="message-sender">
                                    <img src="https://randomuser.me/api/portraits/men/22.jpg" alt="Landlord">
                                    <div class="message-sender-info">
                                        <h4>Michael Johnson</h4>
                                        <p>2 hours ago</p>
                                    </div>
                                </div>
                                <div class="message-preview">
                                    Yes, the apartment is available for those dates...
                                </div>
                            </div>

                            <div class="message-item">
                                <div class="message-sender">
                                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Landlord">
                                    <div class="message-sender-info">
                                        <h4>Sarah Williams</h4>
                                        <p>1 day ago</p>
                                    </div>
                                </div>
                                <div class="message-preview">
                                    Thank you for your interest in the villa...
                                </div>
                            </div>

                            <div class="message-item">
                                <div class="message-sender">
                                    <img src="https://randomuser.me/api/portraits/men/67.jpg" alt="Landlord">
                                    <div class="message-sender-info">
                                        <h4>David Brown</h4>
                                        <p>3 days ago</p>
                                    </div>
                                </div>
                                <div class="message-preview">
                                    The check-in time is flexible...
                                </div>
                            </div>
                        </div>

                        <div class="message-chat">
                            <div class="message-chat-header">
                                <img src="https://randomuser.me/api/portraits/men/22.jpg" alt="Landlord">
                                <div>
                                    <h4>Michael Johnson</h4>
                                    <p style="color: var(--text-medium); font-size: 0.85rem;">Owner of Modern Apartment Downtown</p>
                                </div>
                            </div>

                            <div class="message-chat-body">
                                <div class="chat-message">
                                    <div class="chat-bubble">
                                        <div>Hi! Is your apartment available from December 15-22?</div>
                                        <div class="chat-time">10:30 AM</div>
                                    </div>
                                </div>

                                <div class="chat-message sent">
                                    <div class="chat-bubble">
                                        <div>Hello! Yes, the apartment is available for those dates. Would you like to proceed with the booking?</div>
                                        <div class="chat-time">11:45 AM</div>
                                    </div>
                                </div>

                                <div class="chat-message">
                                    <div class="chat-bubble">
                                        <div>Great! What's included in the rental?</div>
                                        <div class="chat-time">12:15 PM</div>
                                    </div>
                                </div>

                                <div class="chat-message sent">
                                    <div class="chat-bubble">
                                        <div>The rental includes WiFi, utilities, parking, and access to all amenities. Cleaning service is also provided weekly.</div>
                                        <div class="chat-time">12:20 PM</div>
                                    </div>
                                </div>
                            </div>

                            <div class="message-chat-footer">
                                <div class="message-input-container">
                                    <input type="text" placeholder="Type your message...">
                                    <button><i class="fas fa-paper-plane"></i> Send</button>
                                </div>
                            </div>
                        </div>
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
                                    <i class="fas fa-camera"></i> Change Photo
                                </button>
                                <input type="file" id="profileImageInput" accept="image/*" onchange="changePhoto(event)">
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
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="openPasswordModal()">
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
                <div class="property-view-image">
                    <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800" alt="Property" id="modalPropertyImage">
                </div>

                <div class="property-view-info">
                    <h3 id="modalPropertyName">Modern Apartment Downtown</h3>
                    <p><i class="fas fa-map-marker-alt"></i> <span id="modalPropertyLocation">Dhaka, Bangladesh</span></p>
                </div>

                <div class="booking-detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Landlord</span>
                        <span class="detail-value" id="modalLandlord">Michael Johnson</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Landlord Email</span>
                        <span class="detail-value" id="modalLandlordEmail">michael.j@example.com</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Check-in Date</span>
                        <span class="detail-value" id="modalCheckin">Dec 15, 2024</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Check-out Date</span>
                        <span class="detail-value" id="modalCheckout">Dec 22, 2024</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Number of Nights</span>
                        <span class="detail-value" id="modalNights">7</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Number of Guests</span>
                        <span class="detail-value" id="modalGuests">4</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Total Price</span>
                        <span class="detail-value" id="modalPrice" style="color: var(--primary-color);">৳35,000</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Booking Status</span>
                        <span class="detail-value"><span class="status-badge status-active" id="modalStatus">Active</span></span>
                    </div>
                </div>

                <div class="detail-item" style="margin-top: 20px;">
                    <span class="detail-label">Special Requests</span>
                    <p id="modalRequests" style="color: var(--text-dark); margin-top: 10px;">Early check-in requested. Need parking space for 2 vehicles.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" onclick="closeBookingModal()">
                    <i class="fas fa-times-circle"></i> Close
                </button>
                <button class="btn btn-secondary" onclick="contactLandlord()">
                    <i class="fas fa-envelope"></i> Contact Landlord
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
                <form id="passwordForm">
                    <div class="form-group">
                        <label>Current Password *</label>
                        <input type="password" id="currentPassword" required placeholder="Enter current password">
                    </div>
                    <div class="form-group">
                        <label>New Password *</label>
                        <input type="password" id="newPassword" required placeholder="Enter new password" minlength="8">
                        <small style="color: #7f8c8d; font-size: 0.85rem;">
                            Password must be at least 8 characters long and contain at least one number
                        </small>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password *</label>
                        <input type="password" id="confirmPassword" required placeholder="Confirm new password" minlength="8">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" onclick="closePasswordModal()">
                    <i class="fas fa-times-circle"></i> Cancel
                </button>
                <button class="btn btn-primary" id="updatePasswordBtn" type="button" onclick="changePassword(event)">
                    <i class="fas fa-check-circle"></i> Update Password
                </button>
            </div>
        </div>
    </div>

    <script>
        // Navigation
        function showSection(sectionId) {
            // Hide all sections
            const sections = document.querySelectorAll('.content-section');
            sections.forEach(section => section.classList.remove('active'));

            // Show selected section
            document.getElementById(sectionId).classList.add('active');

            // Update active nav item
            const navLinks = document.querySelectorAll('.sidebar-nav a');
            navLinks.forEach(link => link.classList.remove('active'));
            event.target.closest('a').classList.add('active');
        }

        // Modal Functions
        function viewBooking(bookingId) {
            document.getElementById('bookingModal').classList.add('active');
            document.body.style.overflow = 'hidden';
            // Populate modal with booking data based on bookingId
        }

        function writeReview(bookingId) {
            alert('Review form would open here for booking #' + bookingId);
            // Implement review modal
        }

        function viewProperty(propertyId) {
            // Redirect to property details page
            window.location.href = '../property-details.php?id=' + propertyId;
        }

        function bookProperty(propertyId) {
            // Redirect to property details page for booking
            window.location.href = '../property-details.php?id=' + propertyId + '&action=book';
        }

        function removeFavorite(propertyId) {
            if (confirm('Remove this property from favorites?')) {
                alert('Property removed from favorites!');
                // Add remove logic here
            }
        }

        function contactLandlord() {
            // Redirect to chat page
            window.location.href = '../messages.php';
        }

        // Close modal when clicking outside
        document.getElementById('bookingModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeBookingModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeBookingModal();
            }
        });

        // Form submission
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
        async function changePhoto(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('Invalid file type. Please select a JPG, PNG, GIF, or WebP image.');
                event.target.value = ''; // Clear the input
                return;
            }

            // Validate file size (5MB max)
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                alert('File too large. Maximum size is 5MB.');
                event.target.value = ''; // Clear the input
                return;
            }

            // Show loading state
            const profileImg = document.getElementById('profileImage');
            const sidebarImg = document.querySelector('.sidebar-profile img');
            const originalSrc = profileImg.src;

            // Preview image immediately
            const reader = new FileReader();
            reader.onload = function(e) {
                profileImg.src = e.target.result;
                if (sidebarImg) {
                    sidebarImg.src = e.target.result;
                }
            };
            reader.readAsDataURL(file);

            // Upload to server
            try {
                const formData = new FormData();
                formData.append('profile_image', file);

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
            }

            // Clear the input so the same file can be selected again
            event.target.value = '';
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
            const submitBtn = document.getElementById('updatePasswordBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Changing Password...';
            
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
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while changing password. Please try again.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
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
            const sidebar = document.getElementById('tenantSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('mobile-active');
            overlay.classList.toggle('active');
        }

        function closeSidebar() {
            const sidebar = document.getElementById('tenantSidebar');
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




