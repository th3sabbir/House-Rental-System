<?php
// Add Property Page

if (!isset($user_id) || !isset($conn)) {
    header('Location: index.php');
    exit();
}
?>

<div class="dashboard-header">
    <h1><i class="fas fa-plus-circle"></i> Add New Property</h1>
    <p class="welcome-message">Fill in the details below to list your property</p>
</div>

<div class="settings-container">
    <form id="addPropertyForm">
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
                        <p class="upload-hint">Upload 2-5 images. First image will be the main image.</p>
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

            <div class="form-group">
                <label for="addPropertyCity">Location *</label>
                <select id="addPropertyCity" required>
                    <option value="" disabled selected>Select Location</option>
                    <option value="dhaka">Dhaka</option>
                    <option value="chattogram">Chattogram</option>
                    <option value="khulna">Khulna</option>
                    <option value="rajshahi">Rajshahi</option>
                    <option value="sylhet">Sylhet</option>
                    <option value="barisal">Barisal</option>
                    <option value="rangpur">Rangpur</option>
                    <option value="mymensingh">Mymensingh</option>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="addPropertyType">Property Type *</label>
                    <select id="addPropertyType" required>
                        <option value="">Select Type</option>
                        <option value="apartment">Apartment</option>
                        <option value="house">House</option>
                        <option value="villa">Villa</option>
                        <option value="studio">Studio</option>
                        
                    </select>
                </div>

                <div class="form-group">
                    <label for="addRenterType">Suitable For *</label>
                    <select id="addRenterType" required>
                        <option value="">Select Renter Type</option>
                        <option value="Family">Family</option>
                        <option value="Bachelor">Bachelor</option>
                        <!-- <option value="Student">Student</option>
                        <option value="Professional">Professional</option> -->
                        <option value="Any">Any</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="addPropertyPrice">Rent per Month (৳) *</label>
                <input type="number" id="addPropertyPrice" placeholder="e.g., 85000" required>
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
                    <input type="month" id="addAvailableFrom" placeholder="Select month and year">
                    <small class="form-hint">Select the month and year when the property will be available</small>
                </div>

                <div class="form-group">
                    <label for="addPropertyFloorExtra">Additional Info</label>
                    <input type="text" id="addPropertyFloorExtra" placeholder="e.g., Corner Unit">
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
                    <input type="checkbox" value="Modern Kitchen" class="add-amenity-checkbox">
                    <i class="fas fa-utensils"></i> Modern Kitchen
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
                    <input type="checkbox" value="Lift" class="add-amenity-checkbox">
                    <i class="fas fa-elevator"></i> Lift
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" value="Generator Backup" class="add-amenity-checkbox">
                    <i class="fas fa-bolt"></i> Generator Backup
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" value="Rooftop Garden" class="add-amenity-checkbox">
                    <i class="fas fa-tree"></i> Rooftop Garden
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

        <!-- Form Actions -->
        <div class="settings-actions">
            <button type="button" class="settings-btn settings-btn-secondary" onclick="window.location.href='?page=dashboard'">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="submit" class="settings-btn settings-btn-primary">
                <i class="fas fa-plus-circle"></i> Add Property
            </button>
        </div>
    </form>
</div>

<script>
// Image upload handling
let uploadedImages = [];

document.getElementById('addImageUploadInput').addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    
    if (files.length + uploadedImages.length > 5) {
        alert('Maximum 5 images allowed!');
        return;
    }
    
    files.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            uploadedImages.push({
                file: file,
                dataUrl: e.target.result,
                isMain: uploadedImages.length === 0
            });
            renderImagePreviews();
        };
        reader.readAsDataURL(file);
    });
});

function renderImagePreviews() {
    const mainPreview = document.getElementById('addMainImagePreview');
    const thumbnailGrid = document.getElementById('addThumbnailGrid');
    const mainImage = document.getElementById('addMainImage');
    
    if (uploadedImages.length === 0) {
        mainPreview.style.display = 'none';
        thumbnailGrid.innerHTML = '';
        return;
    }
    
    // Show main image
    const mainImg = uploadedImages.find(img => img.isMain) || uploadedImages[0];
    mainImage.src = mainImg.dataUrl;
    mainPreview.style.display = 'block';
    
    // Show thumbnails
    thumbnailGrid.innerHTML = uploadedImages.map((img, index) => `
        <div class="thumbnail-preview-item">
            <img src="${img.dataUrl}" alt="Property Image ${index + 1}">
            <button type="button" class="thumbnail-remove-btn" onclick="removeImage(${index})">
                <i class="fas fa-times"></i>
            </button>
            ${!img.isMain ? `
                <button type="button" class="thumbnail-set-main-btn" onclick="setMainImage(${index})">
                    Set as Main
                </button>
            ` : ''}
        </div>
    `).join('');
}

function removeImage(index) {
    uploadedImages.splice(index, 1);
    if (uploadedImages.length > 0 && !uploadedImages.some(img => img.isMain)) {
        uploadedImages[0].isMain = true;
    }
    renderImagePreviews();
}

function setMainImage(index) {
    uploadedImages.forEach((img, i) => {
        img.isMain = (i === index);
    });
    renderImagePreviews();
}

// Form submission
document.getElementById('addPropertyForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Validate
    if (uploadedImages.length < 2) {
        alert('Please upload at least 2 property images!');
        return;
    }
    
    // Collect amenities
    const amenities = Array.from(document.querySelectorAll('.add-amenity-checkbox:checked'))
        .map(checkbox => checkbox.value);
    
    // Prepare form data
    const formData = new FormData();
    formData.append('title', document.getElementById('addPropertyName').value);
    formData.append('address', document.getElementById('addPropertyAddress').value);
    formData.append('city', document.getElementById('addPropertyCity').value);
    formData.append('property_type', document.getElementById('addPropertyType').value);
    formData.append('renter_type', document.getElementById('addRenterType').value);
    formData.append('price_per_month', document.getElementById('addPropertyPrice').value);
    formData.append('bedrooms', document.getElementById('addPropertyBedrooms').value);
    formData.append('bathrooms', document.getElementById('addPropertyBathrooms').value);
    formData.append('balconies', document.getElementById('addPropertyBalcony').value || 0);
    formData.append('area_sqft', document.getElementById('addPropertySize').value);
    formData.append('floor_number', document.getElementById('addPropertyFloor').value || '');
    formData.append('facing', document.getElementById('addPropertyFacing').value || '');
    
    // Format available_from to YYYY-MM-DD (first day of selected month)
    let availableFrom = document.getElementById('addAvailableFrom').value;
    if (availableFrom) {
        availableFrom = availableFrom + '-01'; // Add day as 01
    }
    formData.append('available_from', availableFrom || '');
    
    formData.append('description', document.getElementById('addPropertyDescription').value);
    formData.append('amenities', JSON.stringify(amenities));
    formData.append('map_url', document.getElementById('addMapUrl').value || '');
    
    // Append images
    uploadedImages.forEach((img, index) => {
        formData.append('property_images[]', img.file);
        if (img.isMain) {
            formData.append('main_image_index', index);
        }
    });
    
    // Show loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding Property...';
    
    try {
        const response = await fetch('../api/add_property.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Property added successfully! ✓');
            window.location.href = '?page=dashboard';
        } else {
            alert('Error: ' + (result.message || 'Failed to add property'));
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
