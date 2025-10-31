<?php
session_start();
require_once __DIR__ . '/includes/auth.php';

$auth = new Auth();
if (!$auth->isLoggedIn() || $_SESSION['role'] !== 'landlord') {
    header('Location: login.php?error=access_denied');
    exit();
}

require_once __DIR__ . '/config/database.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $type = $_POST['type'] ?? 'apartment';
    $price = floatval($_POST['price'] ?? 0);
    $beds = intval($_POST['beds'] ?? 0);
    $baths = intval($_POST['baths'] ?? 0);
    $sqft = intval($_POST['sqft'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    // Validation
    if (empty($title)) $errors[] = 'Title is required.';
    if (empty($address)) $errors[] = 'Address is required.';
    if ($price <= 0) $errors[] = 'Price must be greater than 0.';
    if ($beds < 0) $errors[] = 'Beds cannot be negative.';
    if ($baths < 0) $errors[] = 'Baths cannot be negative.';
    if ($sqft <= 0) $errors[] = 'Area must be greater than 0.';

    // Image upload
    $main_image_path = '';
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $tmp = $_FILES['main_image']['tmp_name'];
        $origName = basename($_FILES['main_image']['name']);
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $allowed)) {
            $errors[] = 'Invalid image type. Allowed: jpg, jpeg, png, webp.';
        } elseif ($_FILES['main_image']['size'] > 5 * 1024 * 1024) {
            $errors[] = 'Image too large. Max 5MB.';
        } else {
            $newName = uniqid('prop_') . '.' . $ext;
            $dest = $uploadDir . $newName;
            if (move_uploaded_file($tmp, $dest)) {
                $main_image_path = 'uploads/' . $newName;
            } else {
                $errors[] = 'Failed to upload image.';
            }
        }
    }

    if (empty($errors)) {
        $landlord_id = $_SESSION['user_id'];
        $sql = "INSERT INTO properties (landlord_id, title, description, property_type, address, city, price, bedrooms, bathrooms, area_sqft, main_image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'available')";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('issssiiiiss', $landlord_id, $title, $description, $type, $address, $city, $price, $beds, $baths, $sqft, $main_image_path);
            if ($stmt->execute()) {
                $success = true;
            } else {
                $errors[] = 'DB error: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $errors[] = 'DB prepare failed: ' . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Property - Landlord</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container" style="padding:40px 0;">
    <h1>Add New Property</h1>
    <?php if (!empty($errors)): ?>
        <div style="color:#9b2c2c; margin-bottom:12px;">
            <?php foreach ($errors as $e) echo '<div>' . htmlspecialchars($e) . '</div>'; ?>
        </div>
    <?php elseif ($success): ?>
        <div style="color:green; margin-bottom:12px;">Property added successfully! <a href="properties.php">View Properties</a></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Title<br><input type="text" name="title" required value="<?php echo htmlspecialchars($_POST['title'] ?? '') ?>"></label><br><br>
        <label>Address<br><input type="text" name="address" required value="<?php echo htmlspecialchars($_POST['address'] ?? '') ?>"></label><br><br>
        <label>City<br><input type="text" name="city" value="<?php echo htmlspecialchars($_POST['city'] ?? '') ?>"></label><br><br>
        <label>Type<br>
            <select name="type">
                <option value="apartment" <?php echo ($_POST['type'] ?? '') === 'apartment' ? 'selected' : '' ?>>Apartment</option>
                <option value="house" <?php echo ($_POST['type'] ?? '') === 'house' ? 'selected' : '' ?>>House</option>
                <option value="studio" <?php echo ($_POST['type'] ?? '') === 'studio' ? 'selected' : '' ?>>Studio</option>
                <option value="room" <?php echo ($_POST['type'] ?? '') === 'room' ? 'selected' : '' ?>>Room</option>
            </select>
        </label><br><br>
        <label>Price (BDT)<br><input type="number" name="price" required value="<?php echo htmlspecialchars($_POST['price'] ?? '') ?>"></label><br><br>
        <label>Beds<br><input type="number" name="beds" value="<?php echo htmlspecialchars($_POST['beds'] ?? '1') ?>"></label><br><br>
        <label>Baths<br><input type="number" name="baths" value="<?php echo htmlspecialchars($_POST['baths'] ?? '1') ?>"></label><br><br>
        <label>Area (sqft)<br><input type="number" name="sqft" required value="<?php echo htmlspecialchars($_POST['sqft'] ?? '') ?>"></label><br><br>
        <label>Main Image<br><input type="file" name="main_image" accept="image/*"></label><br><br>
        <label>Description<br><textarea name="description" rows="6"><?php echo htmlspecialchars($_POST['description'] ?? '') ?></textarea></label><br><br>
        <button class="btn btn-primary" type="submit">Add Property</button>
    </form>

    <p style="margin-top:20px;"><a href="index.php">Back to Home</a></p>
</div>
</body>
</html>
