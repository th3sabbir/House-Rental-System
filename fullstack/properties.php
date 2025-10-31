<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Properties - AmarThikana</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="css/style.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        /* Properties Page Styles */
        .properties-hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 100px 0 60px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .properties-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
        }

        .properties-hero-content {
            position: relative;
            z-index: 1;
        }

        .properties-hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .properties-hero p {
            font-size: 1.2rem;
            opacity: 0.95;
        }

        /* Categories Section */
        .categories-section {
            padding: 60px 0;
            background: #f8f9fa;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }

        .category-card {
            background: white;
            padding: 30px 20px;
            border-radius: 15px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .category-card:hover,
        .category-card.active {
            border-color: var(--primary-color);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .category-card .icon-wrapper {
            width: 70px;
            height: 70px;
            margin: 0 auto 15px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
        }

        .category-card h3 {
            font-size: 1.1rem;
            margin-bottom: 5px;
            color: var(--text-color);
        }

        .category-card .count {
            font-size: 0.9rem;
            color: #666;
        }

        /* Filter & Search Section */
        .filter-section {
            padding: 40px 0;
            background: white;
            border-bottom: 1px solid #e0e0e0;
        }

        .filter-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 45px 12px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .search-box i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        .filter-options {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        /* Custom Dropdown for Properties Page */
        .filter-dropdown {
            position: relative;
        }

        .filter-dropdown select {
            display: none;
        }

        .properties-dropdown-box {
            position: relative;
            min-width: 200px;
        }

        .properties-dropdown-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            border: none;
            border-bottom: 2px solid #16a085;
            background: transparent;
            font-size: 1rem;
            padding: 10px 0;
            color: #222;
            transition: border-color 0.2s;
            width: 100%;
        }

        .properties-dropdown-header:hover {
            border-color: #2980b9;
        }

        .properties-dropdown-header span {
            flex: 1;
            text-align: left;
        }

        .properties-dropdown-header i {
            color: #16a085;
            font-size: 0.9rem;
            transition: transform 0.3s;
            margin-left: 10px;
            flex-shrink: 0;
        }

        .properties-dropdown-box.active .properties-dropdown-header i {
            transform: rotate(180deg);
        }

        .properties-dropdown-list {
            position: absolute;
            top: calc(100% + 5px);
            left: 0;
            right: 0;
            background: white;
            border: 2px solid #16a085;
            border-radius: 8px;
            max-height: 250px;
            overflow-y: auto;
            z-index: 100;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            min-width: 200px;
        }

        .properties-dropdown-box.active .properties-dropdown-list {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .properties-dropdown-item {
            padding: 12px 15px;
            cursor: pointer;
            transition: background 0.2s;
            color: #222;
            font-size: 1rem;
        }

        .properties-dropdown-item:hover {
            background: rgba(22, 160, 133, 0.1);
            color: #16a085;
        }

        .properties-dropdown-item.selected {
            background: rgba(22, 160, 133, 0.15);
            color: #16a085;
            font-weight: 600;
        }

        /* Scrollbar for dropdown */
        .properties-dropdown-list::-webkit-scrollbar {
            width: 6px;
        }

        .properties-dropdown-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .properties-dropdown-list::-webkit-scrollbar-thumb {
            background: #16a085;
            border-radius: 10px;
        }

        .properties-dropdown-list::-webkit-scrollbar-thumb:hover {
            background: #2980b9;
        }

        .sort-by {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #666;
        }

        /* Results Info */
        .results-info {
            padding: 30px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .results-count {
            font-size: 1.1rem;
            color: var(--text-color);
        }

        .results-count span {
            color: var(--primary-color);
            font-weight: 600;
        }

        .view-toggle {
            display: flex;
            gap: 10px;
        }

        .view-btn {
            width: 40px;
            height: 40px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            color: #666;
        }

        .view-btn.active,
        .view-btn:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            background: rgba(22, 160, 133, 0.1);
        }

        /* Properties Grid */
        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            padding: 30px 0;
        }

        .properties-grid.list-view {
            grid-template-columns: 1fr;
        }

        .property-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            transition: all 0.4s;
            cursor: pointer;
        }

        .property-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .properties-grid.list-view .property-card {
            display: flex;
            flex-direction: row;
        }

        .property-image {
            position: relative;
            height: 250px;
            overflow: hidden;
        }

        .properties-grid.list-view .property-image {
            width: 40%;
            height: auto;
            min-height: 280px;
        }

        .property-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s;
        }

        .property-card:hover .property-image img {
            transform: scale(1.1);
        }

        .property-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--secondary-color);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .wishlist-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 40px;
            height: 40px;
            background: white;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            color: #666;
        }

        .wishlist-btn:hover {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }

        .wishlist-btn.active {
            background: #e74c3c;
            color: white;
        }

        .property-info {
            padding: 25px;
        }

        .properties-grid.list-view .property-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .property-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .property-price span {
            font-size: 0.9rem;
            color: #666;
            font-weight: 400;
        }

        .property-title {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: var(--text-color);
            font-weight: 600;
        }

        .property-location {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            margin-bottom: 15px;
        }

        .property-location i {
            color: var(--primary-color);
        }

        .property-features {
            display: flex;
            gap: 20px;
            padding: 15px 0;
            border-top: 1px solid #f0f0f0;
            border-bottom: 1px solid #f0f0f0;
            margin: 15px 0;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            font-size: 0.95rem;
        }

        .feature-item i {
            color: var(--primary-color);
        }

        .property-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }

        .property-type {
            background: rgba(22, 160, 133, 0.1);
            color: var(--primary-color);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .view-details-btn {
            background: var(--primary-color);
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .view-details-btn:hover {
            background: var(--secondary-color);
            transform: translateX(5px);
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            padding: 60px 0;
        }

        .pagination button {
            padding: 10px 18px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            color: #666;
            font-weight: 600;
        }

        .pagination button:hover,
        .pagination button.active {
            border-color: var(--primary-color);
            background: var(--primary-color);
            color: white;
        }

        .pagination button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* No Results */
        .no-results {
            text-align: center;
            padding: 80px 20px;
        }

        .no-results i {
            font-size: 5rem;
            color: #e0e0e0;
            margin-bottom: 20px;
        }

        .no-results h3 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            color: var(--text-color);
        }

        .no-results p {
            color: #666;
            margin-bottom: 30px;
        }

        @media (max-width: 768px) {
            .properties-hero h1 {
                font-size: 2rem;
            }

            .filter-container {
                flex-direction: column;
            }

            .search-box {
                width: 100%;
            }

            .properties-grid {
                grid-template-columns: 1fr;
            }

            .properties-grid.list-view .property-card {
                flex-direction: column;
            }

            .properties-grid.list-view .property-image {
                width: 100%;
                height: 250px;
            }

            .categories-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
   <!-- Header Placeholder -->
    <div id="header-placeholder"></div>

    <!-- Hero Section -->
    <section class="properties-hero">
        <div class="container properties-hero-content">
            <h1>Find Your Perfect Home</h1>
            <p>Browse through our extensive collection of verified rental properties</p>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="container">
            <h2 class="section-title">Browse by Category</h2>
            <div class="categories-grid">
                <div class="category-card active" data-category="all">
                    <div class="icon-wrapper">
                        <i class="fas fa-th-large"></i>
                    </div>
                    <h3>All Properties</h3>
                    <p class="count">245 listings</p>
                </div>

                <div class="category-card" data-category="apartment">
                    <div class="icon-wrapper">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3>Apartments</h3>
                    <p class="count">128 listings</p>
                </div>

                <div class="category-card" data-category="house">
                    <div class="icon-wrapper">
                        <i class="fas fa-home"></i>
                    </div>
                    <h3>Houses</h3>
                    <p class="count">67 listings</p>
                </div>

                <div class="category-card" data-category="studio">
                    <div class="icon-wrapper">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <h3>Studios</h3>
                    <p class="count">32 listings</p>
                </div>

                <div class="category-card" data-category="duplex">
                    <div class="icon-wrapper">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <h3>Duplex</h3>
                    <p class="count">18 listings</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Filter Section -->
    <section class="filter-section">
        <div class="container">
            <div class="filter-container">
                <div class="search-box">
                    <input type="text" placeholder="Search by location, title, or keyword..." id="searchInput">
                    <i class="fas fa-search"></i>
                </div>

                <div class="filter-options">
                    <div class="filter-dropdown">
                        <select id="priceFilter">
                            <option value="">Price Range</option>
                            <option value="0-10000">Under ৳10,000</option>
                            <option value="10000-20000">৳10,000 - ৳20,000</option>
                            <option value="20000-30000">৳20,000 - ৳30,000</option>
                            <option value="30000-50000">৳30,000 - ৳50,000</option>
                            <option value="50000+">Above ৳50,000</option>
                        </select>
                    </div>

                    <div class="filter-dropdown">
                        <select id="bedroomFilter">
                            <option value="">Bedrooms</option>
                            <option value="1">1 Bedroom</option>
                            <option value="2">2 Bedrooms</option>
                            <option value="3">3 Bedrooms</option>
                            <option value="4+">4+ Bedrooms</option>
                        </select>
                    </div>

                    <div class="filter-dropdown">
                        <select id="cityFilter">
                            <option value="">City</option>
                            <option value="dhaka">Dhaka</option>
                            <option value="chattogram">Chattogram</option>
                            <option value="khulna">Khulna</option>
                            <option value="sylhet">Sylhet</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Properties Listing -->
    <section class="properties-listing">
        <div class="container">
            <div class="results-info">
                <div class="results-count">
                    Showing <span id="resultCount">245</span> properties
                </div>
                <div class="sort-by">
                    <span>Sort by:</span>
                    <div class="filter-dropdown">
                        <select id="sortFilter">
                            <option value="featured">Featured</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="newest">Newest First</option>
                        </select>
                    </div>
                </div>
                <div class="view-toggle">
                    <button class="view-btn active" data-view="grid"><i class="fas fa-th"></i></button>
                    <button class="view-btn" data-view="list"><i class="fas fa-list"></i></button>
                </div>
            </div>

            <div class="properties-grid" id="propertiesGrid">
                <?php
                // Include database connection
                require_once __DIR__ . '/config/database.php';

                // Basic filter handling (can be expanded)
                $where = [];
                $params = [];
                $types = '';

                if (!empty($_GET['city'])) {
                    $where[] = 'city = ?';
                    $params[] = $_GET['city'];
                    $types .= 's';
                }
                if (!empty($_GET['type'])) {
                    $where[] = 'property_type = ?';
                    $params[] = $_GET['type'];
                    $types .= 's';
                }

                $sql = "SELECT id, title, price, CONCAT(COALESCE(address,''), CASE WHEN city IS NOT NULL AND city!='' THEN CONCAT(', ', city) ELSE '' END) AS location, bedrooms AS beds, bathrooms AS baths, area_sqft AS sqft, main_image, property_type FROM properties WHERE status = 'available'";
                if ($where) $sql .= ' AND ' . implode(' AND ', $where);
                $sql .= ' ORDER BY created_at DESC';

                $rows = [];
                if ($stmt = $conn->prepare($sql)) {
                    if ($params) {
                        $stmt->bind_param($types, ...$params);
                    }
                    $stmt->execute();
                    $res = $stmt->get_result();
                    while ($r = $res->fetch_assoc()) $rows[] = $r;
                    $stmt->close();
                }

                if (!$rows): ?>
                    <div class="no-results">
                        <i class="fas fa-search"></i>
                        <h3>No Properties Found</h3>
                        <p>Try adjusting your search criteria or browse all properties.</p>
                    </div>
                <?php else: foreach ($rows as $row): ?>
                    <div class="property-card" data-category="<?php echo htmlspecialchars($row['property_type']) ?>" data-price="<?php echo (int)$row['price'] ?>" data-bedrooms="<?php echo (int)$row['beds'] ?>">
                        <div class="property-image">
                            <img src="<?php echo htmlspecialchars($row['main_image'] ?: 'images/default-property.jpg') ?>" alt="<?php echo htmlspecialchars($row['title']) ?>" loading="lazy">
                            <div class="property-badge">Available</div>
                            <button class="wishlist-btn"><i class="far fa-heart"></i></button>
                        </div>
                        <div class="property-info">
                            <div class="property-price">৳<?php echo number_format($row['price']) ?> <span>/month</span></div>
                            <h3 class="property-title"><?php echo htmlspecialchars($row['title']) ?></h3>
                            <div class="property-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($row['location']) ?></span>
                            </div>
                            <div class="property-features">
                                <div class="feature-item">
                                    <i class="fas fa-bed"></i>
                                    <span><?php echo (int)$row['beds'] ?> Beds</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-bath"></i>
                                    <span><?php echo (int)$row['baths'] ?> Baths</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-ruler-combined"></i>
                                    <span><?php echo (int)$row['sqft'] ?> sqft</span>
                                </div>
                            </div>
                            <div class="property-footer">
                                <span class="property-type"><?php echo htmlspecialchars(ucfirst($row['property_type'])) ?></span>
                                <a href="property-details.php?id=<?php echo (int)$row['id'] ?>" class="view-details-btn">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <button disabled><i class="fas fa-chevron-left"></i></button>
                <button class="active">1</button>
                <button>2</button>
                <button>3</button>
                <button>4</button>
                <button>5</button>
                <button><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <!-- Footer Placeholder -->
    <div id="footer-placeholder"></div>

    <!-- Scripts -->
    <script src="js/loader.js"></script>
    <script src="js/script.js"></script>
    <script>
        // Custom Dropdown Functionality for Properties Page
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.filter-dropdown').forEach(function(wrapper) {
                const originalSelect = wrapper.querySelector('select');
                if (!originalSelect) return;

                // Create custom dropdown structure
                const dropdownBox = document.createElement('div');
                dropdownBox.className = 'properties-dropdown-box';
                
                const dropdownHeader = document.createElement('div');
                dropdownHeader.className = 'properties-dropdown-header';
                
                const dropdownList = document.createElement('div');
                dropdownList.className = 'properties-dropdown-list';

                // Create header text
                const headerText = document.createElement('span');
                headerText.textContent = originalSelect.options[0].text;
                
                // Create arrow icon
                const arrow = document.createElement('i');
                arrow.className = 'fas fa-chevron-down';
                
                dropdownHeader.appendChild(headerText);
                dropdownHeader.appendChild(arrow);

                // Populate dropdown items
                Array.from(originalSelect.options).forEach(function(option, index) {
                    if (index === 0) return; // Skip placeholder
                    
                    const dropdownItem = document.createElement('div');
                    dropdownItem.className = 'properties-dropdown-item';
                    dropdownItem.textContent = option.text;
                    dropdownItem.dataset.value = option.value;
                    
                    if (option.selected) {
                        dropdownItem.classList.add('selected');
                    }
                    
                    dropdownItem.addEventListener('click', function() {
                        // Update original select
                        originalSelect.value = this.dataset.value;
                        
                        // Update header text
                        headerText.textContent = this.textContent;
                        
                        // Update selected state
                        dropdownList.querySelectorAll('.properties-dropdown-item').forEach(item => {
                            item.classList.remove('selected');
                        });
                        this.classList.add('selected');
                        
                        // Close dropdown
                        dropdownBox.classList.remove('active');
                        
                        // Trigger change event
                        originalSelect.dispatchEvent(new Event('change'));
                    });
                    
                    dropdownList.appendChild(dropdownItem);
                });

                dropdownBox.appendChild(dropdownHeader);
                dropdownBox.appendChild(dropdownList);
                
                // Insert custom dropdown before original select
                originalSelect.parentNode.insertBefore(dropdownBox, originalSelect);

                // Toggle dropdown
                dropdownHeader.addEventListener('click', function(e) {
                    e.stopPropagation();
                    
                    // Close other dropdowns
                    document.querySelectorAll('.properties-dropdown-box.active').forEach(function(box) {
                        if (box !== dropdownBox) {
                            box.classList.remove('active');
                        }
                    });
                    
                    dropdownBox.classList.toggle('active');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function() {
                    dropdownBox.classList.remove('active');
                });
            });
        });

        // Category Filter
        const categoryCards = document.querySelectorAll('.category-card');
        const propertyCards = document.querySelectorAll('.property-card');

        categoryCards.forEach(card => {
            card.addEventListener('click', () => {
                categoryCards.forEach(c => c.classList.remove('active'));
                card.classList.add('active');
                
                const category = card.dataset.category;
                filterProperties();
            });
        });

        // Price Filter
        document.getElementById('priceFilter').addEventListener('change', filterProperties);

        // Bedroom Filter
        document.getElementById('bedroomFilter').addEventListener('change', filterProperties);

        // City Filter
        document.getElementById('cityFilter').addEventListener('change', filterProperties);

        // Sort Filter
        document.getElementById('sortFilter').addEventListener('change', filterProperties);

        function filterProperties() {
            const category = document.querySelector('.category-card.active').dataset.category;
            const priceRange = document.getElementById('priceFilter').value;
            const bedroomFilter = document.getElementById('bedroomFilter').value;
            const cityFilter = document.getElementById('cityFilter').value;
            const sortBy = document.getElementById('sortFilter').value;
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();

            let visibleCount = 0;
            const cards = document.querySelectorAll('.property-card');
            const cardsArray = Array.from(cards);

            cardsArray.forEach(card => {
                const cardCategory = card.dataset.category;
                const cardPrice = parseInt(card.dataset.price);
                const cardBedrooms = parseInt(card.dataset.bedrooms);
                const title = card.querySelector('.property-title').textContent.toLowerCase();
                const location = card.querySelector('.property-location span').textContent.toLowerCase();

                let show = true;

                // Category filter
                if (category !== 'all' && cardCategory !== category) {
                    show = false;
                }

                // Price filter
                if (priceRange) {
                    const [min, max] = priceRange.split('-').map(v => v === '+' ? Infinity : parseInt(v));
                    if (cardPrice < min || (max !== Infinity && cardPrice > max)) {
                        show = false;
                    }
                }

                // Bedroom filter
                if (bedroomFilter) {
                    if (bedroomFilter === '4+' && cardBedrooms < 4) {
                        show = false;
                    } else if (bedroomFilter !== '4+' && cardBedrooms !== parseInt(bedroomFilter)) {
                        show = false;
                    }
                }

                // Search filter
                if (searchTerm && !title.includes(searchTerm) && !location.includes(searchTerm)) {
                    show = false;
                }

                if (show) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Sort cards
            if (sortBy !== 'featured') {
                const grid = document.getElementById('propertiesGrid');
                const visibleCards = cardsArray.filter(card => card.style.display !== 'none');
                
                visibleCards.sort((a, b) => {
                    const priceA = parseInt(a.dataset.price);
                    const priceB = parseInt(b.dataset.price);
                    
                    switch(sortBy) {
                        case 'price-low':
                            return priceA - priceB;
                        case 'price-high':
                            return priceB - priceA;
                        case 'newest':
                            // For now, keep original order (would need timestamp data)
                            return 0;
                        default:
                            return 0;
                    }
                });
                
                visibleCards.forEach(card => grid.appendChild(card));
            }

            document.getElementById('resultCount').textContent = visibleCount;
        }

        // Search Filter
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', filterProperties);

        // View Toggle
        const viewBtns = document.querySelectorAll('.view-btn');
        const propertiesGrid = document.getElementById('propertiesGrid');

        viewBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                viewBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                const view = btn.dataset.view;
                if (view === 'list') {
                    propertiesGrid.classList.add('list-view');
                } else {
                    propertiesGrid.classList.remove('list-view');
                }
            });
        });

        // Wishlist Toggle
        document.querySelectorAll('.wishlist-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                btn.classList.toggle('active');
                const icon = btn.querySelector('i');
                if (btn.classList.contains('active')) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                }
            });
        });

        // Property Card Click
        propertyCards.forEach(card => {
            card.addEventListener('click', (e) => {
                if (!e.target.closest('.wishlist-btn') && !e.target.closest('.view-details-btn')) {
                    const link = card.querySelector('.view-details-btn');
                    if (link) window.location.href = link.href;
                }
            });
        });
    </script>
</body>
</html>




