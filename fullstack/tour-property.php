<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';
require_once 'includes/auth.php';

// Check if user is logged in and is a tenant
if (!isLoggedIn()) {
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

if ($_SESSION['role'] !== 'tenant') {
    header('Location: index.php');
    exit();
}

$property = null;
$error = null;
$success = null;
$property_id = null;

// Get property ID
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $error = "Invalid property ID.";
} else {
    $property_id = (int)$_GET['id'];
    
    // Fetch property details
    $sql = "SELECT p.*, u.full_name as landlord_name, u.email as landlord_email, u.phone as landlord_phone, 
            CONCAT(UPPER(LEFT(p.city, 1)), LOWER(SUBSTRING(p.city, 2))) as capitalized_city
            FROM properties p 
            JOIN users u ON p.landlord_id = u.user_id 
            WHERE p.property_id = ? AND p.status = 'available'";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('i', $property_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $property = $result->fetch_assoc();
        } else {
            $error = "Property not found or not available.";
        }
        $stmt->close();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_tour']) && $property) {
    $user_id = $_SESSION['user_id'];
    $move_in_date = date('Y-m-d'); // Default to today
    $message = trim($_POST['message'] ?? ''); // Get message from form
    
    // Validate inputs
    $errors = [];
    
    if (empty($message)) {
        $errors[] = "Please provide a message to the landlord.";
    }
    
    if (strlen($message) < 10) {
        $errors[] = "Please provide a more detailed message (at least 10 characters).";
    }
    
    if (empty($errors)) {
        // Calculate end date (12 months from today)
        $end_date = date('Y-m-d', strtotime($move_in_date . ' + 12 months'));
        $total_amount = $property['price_per_month']; // Store monthly amount only
        
        // Insert tour request
        $insert_sql = "INSERT INTO tours (property_id, tenant_id, landlord_id, start_date, end_date, 
                       total_amount, message, status) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";
        
        if ($insert_stmt = $conn->prepare($insert_sql)) {
            $insert_stmt->bind_param('iiiisds', 
                $property_id, 
                $user_id, 
                $property['landlord_id'], 
                $move_in_date, 
                $end_date, 
                $total_amount, 
                $message
            );
            
            if ($insert_stmt->execute()) {
                $tour_id = $insert_stmt->insert_id;
                
                // Also create a message record for the landlord
                $message_subject = "New Tour Request for " . htmlspecialchars($property['title']);
                $message_sql = "INSERT INTO messages (sender_id, receiver_id, property_id, subject, message, is_read) 
                               VALUES (?, ?, ?, ?, ?, 0)";
                
                $message_created = false;
                if ($message_stmt = $conn->prepare($message_sql)) {
                    $message_stmt->bind_param('iiiss', 
                        $user_id, 
                        $property['landlord_id'], 
                        $property_id, 
                        $message_subject, 
                        $message
                    );
                    
                    if ($message_stmt->execute()) {
                        $message_created = true;
                    } else {
                        error_log("Failed to create tour message: " . $message_stmt->error);
                    }
                    
                    $message_stmt->close();
                } else {
                    error_log("Failed to prepare message statement: " . $conn->error);
                }
                
                // Redirect to show success message with GET parameter
                $success_param = $message_created ? 'success' : 'success_no_message';
                header("Location: tour-property.php?id=$property_id&$success_param=1");
                exit();
            } else {
                $error = "Error submitting tour request. Please try again.";
            }
            $insert_stmt->close();
        }
    } else {
        $error = implode('<br>', $errors);
    }
}

// Check for success parameters in URL
if (isset($_GET['success'])) {
    $success = "Your tour request has been submitted successfully! The landlord will review your request and contact you to schedule a property tour. A notification has been sent to the landlord.";
} elseif (isset($_GET['success_no_message'])) {
    $success = "Your tour request has been submitted successfully! The landlord will review your request and contact you to schedule a property tour. (Note: Message notification could not be sent, but tour request was created.)";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Property Tour - AmarThikana</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
:root {
    --primary-color: #2c3e50;
    --secondary-color: #1abc9c;
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

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: var(--font-family-body);
    color: var(--text-dark);
    line-height: 1.7;
    background-color: var(--background-light);
    margin: 0;
    padding: 0;
}

h1, h2, h3, h4 {
    font-family: var(--font-family-heading);
    font-weight: 600;
    color: var(--text-dark);
    line-height: 1.3;
}

.container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

.btn {
    display: inline-block;
    padding: 14px 32px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    cursor: pointer;
    border: 1px solid transparent;
    text-align: center;
}

.btn-primary { 
    background-color: var(--secondary-color); 
    color: var(--background-white); 
    border: none;
}

.btn-primary:hover { 
    background-color: #16a085; 
    color: white !important;
    transform: translateY(-2px);
    box-shadow: var(--shadow-medium);
}

.btn-secondary {
    background-color: transparent;
    color: var(--secondary-color);
    border: 2px solid var(--secondary-color);
}

.btn-secondary:hover { 
    background-color: var(--secondary-color); 
    color: var(--background-white); 
}

.page-wrapper { 
    padding-top: 100px; 
    padding-bottom: 60px; 
    min-height: 100vh;
}

.booking-container {
    max-width: 900px;
    margin: 0 auto;
}

.booking-grid {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 2.5rem;
    margin-top: 2rem;
}

.booking-form-section {
    background: var(--background-white);
    padding: 2.5rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-soft);
}

