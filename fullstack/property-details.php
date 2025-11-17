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
$error = null;
$property_id = null;

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $error = "Invalid property ID.";
} else {
    $property_id = (int)$_GET['id'];
    
    $sql = "SELECT p.*, u.full_name as landlord_name, u.profile_image as landlord_avatar, u.phone as landlord_phone, CONCAT(UPPER(LEFT(p.city, 1)), LOWER(SUBSTRING(p.city, 2))) as capitalized_city
            FROM properties p 
            JOIN users u ON p.landlord_id = u.user_id 
            WHERE p.property_id = ? AND p.status = 'available'";
    
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
        
        // Check if property is favorited by current user
        $is_favorited = false;
        if (isLoggedIn()) {
            $user_id = $_SESSION['user_id'];
            $fav_check_sql = "SELECT favorite_id FROM favorites WHERE user_id = ? AND property_id = ?";
            if ($fav_check_stmt = $conn->prepare($fav_check_sql)) {
                $fav_check_stmt->bind_param('ii', $user_id, $property_id);
                $fav_check_stmt->execute();
                $fav_check_result = $fav_check_stmt->get_result();
                $is_favorited = $fav_check_result->num_rows > 0;
                $fav_check_stmt->close();
            }
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
        
        // Handle tour request
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_tour'])) {
            if (isLoggedIn() && $_SESSION['role'] === 'tenant') {
                $user_id = $_SESSION['user_id'];
                $start_date = $_POST['move_in_date'];
                $duration = (int)$_POST['duration'];
                $message = trim($_POST['message']);
                $end_date = date('Y-m-d', strtotime($start_date . ' + ' . $duration . ' months'));
                $total_amount = $property['price_per_month'] * $duration;
                
                $insert_sql = "INSERT INTO tours (property_id, tenant_id, landlord_id, start_date, end_date, total_amount, message, status, created_at) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
                
                if ($insert_stmt = $conn->prepare($insert_sql)) {
                    $insert_stmt->bind_param('iiissds', $property_id, $user_id, $property['landlord_id'], $start_date, $end_date, $total_amount, $message);
                    if ($insert_stmt->execute()) {
                        $success_message = 'Tour request submitted successfully!';
                    } else {
                        $error_message = 'Error submitting tour request.';
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
    <link rel="stylesheet" href="css/mobile.css">

    <style>
        body { background-color: #f8f9fa; }
    .page-wrapper { padding-top: 100px; padding-bottom: 60px; }
    .details-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 2.5rem; align-items: flex-start; }
    .booking-box { background: var(--background-white); padding: 32px; border-radius: var(--border-radius); box-shadow: var(--shadow-medium); }
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; font-size: 1rem; margin-bottom: 2.5rem; }
    .info-grid-item { display: flex; align-items: center; gap: 12px; background: var(--background-white); border: 1px solid var(--border-color); border-radius: 8px; padding: 12px 16px; justify-content: flex-start; }
    .info-grid-item i { color: var(--secondary-color); }
    .amenities-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2.5rem; }
    .amenity-item { display: flex; align-items: center; gap: 8px; }
    .about-section { background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 2.5rem; }
    .btn-outline-favorite {
        background: white;
        color: var(--text-dark);
        border: 2px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s ease;
    }
    .btn-outline-favorite:hover {
        border-color: #e74c3c;
        color: #e74c3c;
        transform: translateY(-2px);
    }
    .btn-outline-favorite.active {
        background: #fff5f5;
        border-color: #e74c3c;
        color: #e74c3c;
    }
    .btn-outline-favorite i {
        font-size: 1.1rem;
    }
    .btn-outline-favorite.active i {
        color: #e74c3c;
    }
    .safety-tips-box {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        padding: 28px;
        box-shadow: var(--shadow-soft);
        transition: all 0.3s ease;
    }
    .safety-tips-box:hover {
        box-shadow: var(--shadow-medium);
    }
    .safety-tips-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--secondary-color);
    }
    .safety-tips-header i {
        font-size: 1.5rem;
        color: var(--secondary-color);
        background: rgba(26, 188, 156, 0.1);
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .safety-tips-header h3 {
        margin: 0;
        font-size: 1.25rem;
        color: var(--text-dark);
    }
    .safety-tip-content {
        background: white;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 1.5rem;
        /* border-left: 4px solid var(--secondary-color); */
        min-height: 100px;
        display: flex;
        align-items: center;
    }
    .safety-tip-slide {
        color: var(--text-medium);
        font-size: 0.95rem;
        line-height: 1.7;
        letter-spacing: 0.3px;
    }
    .safety-tips-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }
    .safety-tips-controls a {
        color: #e91e63;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.3s ease;
        padding: 8px 16px;
        border-radius: 20px;
        border: 1.5px solid #e91e63;
        background: transparent;
        display: inline-block;
    }
    .safety-tips-controls a:hover {
        color: #e91e63;
        background: rgba(233, 30, 99, 0.05);
        border-color: #c2185b;
        color: #c2185b;
    }
    .safety-nav-btn {
        width: 40px;
        height: 40px;
        border: 1.5px solid var(--border-color);
        border-radius: 8px;
        background: transparent;
        cursor: pointer;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        color: var(--text-dark);
        font-size: 0.9rem;
    }
    .safety-nav-btn:hover {
        background: transparent;
        border-color: var(--secondary-color);
        color: var(--secondary-color);
        transform: translateY(-2px);
    }
    .booking-box .btn-outline-favorite
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
.booking-box { background: var(--background-white); padding: 32px; border-radius: var(--border-radius); box-shadow: var(--shadow-medium); }
    .booking-box .btn { width: 100%; margin-top: 16px; }

    /* Booking actions wrapper to ensure consistent alignment/spacing */
    .booking-actions { display: flex; flex-direction: column; gap: 0px; align-items: center; }
    /* Force full-width buttons and prevent other rules from narrowing them */
    .booking-actions .btn { display: block !important; width: 100% !important; max-width: none !important; box-sizing: border-box; text-align: center; padding: 12px 18px !important; border-radius: 12px; line-height: 1.2; }
    .booking-actions p { margin: 0; text-align: center; width: 100%; }

    @media (max-width: 768px) {
        .page-wrapper { padding-top: 80px; padding-bottom: 20px; }
        .detail-section { padding: 15px 0; }
        .container { padding: 0 15px; }
        
        /* Two column layout becomes single column */
        section.detail-section > div:first-child { 
            display: flex !important; 
            flex-direction: column !important; 
            gap: 1.5rem !important; 
            grid-template-columns: 1fr !important; 
        }
        
        /* Image gallery mobile styles */
        .main-image { margin-bottom: 0.75rem !important; }
        .main-image img { height: 250px !important; }
        
        /* Thumbnail wrapper mobile */
        .thumbnail-wrapper { padding: 0 30px !important; }
        .thumbnail-grid { 
            grid-template-columns: repeat(4, 1fr) !important; 
            gap: 0.5rem !important; 
        }
        .thumb-img { height: 55px !important; }
        
        /* Buttons on images */
        #imgPrevBtn, #imgNextBtn { width: 30px !important; height: 30px !important; font-size: 1rem !important; }
        #mainImgPrevBtn, #mainImgNextBtn { width: 35px !important; height: 35px !important; font-size: 1rem !important; }
        #imgCounter { font-size: 0.8rem !important; padding: 3px 10px !important; }
        
        /* Booking boxes */
        .booking-box { padding: 18px !important; margin-bottom: 1rem; }
        .booking-box h3 { font-size: 1rem !important; }
        .btn { padding: 12px 24px !important; font-size: 0.95rem !important; }
        
        /* Safety tips */
        .safety-tips-box { padding: 18px !important; }
        .safety-tips-header { margin-bottom: 1rem !important; padding-bottom: 0.75rem !important; }
        .safety-tips-header i { width: 40px !important; height: 40px !important; font-size: 1.2rem !important; }
        .safety-tips-header h3 { font-size: 1rem !important; }
        .safety-tip-content { padding: 14px !important; min-height: 80px !important; margin-bottom: 1rem !important; }
        .safety-tip-slide { font-size: 0.85rem !important; line-height: 1.6 !important; }
        .safety-tips-controls a { font-size: 0.85rem !important; padding: 6px 14px !important; }
        .safety-nav-btn { width: 36px !important; height: 36px !important; }
        
        /* Property details */
        .detail-content { margin-top: 1.5rem !important; }
        .detail-content h1 { font-size: 1.6rem !important; line-height: 1.3 !important; margin-bottom: 0.75rem !important; }
        .detail-content .location { font-size: 0.95rem !important; margin-bottom: 1rem !important; }
        
        /* Price info section */
        .price-info { 
            flex-direction: row !important; 
            gap: 2rem !important; 
            align-items: center !important; 
            margin-bottom: 1.5rem !important; 
            justify-content: flex-start !important;
        }
        .price-info > div { 
            border-left: 1px solid var(--border-color) !important; 
            padding-left: 2rem !important; 
        }
        .price-info > div:first-child {
            border-left: none !important;
            padding-left: 0 !important;
        }
        .price-info p:first-child { font-size: 1.5rem !important; }
        
        /* Info grid */
        h2 { font-size: 1.4rem !important; margin-bottom: 1rem !important; }
        .info-grid { 
            grid-template-columns: repeat(2, 1fr) !important; 
            gap: 0.75rem !important; 
            margin-bottom: 2rem !important; 
        }
        .info-grid-item { font-size: 0.85rem !important; }
        
        /* About section */
        .about-section { padding: 15px !important; margin-bottom: 2rem !important; }
        .about-section p { font-size: 0.9rem !important; line-height: 1.6 !important; }
        
        /* Amenities */
        .amenities-grid { 
            grid-template-columns: repeat(2, 1fr) !important; 
            gap: 0.75rem !important; 
            margin-bottom: 2rem !important; 
        }
        .amenity-item { padding: 10px 12px !important; font-size: 0.85rem !important; }
        
        /* Map */
        iframe { height: 250px !important; }
    }
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
                            <button id="mainImgPrevBtn" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.5); color: white; border: none; border-radius: 50%; width: 40px; height: 40px; cursor: pointer; display: none; align-items: center; justify-content: center; font-size: 1.2rem; z-index: 5;"><i class="fas fa-chevron-left"></i></button>
                            <button onclick="toggleFavorite(<?php echo $property_id; ?>, this)" class="btn btn-outline-favorite <?php echo $is_favorited ? 'active' : ''; ?>" style="position: absolute; top: 16px; right: 16px; z-index: 10; padding: 12px; border-radius: 50%; background: rgba(255,255,255,0.95); border: 2px solid var(--border-color); box-shadow: var(--shadow-soft); transition: all 0.3s ease; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                <i class="<?php echo $is_favorited ? 'fas' : 'far'; ?> fa-heart" style="font-size: 1.2rem;"></i>
                                <span id="favoriteBtnText" style="display: none;"><?php echo $is_favorited ? 'Saved' : 'Save'; ?></span>
                            </button>
                            <img id="mainPropertyImage" src="<?php echo htmlspecialchars(!empty($property_images[0]['image_path']) ? $property_images[0]['image_path'] : 'img/default-property.jpg'); ?>" alt="Main property view" title="Click to zoom" style="width: 100%; height: auto; object-fit: cover; border-radius: var(--border-radius);">
                            <button id="mainImgNextBtn" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.5); color: white; border: none; border-radius: 50%; width: 40px; height: 40px; cursor: pointer; display: none; align-items: center; justify-content: center; font-size: 1.2rem; z-index: 5;"><i class="fas fa-chevron-right"></i></button>
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
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                        <aside class="booking-box">
                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color);">
                                <?php
                                $landlordAvatar = 'img/default-avatar.svg'; // Default fallback
                                if (!empty($property['landlord_avatar']) && $property['landlord_avatar'] !== 'default-avatar.png' && $property['landlord_avatar'] !== 'default-avatar.svg') {
                                    $profilePath = 'uploads/' . $property['landlord_avatar'];
                                    if (file_exists(__DIR__ . '/' . $profilePath)) {
                                        $landlordAvatar = $profilePath;
                                    }
                                }
                                ?>
                                <img src="<?php echo htmlspecialchars($landlordAvatar); ?>" alt="<?php echo htmlspecialchars($property['landlord_name']); ?>" onerror="this.src='img/default-avatar.svg';" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid var(--secondary-color); flex-shrink: 0; background-color: #f5f5f5;">
                                <div style="flex: 1; min-width: 0;">
                                    <h3 style="margin: 0 0 0.25rem 0; font-size: 1.1rem; font-weight: 600; line-height: 1.3; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo htmlspecialchars($property['landlord_name']); ?></h3>
                                    <p style="color: var(--text-medium); font-size: 0.875rem; margin: 0; line-height: 1.4;">Property Owner</p>
                                </div>
                            </div>

                            <?php if (isLoggedIn() && $_SESSION['role'] === 'tenant'): ?>
                                <div class="booking-actions">
                                    <a href="tel:<?php echo htmlspecialchars($property['landlord_phone'] ?? '+880170000000'); ?>" class="btn btn-secondary">Call Landlord</a>
                                    <a href="tour-property.php?id=<?php echo $property_id; ?>" class="btn btn-primary">Request a Tour</a>
                                </div>
                            <?php elseif (isLoggedIn() && ($_SESSION['role'] === 'landlord' || $_SESSION['role'] === 'admin')): ?>
                                            <p style="font-size: 0.875rem; color: var(--text-medium); text-align: center; margin-bottom: 1rem; line-height: 1.5;">You are logged in as a landlord/admin and cannot book properties.</p>
                                            <div class="booking-actions">
                                                <a href="tel:<?php echo htmlspecialchars($property['landlord_phone'] ?? '+880170000000'); ?>" class="btn btn-secondary">Call Landlord</a>
                                            </div>
                            <?php else: ?>
                                <div class="booking-actions">
                                    <a href="login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn btn-primary">Login to Request Tour</a>
                                    <p style="font-size: 0.875rem; color: var(--text-medium); text-align: center; margin: 1rem 0 0.75rem 0; line-height: 1.5;">or contact landlord:</p>
                                    <a href="tel:<?php echo htmlspecialchars($property['landlord_phone'] ?? '+880170000000'); ?>" class="btn btn-secondary">Call Landlord</a>
                                </div>
                            <?php endif; ?>
                        </aside>

                        <!-- Safety Tips Box -->
                        <aside class="booking-box safety-tips-box">
                            <div class="safety-tips-header">
                                <i class="fas fa-shield-alt"></i>
                                <h3>Safety Tips</h3>
                            </div>
                            
                            <div id="safetyTipsCarousel" style="transition: all 0.5s ease-in-out;">
                                <div class="safety-tip-content">
                                    <div class="safety-tip-slide">
                                        Check the details carefully, including property descriptions, photos, and contact information. If anything seems unclear, request additional details or images from the advertiser.
                                    </div>
                                </div>
                                <div class="safety-tip-content" style="display: none;">
                                    <div class="safety-tip-slide">
                                        Never finalize a deal based solely on online information. Schedule an on-site visit to verify the property's condition, location, and amenities. For added safety, visit during daylight hours and bring a friend or family member along.
                                    </div>
                                </div>
                                <div class="safety-tip-content" style="display: none;">
                                    <div class="safety-tip-slide">
                                        Before committing to a property, ensure the seller or landlord provides valid ownership documents. Verify these with local authorities to confirm there are no disputes or legal issues tied to the property.
                                    </div>
                                </div>
                                <div class="safety-tip-content" style="display: none;">
                                    <div class="safety-tip-slide">
                                        Be cautious about sharing sensitive personal information like your national ID, financial details, or address. Share these only when necessary and with verified parties through AmarThikana's secure channels.
                                    </div>
                                </div>
                                <div class="safety-tip-content" style="display: none;">
                                    <div class="safety-tip-slide">
                                        If you come across misleading ads, fake listings, or suspicious behavior, report them to AmarThikana immediately. The platform's team takes user safety seriously and acts promptly to address such issues.
                                    </div>
                                </div>
                                <div class="safety-tip-content" style="display: none;">
                                    <div class="safety-tip-slide">
                                        Communicate with buyers, sellers, and renters through AmarThikana's internal messaging system. This adds a layer of security and keeps your personal contact information private until you feel comfortable sharing it.
                                    </div>
                                </div>
                            </div>

                            <div class="safety-tips-controls">
                                <a href="safety-tips.php" style="color: #e91e63; font-weight: 600; font-size: 0.9rem; text-decoration: none;">View all</a>
                                <div class="safety-nav-buttons">
                                    <button id="prevTip" type="button" class="safety-nav-btn">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <button id="nextTip" type="button" class="safety-nav-btn">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>

                <div class="detail-content" style="margin-top: 0.7rem;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1.5rem; margin-bottom: 2rem;">
                        <div style="flex: 1;">
                            <h1 style="margin: 0; margin-bottom: 1rem; font-size: 2.2rem;"><?php echo htmlspecialchars($property['title']); ?></h1>
                            <p class="location" style="margin-bottom: 1.5rem;"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($property['address'] . ', ' . $property['capitalized_city']); ?></p>
                            
                            <div class="price-info" style="display: flex; gap: 3rem; align-items: center; margin-bottom: 0;">
                                <div>
                                    <!-- <p style="color: var(--text-medium); font-size: 0.9rem; margin-bottom: 4px;">Price</p> -->
                                    <p style="font-size: 1.8rem; font-weight: 700; color: var(--text-dark);">৳<?php echo number_format($property['price_per_month']); ?></p>
                                    <p style="color: var(--text-medium); font-size: 0.85rem;">Per Month</p>
                                </div>
                                <div style="border-left: 2px solid var(--border-color); padding-left: 3rem;">
                                    <p style="color: var(--text-medium); font-size: 0.9rem; margin-bottom: 4px;">Available from</p>
                                    <p style="font-size: 1.2rem; font-weight: 600; color: var(--text-dark);"><?php echo date('j M Y', strtotime($property['available_from'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h2 style="font-size: 1.6rem; margin-bottom: 1.5rem;">Basic Information</h2>
                    <div class="info-grid" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
                        <div class="info-grid-item"><i class="fas fa-building"></i> <?php echo htmlspecialchars(ucfirst($property['property_type'])); ?></div>
                        <div class="info-grid-item"><i class="fas fa-users"></i> Family</div>
                        <div class="info-grid-item"><i class="fas fa-ruler-combined"></i> <?php echo htmlspecialchars($property['area_sqft']); ?> sqft</div>
                        <div class="info-grid-item"><i class="fas fa-layer-group"></i> 5th Floor</div>
                        <div class="info-grid-item"><i class="fas fa-bed"></i> <?php echo htmlspecialchars($property['bedrooms']); ?> Beds</div>
                        <div class="info-grid-item"><i class="fas fa-bath"></i> <?php echo htmlspecialchars($property['bathrooms']); ?> Baths</div>
                        <div class="info-grid-item"><i class="fas fa-door-open"></i> 2 Balcony</div>
                        <div class="info-grid-item"><i class="fas fa-compass"></i> South Facing</div>
                        <div class="info-grid-item"><i class="fas fa-calendar-alt"></i> Posted: <?php echo date('j M Y', strtotime($property['created_at'])); ?></div>
                    </div>

                    <h2>About this place</h2>
                    <div class="about-section">
                        <p><?php echo nl2br(htmlspecialchars($property['description'])); ?></p>
                    </div>

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
                        <?php if (!empty($property['map_url'])): ?>
                            <iframe
                                src="<?php echo htmlspecialchars($property['map_url']); ?>"
                                width="100%"
                                height="350"
                                style="border:0; border-radius:12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        <?php else: ?>
                            <div style="background: #f8f9fa; border: 2px dashed var(--border-color); border-radius: 12px; padding: 40px 20px; text-align: center; height: 350px; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                <i class="fas fa-map-marker-alt" style="font-size: 3rem; color: var(--text-medium); margin-bottom: 1rem;"></i>
                                <h3 style="color: var(--text-medium); margin-bottom: 0.5rem; font-size: 1.2rem;">Map Not Available</h3>
                                <p style="color: var(--text-medium); font-size: 0.95rem; margin: 0;">The landlord hasn't added a map link for this property yet.</p>
                                <p style="color: var(--secondary-color); font-size: 0.9rem; margin-top: 1rem; font-weight: 600;"><i class="fas fa-info-circle"></i> Contact the landlord for location details</p>
                            </div>
                        <?php endif; ?>
                        <div style="margin-top: 0.7rem; color: #7f8c8d; font-size: 1rem;">
                            <i class="fas fa-map-marker-alt" style="color: #16a085; margin-right: 6px;"></i>
                            <strong><?php echo htmlspecialchars($property['address'] . ', ' . $property['capitalized_city']); ?></strong>
                        </div>
                    </div>
                    <!-- End Location Section -->
                </div>
            </section>
        <?php endif; ?>
    </main>
</div>

<?php include 'footer.php'; ?>

<script>
    // Ensure core scripts are loaded on this page
    (function() {
        var script = document.createElement('script');
        script.src = 'js/script.js';
        script.defer = true;
        script.onload = function() {
            var mobileScript = document.createElement('script');
            mobileScript.src = 'js/mobile.js';
            mobileScript.defer = true;
            document.body.appendChild(mobileScript);
        };
        document.body.appendChild(script);
    })();
const propertyImages = <?php echo json_encode($property_images); ?>;

document.addEventListener('DOMContentLoaded', function() {
    const thumbImages = document.querySelectorAll('.thumb-img');
    const imgCounter = document.getElementById('imgCounter');
    const prevBtn = document.getElementById('imgPrevBtn');
    const nextBtn = document.getElementById('imgNextBtn');
    const mainPrevBtn = document.getElementById('mainImgPrevBtn');
    const mainNextBtn = document.getElementById('mainImgNextBtn');
    const thumbnailGrid = document.querySelector('.thumbnail-grid');
    const mainImg = document.getElementById('mainPropertyImage');

    let currentIndex = 0;
    const totalImages = thumbImages.length;
    const visibleThumbs = 5;
    let firstVisibleThumb = 0;

    function updateGallery(newIndex, isThumbnailClick = false) {
        // Update main image
        if (mainImg) {
            mainImg.src = thumbImages[newIndex].src;
        }
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
        if(mainPrevBtn) mainPrevBtn.style.display = 'flex';
        if(mainNextBtn) mainNextBtn.style.display = 'flex';
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
    if (mainImg && propertyImages && propertyImages.length > 0) {
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
            modalImg.onerror = function() {
                this.src = 'img/default-property.jpg';
                this.alt = 'Image not available';
            };

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
                modalImg.src = propertyImages[index].image_path;
                modalImg.alt = 'Property image ' + (index + 1);
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
    }
});

// Safety Tips Carousel
document.addEventListener('DOMContentLoaded', function() {
    const carouselContainer = document.getElementById('safetyTipsCarousel');
    const slides = document.querySelectorAll('.safety-tip-content');
    const prevBtn = document.getElementById('prevTip');
    const nextBtn = document.getElementById('nextTip');
    let currentSlide = 0;
    let autoScrollInterval;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.style.display = i === index ? 'flex' : 'none';
        });
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }

    function prevSlide() {
        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(currentSlide);
    }

    function startAutoScroll() {
        autoScrollInterval = setInterval(nextSlide, 5000);
    }

    function stopAutoScroll() {
        clearInterval(autoScrollInterval);
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            stopAutoScroll();
            nextSlide();
            startAutoScroll();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            stopAutoScroll();
            prevSlide();
            startAutoScroll();
        });
    }

    showSlide(0);
    startAutoScroll();
});

// Favorite toggle function
function toggleFavorite(propertyId, button) {
    fetch('api/toggle_favorite.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'property_id=' + propertyId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const icon = button.querySelector('i');
            const text = button.querySelector('#favoriteBtnText');
            
            if (data.is_favorited) {
                button.classList.add('active');
                icon.classList.remove('far');
                icon.classList.add('fas');
                text.textContent = 'Saved';
                showNotification('Added to favorites', 'success');
            } else {
                button.classList.remove('active');
                icon.classList.remove('fas');
                icon.classList.add('far');
                text.textContent = 'Save Property';
                showNotification('Removed from favorites', 'success');
            }
        } else {
            if (data.message === 'Please log in to manage favorites') {
                window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.pathname + window.location.search);
            } else {
                showNotification(data.message || 'Failed to update favorites', 'error');
            }
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

const notifStyle = document.createElement('style');
notifStyle.textContent = `
    @keyframes slideIn {
        from { transform: translateX(400px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(400px); opacity: 0; }
    }
`;
document.head.appendChild(notifStyle);
</script>

</body>
</html>





