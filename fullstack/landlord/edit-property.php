<?php
// Edit Property Page - Full editing functionality for properties

if (!isset($user_id) || !isset($conn)) {
    die('Invalid access');
}

// Get property ID from URL
$property_id = $_GET['id'] ?? null;
if (!$property_id) {
    header('Location: ?page=my-properties');
    exit();
}

// Fetch property data
try {
    $stmt = $conn->prepare("
        SELECT p.* FROM properties p
        WHERE p.property_id = ? AND p.landlord_id = ?
    ");
    $stmt->execute([$property_id, $user_id]);
    $property = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$property) {
        echo '<div class="error-message">Property not found or you don\'t have permission to edit it.</div>';
        echo '<a href="?page=my-properties" class="btn btn-primary">Back to My Properties</a>';
        exit();
    }

    // Fetch existing images
    $stmt = $conn->prepare("
        SELECT * FROM property_images
        WHERE property_id = ?
        ORDER BY is_primary DESC, upload_date ASC
    ");
    $stmt->execute([$property_id]);
    $existing_images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch amenities
    $stmt = $conn->prepare("
        SELECT amenity FROM property_amenities
        WHERE property_id = ?
    ");
    $stmt->execute([$property_id]);
    $amenities = $stmt->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
    error_log("Edit Property Error: " . $e->getMessage());
    echo '<div class="error-message">An error occurred while loading the property.</div>';
    echo '<a href="?page=my-properties" class="btn btn-primary">Back to My Properties</a>';
    exit();
}
?>

<div class="dashboard-header">
    <div>
        <h1><i class="fas fa-edit"></i> Edit Property</h1>
        <p class="welcome-message">Update your property details and images</p>
    </div>
    <div>
        <a href="?page=my-properties" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back to Properties
        </a>
    </div>
</div>

<div class="settings-container">
    <form id="editPropertyForm" data-property-id="<?php echo $property_id; ?>">
        <!-- Property Images Section -->
        <div class="form-section">
            <h3 class="form-section-title">
                <i class="fas fa-images"></i> Property Images
            </h3>

            <!-- Existing Images -->
            <?php if (!empty($existing_images)): ?>
            <div class="existing-images-section">
                <h4>Current Images</h4>
                <div class="existing-images-grid">
                    <?php foreach ($existing_images as $image): ?>
                    <div class="existing-image-item" data-image-id="<?php echo $image['image_id']; ?>">
                        <img src="../<?php echo htmlspecialchars($image['image_path']); ?>"
                             alt="Property Image"
                             onerror="this.src='https://via.placeholder.com/200x150?text=Image+Error'">
                        <?php if ($image['is_primary']): ?>
                        <div class="image-badge primary">Main Image</div>
                        <?php else: ?>
                        <div class="image-badge existing">Existing</div>
                        <?php endif; ?>
                        <div class="image-actions">
                            <?php if (!$image['is_primary']): ?>
                            <button type="button" class="image-action-btn set-main" onclick="setAsMainImage(<?php echo $image['image_id']; ?>)">
                                <i class="fas fa-star"></i> Set Main
                            </button>
                            <?php endif; ?>
                            <button type="button" class="image-action-btn delete" onclick="deleteExistingImage(<?php echo $image['image_id']; ?>)">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- New Images Upload -->
            <div class="new-images-section">
                <h4>Add New Images</h4>
                <div class="image-upload-container">
                    <div class="main-image-preview" id="editMainImagePreview" style="display: none;">
                        <img id="editMainImage" src="" alt="Main Property Image">
                        <div class="main-image-badge">New Main Image</div>
                    </div>

                    <div class="thumbnail-preview-grid" id="editThumbnailGrid">
                        <!-- New thumbnails will be added here dynamically -->
                    </div>

                    <div class="image-upload-actions">
                        <label for="editImageUploadInput" class="btn btn-secondary">
                            <i class="fas fa-cloud-upload-alt"></i> Upload Additional Images
                        </label>
                        <input type="file" id="editImageUploadInput" accept="image/*" multiple style="display: none;">
                        <p class="upload-hint">Upload additional images (max 5 total including existing, minimum 2 required).</p>
                    </div>
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
                <input type="text" id="editPropertyName" value="<?php echo htmlspecialchars($property['title']); ?>" placeholder="e.g., Modern Family Flat in Gulshan" required>
            </div>

            <div class="form-group">
                <label for="editPropertyAddress">Full Address *</label>
                <input type="text" id="editPropertyAddress" value="<?php echo htmlspecialchars($property['address']); ?>" placeholder="e.g., Gulshan, Dhaka" required>
            </div>

            <div class="form-group">
                <label for="editPropertyCity">Location *</label>
                <select id="editPropertyCity" required>
                    <option value="" disabled>Select Location</option>
                    <option value="dhaka" <?php echo $property['city'] === 'dhaka' ? 'selected' : ''; ?>>Dhaka</option>
                    <option value="chattogram" <?php echo $property['city'] === 'chattogram' ? 'selected' : ''; ?>>Chattogram</option>
                    <option value="khulna" <?php echo $property['city'] === 'khulna' ? 'selected' : ''; ?>>Khulna</option>
                    <option value="rajshahi" <?php echo $property['city'] === 'rajshahi' ? 'selected' : ''; ?>>Rajshahi</option>
                    <option value="sylhet" <?php echo $property['city'] === 'sylhet' ? 'selected' : ''; ?>>Sylhet</option>
                    <option value="barisal" <?php echo $property['city'] === 'barisal' ? 'selected' : ''; ?>>Barisal</option>
                    <option value="rangpur" <?php echo $property['city'] === 'rangpur' ? 'selected' : ''; ?>>Rangpur</option>
                    <option value="mymensingh" <?php echo $property['city'] === 'mymensingh' ? 'selected' : ''; ?>>Mymensingh</option>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="editPropertyType">Property Type *</label>
                    <select id="editPropertyType" required>
                        <option value="">Select Type</option>
                        <option value="apartment" <?php echo $property['property_type'] === 'apartment' ? 'selected' : ''; ?>>Apartment</option>
                        <option value="house" <?php echo $property['property_type'] === 'house' ? 'selected' : ''; ?>>House</option>
                        <option value="villa" <?php echo $property['property_type'] === 'villa' ? 'selected' : ''; ?>>Villa</option>
                        <option value="studio" <?php echo $property['property_type'] === 'studio' ? 'selected' : ''; ?>>Studio</option>
                        <option value="room" <?php echo $property['property_type'] === 'room' ? 'selected' : ''; ?>>Single Room</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="editRenterType">Suitable For *</label>
                    <select id="editRenterType" required>
                        <option value="">Select Renter Type</option>
                        <option value="Family" <?php echo $property['renter_type'] === 'Family' ? 'selected' : ''; ?>>Family</option>
                        <option value="Bachelor" <?php echo $property['renter_type'] === 'Bachelor' ? 'selected' : ''; ?>>Bachelor</option>
                        <option value="Student" <?php echo $property['renter_type'] === 'Student' ? 'selected' : ''; ?>>Student</option>
                        <option value="Professional" <?php echo $property['renter_type'] === 'Professional' ? 'selected' : ''; ?>>Professional</option>
                        <option value="Any" <?php echo $property['renter_type'] === 'Any' ? 'selected' : ''; ?>>Any</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="editPropertyPrice">Rent per Month (৳) *</label>
                <input type="number" id="editPropertyPrice" value="<?php echo $property['price_per_month']; ?>" placeholder="e.g., 85000" required>
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
                    <input type="number" id="editPropertyBedrooms" value="<?php echo $property['bedrooms']; ?>" min="1" placeholder="e.g., 3" required>
                </div>

                <div class="form-group">
                    <label for="editPropertyBathrooms">Bathrooms *</label>
                    <input type="number" id="editPropertyBathrooms" value="<?php echo $property['bathrooms']; ?>" min="1" placeholder="e.g., 3" required>
                </div>

                <div class="form-group">
                    <label for="editPropertyBalcony">Balconies</label>
                    <input type="number" id="editPropertyBalcony" value="<?php echo $property['balconies'] ?? ''; ?>" min="0" placeholder="e.g., 2">
                </div>
            </div>

            <div class="form-row-three">
                <div class="form-group">
                    <label for="editPropertySize">Size (sqft) *</label>
                    <input type="number" id="editPropertySize" value="<?php echo $property['area_sqft']; ?>" placeholder="e.g., 1800" required>
                </div>

                <div class="form-group">
                    <label for="editPropertyFloor">Floor Number</label>
                    <input type="text" id="editPropertyFloor" value="<?php echo htmlspecialchars($property['floor_number'] ?? ''); ?>" placeholder="e.g., 5th Floor">
                </div>

                <div class="form-group">
                    <label for="editPropertyFacing">Facing Direction</label>
                    <select id="editPropertyFacing">
                        <option value="">Select Facing</option>
                        <option value="North" <?php echo ($property['facing'] ?? '') === 'North' ? 'selected' : ''; ?>>North</option>
                        <option value="South" <?php echo ($property['facing'] ?? '') === 'South' ? 'selected' : ''; ?>>South</option>
                        <option value="East" <?php echo ($property['facing'] ?? '') === 'East' ? 'selected' : ''; ?>>East</option>
                        <option value="West" <?php echo ($property['facing'] ?? '') === 'West' ? 'selected' : ''; ?>>West</option>
                        <option value="North-East" <?php echo ($property['facing'] ?? '') === 'North-East' ? 'selected' : ''; ?>>North-East</option>
                        <option value="North-West" <?php echo ($property['facing'] ?? '') === 'North-West' ? 'selected' : ''; ?>>North-West</option>
                        <option value="South-East" <?php echo ($property['facing'] ?? '') === 'South-East' ? 'selected' : ''; ?>>South-East</option>
                        <option value="South-West" <?php echo ($property['facing'] ?? '') === 'South-West' ? 'selected' : ''; ?>>South-West</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="editPropertyStatus">Property Status *</label>
                    <select id="editPropertyStatus" required>
                        <option value="available" <?php echo $property['status'] === 'available' ? 'selected' : ''; ?>>Available for Rent</option>
                        <option value="rented" <?php echo $property['status'] === 'rented' ? 'selected' : ''; ?>>Rented Out</option>
                    </select>
                    <small class="form-hint">Set to "Rented Out" to remove from public listings while keeping it in your dashboard.</small>
                </div>

                <div class="form-group">
                    <label for="editAvailableFrom">Available From</label>
                    <input type="date" id="editAvailableFrom" value="<?php echo $property['available_from'] ?? ''; ?>">
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
                <textarea id="editPropertyDescription" rows="5" placeholder="Describe your property in detail..." required><?php echo htmlspecialchars($property['description']); ?></textarea>
                <small class="form-hint">Provide a detailed description of your property, including its features and nearby facilities.</small>
            </div>
        </div>

        <!-- Amenities Section -->
        <div class="form-section">
            <h3 class="form-section-title">
                <i class="fas fa-check-circle"></i> Amenities & Features
            </h3>

            <div class="amenities-checkboxes">
                <?php
                $available_amenities = ['Modern Kitchen', 'Reserved Parking', '24/7 Security', 'Lift', 'Generator Backup', 'Rooftop Garden'];
                foreach ($available_amenities as $amenity):
                ?>
                <label class="checkbox-label">
                    <input type="checkbox" value="<?php echo $amenity; ?>" class="edit-amenity-checkbox" <?php echo in_array($amenity, $amenities) ? 'checked' : ''; ?>>
                    <i class="fas fa-<?php
                        $icon = match($amenity) {
                            'Modern Kitchen' => 'utensils',
                            'Reserved Parking' => 'car',
                            '24/7 Security' => 'shield-alt',
                            'Lift' => 'elevator',
                            'Generator Backup' => 'bolt',
                            'Rooftop Garden' => 'tree',
                            default => 'check'
                        };
                        echo $icon;
                    ?>"></i> <?php echo $amenity; ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Location Section -->
        <div class="form-section">
            <h3 class="form-section-title">
                <i class="fas fa-map-marker-alt"></i> Location Details
            </h3>

            <div class="form-group">
                <label for="editMapUrl">Google Maps Embed URL (Optional)</label>
                <input type="url" id="editMapUrl" value="<?php echo htmlspecialchars($property['map_url'] ?? ''); ?>" placeholder="Paste Google Maps embed URL">
                <small class="form-hint">Go to Google Maps, search your location, click Share → Embed a map, and copy the URL.</small>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="settings-actions">
            <button type="button" class="settings-btn settings-btn-secondary" onclick="window.location.href='?page=my-properties'">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="submit" class="settings-btn settings-btn-primary">
                <i class="fas fa-save"></i> Update Property
            </button>
        </div>
    </form>
</div>

<style>
/* Existing Images Styles */
.existing-images-section h4,
.new-images-section h4 {
    margin-bottom: 15px;
    color: #2c3e50;
    font-size: 1.1rem;
}

.existing-images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 30px;
}

.existing-image-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid #e0e6ed;
    transition: all 0.3s ease;
}

