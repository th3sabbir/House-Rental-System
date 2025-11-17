<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AmarThikana - Find Your Perfect Rental Home</title>
    
   
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">

   
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css"/>

    
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/mobile.css">
    <style>
        /* Mobile Search Form Fix */
        @media (max-width: 768px) {
            .hero-content {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                padding: 0 10px !important;
                width: 100% !important;
            }
            
            .search-form {
                display: flex !important;
                flex-direction: column !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 20px 15px !important;
                gap: 15px !important;
                margin: 30px 0 0 0 !important;
                box-sizing: border-box !important;
                align-items: stretch !important;
            }
            
            .custom-select-wrapper {
                width: 100% !important;
                max-width: 100% !important;
                display: flex !important;
                align-items: center !important;
                background: rgba(255, 255, 255, 0.9) !important;
                padding: 12px 15px !important;
                border-radius: 8px !important;
                box-sizing: border-box !important;
            }
            
            .custom-select-wrapper i {
                margin-right: 10px !important;
                color: #7f8c8d !important;
            }
            
            .custom-select-wrapper select {
                width: 100% !important;
                flex: 1 !important;
                border: none !important;
                background: transparent !important;
                font-size: 1rem !important;
            }
            
            .custom-select {
                width: 100% !important;
                max-width: 100% !important;
            }
            
            .custom-select-trigger {
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
            }
            
            .custom-options {
                width: 100% !important;
                left: 0 !important;
                right: 0 !important;
            }
            
            .search-form .btn {
                width: 100% !important;
                max-width: 100% !important;
                padding: 14px !important;
                margin: 0 !important;
                box-sizing: border-box !important;
            }
        }
        
        @media (max-width: 480px) {
            .search-form {
                padding: 15px 10px !important;
                margin: 20px 0 0 0 !important;
            }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

   <div id="header-placeholder"></div>
   

    <main>
        
        <section class="hero-section">
            <div class="hero-overlay"></div>
            <div class="hero-content container">
                <h1>Find a place that feels like a home.</h1>
                <p>Discover thousands of apartments, houses, and rooms for rent.</p>
                
                
               
                <form class="search-form" method="get" action="search-result.php">
    <!-- <div class="input-group select-group"> -->
        <div class="custom-select-wrapper">
        <i class="fas fa-map-marker-alt"></i>
        <select 
            name="location"
            style="
                border: none;
                border-bottom: 2px solid #16a085;
                background: transparent;
                font-size: 1.07rem;
                padding: 4px 0 2px 0;
                outline: none;
                color: #222;
                transition: border-color 0.2s;
                appearance: none;
                -webkit-appearance: none;
                -moz-appearance: none;
            "
            onfocus="this.style.borderColor='#2980b9'"
            onblur="this.style.borderColor='#16a085'"
        >
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
        <!-- <i class="fas fa-chevron-down" style="color:#16a085; font-size:0.95rem; margin-left:-22px; pointer-events:none;"></i> -->
    </div>
    <!-- <div class="input-group select-group"> -->
        <div class="custom-select-wrapper">
        <i class="fas fa-building"></i>
        <select 
            name="property_type"
            style="
                border: none;
                border-bottom: 2px solid #16a085;
                background: transparent;
                font-size: 1.07rem;
                padding: 4px 0 2px 0;
                outline: none;
                color: #222;
                transition: border-color 0.2s;
                appearance: none;
                -webkit-appearance: none;
                -moz-appearance: none;
            "
            onfocus="this.style.borderColor='#2980b9'"
            onblur="this.style.borderColor='#16a085'"
        >
            <option disabled selected>Property Type</option>
        <option value="apartment">Apartment</option>
            <option value="house">House</option>
            <option value="studio">Studio</option>
        </select>
        <!-- <i class="fas fa-chevron-down" style="color:#16a085; font-size:0.95rem; margin-left:-22px; pointer-events:none;"></i> -->
    </div>
    <!-- <div class="input-group select-group"> -->
        <div class="custom-select-wrapper">
        <i class="fa-solid fa-bangladeshi-taka-sign"></i>
        <select 
            name="price_range"
            style="
                border: none;
                border-bottom: 2px solid #16a085;
                background: transparent;
                font-size: 1.07rem;
                padding: 4px 0 2px 0;
                outline: none;
                color: #222;
                transition: border-color 0.2s;
                appearance: none;
                -webkit-appearance: none;
                -moz-appearance: none;
            "
            onfocus="this.style.borderColor='#2980b9'"
            onblur="this.style.borderColor='#16a085'"
        >
            <option disabled selected>Any Price</option>
            <option value="0-5000">Under ৳5,000</option>
            <option value="5000-10000">৳5,000 - ৳10,000</option>
            <option value="10000-20000">৳10,000 - ৳20,000</option>
            <option value="20000-40000">৳20,000 - ৳40,000</option>
            <option value="40000-100000">Over ৳40,000</option>
        </select>
        <!-- <i class="fas fa-chevron-down" style="color:#16a085; font-size:0.95rem; margin-left:-22px; pointer-events:none;"></i> -->
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
</form>

            </div>
        </section>

       
        <section class="featured-properties fade-in-section">
            <div class="container">
                <h2 class="section-title">Featured Rentals</h2>
                <p class="section-subtitle">Handpicked properties from the best neighborhoods to live in.</p>
                <div class="property-grid">
                    <?php
                    // Include database connection
                    require_once __DIR__ . '/config/database.php';

                    // Pagination settings
                    $properties_per_page = 12;
                    $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                    $offset = ($current_page - 1) * $properties_per_page;

                    // Get total count for pagination
                    $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM properties WHERE status = 'available'");
                    $total_properties = 0;
                    if ($count_stmt) {
                        $count_stmt->execute();
                        $count_result = $count_stmt->get_result();
                        $total_properties = $count_result->fetch_assoc()['total'];
                        $count_stmt->close();
                    }
                    $total_pages = ceil($total_properties / $properties_per_page);

                    // Fetch properties for current page
                    $stmt = $conn->prepare("SELECT property_id, title, CONCAT(COALESCE(address,''), CASE WHEN city IS NOT NULL AND city!='' THEN CONCAT(', ', CONCAT(UPPER(LEFT(city, 1)), LOWER(SUBSTRING(city, 2)))) ELSE '' END) AS location, price_per_month, bedrooms AS beds, bathrooms AS baths, area_sqft AS sqft, main_image FROM properties WHERE status = 'available' ORDER BY created_at DESC LIMIT ? OFFSET ?");
                    if ($stmt) {
                        $stmt->bind_param('ii', $properties_per_page, $offset);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $img = $row['main_image'] ?: 'images/default-property.jpg';
                                $title = htmlspecialchars($row['title']);
                                $location = htmlspecialchars($row['location']);
                                $price = number_format($row['price_per_month']);
                                $beds = (int)$row['beds'];
                                $baths = (int)$row['baths'];
                                $sqft = (int)$row['sqft'];
                                $id = (int)$row['property_id'];

                                echo '<div class="property-card">';
                                echo '  <div class="card-image">';
                                echo '    <img src="' . htmlspecialchars($img) . '" alt="' . $title . '" loading="lazy">';
                                echo '  </div>';
                                echo '  <div class="card-content">';
                                echo '    <h3>' . $title . '</h3>';
                                echo '    <p class="address"><i class="fas fa-map-marker-alt"></i> ' . $location . '</p>';
                                echo '    <div class="property-specs">';
                                echo '      <span><i class="fas fa-bed"></i> ' . $beds . ' Beds</span>';
                                echo '      <span><i class="fas fa-bath"></i> ' . $baths . ' Baths</span>';
                                echo '      <span><i class="fas fa-ruler-combined"></i> ' . $sqft . ' sqft</span>';
                                echo '    </div>';
                                echo '    <div style="font-weight:600; color:var(--secondary-color); margin-bottom:8px;">৳ ' . $price . ' / Month</div>';
                                echo '    <a href="property-details.php?id=' . $id . '" class="btn btn-secondary" style="padding:8px 20px; font-size:0.95rem;">View Details</a>';
                                echo '  </div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<div class="no-properties">';
                            echo '<i class="fas fa-home"></i>';
                            echo '<h3>No Properties Available</h3>';
                            echo '<p>Check back later for new listings!</p>';
                            echo '</div>';
                        }
                        $stmt->close();
                    } else {
                        echo '<p>Unable to load properties. Please check database connection.</p>';
                    }
                    ?>
                </div>

                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php
                    // Previous button
                    if ($current_page > 1) {
                        echo '<a href="?page=' . ($current_page - 1) . '" class="page-link"><i class="fas fa-chevron-left"></i> Previous</a>';
                    }

                    // First page
                    if ($current_page > 3) {
                        echo '<a href="?page=1" class="page-link">1</a>';
                        if ($current_page > 4) {
                            echo '<span class="page-dots">...</span>';
                        }
                    }

                    // Page numbers around current page
                    for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++) {
                        if ($i == $current_page) {
                            echo '<span class="page-link current">' . $i . '</span>';
                        } else {
                            echo '<a href="?page=' . $i . '" class="page-link">' . $i . '</a>';
                        }
                    }

                    // Last page
                    if ($current_page < $total_pages - 2) {
                        if ($current_page < $total_pages - 3) {
                            echo '<span class="page-dots">...</span>';
                        }
                        echo '<a href="?page=' . $total_pages . '" class="page-link">' . $total_pages . '</a>';
                    }

                    // Next button
                    if ($current_page < $total_pages) {
                        echo '<a href="?page=' . ($current_page + 1) . '" class="page-link">Next <i class="fas fa-chevron-right"></i></a>';
                    }
                    ?>
                </div>
                <?php endif; ?>
            </div>
        </section>
        

        
        <section class="how-it-works fade-in-section">
            <div class="container">
                <h2 class="section-title">Renting Made Simple</h2>
                <div class="steps-grid">
                    <div class="step">
                        <span class="step-number">01</span>
                        <i class="fas fa-search-location"></i>
                        <h4>Search & Discover</h4>
                        <p>Use our advanced filters to find the perfect place to call home.</p>
                    </div>
                    <div class="step">
                        <span class="step-number">02</span>
                        <i class="fas fa-calendar-check"></i>
                        <h4>Tour & Inquire</h4>
                        <p>Easily schedule a virtual or in-person tour with the property manager.</p>
                    </div>
                    <div class="step">
                        <span class="step-number">03</span>
                        <i class="fas fa-file-signature"></i>
                        <h4>Apply & Secure</h4>
                        <p>Submit your application online and sign your lease digitally.</p>
                    </div>
                </div>
            </div>
        </section>

        
        <section class="why-choose-us fade-in-section">
            <div class="container">
                <div class="why-choose-us-grid">
                    <div class="why-choose-us-content">
                        <h2 class="section-title">Why Choose HouseRental?</h2>
                        <p>We provide a seamless and trustworthy rental experience from search to signature. Our platform is designed to empower both renters and property owners.</p>
                        <ul>
                            <li><i class="fas fa-check-circle"></i> Verified Listings Only</li>
                            <li><i class="fas fa-check-circle"></i> Transparent Pricing</li>
                            <li><i class="fas fa-check-circle"></i> 24/7 Customer Support</li>
                        </ul>
                        <a href="#" class="btn btn-primary">Learn More</a>
                    </div>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <h3>10k+</h3>
                            <p>Active Listings</p>
                        </div>
                        <div class="stat-item">
                            <h3>5k+</h3>
                            <p>Happy Renters</p>
                        </div>
                        <div class="stat-item">
                            <h3>98%</h3>
                            <p>Satisfaction Rate</p>
                        </div>
                        <div class="stat-item">
                            <h3>24h</h3>
                            <p>Average Response</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

      
        <!-- <section class="testimonials-section fade-in-section">
            <div class="container">
                <h2 class="section-title">What Our Users Say</h2>
                <div class="swiper testimonials-slider">
                    <div class="swiper-wrapper">
                       
                        <div class="swiper-slide">
                            <div class="testimonial-card">
                                <i class="fas fa-quote-left"></i>
                                <p class="quote">"HouseRental made finding our new apartment a breeze. The process was so transparent and easy. Highly recommended!"</p>
                                <div class="author-info">
                                    <img src="https://images.pexels.com/photos/774909/pexels-photo-774909.jpeg?auto=compress&cs=tinysrgb&w=160&h=150&dpr=1" alt="User Photo">
                                    <span class="author">- Sarah J., Renter</span>
                                </div>
                            </div>
                        </div>
                    
                        <div class="swiper-slide">
                            <div class="testimonial-card">
                                <i class="fas fa-quote-left"></i>
                                <p class="quote">"As a landlord, listing my property was incredibly fast. I found a qualified tenant in less than a week. Fantastic service!"</p>
                                <div class="author-info">
                                    <img src="https://images.pexels.com/photos/91227/pexels-photo-91227.jpeg?auto=compress&cs=tinysrgb&w=160&h=150&dpr=1" alt="User Photo">
                                    <span class="author">- Michael B., Landlord</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="swiper-slide">
                            <div class="testimonial-card">
                                <i class="fas fa-quote-left"></i>
                                <p class="quote">"The virtual tour feature is a game-changer! I could explore properties from the comfort of my home."</p>
                                 <div class="author-info">
                                    <img src="https://images.pexels.com/photos/1239291/pexels-photo-1239291.jpeg?auto=compress&cs=tinysrgb&w=160&h=150&dpr=1" alt="User Photo">
                                    <span class="author">- Emily R., Renter</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </section> -->

    </main>

    
