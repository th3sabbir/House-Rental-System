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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

   <div id="header-placeholder"></div>
   <!-- <header class="main-header">
        <nav class="navbar container">
            <a href="#" class="logo"><i class="fa-solid fa-house-chimney-window"></i> AmarThikana</a>
            <ul class="nav-links">
                <li><a href="#">Rent</a></li>
                <li><a href="properties.php">Properties</a></li>
                
                <li><a href="about-us.php">About Us</a></li>
            </ul>
            <div class="nav-actions">
                <a href="login.php" class="login-btn">Login</a>
                <a href="signup.php" class="btn btn-primary">Sign Up</a>
            </div>
            <div class="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </nav>
    </header> -->

    <main>
        
        <section class="hero-section">
            <div class="hero-overlay"></div>
            <div class="hero-content container">
                <h1>Find a place that feels like a home.</h1>
                <p>Discover thousands of apartments, houses, and rooms for rent.</p>
                
                
               
<form class="search-form">
    <!-- <div class="input-group select-group"> -->
        <div class="custom-select-wrapper">
        <i class="fas fa-map-marker-alt"></i>
        <select 
            style="
                border: none;
                border-bottom: 2px solid #16a085;
                background: transparent;
                font-size: 1.07rem;
                padding: 4px 0 2px 0;
                outline: none;
                width: 150px;
                color: #222;
                transition: border-color 0.2s;
                appearance: none;
                -webkit-appearance: none;
                -moz-appearance: none;
            "
            onfocus="this.style.borderColor='#2980b9'"
            onblur="this.style.borderColor='#16a085'"
        >
            <option value="" disabled selected>Select City</option>
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
            style="
                border: none;
                border-bottom: 2px solid #16a085;
                background: transparent;
                font-size: 1.07rem;
                padding: 4px 0 2px 0;
                outline: none;
                width: 150px;
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
        <option>Apartment</option>
            <option>House</option>
            <option>Studio</option>
        </select>
        <!-- <i class="fas fa-chevron-down" style="color:#16a085; font-size:0.95rem; margin-left:-22px; pointer-events:none;"></i> -->
    </div>
    <!-- <div class="input-group select-group"> -->
        <div class="custom-select-wrapper">
        <i class="fa-solid fa-bangladeshi-taka-sign"></i>
        <select 
            style="
                border: none;
                border-bottom: 2px solid #16a085;
                background: transparent;
                font-size: 1.07rem;
                padding: 4px 0 2px 0;
                outline: none;
                width: 150px;
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
            <option>5000 - 10000</option>
            <option>10000 - 20000</option>
            <option>20000 - 40000</option>
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
                <!-- ...existing code... -->
        <section class="similar-properties container" style="margin-bottom: 60px;">
            <!-- <h2 class="section-title" style="font-size:2rem; margin-bottom: 1.5rem; text-align:left;">Similar Properties</h2> -->
            <div class="property-grid">
                <div class="property-card">
                    <div class="card-image">
                        <img src="https://images.pexels.com/photos/271624/pexels-photo-271624.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Modern Apartment in Dhanmondi">
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
                        <a href="property-details.php" class="btn btn-secondary" style="padding:8px 20px; font-size:0.95rem;">View Details</a>
                    </div>
                </div>
                <div class="property-card">
                    <div class="card-image">
                        <img src="https://images.pexels.com/photos/276724/pexels-photo-276724.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Cozy Studio in Mohammadpur">
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
                        <a href="property-details.php" class="btn btn-secondary" style="padding:8px 20px; font-size:0.95rem;">View Details</a>
                    </div>
                </div>
                <div class="property-card">
                    <div class="card-image">
                        <img src="https://images.pexels.com/photos/5998120/pexels-photo-5998120.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Family Flat in Mirpur">
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
                        <a href="property-details.php" class="btn btn-secondary" style="padding:8px 20px; font-size:0.95rem;">View Details</a>
                    </div>
                </div>
                <div class="property-card">
                    <div class="card-image">
                        <img src="https://images.pexels.com/photos/323780/pexels-photo-323780.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Luxury Condo in Bashundhara">
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
                        <a href="property-details.php" class="btn btn-secondary" style="padding:8px 20px; font-size:0.95rem;">View Details</a>
                    </div>
                </div>
                <div class="property-card">
                    <div class="card-image">
                        <img src="https://images.pexels.com/photos/210617/pexels-photo-210617.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Single Room in Uttara">
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
                        <a href="property-details.php" class="btn btn-secondary" style="padding:8px 20px; font-size:0.95rem;">View Details</a>
                    </div>
                </div>
                <div class="property-card">
                    <div class="card-image">
                        <img src="https://images.pexels.com/photos/1643383/pexels-photo-1643383.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Spacious Duplex in Gulshan">
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
                        <a href="property-details.php" class="btn btn-secondary" style="padding:8px 20px; font-size:0.95rem;">View Details</a>
                    </div>
                </div>
                <!-- <div class="property-card">
                    <div class="card-image">
                        <img src="https://images.pexels.com/photos/259588/pexels-photo-259588.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Elegant Flat in Banani">
                    </div>
                    <div class="card-content">
                        <h3>Elegant Flat in Banani</h3>
                        <p class="address"><i class="fas fa-map-marker-alt"></i> Banani, Dhaka</p>
                        <div class="property-specs">
                            <span><i class="fas fa-bed"></i> 2 Beds</span>
                            <span><i class="fas fa-bath"></i> 2 Baths</span>
                            <span><i class="fas fa-ruler-combined"></i> 1100 sqft</span>
                        </div>
                        <div style="font-weight:600; color:var(--secondary-color); margin-bottom:8px;">৳ 30,000 / Month</div>
                        <a href="#" class="btn btn-secondary" style="padding:8px 20px; font-size:0.95rem;">View Details</a>
                    </div>
                </div> -->
                <div class="property-card">
                    <div class="card-image">
                        <img src="https://images.pexels.com/photos/1571460/pexels-photo-1571460.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Sunny Studio in Shyamoli">
                    </div>
                    <div class="card-content">
                        <h3>Sunny Studio in Shyamoli</h3>
                        <p class="address"><i class="fas fa-map-marker-alt"></i> Shyamoli, Dhaka</p>
                        <div class="property-specs">
                            <span><i class="fas fa-bed"></i> 1 Bed</span>
                            <span><i class="fas fa-bath"></i> 1 Bath</span>
                            <span><i class="fas fa-ruler-combined"></i> 500 sqft</span>
                        </div>
                        <div style="font-weight:600; color:var(--secondary-color); margin-bottom:8px;">৳ 10,500 / Month</div>
                        <a href="property-details.php" class="btn btn-secondary" style="padding:8px 20px; font-size:0.95rem;">View Details</a>
                    </div>
                </div>
                <div class="property-card">
                    <div class="card-image">
                        <img src="https://images.pexels.com/photos/1396122/pexels-photo-1396122.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Family Apartment in Baridhara">
                    </div>
                    <div class="card-content">
                        <h3>Family Apartment in Baridhara</h3>
                        <p class="address"><i class="fas fa-map-marker-alt"></i> Baridhara, Dhaka</p>
                        <div class="property-specs">
                            <span><i class="fas fa-bed"></i> 3 Beds</span>
                            <span><i class="fas fa-bath"></i> 3 Baths</span>
                            <span><i class="fas fa-ruler-combined"></i> 1600 sqft</span>
                        </div>
                        <div style="font-weight:600; color:var(--secondary-color); margin-bottom:8px;">৳ 38,000 / Month</div>
                        <a href="property-details.php" class="btn btn-secondary" style="padding:8px 20px; font-size:0.95rem;">View Details</a>
                    </div>
                </div>
                <div class="property-card">
                    <div class="card-image">
                        <img src="https://images.pexels.com/photos/1571458/pexels-photo-1571458.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Classic Home in Lalmatia">
                    </div>
                    <div class="card-content">
                        <h3>Classic Home in Lalmatia</h3>
                        <p class="address"><i class="fas fa-map-marker-alt"></i> Lalmatia, Dhaka</p>
                        <div class="property-specs">
                            <span><i class="fas fa-bed"></i> 2 Beds</span>
                            <span><i class="fas fa-bath"></i> 2 Baths</span>
                            <span><i class="fas fa-ruler-combined"></i> 950 sqft</span>
                        </div>
                        <div style="font-weight:600; color:var(--secondary-color); margin-bottom:8px;">৳ 22,000 / Month</div>
                        <a href="property-details.php" class="btn btn-secondary" style="padding:8px 20px; font-size:0.95rem;">View Details</a>
                     </div>
                </div>
            </div>
            <div class="pagination">
                <a href="#">First</a>
                <a href="#" class="active">1</a>
                <a href="#">2</a>
                <a href="#">3</a>
                <a href="#">Next</a>
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

    <!-- Footer -->
    <!-- <footer class="main-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col about-col">
                    <a href="#" class="logo"><i class="fa-solid fa-house-chimney-window"></i> AmarThikana</a>
                    <p>Our mission is to make renting simple, transparent, and trustworthy for everyone.</p>
                     <div class="social-icons">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="rent.php">Rent</a></li>
                        <li><a href="properties.php">Properties</a></li>
                        <li><a href="about-us.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                 <div class="footer-col">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="faq.php">FAQ</a></li>
                        <li><a href="disclaimer.php">Disclaimer</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Stay Updated</h4>
                    <p>Subscribe to our newsletter for the latest listings and deals.</p>
                    <form class="newsletter-form">
                        <input type="email" placeholder="Enter your email">
                        <button type="submit"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 AmarThikana. All Rights Reserved.</p>
            </div>
        </div>
    </footer> -->
<div id="footer-placeholder"></div>
   
    <!-- <button id="scrollToTopBtn" title="Go to top"><i class="fas fa-arrow-up"></i></button> -->

   
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    
    <script src="js/loader.js"></script>
    <script src="js/script.js"></script>
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