.existing-image-item:hover {
    border-color: #1abc9c;
    box-shadow: 0 4px 15px rgba(26, 188, 156, 0.2);
}

.existing-image-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

.image-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    color: white;
}

.image-badge.primary {
    background: #e74c3c;
}

.image-badge.existing {
    background: #27ae60;
}

.image-actions {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.8);
    padding: 8px;
    display: flex;
    gap: 5px;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.existing-image-item:hover .image-actions {
    opacity: 1;
}

.image-action-btn {
    padding: 4px 8px;
    border: none;
    border-radius: 4px;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 4px;
}

.image-action-btn.set-main {
    background: #f39c12;
    color: white;
}

.image-action-btn.set-main:hover {
    background: #e67e22;
}

.image-action-btn.delete {
    background: #e74c3c;
    color: white;
}

.image-action-btn.delete:hover {
    background: #c0392b;
}

.new-images-section {
    margin-top: 30px;
    padding-top: 30px;
    border-top: 1px solid #e0e6ed;
}

.thumbnail-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
    margin-bottom: 20px;
}

.thumbnail-preview-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid #e0e6ed;
}

.thumbnail-preview-item img {
    width: 100%;
    height: 120px;
    object-fit: cover;
}

.thumbnail-remove-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background: #e74c3c;
    color: white;
    border: none;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.thumbnail-set-main-btn {
    position: absolute;
    bottom: 5px;
    left: 5px;
    right: 5px;
    background: #f39c12;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 4px;
    font-size: 0.7rem;
    cursor: pointer;
}

