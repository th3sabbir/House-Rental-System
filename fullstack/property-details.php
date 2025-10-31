<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $property ? htmlspecialchars($property['title']) : 'Property Details'; ?> - HouseRental</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    .booking-form input, .booking-form textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
    .booking-form textarea { resize: vertical; min-height: 80px; }
    .alert { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
    .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    
    
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
/* .main-header {
    background-color: transparent;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
    padding: 20px 0;
    transition: background-color 0.4s ease, box-shadow 0.4s ease, padding 0.4s ease;
}
.main-header.scrolled {
    background-color: var(--primary-color);
    box-shadow: var(--shadow-soft);
    padding: 15px 0;
} */

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

/* --- Dashboard Page --- */
.dashboard-layout { display: grid; grid-template-columns: 280px 1fr; min-height: 100vh; }
.sidebar { background-color: var(--background-white); border-right: 1px solid var(--border-color); padding: 32px; }
.sidebar-profile { text-align: center; margin-bottom: 32px; }
.sidebar-profile img { width: 80px; height: 80px; border-radius: 50%; margin-bottom: 12px; }
.sidebar-profile h3 { font-size: 1.2rem; }
.sidebar-profile p { font-size: 0.9rem; color: var(--text-medium); margin: 0; }

.sidebar-nav { list-style: none; }
.sidebar-nav li a {
    display: flex; align-items: center; gap: 12px; padding: 14px 18px;
    border-radius: var(--border-radius); color: var(--text-medium); font-weight: 500; margin-bottom: 8px;
}
.sidebar-nav li a:hover { background-color: #f8f9fa; color: var(--text-dark); }
.sidebar-nav li a.active { background-color: var(--secondary-color); color: var(--background-white); font-weight: 600; }
.sidebar-nav li a i { width: 20px; text-align: center; font-size: 1.1rem; }

.dashboard-content { padding: 48px; background-color: #f8f9fa; }
.dashboard-content h1 { font-size: 2.5rem; margin-bottom: 0.5rem; }
.dashboard-content .welcome-message { color: var(--text-medium); margin-bottom: 2.5rem; }
.section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
.section-header h2 { margin-bottom: 0; font-size: 1.8rem; }
.dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; margin-bottom: 48px; }

.info-table { width: 100%; border-collapse: collapse; background-color: var(--background-white); border-radius: var(--border-radius); box-shadow: var(--shadow-soft); overflow: hidden; }
.info-table thead th { text-align: left; background-color: #f8f9fa; color: var(--text-medium); font-weight: 600; padding: 16px; font-size: 0.9rem; border-bottom: 2px solid var(--border-color); }
.info-table tbody td { padding: 16px; vertical-align: middle; border-bottom: 1px solid var(--border-color); }
.info-table tbody tr:last-child td { border-bottom: none; }
.status-badge { padding: 5px 14px; border-radius: 50px; font-weight: 600; font-size: 0.8rem; }
.status-pending { background-color: #fef3c7; color: #92400e; }
.status-accepted { background-color: #d1fae5; color: #065f46; }

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

/* --- Chat Page --- */
.chat-layout { display: grid; grid-template-columns: 350px 1fr; height: calc(100vh - 73px); }
.chat-sidebar { border-right: 1px solid var(--border-color); display: flex; flex-direction: column; background: var(--background-white); }
.chat-list { flex-grow: 1; overflow-y: auto; }
.chat-item { display: flex; align-items: center; gap: 12px; padding: 16px; cursor: pointer; border-bottom: 1px solid var(--border-color); }
.chat-item:hover { background-color: #f8f9fa; }
.chat-item.active { background-color: #e8f8f5; border-right: 4px solid var(--secondary-color); }
.chat-item img { width: 50px; height: 50px; border-radius: 50%; }
.chat-item h4 { font-size: 1rem; margin: 0; }
.chat-item p { font-size: 0.9rem; margin: 0; color: var(--text-medium); }

.chat-window { display: flex; flex-direction: column; background-color: #f8f9fa; }
.chat-header { padding: 16px 24px; background: var(--background-white); border-bottom: 1px solid var(--border-color); font-weight: 600; font-size: 1.2rem; }
.message-area { flex-grow: 1; padding: 24px; overflow-y: auto; display: flex; flex-direction: column; gap: 16px; }
.message-bubble { max-width: 65%; padding: 14px 20px; border-radius: 20px; line-height: 1.5; }
.message-bubble .timestamp { font-size: 0.8rem; color: var(--text-medium); margin-bottom: 4px; display: block; }
.sent { background: var(--secondary-color); color: var(--background-white); align-self: flex-end; border-bottom-right-radius: 4px; }
.sent .timestamp { color: rgba(255,255,255,0.7); }
.received { background: var(--background-white); color: var(--text-dark); align-self: flex-start; border: 1px solid var(--border-color); border-bottom-left-radius: 4px; }
.message-input-form { display: flex; align-items: center; gap: 12px; padding: 16px 24px; background: var(--background-white); border-top: 1px solid var(--border-color); }
.message-input-form input { flex-grow: 1; padding: 14px; border-radius: 50px; background: #f8f9fa; border: 1px solid var(--border-color); }
.message-input-form .btn { border-radius: 50%; width: 52px; height: 52px; padding: 0; font-size: 1.2rem; }

/* --- Search Results Page --- */
.filter-bar { display: flex; flex-wrap: wrap; gap: 12px; padding: 24px 0; border-bottom: 1px solid var(--border-color); margin-bottom: 24px; }
.filter-btn { background: var(--background-white); border: 2px solid var(--border-color); padding: 10px 20px; border-radius: 50px; cursor: pointer; font-weight: 500; }
.results-header { margin-bottom: 24px; font-size: 1.2rem; }
.results-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; }


.pagination { display: flex; justify-content: center; align-items: center; gap: 8px; margin-top: 60px; padding-bottom: 60px; }
.pagination a { padding: 10px 18px; border: 2px solid var(--border-color); border-radius: 50px; color: var(--text-dark); font-weight: 600; }
.pagination a.active, .pagination a:hover { background: var(--secondary-color); color: var(--background-white); border-color: var(--secondary-color); }
    </style>
</head>
    <?php
            require_once __DIR__ . '/config/database.php';
            require_once __DIR__ . '/includes/auth.php';

            $property = null;
            $error = '';

            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                $error = 'Invalid property ID';
            } else {
                $id = (int)$_GET['id'];
                $sql = "SELECT p.*, u.name as landlord_name, u.phone as landlord_phone FROM properties p LEFT JOIN users u ON p.landlord_id = u.id WHERE p.id = ? AND p.status = 'available'";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param('i', $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $property = $result->fetch_assoc();
                    $stmt->close();
                }

                if (!$property) {
                    $error = 'Property not found or not available';
                }
            }

            if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php else: ?>
<body>
    
    
<!-- Header Placeholder -->
    <div id="header-placeholder"></div>


            <div style="max-width:1200px; margin:40px auto 40px auto; padding:0 24px;">
    <main class="page-wrapper">
        <section class="detail-section container">

              

<div class="detail-layout" style="display: flex; gap: 2.5rem; align-items: flex-start; max-width: 1100px; margin: 0 auto;">
    
    <div style="flex:2 1 0; min-width:0;">
        
         <div class="property-images" style="width:100%; max-width:100%; margin:0 auto 2.5rem auto;">
            <div class="main-image-box" style="width:100%; max-width:1100px; margin:0 auto; aspect-ratio: 16/7; border-radius:16px; overflow:hidden; box-shadow:0 6px 32px rgba(44,62,80,0.13); position:relative;">
                <img id="mainPropertyImage"
                     src="<?php echo htmlspecialchars($property['main_image'] ?: 'images/default-property.jpg'); ?>"
                     alt="<?php echo htmlspecialchars($property['title']); ?>"
                     style="width:100%; height:100%; object-fit:cover; transition:0.2s; cursor: zoom-in;">
                
                <span id="imgCounter" style="position:absolute; bottom:18px; right:34px; background:rgba(0,0,0,0.55); color:#fff; font-size:1.08rem; border-radius:7px; padding:3px 13px;">1 / 5</span>
            </div>

            
            <div class="thumbnail-controls" style="display: flex; align-items: center; justify-content: center; gap: 1rem; max-width: 1100px; margin: 1rem auto 0 auto;">
    <!-- Left Arrow -->
    <button id="imgPrevBtn" type="button"
        style="background: #f0f9f7; border: 1px solid var(--border-color); border-radius:50%; width:44px; height:44px; display:flex; align-items:center; justify-content:center; box-shadow:var(--shadow-soft); font-size:1.2rem; cursor:pointer; flex-shrink: 0; transition: all 0.3s ease;"
        onmouseover="this.style.borderColor='#0e7c68'; this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(22, 160, 133, 0.3)'; this.querySelector('i').style.color='#0e7c68';"
        onmouseout="this.style.borderColor='#f0f9f7'; this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.06)'; this.querySelector('i').style.color='#16a085';">
        <i class="fas fa-chevron-left" style="color:#16a085; transition: color 0.3s ease;"></i>
    </button>

    <div class="thumbnail-grid" style="display:grid; grid-template-columns: repeat(5, 1fr); gap:1rem; width:100%;">
        <!-- Thumbnails will be generated by JavaScript -->
    </div>

    <!-- Right Arrow -->
    <button id="imgNextBtn" type="button"
        style="background: #f0f9f7; border: 1px solid var(--border-color); border-radius:50%; width:44px; height:44px; display:flex; align-items:center; justify-content:center; box-shadow:var(--shadow-soft); font-size:1.2rem; cursor:pointer; flex-shrink: 0; transition: all 0.3s ease;"
        onmouseover="this.style.borderColor='#0e7c68'; this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(22, 160, 133, 0.3)'; this.querySelector('i').style.color='#0e7c68';"
        onmouseout="this.style.borderColor='#f0f9f7'; this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.06)'; this.querySelector('i').style.color='#16a085';">
        <i class="fas fa-chevron-right" style="color:#16a085; transition: color 0.3s ease;"></i>
    </button>
</div>
        </div>
                
                

    </div>
    
    <aside class="booking-box" style="background:#fff; padding:32px 28px; border-radius:16px; box-shadow:0 4px 24px rgba(44,62,80,0.10); position:sticky; top:120px; max-width:340px; min-width:260px; margin-left:auto; flex:1 1 340px;">
        <h3 style="font-size:2rem; font-weight:700; color:var(--secondary-color); margin-bottom:0.7rem;">৳ <?php echo number_format($property['price']); ?> <span style="font-size:1rem; font-weight:400; color:#888;">/ Month</span></h3>
        <p style="margin-bottom:1.2rem; color:#555;">Includes all utilities and fees.</p>

        <?php if (isLoggedIn() && $_SESSION['user_role'] === 'tenant'): ?>
            <div class="booking-form">
                <h4>Request to Book</h4>
                <?php
                // Check if user already has a pending request for this property
                $user_id = $_SESSION['user_id'];
                $check_sql = "SELECT id FROM bookings WHERE property_id = ? AND tenant_id = ? AND status = 'pending'";
                $has_pending = false;
                if ($check_stmt = $conn->prepare($check_sql)) {
                    $check_stmt->bind_param('ii', $property['id'], $user_id);
                    $check_stmt->execute();
                    $check_stmt->store_result();
                    $has_pending = $check_stmt->num_rows > 0;
                    $check_stmt->close();
                }

                if (isset($_POST['submit_booking'])) {
                    if ($has_pending) {
                        echo '<div class="alert alert-error">You already have a pending booking request for this property.</div>';
                    } else {
                        $move_in_date = $_POST['move_in_date'];
                        $duration = (int)$_POST['duration'];
                        $message = trim($_POST['message']);

                        $insert_sql = "INSERT INTO bookings (property_id, tenant_id, landlord_id, move_in_date, duration_months, message, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())";
                        if ($insert_stmt = $conn->prepare($insert_sql)) {
                            $insert_stmt->bind_param('iiisis', $property['id'], $user_id, $property['landlord_id'], $move_in_date, $duration, $message);
                            if ($insert_stmt->execute()) {
                                echo '<div class="alert alert-success">Booking request submitted successfully! The landlord will contact you soon.</div>';
                                $has_pending = true;
                            } else {
                                echo '<div class="alert alert-error">Error submitting booking request. Please try again.</div>';
                            }
                            $insert_stmt->close();
                        }
                    }
                }

                if (!$has_pending): ?>
                    <form method="POST">
                        <div class="form-group">
                            <label for="move_in_date">Preferred Move-in Date:</label>
                            <input type="date" id="move_in_date" name="move_in_date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="duration">Duration (months):</label>
                            <input type="number" id="duration" name="duration" required min="1" max="24" value="12">
                        </div>
                        <div class="form-group">
                            <label for="message">Message to Landlord:</label>
                            <textarea id="message" name="message" placeholder="Tell the landlord why you're interested in this property..."></textarea>
                        </div>
                        <button type="submit" name="submit_booking" class="btn btn-primary" style="width:100%;">Submit Booking Request</button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-success">You have a pending booking request for this property.</div>
                <?php endif; ?>
            </div>
        <?php elseif (isLoggedIn() && $_SESSION['user_role'] === 'landlord'): ?>
            <div class="alert alert-error">Landlords cannot book properties. This is your property.</div>
        <?php else: ?>
            <a href="login.php" class="btn btn-primary" style="width:100%; margin-bottom:1.1rem;">Login to Book</a>
            <p style="text-align: center; color: #666; font-size: 0.9rem;">Only registered tenants can request bookings.</p>
        <?php endif; ?>

        <div style="margin-top:1.2rem; color:#888; font-size:0.97rem;">
            <i class="fas fa-phone-alt" style="color:var(--secondary-color);"></i>
            For direct queries:<br>
            <a href="tel:<?php echo htmlspecialchars($property['landlord_phone'] ?: '+880170000000'); ?>" class="btn btn-secondary" style="width:100%; margin-top: 10px;">Call Landlord</a>
        </div>
    </aside>
</div>


           

           
               <div class="detail-content" style="flex:2 1 500px;">
                    <h1><?php echo htmlspecialchars($property['title']); ?></h1>
                    <p class="location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($property['address'] . ($property['city'] ? ', ' . $property['city'] : '')); ?></p>
                    <!-- <div class="detail-specs" style="margin-bottom: 1.2rem;">
                        <span><i class="fas fa-bed"></i> 3 Beds</span>
                        <span><i class="fas fa-bath"></i> 3 Baths</span>
                        <span><i class="fas fa-ruler-combined"></i> 1800 sqft</span>
                    </div> -->
                    
                
                    <!-- <div class="basic-info-box" style="background:#f8fafc; border-radius:12px; box-shadow:0 2px 8px rgba(44,62,80,0.07); padding:1.2rem 1.5rem; margin-bottom:2rem; margin-top:1.2rem;"> -->
                        <h2>Basic Information</h2>
                         <div style="display:grid; grid-template-columns: repeat(5, 1fr); gap:1.1rem 1.5rem; font-size:1.05rem;">
        <!-- 10 items, 5 per row -->
        <div style="display:flex;align-items:center;gap:7px;"><i class="fas fa-building" style="color:#16a085;"></i><?php echo htmlspecialchars(ucfirst($property['property_type'])); ?></div>
        <div style="display:flex;align-items:center;gap:7px;"><i class="fas fa-users" style="color:#16a085;"></i><?php echo htmlspecialchars($property['suitable_for'] ?: 'Family'); ?></div>
        <div style="display:flex;align-items:center;gap:7px;"><i class="fas fa-vector-square" style="color:#16a085;"></i><?php echo (int)$property['area_sqft']; ?> sqft</div>
        <div style="display:flex;align-items:center;gap:7px;"><i class="fas fa-layer-group" style="color:#16a085;"></i><?php echo (int)$property['floor']; ?>th Floor</div>
        <div style="display:flex;align-items:center;gap:7px;"><i class="fas fa-bed" style="color:#16a085;"></i><?php echo (int)$property['bedrooms']; ?> Beds</div>
        <div style="display:flex;align-items:center;gap:7px;"><i class="fas fa-bath" style="color:#16a085;"></i><?php echo (int)$property['bathrooms']; ?> Baths</div>
        <div style="display:flex;align-items:center;gap:7px;"><i class="fas fa-door-open" style="color:#16a085;"></i><?php echo (int)$property['balconies']; ?> Balcony</div>
        <div style="display:flex;align-items:center;gap:7px;"><i class="fas fa-compass" style="color:#16a085;"></i><?php echo htmlspecialchars($property['facing'] ?: 'South'); ?> Facing</div>
        <div style="display:flex;align-items:center;gap:7px;"><i class="fas fa-calendar-check" style="color:#16a085;"></i>Available: <?php echo htmlspecialchars($property['available_from'] ?: 'Immediate'); ?></div>
        <div style="display:flex;align-items:center;gap:7px;"><i class="fas fa-calendar-alt" style="color:#16a085;"></i>Posted: <?php echo date('d M Y', strtotime($property['created_at'])); ?></div>
    </div>
</div>
                    <!-- </div> -->
                    
                    
                    <h2>About this place</h2>
                    <p><?php echo nl2br(htmlspecialchars($property['description'] ?: 'No description available.')); ?></p>
                    
                    
<h2>What this place offers</h2>
<div class="amenities-grid" style="display:grid; grid-template-columns: repeat(5, 1fr); gap:1rem 1.5rem; margin-bottom:2rem;">
    <?php
    $amenities = json_decode($property['amenities'], true) ?: [];
    $default_amenities = ['WiFi', 'Parking', 'Security', 'Generator', 'Elevator'];
    $all_amenities = array_unique(array_merge($amenities, $default_amenities));

    foreach ($all_amenities as $amenity): ?>
        <div class="amenity-item"><i class="fas fa-check"></i> <?php echo htmlspecialchars($amenity); ?></div>
    <?php endforeach; ?>
</div>


                    <!-- Location Section (at the end) -->
                    <h2 style="margin-top:2.2rem;">Location</h2>
                    <div style="margin-bottom: 1.5rem;">
                        <iframe
                            src="https://www.google.com/maps?q=<?php echo urlencode($property['address'] . ', ' . $property['city']); ?>&output=embed"
                            width="100%"
                            height="260"
                            style="border:1px solid #ecf0f1; border-radius:12px;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                        <div style="margin-top:0.7rem; color:var(--text-medium); font-size:1rem;">
                            <i class="fas fa-map-marker-alt" style="color:var(--secondary-color);"></i>
                            <?php echo htmlspecialchars($property['address'] . ($property['city'] ? ', ' . $property['city'] : '')); ?>
                        </div>
                    </div>
                    <!-- End Location Section -->
                </div>

                <!-- <aside class="booking-box">
                    <h3>৳ 65,000 / Month</h3>
                    <p>Includes all utilities and fees.</p>
                    <a href="#" class="btn btn-primary">Request to Book</a>
                    <div style="margin-top:1.2rem; color:#888; font-size:0.97rem;">
                            <i class="fas fa-phone-alt" style="color:var(--secondary-color);"></i>
                            For direct booking or queries: <br>
                        </div>
                    <a href="tel:+880170000000" class="btn btn-secondary">Call Landlord</a>
                    
                </aside>  -->
            </div>
        
     <!-- ...existing code... -->

    <a href="messages.php"
   class="chat-float-btn"
   title="Chat with Landlord"
   target="_blank"
   style="
        position: fixed;
        bottom: 100px;  /* Changed from 32px to 100px */
        right: 30px;
        z-index: 9999;
        background: #fff;
        color: #16a085;
        border-radius: 50%;
        width: 62px;
        height: 62px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 24px rgba(22,160,133,0.18);
        font-size: 2.1rem;
        border: 2.5px solid #16a085;
        transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        text-decoration: none;
   "
   onmouseover="this.style.background='#16a085';this.style.color='#fff';"
   onmouseout="this.style.background='#fff';this.style.color='#16a085';"
>
    <i class="fas fa-comments"></i>
</a>


        
        <!-- Similar Property Section -->
        <section class="similar-properties container" style="margin-bottom: 60px;">
            <h2 class="section-title" style="font-size:2rem; margin-bottom: 1.5rem; text-align:left;">Similar Properties</h2>
            <div class="property-grid">
                <div class="property-card">
                    <div class="card-image">
                        <img src="https://images.pexels.com/photos/271624/pexels-photo-271624.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Property 1">
                    </div>
                    <div class="card-content">
                        <h3>Modern Apartment in Dhanmondi</h3>
                        <p class="address"><i class="fas fa-map-marker-alt"></i> Dhanmondi, Dhaka</p>
                        <div class="property-specs">
                            <span><i class="fas fa-bed"></i> 2 Beds</span>
                            <span><i class="fas fa-bath"></i> 2 Baths</span>
                            <span><i class="fas fa-ruler-combined"></i> 1200 sqft</span>
                        </div>
                        <div style="font-weight:600; color:var(--secondary-color); margin-bottom:8px;">৳ 25,000 / Month</div>
                        <a href="#" class="btn btn-secondary" style="padding:8px 20px; font-size:0.95rem;">View Details</a>
                    </div>
                </div>
                <div class="property-card">
                    <div class="card-image">
                        <img src="https://images.pexels.com/photos/276724/pexels-photo-276724.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Property 2">
                    </div>
                    <div class="card-content">
                        <h3>Cozy Studio in Mohammadpur</h3>
                        <p class="address"><i class="fas fa-map-marker-alt"></i> Mohammadpur, Dhaka</p>
                        <div class="property-specs">
                            <span><i class="fas fa-bed"></i> 1 Bed</span>
                            <span><i class="fas fa-bath"></i> 1 Bath</span>
                            <span><i class="fas fa-ruler-combined"></i> 650 sqft</span>
                        </div>
                        <div style="font-weight:600; color:var(--secondary-color); margin-bottom:8px;">৳ 12,000 / Month</div>
                        <a href="#" class="btn btn-secondary" style="padding:8px 20px; font-size:0.95rem;">View Details</a>
                    </div>
                </div>
                <div class="property-card">
                    <div class="card-image">
                        <img src="https://images.pexels.com/photos/5998120/pexels-photo-5998120.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Property 3">
                    </div>
                    <div class="card-content">
                        <h3>Family Flat in Mirpur</h3>
                        <p class="address"><i class="fas fa-map-marker-alt"></i> Mirpur, Dhaka</p>
                        <div class="property-specs">
                            <span><i class="fas fa-bed"></i> 3 Beds</span>
                            <span><i class="fas fa-bath"></i> 2 Baths</span>
                            <span><i class="fas fa-ruler-combined"></i> 1450 sqft</span>
                        </div>
                        <div style="font-weight:600; color:var(--secondary-color); margin-bottom:8px;">৳ 18,500 / Month</div>
                        <a href="#" class="btn btn-secondary" style="padding:8px 20px; font-size:0.95rem;">View Details</a>
                    </div>
                </div>
                <div class="property-card">
                    <div class="card-image">
                        <img src="https://images.pexels.com/photos/323780/pexels-photo-323780.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Property 4">
                    </div>
                    <div class="card-content">
                        <h3>Luxury House in Bashundhara</h3>
                        <p class="address"><i class="fas fa-map-marker-alt"></i> Bashundhara, Dhaka</p>
                        <div class="property-specs">
                            <span><i class="fas fa-bed"></i> 3 Beds</span>
                            <span><i class="fas fa-bath"></i> 3 Baths</span>
                            <span><i class="fas fa-ruler-combined"></i> 1800 sqft</span>
                        </div>
                        <div style="font-weight:600; color:var(--secondary-color); margin-bottom:8px;">৳ 40,000 / Month</div>
                        <a href="#" class="btn btn-secondary" style="padding:8px 20px; font-size:0.95rem;">View Details</a>
                    </div>
                </div>
                <div class="property-card">
                    <div class="card-image">
                        <img src="https://images.pexels.com/photos/210617/pexels-photo-210617.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Property 5">
                    </div>
                    <div class="card-content">
                        <h3>Single Room in Uttara</h3>
                        <p class="address"><i class="fas fa-map-marker-alt"></i> Uttara, Dhaka</p>
                        <div class="property-specs">
                            <span><i class="fas fa-bed"></i> 1 Bed</span>
                            <span><i class="fas fa-bath"></i> 1 Bath</span>
                            <span><i class="fas fa-ruler-combined"></i> 400 sqft</span>
                        </div>
                        <div style="font-weight:600; color:var(--secondary-color); margin-bottom:8px;">৳ 8,000 / Month</div>
                        <a href="#" class="btn btn-secondary" style="padding:8px 20px; font-size:0.95rem;">View Details</a>
                    </div>
                </div>
                <div class="property-card">
                    <div class="card-image">
                        <img src="https://images.pexels.com/photos/1643383/pexels-photo-1643383.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Property 6">
                    </div>
                    <div class="card-content">
                        <h3>Spacious Duplex in Gulshan</h3>
                        <p class="address"><i class="fas fa-map-marker-alt"></i> Gulshan, Dhaka</p>
                        <div class="property-specs">
                            <span><i class="fas fa-bed"></i> 4 Beds</span>
                            <span><i class="fas fa-bath"></i> 4 Baths</span>
                            <span><i class="fas fa-ruler-combined"></i> 2500 sqft</span>
                        </div>
                        <div style="font-weight:600; color:var(--secondary-color); margin-bottom:8px;">৳ 85,000 / Month</div>
                        <a href="#" class="btn btn-secondary" style="padding:8px 20px; font-size:0.95rem;">View Details</a>
                    </div>
                </div>
            </div>
        </section>
    </main>
    </div>
            <?php endif; ?>

    <!-- Footer Placeholder -->
    <div id="footer-placeholder"></div>

    <!-- Scripts -->
    <script src="js/loader.js"></script>

    

   <script>
document.addEventListener('DOMContentLoaded', function() {
    const images = [
        { thumb: "https://images.pexels.com/photos/1643383/pexels-photo-1643383.jpeg?auto=compress&cs=tinysrgb&w=400", full: "https://images.pexels.com/photos/1643383/pexels-photo-1643383.jpeg?auto=compress&cs=tinysrgb&w=1600" },
        { thumb: "https://images.pexels.com/photos/271624/pexels-photo-271624.jpeg?auto=compress&cs=tinysrgb&w=400", full: "https://images.pexels.com/photos/271624/pexels-photo-271624.jpeg?auto=compress&cs=tinysrgb&w=1600" },
        { thumb: "https://images.pexels.com/photos/5998120/pexels-photo-5998120.jpeg?auto=compress&cs=tinysrgb&w=400", full: "https://images.pexels.com/photos/5998120/pexels-photo-5998120.jpeg?auto=compress&cs=tinysrgb&w=1600" },
        { thumb: "https://images.pexels.com/photos/210617/pexels-photo-210617.jpeg?auto=compress&cs=tinysrgb&w=400", full: "https://images.pexels.com/photos/210617/pexels-photo-210617.jpeg?auto=compress&cs=tinysrgb&w=1600" },
        { thumb: "https://images.pexels.com/photos/1571460/pexels-photo-1571460.jpeg?auto=compress&cs=tinysrgb&w=400", full: "https://images.pexels.com/photos/1571460/pexels-photo-1571460.jpeg?auto=compress&cs=tinysrgb&w=1600" }
    ];
    let currentImg = 0;

    const mainImg = document.getElementById('mainPropertyImage');
    const imgCounter = document.getElementById('imgCounter');
    const thumbnailGrid = document.querySelector('.thumbnail-grid');

    // Clear any existing thumbnails
    thumbnailGrid.innerHTML = '';

    // Store all thumbnail boxes in an array for easy reference
    const thumbBoxes = [];

    // Create thumbnails
    images.forEach((img, idx) => {
        const thumbBox = document.createElement('div');
        thumbBox.style.cssText = "position:relative; border-radius:8px; overflow:hidden; aspect-ratio:16/10; cursor:pointer; transition: all 0.3s ease;";
        thumbBox.dataset.index = idx;
        
        const thumbImg = document.createElement('img');
        thumbImg.src = img.thumb;
        thumbImg.className = 'thumb-img';
        thumbImg.style.cssText = "width:100%; height:100%; object-fit:cover; border:2px solid #eee; border-radius:8px; transition: all 0.3s ease;";
        
        const thumbShade = document.createElement('div');
        thumbShade.className = 'thumb-shade';
        thumbShade.style.cssText = "position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.6); pointer-events:none; transition: all 0.3s ease;";

        thumbBox.appendChild(thumbImg);
        thumbBox.appendChild(thumbShade);
        
        // Add hover effects
        thumbBox.addEventListener('mouseenter', function() {
            if (idx !== currentImg) {
                thumbImg.style.transform = 'scale(1.05)';
                thumbShade.style.background = 'rgba(255,255,255,0.3)';
                thumbBox.style.transform = 'translateY(-3px)';
            }
        });
        
        thumbBox.addEventListener('mouseleave', function() {
            if (idx !== currentImg) {
                thumbImg.style.transform = 'scale(1)';
                thumbShade.style.background = 'rgba(255,255,255,0.6)';
                thumbBox.style.transform = 'translateY(0)';
            }
        });
        
        // Add click event to the box
        thumbBox.addEventListener('click', function() {
            updateGallery(idx);
        });
        
        thumbnailGrid.appendChild(thumbBox);
        thumbBoxes.push(thumbBox);
    });

    function updateGallery(newIndex) {
        currentImg = newIndex;
        
        // Update main image
        mainImg.src = images[currentImg].full;
        imgCounter.textContent = `${currentImg + 1} / ${images.length}`;

        // Update all thumbnails
        thumbBoxes.forEach((box, idx) => {
            const img = box.querySelector('.thumb-img');
            const shade = box.querySelector('.thumb-shade');
            
            if (idx === currentImg) {
                img.style.border = "2px solid #16a085";
                img.style.transform = 'scale(1)';
                shade.style.display = 'none';
                box.style.transform = 'translateY(0)';
            } else {
                img.style.border = "2px solid #eee";
                img.style.transform = 'scale(1)';
                shade.style.display = 'block';
                shade.style.background = 'rgba(255,255,255,0.6)';
                box.style.transform = 'translateY(0)';
            }
        });
    }

    // Initialize with first image
    updateGallery(0);

    // Previous/Next button functionality
    document.getElementById('imgPrevBtn').onclick = () => {
        let newIndex = (currentImg - 1 + images.length) % images.length;
        updateGallery(newIndex);
    };
    
    document.getElementById('imgNextBtn').onclick = () => {
        let newIndex = (currentImg + 1) % images.length;
        updateGallery(newIndex);
    };

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
        
        // Add hover effect to close button
        closeBtn.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.2) rotate(90deg)';
        });
        closeBtn.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) rotate(0deg)';
        });
        
        const prevBtn = document.createElement('button');
        prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
        prevBtn.style.cssText = `
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
        
        // Add hover effect to prev button
        prevBtn.addEventListener('mouseenter', function() {
            this.style.background = 'rgba(22,160,133,0.8)';
            this.style.transform = 'translateY(-50%) scale(1.1)';
        });
        prevBtn.addEventListener('mouseleave', function() {
            this.style.background = 'rgba(0,0,0,0.3)';
            this.style.transform = 'translateY(-50%) scale(1)';
        });
        
        const nextBtn = document.createElement('button');
        nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
        nextBtn.style.cssText = `
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
        
        // Add hover effect to next button
        nextBtn.addEventListener('mouseenter', function() {
            this.style.background = 'rgba(22,160,133,0.8)';
            this.style.transform = 'translateY(-50%) scale(1.1)';
        });
        nextBtn.addEventListener('mouseleave', function() {
            this.style.background = 'rgba(0,0,0,0.3)';
            this.style.transform = 'translateY(-50%) scale(1)';
        });
        
        const modalImg = document.createElement('img');
        modalImg.style.cssText = `
            max-width: 90vw;
            max-height: 85vh;
            object-fit: contain;
        `;
        
        function showImageInModal(index) {
            modalImg.src = images[index].full;
            currentImg = index;
            updateGallery(index);
        }
        
        function closeModal() {
            document.removeEventListener('keydown', keydownHandler);
            document.body.removeChild(modal);
        }
        
        function keydownHandler(e) {
            if (e.key === 'ArrowLeft') prevBtn.click();
            else if (e.key === 'ArrowRight') nextBtn.click();
            else if (e.key === 'Escape') closeModal();
        }
        
        closeBtn.addEventListener('click', closeModal);
        
        prevBtn.addEventListener('click', function() {
            let newIndex = (currentImg - 1 + images.length) % images.length;
            showImageInModal(newIndex);
        });
        
        nextBtn.addEventListener('click', function() {
            let newIndex = (currentImg + 1) % images.length;
            showImageInModal(newIndex);
        });
        
        document.addEventListener('keydown', keydownHandler);
        
        modal.appendChild(closeBtn);
        modal.appendChild(prevBtn);
        modal.appendChild(nextBtn);
        modal.appendChild(modalImg);
        
        document.body.appendChild(modal);
        showImageInModal(currentImg);
    });
});
</script>
   
    <!-- <button id="scrollToTopBtn" title="Go to top"><i class="fas fa-arrow-up"></i></button>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    
   
    <script src="script.js"></script> -->
</body>
</html>