.property-summary {
    background: var(--background-white);
    padding: 2rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-soft);
    position: sticky;
    top: 100px;
    height: fit-content;
}

.property-summary img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: var(--border-radius);
    margin-bottom: 1.5rem;
}

.property-summary h3 {
    font-size: 1.4rem;
    margin-bottom: 0.5rem;
}

.property-summary .location {
    color: var(--text-medium);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.property-summary .price {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--secondary-color);
    margin-bottom: 1.5rem;
}

.summary-details {
    padding-top: 1.5rem;
    border-top: 2px solid var(--border-color);
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    font-size: 1rem;
}

.summary-row.total {
    font-size: 1.2rem;
    font-weight: 700;
    padding-top: 1rem;
    border-top: 2px solid var(--border-color);
    margin-top: 1rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--text-dark);
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    font-family: inherit;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--secondary-color);
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.form-group small {
    display: block;
    margin-top: 0.5rem;
    color: var(--text-medium);
    font-size: 0.9rem;
}

.alert {
    padding: 1rem 1.5rem;
    border-radius: var(--border-radius);
    margin-bottom: 1.5rem;
    font-weight: 500;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-info {
    background-color: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.landlord-info {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: var(--border-radius);
    margin-bottom: 1.5rem;
}

.landlord-info h4 {
    font-size: 1.1rem;
    margin-bottom: 0.8rem;
}

.landlord-info p {
    margin-bottom: 0.5rem;
    color: var(--text-dark);
}

@media (max-width: 768px) {
    .page-wrapper {
        padding-top: 80px;
        padding-bottom: 30px;
    }
    
    .booking-container {
        padding: 0 15px;
    }
    
    .booking-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin-top: 1.5rem;
    }
    
    .property-summary {
        position: static;
        order: -1;
        padding: 1.5rem;
    }
    
    .booking-form-section {
        padding: 1.5rem;
    }
    
    .property-summary img {
        height: 180px;
        margin-bottom: 1rem;
    }
    
    .property-summary h3 {
        font-size: 1.2rem;
        margin-bottom: 0.75rem;
    }
    
    .property-summary .price {
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .summary-details h4 {
        font-size: 1rem;
        margin-bottom: 0.75rem;
    }
    
    .landlord-info {
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .landlord-info h4 {
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .form-group {
        margin-bottom: 1.25rem;
    }
    
    .form-group label {
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 14px 16px;
        font-size: 16px; /* Prevents zoom on iOS */
    }
    
    .form-group textarea {
        min-height: 120px;
    }
    
    .form-group small {
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }
    
    .alert {
        padding: 1rem;
        font-size: 0.95rem;
        margin-bottom: 1.25rem;
    }
    
    .btn {
        padding: 16px 24px;
        font-size: 1rem;
        width: auto;
        max-width: 300px;
        margin: 0 auto 0.5rem auto;
        display: block;
    }
    
    .summary-row {
        font-size: 0.95rem;
        margin-bottom: 0.75rem;
    }
    
    .summary-row.total {
        font-size: 1.1rem;
        margin-top: 0.75rem;
        padding-top: 0.75rem;
    }
    
    h1 {
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
    }
    
    h2 {
        font-size: 1.4rem;
        margin-bottom: 1.25rem;
    }
    
    .container p {
        font-size: 0.95rem;
        margin-bottom: 1.5rem;
    }
}

/* Custom Header Styles - Removed */

/* Force header styling on booking page */
.main-header {
    background-color: #2c3e50 !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.06) !important;
    opacity: 1 !important;
    transform: none !important;
}

.main-header .logo,
.main-header .nav-links a,
.main-header .login-btn,
.main-header .menu-toggle,
.main-header .user-name,
.main-header .user-role,
.main-header .dropdown-arrow {
    color: white !important;
}

/* Override dropdown colors to ensure visibility */
.user-dropdown,
.user-dropdown * {
    color: #2c3e50 !important;
}

/* Ensure hover effects work on dropdown items */
.user-dropdown a:hover,
.user-dropdown button:hover {
    background-color: #e9ecef !important;
    color: #1abc9c !important;
}
    </style>
<body>

<?php include 'header.php'; ?>

<div class="page-wrapper">

<div class="page-wrapper">
    <div class="container booking-container">
        <h1 style="margin-bottom: 0.5rem;">Request Property Tour</h1>
        <p style="color: var(--text-medium); margin-bottom: 2rem;">Complete the form below to send a tour request to the landlord.</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if ($property && !$success): ?>
        <div class="booking-grid">
            <!-- Booking Form -->
            <div class="booking-form-section">
                <h2 style="margin-bottom: 1.5rem;">Tour Request Details</h2>

                <div class="landlord-info">
                    <h4><i class="fas fa-user"></i> Landlord Information</h4>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($property['landlord_name']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($property['landlord_phone'] ?? 'Not provided'); ?></p>
                </div>

                <form method="POST" action="" id="tourForm">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Note:</strong> This will submit a tour request for this property. The landlord will review your request and contact you to schedule a property viewing.
                    </div>

                    <div class="form-group">
                        <label for="message">Message to Landlord <span style="color: #e74c3c;">*</span></label>
                        <textarea name="message" id="message" placeholder="Introduce yourself and explain why you're interested in this property..." required></textarea>
                        <small>Let the landlord know a bit about yourself and your requirements.</small>
                    </div>

                    <button type="submit" name="submit_tour" class="btn btn-primary" style="width: 100%; font-size: 1.1rem;">
                        <i class="fas fa-paper-plane"></i> Submit Tour Request
                    </button>
                </form>
            </div>

            <!-- Property Summary -->
            <div class="property-summary">
                <?php
                    $imgSrc = '';
                    if (!empty($property['main_image'])) {
                        $mi = trim($property['main_image']);
                        if (preg_match('/^https?:\/\//i', $mi)) {
                            $imgSrc = $mi;
                        } elseif (strpos($mi, 'uploads/') === 0 || strpos($mi, 'images/') === 0 || strpos($mi, 'img/') === 0) {
                            $imgSrc = $mi;
                        } elseif (file_exists(__DIR__ . '/uploads/properties/' . $mi)) {
                            $imgSrc = 'uploads/properties/' . $mi;
                        } elseif (file_exists(__DIR__ . '/images/' . $mi)) {
                            $imgSrc = 'images/' . $mi;
                        } elseif (file_exists(__DIR__ . '/img/' . $mi)) {
                            $imgSrc = 'img/' . $mi;
                        } else {
                            $imgSrc = 'https://images.pexels.com/photos/1643383/pexels-photo-1643383.jpeg?auto=compress&cs=tinysrgb&w=800';
                        }
                    } else {
                        $imgSrc = 'https://images.pexels.com/photos/1643383/pexels-photo-1643383.jpeg?auto=compress&cs=tinysrgb&w=800';
                    }
                ?>
                <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($property['title']); ?>" loading="lazy" onerror="this.onerror=null;this.src='https://images.pexels.com/photos/1643383/pexels-photo-1643383.jpeg?auto=compress&cs=tinysrgb&w=800';">
                
                <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                <p class="location">
                    <i class="fas fa-map-marker-alt"></i>
                    <?php echo htmlspecialchars($property['address'] . ', ' . $property['capitalized_city']); ?>
                </p>
                
                <div class="price">
                    ৳<?php echo number_format($property['price_per_month']); ?> <span style="font-size: 0.9rem; font-weight: 400;">/month</span>
                </div>

                <div style="display: flex; gap: 1.5rem; margin-bottom: 1.5rem; color: var(--text-medium);">
                    <span><i class="fas fa-bed"></i> <?php echo $property['bedrooms']; ?> Beds</span>
                    <span><i class="fas fa-bath"></i> <?php echo $property['bathrooms']; ?> Baths</span>
                    <span><i class="fas fa-ruler-combined"></i> <?php echo $property['area_sqft']; ?> sqft</span>
                </div>

                <div class="summary-details">
                    <h4 style="margin-bottom: 1rem;">Tour Request Summary</h4>
                    <div class="summary-row">
                        <span>Monthly Rent:</span>
                        <span>৳<?php echo number_format($property['price_per_month']); ?></span>
                    </div>
                </div>

                <a href="property-details.php?id=<?php echo $property_id; ?>" class="btn btn-secondary" style="width: 100%; margin-top: 1.5rem;">
                    <i class="fas fa-arrow-left"></i> Back to Property
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('tourForm');
    const messageField = document.getElementById('message');
    
    form.addEventListener('submit', function(e) {
        const message = messageField.value.trim();
        
        if (message.length === 0) {
            alert('Please provide a message to the landlord.');
            messageField.focus();
            e.preventDefault();
            return false;
        }
        
        if (message.length < 10) {
            alert('Please provide a more detailed message (at least 10 characters).');
            messageField.focus();
            e.preventDefault();
            return false;
        }
        
        return true;
    });
});
</script>

</body>
</html>
