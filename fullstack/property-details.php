<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';
require_once 'includes/auth.php';

$property = null;
$property_images = [];
$property_amenities = [];
$similar_properties = [];
$error = null;
$property_id = null;

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $error = "Invalid property ID.";
} else {
    $property_id = (int)$_GET['id'];
    
    $sql = "SELECT p.*, u.full_name as landlord_name, u.profile_image as landlord_avatar 
            FROM properties p 
            JOIN users u ON p.landlord_id = u.user_id 
            WHERE p.property_id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('i', $property_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $property = $result->fetch_assoc();
        }
        $stmt->close();
    }
    
    if ($property) {
        // Increment view count
        $update_sql = "UPDATE properties SET views = views + 1 WHERE property_id = ?";
        if ($update_stmt = $conn->prepare($update_sql)) {
            $update_stmt->bind_param('i', $property_id);
            $update_stmt->execute();
            $update_stmt->close();
        }
        
        // Fetch property images
        $img_sql = "SELECT image_path, is_primary FROM property_images WHERE property_id = ? ORDER BY is_primary DESC, upload_date ASC";
        if ($img_stmt = $conn->prepare($img_sql)) {
            $img_stmt->bind_param('i', $property_id);
            $img_stmt->execute();
            $img_result = $img_stmt->get_result();
            while ($img_row = $img_result->fetch_assoc()) {
                $property_images[] = $img_row;
            }
            $img_stmt->close();
        }
        
        // Fetch property amenities
        $amenity_sql = "SELECT amenity FROM property_amenities WHERE property_id = ?";
        if ($amenity_stmt = $conn->prepare($amenity_sql)) {
            $amenity_stmt->bind_param('i', $property_id);
            $amenity_stmt->execute();
            $amenity_result = $amenity_stmt->get_result();
            while ($amenity_row = $amenity_result->fetch_assoc()) {
                $property_amenities[] = $amenity_row['amenity'];
            }
            $amenity_stmt->close();
        }
        
        // Fetch similar properties
        $search_city = !empty($property['city']) ? $property['city'] : 'Dhaka';
        $similar_sql = "SELECT p.*, 
                        (SELECT image_path FROM property_images WHERE property_id = p.property_id AND is_primary = 1 LIMIT 1) as main_image
                        FROM properties p 
                        WHERE p.city = ? 
                        AND p.property_id != ? 
                        AND p.status = 'available' 
                        ORDER BY RAND() 
                        LIMIT 3";
        if ($similar_stmt = $conn->prepare($similar_sql)) {
            $similar_stmt->bind_param('si', $search_city, $property_id);
            $similar_stmt->execute();
            $similar_result = $similar_stmt->get_result();
            while ($similar_row = $similar_result->fetch_assoc()) {
                $similar_properties[] = $similar_row;
            }
            $similar_stmt->close();
        }
        
        // Handle booking request
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_booking'])) {
            if (isLoggedIn() && $_SESSION['role'] === 'tenant') {
                $user_id = $_SESSION['user_id'];
                $start_date = $_POST['move_in_date'];
                $duration = (int)$_POST['duration'];
                $message = trim($_POST['message']);
                $end_date = date('Y-m-d', strtotime($start_date . ' + ' . $duration . ' months'));
                $total_amount = $property['price_per_month'] * $duration;
                
                $insert_sql = "INSERT INTO bookings (property_id, tenant_id, landlord_id, start_date, end_date, total_amount, message, status, created_at) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
                
                if ($insert_stmt = $conn->prepare($insert_sql)) {
                    $insert_stmt->bind_param('iiissds', $property_id, $user_id, $property['landlord_id'], $start_date, $end_date, $total_amount, $message);
                    if ($insert_stmt->execute()) {
                        $success_message = 'Booking request submitted successfully!';
                    } else {
                        $error_message = 'Error submitting booking request.';
                    }
                    $insert_stmt->close();
                }
            }
        }
    } else {
        $error = 'Property not found';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $property ? htmlspecialchars($property['title']) : 'Property Details'; ?> - AmarThikana</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">

    <style>
        body { background-color: #f8f9fa; }
    .page-wrapper { padding-top: 100px; padding-bottom: 60px; }
    .details-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 2.5rem; align-items: flex-start; }
    .booking-box { background: var(--background-white); padding: 32px; border-radius: var(--border-radius); box-shadow: var(--shadow-medium); position: sticky; top: 120px; }
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; font-size: 1rem; margin-bottom: 2.5rem; background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--border-color); }
    .info-grid-item { display: flex; align-items: center; gap: 8px; }
    .info-grid-item i { color: var(--secondary-color); }
    .amenities-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2.5rem; }
    .amenity-item { display: flex; align-items: center; gap: 8px; }
    .amenity-item i { color: var(--secondary-color); }
    .booking-form { margin-top: 20px; }
    .booking-form .form-group { margin-bottom: 15px; }
    .booking-form label { display: block; margin-bottom: 5px; font-weight: 600; }
    .booking-form input, .booking-form textarea, .booking-form select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit; }
    .booking-form textarea { resize: vertical; min-height: 80px; }
    .alert { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
    .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .alert-info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    
    
    .booking-box .btn-primary:hover {
        color: white !important;
        
    }
:root {
    --primary-color: #2c3e50; /* Slate Blue */
    --secondary-color: #1abc9c; /* Vibrant Teal */
    --background-light: #fdfdfd;
    --background-white: #ffffff;
    --text-dark: #34495e;
    --text-medium: #7f8c8d;
    --border-color: #ecf0f1;
    --font-family-body: 'Lato', sans-serif;
    --font-family-heading: 'Poppins', sans-serif;
    --shadow-soft: 0 4px 15px rgba(0,0,0,0.06);
    --shadow-medium: 0 8px 25px rgba(44, 62, 80, 0.1);
    --border-radius: 12px;
}

html { scroll-behavior: smooth; }
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: var(--font-family-body);
    color: var(--text-dark);
    line-height: 1.7;
    background-color: var(--background-light);
}