.main-image-preview {
    position: relative;
    margin-bottom: 20px;
    border-radius: 8px;
    overflow: hidden;
    border: 3px solid #1abc9c;
}

.main-image-preview img {
    width: 100%;
    max-height: 300px;
    object-fit: cover;
}

.main-image-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #1abc9c;
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 600;
}
</style>

<script>
// Image management variables
let newUploadedImages = [];
let existingImages = <?php echo json_encode($existing_images); ?>;
let imagesToDelete = [];

// Image upload handling for new images
document.getElementById('editImageUploadInput').addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    const totalImages = existingImages.length - imagesToDelete.length + newUploadedImages.length + files.length;

    if (totalImages > 5) {
        alert('Maximum 5 images allowed! You currently have ' + (existingImages.length - imagesToDelete.length + newUploadedImages.length) + ' images.');
        return;
    }

    files.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            newUploadedImages.push({
                file: file,
                dataUrl: e.target.result,
                isMain: false
            });
            renderNewImagePreviews();
        };
        reader.readAsDataURL(file);
    });
});

function renderNewImagePreviews() {
    const mainPreview = document.getElementById('editMainImagePreview');
    const thumbnailGrid = document.getElementById('editThumbnailGrid');
    const mainImage = document.getElementById('editMainImage');

    if (newUploadedImages.length === 0) {
        mainPreview.style.display = 'none';
        thumbnailGrid.innerHTML = '';
        return;
    }

    // Show main image if any new image is set as main
    const newMainImg = newUploadedImages.find(img => img.isMain);
    if (newMainImg) {
        mainImage.src = newMainImg.dataUrl;
        mainPreview.style.display = 'block';
    } else {
        mainPreview.style.display = 'none';
    }

    // Show thumbnails for new images
    thumbnailGrid.innerHTML = newUploadedImages.map((img, index) => `
        <div class="thumbnail-preview-item">
            <img src="${img.dataUrl}" alt="New Property Image ${index + 1}">
            <button type="button" class="thumbnail-remove-btn" onclick="removeNewImage(${index})">
                <i class="fas fa-times"></i>
            </button>
            <button type="button" class="thumbnail-set-main-btn" onclick="setNewMainImage(${index})">
                Set as Main
            </button>
        </div>
    `).join('');
}

