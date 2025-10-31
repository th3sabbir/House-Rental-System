<?php
require_once 'includes/session_check.php';
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | AmarThikana</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .profile-container {
            max-width: 900px;
            margin: 100px auto 50px;
            padding: 20px;
        }
        .profile-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin-bottom: 30px;
        }
        .profile-header {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
        }
        .profile-avatar-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #1abc9c;
        }
        .profile-avatar-placeholder-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1abc9c, #16a085);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 3rem;
            border: 4px solid #1abc9c;
        }
        .profile-info h2 {
            margin: 0 0 10px 0;
            color: #2c3e50;
        }
        .profile-role {
            display: inline-block;
            background: #1abc9c;
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: capitalize;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        .info-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .info-label {
            font-weight: 600;
            color: #7f8c8d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-value {
            color: #2c3e50;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .info-value i {
            color: #1abc9c;
            width: 20px;
        }
        .btn-edit {
            background: #1abc9c;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .btn-edit:hover {
            background: #16a085;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 188, 156, 0.3);
        }
    </style>
</head>
<body>
    <div id="header-placeholder"></div>

    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <?php if ($current_user['profile_image']): ?>
                    <img src="<?php echo htmlspecialchars($current_user['profile_image']); ?>" alt="Profile" class="profile-avatar-large">
                <?php else: ?>
                    <div class="profile-avatar-placeholder-large">
                        <?php echo strtoupper(substr($current_user['full_name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
                <div class="profile-info">
                    <h2><?php echo htmlspecialchars($current_user['full_name']); ?></h2>
                    <span class="profile-role"><?php echo htmlspecialchars($current_user['role']); ?></span>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Username</span>
                    <span class="info-value">
                        <i class="fas fa-user"></i>
                        <?php echo htmlspecialchars($current_user['username']); ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email</span>
                    <span class="info-value">
                        <i class="fas fa-envelope"></i>
                        <?php echo htmlspecialchars($current_user['email']); ?>
                    </span>
                </div>
            </div>

            <a href="settings.php" class="btn-edit">
                <i class="fas fa-edit"></i>
                Edit Profile
            </a>
        </div>

        <?php if ($current_user['role'] === 'landlord'): ?>
        <div class="profile-card">
            <h3 style="margin-bottom: 20px; color: #2c3e50;">
                <i class="fas fa-home" style="color: #1abc9c; margin-right: 10px;"></i>
                My Properties
            </h3>
            <p style="color: #7f8c8d; margin-bottom: 20px;">Manage your property listings from your dashboard.</p>
            <a href="landlord/index.php" class="btn-edit">
                <i class="fas fa-th-large"></i>
                Go to Dashboard
            </a>
        </div>
        <?php elseif ($current_user['role'] === 'tenant'): ?>
        <div class="profile-card">
            <h3 style="margin-bottom: 20px; color: #2c3e50;">
                <i class="fas fa-heart" style="color: #1abc9c; margin-right: 10px;"></i>
                My Favorites & Bookings
            </h3>
            <p style="color: #7f8c8d; margin-bottom: 20px;">View your saved properties and manage bookings.</p>
            <a href="tenant/index.php" class="btn-edit">
                <i class="fas fa-th-large"></i>
                Go to Dashboard
            </a>
        </div>
        <?php endif; ?>
    </div>

    <div id="footer-placeholder"></div>

    <script src="js/loader.js"></script>
</body>
</html>