<div id="footer-placeholder"></div>
   
    <!-- <button id="scrollToTopBtn" title="Go to top"><i class="fas fa-arrow-up"></i></button> -->

   
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    
    <script src="js/loader.js"></script>
    <script src="js/script.js"></script>
    <script src="js/mobile.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.custom-select-wrapper').forEach(function(wrapper) {
        const originalSelect = wrapper.querySelector('select');
        const icon = wrapper.querySelector('i');

        // Create custom dropdown structure
        const customSelect = document.createElement('div');
        customSelect.classList.add('custom-select');
        
        const trigger = document.createElement('div');
        trigger.classList.add('custom-select-trigger');
        
        const options = document.createElement('div');
        options.classList.add('custom-options');

        // Move icon inside the trigger
        if (icon) {
            trigger.appendChild(icon.cloneNode(true));
            icon.style.display = 'none'; // Hide original icon
        }
        
        const triggerText = document.createElement('span');
        trigger.appendChild(triggerText);
        
        customSelect.appendChild(trigger);
        customSelect.appendChild(options);
        wrapper.appendChild(customSelect);

        // Populate custom options
        Array.from(originalSelect.options).forEach(function(optionElement) {
            const customOption = document.createElement('div');
            customOption.classList.add('custom-option');
            customOption.textContent = optionElement.textContent;
            customOption.dataset.value = optionElement.value;
            
            if (optionElement.selected) {
                triggerText.textContent = optionElement.textContent;
                customOption.classList.add('selected');
            }
            if (optionElement.disabled) {
                customOption.style.display = 'none';
            }

            customOption.addEventListener('click', function() {
                if (customSelect.querySelector('.custom-option.selected')) {
                    customSelect.querySelector('.custom-option.selected').classList.remove('selected');
                }
                this.classList.add('selected');
                triggerText.textContent = this.textContent;
                originalSelect.value = this.dataset.value;
                customSelect.classList.remove('open');
            });
            
            options.appendChild(customOption);
        });

        // Toggle dropdown
        trigger.addEventListener('click', function() {
            customSelect.classList.toggle('open');
        });
    });

    // Close dropdown when clicking outside
    window.addEventListener('click', function(e) {
        document.querySelectorAll('.custom-select').forEach(function(select) {
            if (!select.contains(e.target)) {
                select.classList.remove('open');
            }
        });
    });
});
</script>
</body>

</html>