function removeNewImage(index) {
    newUploadedImages.splice(index, 1);
    renderNewImagePreviews();
}

function setNewMainImage(index) {
    // Unset main from existing images
    existingImages.forEach(img => img.is_primary = false);
    // Unset main from new images
    newUploadedImages.forEach((img, i) => {
        img.isMain = (i === index);
    });
    renderNewImagePreviews();
    updateExistingImageDisplay();
}

function setAsMainImage(imageId) {
    // Unset main from existing images
    existingImages.forEach(img => {
        img.is_primary = (img.image_id == imageId);
    });
    // Unset main from new images
    newUploadedImages.forEach(img => img.isMain = false);
    updateExistingImageDisplay();
    renderNewImagePreviews();
}

function deleteExistingImage(imageId) {
    if (confirm('Are you sure you want to delete this image?')) {
        imagesToDelete.push(imageId);
        // Remove from display
        const imageElement = document.querySelector(`[data-image-id="${imageId}"]`);
        if (imageElement) {
            imageElement.remove();
        }
        // If this was the main image, set another as main
        const deletedImage = existingImages.find(img => img.image_id == imageId);
        if (deletedImage && deletedImage.is_primary) {
            const remainingImages = existingImages.filter(img => !imagesToDelete.includes(img.image_id) && img.image_id != imageId);
            if (remainingImages.length > 0) {
                setAsMainImage(remainingImages[0].image_id);
            }
        }
    }
}