h1, h2, h3, h4 {
    font-family: var(--font-family-heading);
    font-weight: 600;
    color: var(--text-dark);
    line-height: 1.3;
}

h1 { font-size: 3.2rem; }
h2.section-title { font-size: 2.5rem; text-align: center; margin-bottom: 1rem; }
p.section-subtitle { text-align: center; max-width: 600px; margin: 0 auto 3.5rem auto; color: var(--text-medium); }
h3 { font-size: 1.35rem; }

a { text-decoration: none; color: var(--secondary-color); transition: color 0.3s ease; }
a:hover { color: #16a085; }

.container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

.btn {
    display: inline-block;
    padding: 14px 32px;
    border-radius: 50px; /* Pill shape */
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    cursor: pointer;
    border: 1px solid transparent;
    text-align: center;
}
.btn-primary { background-color: var(--secondary-color); color: var(--background-white); }
/* Fix for Header Sign Up button hover text color */
    .nav-actions .btn-primary:hover {
        color: white !important;
    }
    
    /* Universal fix for all primary buttons if needed */
    .btn-primary:hover {
        color: white !important;
    }
/* .btn-primary:hover { text-decoration-color: white; background-color: #16a085; transform: translateY(-3px); box-shadow: var(--shadow-medium); } */
.btn-secondary {
    background-color: transparent;
    color: var(--secondary-color);
    border: 2px solid var(--secondary-color);
}
.btn-secondary:hover { background-color: var(--secondary-color); color: var(--background-white); }

.main-header {
    background-color: #2c3e50 !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.06) !important;
}

.property-card {
    background-color: var(--background-white); border-radius: var(--border-radius); overflow: hidden;
    border: 1px solid var(--border-color); transition: transform 0.3s ease, box-shadow 0.3s ease; box-shadow: var(--shadow-soft);
}
.property-card:hover { transform: translateY(-10px); box-shadow: var(--shadow-medium); }

.main-footer { 
    background-color: var(--primary-color);
    padding: 60px 0 20px; 
    color: #ecf0f1;
}

.page-header {
    background-color: var(--background-white);
    padding: 15px 0;
    border-bottom: 1px solid var(--border-color);
    box-shadow: var(--shadow-soft);
}
.page-header .logo { color: var(--text-dark); }
.page-header .nav-links a { color: var(--text-dark); }

.user-avatar img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--secondary-color);
}

/* --- Property Detail Page --- */
.page-wrapper { padding-top: 80px; } /* Offset for fixed header */
.detail-section { padding: 60px 0; }
.image-gallery { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 48px; }
.main-image img { width: 100%; height: 500px; object-fit: cover; border-radius: var(--border-radius); }
.thumb-images { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
.thumb-images img { width: 100%; height: 238px; object-fit: cover; border-radius: var(--border-radius); }

.detail-layout { display: grid; grid-template-columns: 2.5fr 1.5fr; gap: 60px; }
.detail-content h1 { font-size: 2.8rem; margin-bottom: 8px; }
.detail-content .location { font-size: 1.1rem; color: var(--text-medium); margin-bottom: 24px; }
.detail-specs { display: flex; gap: 32px; padding: 24px 0; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); }
.detail-specs span { font-size: 1.1rem; font-weight: 500; }
.detail-specs i { color: var(--secondary-color); margin-right: 8px; }
.detail-section h2 { font-size: 1.8rem; margin-bottom: 1rem; margin-top: 2rem; }
.amenities-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
.amenity-item { background: #f8f9fa; border: 1px solid var(--border-color); border-radius: var(--border-radius); padding: 16px; display: flex; align-items: center; gap: 12px; }
.booking-box { background: var(--background-white); padding: 32px; border-radius: var(--border-radius); box-shadow: var(--shadow-medium); position: sticky; top: 120px; }
.booking-box .btn { width: 100%; margin-top: 16px; }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="page-wrapper">
    <main class="container">
        <?php if ($error): ?>
            <div class="alert alert-danger" style="text-align: center; font-size: 1.2rem;"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif ($property): ?>
            <section class="detail-section">
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2.5rem; align-items: flex-start;">
                    
                    <!-- Image Gallery Column -->
                    <div>
                        <div class="main-image" style="cursor: zoom-in; position: relative; margin-bottom: 1rem;">
                            <button id="mainImgPrevBtn" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.9); border: 1px solid var(--border-color); border-radius: 50%; width: 40px; height: 40px; cursor: pointer; z-index: 10; font-size: 1.3rem; box-shadow: var(--shadow-soft); display: none; align-items: center; justify-content: center;"><i class="fas fa-chevron-left"></i></button>
                            <img id="mainPropertyImage" src="<?php echo htmlspecialchars(!empty($property_images[0]['image_path']) ? $property_images[0]['image_path'] : 'img/default-property.jpg'); ?>" alt="Main property view" title="Click to zoom" style="width: 100%; height: auto; object-fit: cover; border-radius: var(--border-radius);">
                            <button id="mainImgNextBtn" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.9); border: 1px solid var(--border-color); border-radius: 50%; width: 40px; height: 40px; cursor: pointer; z-index: 10; font-size: 1.3rem; box-shadow: var(--shadow-soft); display: none; align-items: center; justify-content: center;"><i class="fas fa-chevron-right"></i></button>
                            <div id="imgCounter" style="position: absolute; bottom: 16px; right: 16px; background: rgba(0,0,0,0.6); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.9rem; font-weight: 500;">1 / <?php echo count($property_images); ?></div>
                        </div>
                        
                        <?php if (count($property_images) > 1): ?>
                        <div class="thumbnail-wrapper" style="position: relative; padding: 0 40px;">
                             <button id="imgPrevBtn" style="position: absolute; left: 0; top: 50%; transform: translateY(-50%); background: #fff; border: 1px solid var(--border-color); border-radius: 50%; width: 36px; height: 36px; cursor: pointer; z-index: 10; font-size: 1.2rem; box-shadow: var(--shadow-soft);"><i class="fas fa-chevron-left"></i></button>
                            <div class="thumbnail-grid" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; overflow: hidden;">
                                <?php foreach ($property_images as $index => $img): ?>
                                    <img src="<?php echo htmlspecialchars($img['image_path']); ?>" alt="Property thumbnail <?php echo $index + 1; ?>" data-index="<?php echo $index; ?>" class="thumb-img" style="width: 100%; height: 80px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 2px solid transparent; transition: border-color 0.3s ease;">
                                <?php endforeach; ?>
                            </div>
                            <button id="imgNextBtn" style="position: absolute; right: 0; top: 50%; transform: translateY(-50%); background: #fff; border: 1px solid var(--border-color); border-radius: 50%; width: 36px; height: 36px; cursor: pointer; z-index: 10; font-size: 1.2rem; box-shadow: var(--shadow-soft);"><i class="fas fa-chevron-right"></i></button>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Booking Box Column -->
                    <aside class="booking-box">
                        <h3>৳<?php echo number_format($property['price_per_month']); ?> / Month</h3>
                        <p style="margin-bottom: 1.5rem;">Includes all utilities and fees.</p>

                        <?php if (isLoggedIn() && $_SESSION['role'] === 'tenant'): ?>
                            <a href="tel:<?php echo htmlspecialchars($property['landlord_phone'] ?? '+880170000000'); ?>" class="btn btn-primary">Request to Book</a>
                            <p style="font-size: 0.9rem; color: var(--text-medium); text-align: center; margin-top: 1rem; margin-bottom: 0.5rem;">For direct booking or queries:</p>
                            <a href="tel:<?php echo htmlspecialchars($property['landlord_phone'] ?? '+880170000000'); ?>" class="btn btn-secondary">Call Landlord</a>
                        <?php elseif (isLoggedIn() && ($_SESSION['role'] === 'landlord' || $_SESSION['role'] === 'admin')): ?>
                             <p style="font-size: 0.9rem; color: var(--text-medium); text-align: center; margin-top: 1rem;">You are logged in as a landlord/admin and cannot book properties.</p>
                             <a href="tel:<?php echo htmlspecialchars($property['landlord_phone'] ?? '+880170000000'); ?>" class="btn btn-secondary">Call Landlord</a>
                        <?php else: ?>
                            <a href="login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn btn-primary">Login to Book</a>
                             <p style="font-size: 0.9rem; color: var(--text-medium); text-align: center; margin-top: 1rem; margin-bottom: 0.5rem;">For direct booking or queries:</p>
                            <a href="tel:<?php echo htmlspecialchars($property['landlord_phone'] ?? '+880170000000'); ?>" class="btn btn-secondary">Call Landlord</a>
                        <?php endif; ?>
                    </aside>
                </div>

                <div class="detail-content" style="margin-top: 2.5rem;">
                    <h1><?php echo htmlspecialchars($property['title']); ?></h1>
                    <p class="location" style="margin-bottom: 2rem;"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($property['address'] . ', ' . $property['city']); ?></p>
                    
                    <h2 style="font-size: 1.6rem; margin-bottom: 1.5rem;">Basic Information</h2>
                    <div class="info-grid" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1.5rem; padding: 1.5rem; margin-bottom: 3rem;">
                        <div class="info-grid-item"><span><i class="fas fa-building"></i> <?php echo htmlspecialchars(ucfirst($property['property_type'])); ?></span></div>
                        <div class="info-grid-item"><span><i class="fas fa-users"></i> Family</span></div>
                        <div class="info-grid-item"><span><i class="fas fa-ruler-combined"></i> <?php echo htmlspecialchars($property['area_sqft']); ?> sqft</span></div>
                        <div class="info-grid-item"><span><i class="fas fa-layer-group"></i> 5th Floor</span></div>
                        <div class="info-grid-item"><span><i class="fas fa-bed"></i> <?php echo htmlspecialchars($property['bedrooms']); ?> Beds</span></div>
                        <div class="info-grid-item"><span><i class="fas fa-bath"></i> <?php echo htmlspecialchars($property['bathrooms']); ?> Baths</span></div>
                        <div class="info-grid-item"><span><i class="fas fa-door-open"></i> 2 Balcony</span></div>
                        <div class="info-grid-item"><span><i class="fas fa-compass"></i> South Facing</span></div>
                        <div class="info-grid-item"><span><i class="fas fa-calendar-check"></i> Available: <?php echo date('j M Y', strtotime($property['available_from'])); ?></span></div>
                        <div class="info-grid-item"><span><i class="fas fa-calendar-alt"></i> Posted: <?php echo date('j M Y', strtotime($property['created_at'])); ?></span></div>
                    </div>

                    <h2>About this place</h2>
                    <p><?php echo nl2br(htmlspecialchars($property['description'])); ?></p>

                    <?php if (!empty($property_amenities)): ?>
                    <h2>What this place offers</h2>
                    <div class="amenities-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                        <?php foreach ($property_amenities as $amenity): ?>
                            <div class="amenity-item" style="background: var(--background-white); border: 1px solid var(--border-color); border-radius: 8px; padding: 12px 16px; justify-content: flex-start;"><i class="fas fa-check-circle" style="color: var(--secondary-color);"></i> <?php echo htmlspecialchars(ucfirst($amenity)); ?></div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Location Section -->
                    <h2 style="margin-top: 2rem;">Location</h2>
                    <div style="margin-bottom: 1.5rem;">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3651.9084832050435!2d90.39225931544305!3d23.750901084587947!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755b8b087026b81%3A0x8fa563bbdd5904c2!2sDhaka%2C%20Bangladesh!5e0!3m2!1sen!2sus!4v1234567890123!5m2!1sen!2sus"
                            width="100%"
                            height="350"
                            style="border:0; border-radius:12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                        <div style="margin-top: 0.7rem; color: #7f8c8d; font-size: 1rem;">
                            <i class="fas fa-map-marker-alt" style="color: #16a085; margin-right: 6px;"></i>
                            <strong><?php echo htmlspecialchars($property['address'] . ', ' . $property['city']); ?></strong>
                        </div>
                    </div>
                    <!-- End Location Section -->
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($similar_properties)): ?>
        <section class="detail-section" style="border-top: 1px solid var(--border-color); padding-top: 60px;">
            <h2 class="section-title">Similar Properties in <?php echo htmlspecialchars($property['city']); ?></h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
                <?php foreach ($similar_properties as $similar): ?>
                <div class="property-card">
                    <div style="position: relative; overflow: hidden; border-radius: var(--border-radius) var(--border-radius) 0 0;">
                        <img src="<?php echo htmlspecialchars($similar['main_image'] ? $similar['main_image'] : 'img/default-property.jpg'); ?>" alt="<?php echo htmlspecialchars($similar['title']); ?>" style="width: 100%; height: 220px; object-fit: cover;">
                    </div>
                    <div style="padding: 20px;">
                        <h3 style="font-size: 1.3rem; margin-bottom: 8px;">
                            <a href="property-details.php?id=<?php echo $similar['property_id']; ?>" style="color: var(--text-dark);"><?php echo htmlspecialchars($similar['title']); ?></a>
                        </h3>
                        <p style="color: var(--text-medium); margin-bottom: 12px;">
                            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($similar['address'] . ', ' . $similar['city']); ?>
                        </p>
                        <div style="display: flex; gap: 16px; margin-bottom: 12px; font-size: 0.95rem; color: var(--text-medium);">
                            <span><i class="fas fa-bed"></i> <?php echo $similar['bedrooms']; ?></span>
                            <span><i class="fas fa-bath"></i> <?php echo $similar['bathrooms']; ?></span>
                            <span><i class="fas fa-ruler-combined"></i> <?php echo $similar['area_sqft']; ?></span>
                        </div>
                        <div style="font-size: 1.4rem; font-weight: 600; color: var(--secondary-color); margin-bottom: 12px;">
                            ৳<?php echo number_format($similar['price_per_month']); ?> <span style="font-size: 0.9rem; font-weight: 400;">/month</span>
                        </div>
                        <a href="property-details.php?id=<?php echo $similar['property_id']; ?>" class="btn btn-secondary" style="width: 100%; padding: 10px;">View Details</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </main>