function updateExistingImageDisplay() {
    document.querySelectorAll('.existing-image-item').forEach(item => {
        const imageId = parseInt(item.dataset.imageId);
        const image = existingImages.find(img => img.image_id == imageId);
        if (image) {
            const badge = item.querySelector('.image-badge');
            if (image.is_primary) {
                badge.className = 'image-badge primary';
                badge.textContent = 'Main Image';
            } else {
                badge.className = 'image-badge existing';
                badge.textContent = 'Existing';
            }
        }
    });
}

// Form submission
document.getElementById('editPropertyForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const totalImages = existingImages.length - imagesToDelete.length + newUploadedImages.length;
    if (totalImages < 2) {
        alert('Please keep at least 2 property images!');
        return;
    }

    // Collect amenities
    const amenities = Array.from(document.querySelectorAll('.edit-amenity-checkbox:checked'))
        .map(checkbox => checkbox.value);

    // Prepare form data
    const formData = new FormData();
    formData.append('property_id', <?php echo $property_id; ?>);
    formData.append('title', document.getElementById('editPropertyName').value);
    formData.append('address', document.getElementById('editPropertyAddress').value);
    formData.append('city', document.getElementById('editPropertyCity').value);
    formData.append('property_type', document.getElementById('editPropertyType').value);
    formData.append('renter_type', document.getElementById('editRenterType').value);
    formData.append('price_per_month', document.getElementById('editPropertyPrice').value);
    formData.append('bedrooms', document.getElementById('editPropertyBedrooms').value);
    formData.append('bathrooms', document.getElementById('editPropertyBathrooms').value);
    formData.append('balconies', document.getElementById('editPropertyBalcony').value || 0);
    formData.append('area_sqft', document.getElementById('editPropertySize').value);
    formData.append('floor_number', document.getElementById('editPropertyFloor').value || '');
    formData.append('facing', document.getElementById('editPropertyFacing').value || '');
    formData.append('status', document.getElementById('editPropertyStatus').value);
    formData.append('available_from', document.getElementById('editAvailableFrom').value || '');
    formData.append('description', document.getElementById('editPropertyDescription').value);
    formData.append('amenities', JSON.stringify(amenities));
    formData.append('map_url', document.getElementById('editMapUrl').value || '');

    // Images to delete
    formData.append('images_to_delete', JSON.stringify(imagesToDelete));

    // New images
    newUploadedImages.forEach((img, index) => {
        formData.append('new_property_images[]', img.file);
        if (img.isMain) {
            formData.append('new_main_image_index', index);
        }
    });

    // Main image (existing or new)
    const existingMain = existingImages.find(img => img.is_primary && !imagesToDelete.includes(img.image_id));
    if (existingMain) {
        formData.append('main_image_type', 'existing');
        formData.append('main_image_id', existingMain.image_id);
    } else {
        formData.append('main_image_type', 'new');
    }

    // Show loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating Property...';

    try {
        const response = await fetch('../api/edit_property.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert('Property updated successfully! ✓');
            window.location.href = '?page=my-properties';
        } else {
            alert('Error: ' + (result.message || 'Failed to update property'));
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});
</script>