</div>

<?php include 'footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mainImg = document.getElementById('mainPropertyImage');
    const thumbImages = document.querySelectorAll('.thumb-img');
    const imgCounter = document.getElementById('imgCounter');
    const prevBtn = document.getElementById('imgPrevBtn');
    const nextBtn = document.getElementById('imgNextBtn');
    const mainPrevBtn = document.getElementById('mainImgPrevBtn');
    const mainNextBtn = document.getElementById('mainImgNextBtn');
    const thumbnailGrid = document.querySelector('.thumbnail-grid');

    let currentIndex = 0;
    const totalImages = thumbImages.length;
    const visibleThumbs = 5;
    let firstVisibleThumb = 0;

    function updateGallery(newIndex, isThumbnailClick = false) {
        // Update main image
        mainImg.src = thumbImages[newIndex].src;
        currentIndex = newIndex;

        // Update counter
        if (imgCounter) {
            imgCounter.textContent = `${currentIndex + 1} / ${totalImages}`;
        }

        // Update active thumbnail style
        thumbImages.forEach((thumb, idx) => {
            if (idx === currentIndex) {
                thumb.style.borderColor = 'var(--secondary-color)';
                thumb.style.opacity = '1';
            } else {
                thumb.style.borderColor = 'transparent';
                thumb.style.opacity = '0.7';
            }
        });

        // Scroll thumbnail into view if not a thumbnail click
        if (!isThumbnailClick) {
            if (currentIndex < firstVisibleThumb || currentIndex >= firstVisibleThumb + visibleThumbs) {
                firstVisibleThumb = Math.max(0, Math.min(currentIndex - Math.floor(visibleThumbs / 2), totalImages - visibleThumbs));
                updateThumbnailVisibility();
            }
        }
    }

    function updateThumbnailVisibility() {
        thumbImages.forEach((thumb, idx) => {
            if (idx >= firstVisibleThumb && idx < firstVisibleThumb + visibleThumbs) {
                thumb.style.display = 'block';
            } else {
                thumb.style.display = 'none';
            }
        });
        if(prevBtn) prevBtn.disabled = firstVisibleThumb === 0;
        if(nextBtn) nextBtn.disabled = firstVisibleThumb >= totalImages - visibleThumbs;
    }

    // Click handlers for thumbnails
    thumbImages.forEach((thumb, index) => {
        thumb.addEventListener('click', function() {
            updateGallery(index, true);
        });
    });

    // Click handlers for thumbnail prev/next buttons
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (firstVisibleThumb < totalImages - visibleThumbs) {
                firstVisibleThumb++;
                updateThumbnailVisibility();
            }
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (firstVisibleThumb > 0) {
                firstVisibleThumb--;
                updateThumbnailVisibility();
            }
        });
    }

    // Click handlers for main image prev/next buttons
    if(mainNextBtn) {
        mainNextBtn.addEventListener('click', () => {
            let newIndex = (currentIndex + 1) % totalImages;
            updateGallery(newIndex);
        });
    }
    if(mainPrevBtn) {
        mainPrevBtn.addEventListener('click', () => {
            let newIndex = (currentIndex - 1 + totalImages) % totalImages;
            updateGallery(newIndex);
        });
    }

    // Show/hide main image arrows on hover
    const mainImageContainer = document.querySelector('.main-image');
    if(mainImageContainer && totalImages > 1) {
        mainImageContainer.addEventListener('mouseenter', () => {
            if(mainPrevBtn) mainPrevBtn.style.display = 'flex';
            if(mainNextBtn) mainNextBtn.style.display = 'flex';
        });
        mainImageContainer.addEventListener('mouseleave', () => {
            if(mainPrevBtn) mainPrevBtn.style.display = 'none';
            if(mainNextBtn) mainNextBtn.style.display = 'none';
        });
    }


    // Initial setup
    if (totalImages > 0) {
        updateGallery(0);
    }
    if (totalImages > visibleThumbs) {
        updateThumbnailVisibility();
    } else {
        if(prevBtn) prevBtn.style.display = 'none';
        if(nextBtn) nextBtn.style.display = 'none';
    }


    // Modal gallery when clicking the main image
    mainImg.addEventListener('click', function() {
        const modal = document.createElement('div');
        modal.id = 'imageModal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
        `;
        
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '<i class="fas fa-times"></i>';
        closeBtn.style.cssText = `
            position: absolute;
            top: 20px;
            right: 35px;
            font-size: 2.5rem;
            color: #fff;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        `;
        
        closeBtn.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.2) rotate(90deg)';
        });
        closeBtn.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) rotate(0deg)';
        });

        const modalImg = document.createElement('img');
        modalImg.style.cssText = `
            max-width: 90vw;
            max-height: 85vh;
            object-fit: contain;
        `;

        const modalPrevBtn = document.createElement('button');
        modalPrevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
        modalPrevBtn.style.cssText = `
            position: absolute;
            top: 50%;
            left: 30px;
            transform: translateY(-50%);
            font-size: 3rem;
            color: #fff;
            background: rgba(0,0,0,0.3);
            border: none;
            cursor: pointer;
            padding: 10px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
        `;
        modalPrevBtn.addEventListener('mouseenter', function() { this.style.background = 'rgba(22,160,133,0.8)'; });
        modalPrevBtn.addEventListener('mouseleave', function() { this.style.background = 'rgba(0,0,0,0.3)'; });


        const modalNextBtn = document.createElement('button');
        modalNextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
        modalNextBtn.style.cssText = `
            position: absolute;
            top: 50%;
            right: 30px;
            transform: translateY(-50%);
            font-size: 3rem;
            color: #fff;
            background: rgba(0,0,0,0.3);
            border: none;
            cursor: pointer;
            padding: 10px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
        `;
        modalNextBtn.addEventListener('mouseenter', function() { this.style.background = 'rgba(22,160,133,0.8)'; });
        modalNextBtn.addEventListener('mouseleave', function() { this.style.background = 'rgba(0,0,0,0.3)'; });

        function showImageInModal(index) {
            modalImg.src = thumbImages[index].src;
            currentIndex = index;
            updateGallery(index);
        }

        modalPrevBtn.addEventListener('click', function() {
            let newIndex = (currentIndex - 1 + totalImages) % totalImages;
            showImageInModal(newIndex);
        });

        modalNextBtn.addEventListener('click', function() {
            let newIndex = (currentIndex + 1) % totalImages;
            showImageInModal(newIndex);
        });
        
        closeBtn.addEventListener('click', function() {
            document.body.removeChild(modal);
        });
        
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                document.body.removeChild(modal);
            }
        });
        
        modal.appendChild(closeBtn);
        modal.appendChild(modalImg);
        modal.appendChild(modalPrevBtn);
        modal.appendChild(modalNextBtn);
        document.body.appendChild(modal);
        showImageInModal(currentIndex);
    });
});
</script>

</body>
</html>





