<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - AmarThikana</title>
    
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
        /* Contact Page Specific Styles */
        .contact-hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 100px 0 60px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .contact-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
        }

        .contact-hero-content {
            position: relative;
            z-index: 1;
        }

        .contact-hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .contact-hero p {
            font-size: 1.2rem;
            opacity: 0.95;
            max-width: 700px;
            margin: 0 auto;
        }

        .contact-content {
            padding: 80px 0;
        }

        .contact-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .contact-info-card {
            background: white;
            padding: 40px 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            transition: all 0.4s;
            border: 2px solid transparent;
        }

        .contact-info-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .contact-info-card .icon-wrapper {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }

        .contact-info-card h3 {
            font-size: 1.4rem;
            margin-bottom: 15px;
            color: var(--text-color);
        }

        .contact-info-card p {
            color: #666;
            line-height: 1.8;
            margin-bottom: 10px;
        }

        .contact-info-card a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .contact-info-card a:hover {
            color: var(--secondary-color);
        }

        .contact-form-section {
            background: white;
            padding: 60px;
            border-radius: 25px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-bottom: 60px;
        }

        .contact-form-section h2 {
            font-size: 2.2rem;
            margin-bottom: 15px;
            color: var(--text-color);
            text-align: center;
        }

        .contact-form-section .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 40px;
            font-size: 1.1rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: var(--text-color);
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            font-family: 'Lato', sans-serif;
            transition: all 0.3s;
            background: #fafafa;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 3px rgba(22, 160, 133, 0.1);
        }

        /* Contact Form Custom Dropdown - Unique Classes */
        .contact-dropdown-wrapper {
            position: relative;
        }

        .contact-dropdown-wrapper select {
            display: none;
        }

        .contact-dropdown-box {
            position: relative;
            width: 100%;
        }

        .contact-dropdown-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            border: none;
            border-bottom: 2px solid #16a085;
            background: transparent;
            font-size: 1.07rem;
            padding: 10px 0;
            color: #222;
            transition: border-color 0.2s;
            width: 100%;
        }

        .contact-dropdown-header:hover {
            border-color: #2980b9;
        }

        .contact-dropdown-header span {
            flex: 1;
            text-align: left;
        }

        .contact-dropdown-header i {
            color: #16a085;
            font-size: 0.9rem;
            transition: transform 0.3s;
            margin-left: 10px;
            flex-shrink: 0;
        }

        .contact-dropdown-box.active .contact-dropdown-header i {
            transform: rotate(180deg);
        }

        .contact-dropdown-list {
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
        }

        .contact-dropdown-box.active .contact-dropdown-list {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .contact-dropdown-item {
            padding: 12px 15px;
            cursor: pointer;
            transition: background 0.2s;
            color: #222;
            font-size: 1rem;
        }

        .contact-dropdown-item:hover {
            background: rgba(22, 160, 133, 0.1);
            color: #16a085;
        }

        .contact-dropdown-item.selected {
            background: rgba(22, 160, 133, 0.15);
            color: #16a085;
            font-weight: 600;
        }

        /* Scrollbar for dropdown */
        .contact-dropdown-list::-webkit-scrollbar {
            width: 6px;
        }

        .contact-dropdown-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .contact-dropdown-list::-webkit-scrollbar-thumb {
            background: #16a085;
            border-radius: 10px;
        }

        .contact-dropdown-list::-webkit-scrollbar-thumb:hover {
            background: #2980b9;
        }

        .form-group textarea {
            min-height: 180px;
            resize: vertical;
        }

        .form-group .char-count {
            text-align: right;
            font-size: 0.85rem;
            color: #999;
            margin-top: 5px;
        }

        .submit-btn {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 18px 50px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 auto;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(22, 160, 133, 0.3);
        }

        .submit-btn i {
            font-size: 1.2rem;
        }

        .map-section {
            margin-bottom: 60px;
        }

        .map-section h2 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 30px;
            color: var(--text-color);
        }

        .map-container {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            height: 450px;
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .social-connect {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 60px;
            border-radius: 25px;
            text-align: center;
            color: white;
            margin-top: 60px;
        }

        .social-connect h2 {
            font-size: 2.2rem;
            margin-bottom: 15px;
        }

        .social-connect p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            opacity: 0.95;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .social-link {
            width: 60px;
            height: 60px;
            background: white;
            color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            transition: all 0.3s;
            text-decoration: none;
        }

        .social-link:hover {
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        @media (max-width: 768px) {
            .contact-hero h1 {
                font-size: 2rem;
            }

            .contact-hero p {
                font-size: 1rem;
            }

            .contact-form-section {
                padding: 30px 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .social-connect {
                padding: 40px 20px;
            }

            .map-container {
                height: 300px;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <!-- Header Placeholder -->
    <div id="header-placeholder"></div>

    <!-- Contact Hero Section -->
    <section class="contact-hero">
        <div class="container contact-hero-content">
            <h1>Get In Touch</h1>
            <p>Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
        </div>
    </section>

    <!-- Contact Info Cards -->
    <section class="contact-content">
        <div class="container">
            <div class="contact-info-grid">
                <div class="contact-info-card">
                    <div class="icon-wrapper">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h3>Call Us</h3>
                    <p>Speak directly with our support team</p>
                    <a href="tel:+8801712345678">+880 1712-345678</a><br>
                    <a href="tel:+8801812345678">+880 1812-345678</a>
                </div>

                <div class="contact-info-card">
                    <div class="icon-wrapper">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>Email Us</h3>
                    <p>Send us an email anytime</p>
                    <a href="mailto:support@amarthikana.com">support@amarthikana.com</a><br>
                    <a href="mailto:info@amarthikana.com">info@amarthikana.com</a>
                </div>

                <!-- <div class="contact-info-card">
                    <div class="icon-wrapper">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>Visit Us</h3>
                    <p>123 Gulshan Avenue<br>Dhaka 1212<br>Bangladesh</p>
                    <a href="#map">View on Map</a>
                </div> -->

                <div class="contact-info-card">
                    <div class="icon-wrapper">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3>Live Chat</h3>
                    <p>Chat with our support team in real-time</p>
                    <a href="#" onclick="alert('Live chat feature coming soon!'); return false;">Start Chat</a>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form-section">
                <h2>Send Us a Message</h2>
                <p class="subtitle">Fill out the form below and we'll get back to you within 24 hours</p>
                
                <form id="contactForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">First Name *</label>
                            <input type="text" id="firstName" name="firstName" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name *</label>
                            <input type="text" id="lastName" name="lastName" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <div class="contact-dropdown-wrapper">
                            <select id="subject" name="subject" required>
                                <option value="">Select a subject</option>
                                <option value="general">General Inquiry</option>
                                <option value="property">Property Related</option>
                                <option value="account">Account Support</option>
                                <option value="payment">Payment Issue</option>
                                <option value="technical">Technical Support</option>
                                <option value="feedback">Feedback</option>
                                <option value="partnership">Partnership Opportunity</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="message">Your Message *</label>
                        <textarea id="message" name="message" required maxlength="1000"></textarea>
                        <div class="char-count"><span id="charCount">0</span>/1000 characters</div>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i>
                        Send Message
                    </button>
                </form>
            </div>

            <!-- Map Section -->
            <div class="map-section" id="map">
                <h2>Find Us Here</h2>
                <div class="map-container">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3651.0977493861234!2d90.41254731543294!3d23.780887384577853!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c7a1f0a64b49%3A0x51b607b11e6b2e33!2sGulshan%2C%20Dhaka!5e0!3m2!1sen!2sbd!4v1234567890123!5m2!1sen!2sbd" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>

            <!-- Social Connect -->
            <div class="social-connect">
                <h2>Connect With Us</h2>
                <p>Follow us on social media for updates, tips, and exclusive listings</p>
                <div class="social-links">
                    <a href="#" class="social-link" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="social-link" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="social-link" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                </div>
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
        // Contact Form Custom Dropdown - Using Unique Class Names
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.contact-dropdown-wrapper').forEach(function(wrapper) {
                const originalSelect = wrapper.querySelector('select');

                // Create custom dropdown structure
                const dropdownBox = document.createElement('div');
                dropdownBox.className = 'contact-dropdown-box';
                
                const dropdownHeader = document.createElement('div');
                dropdownHeader.className = 'contact-dropdown-header';
                
                const dropdownList = document.createElement('div');
                dropdownList.className = 'contact-dropdown-list';

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
                    dropdownItem.className = 'contact-dropdown-item';
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
                        dropdownList.querySelectorAll('.contact-dropdown-item').forEach(item => {
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
                    document.querySelectorAll('.contact-dropdown-box.active').forEach(function(box) {
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

        // Character counter for message textarea
        const messageTextarea = document.getElementById('message');
        const charCount = document.getElementById('charCount');

        messageTextarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });

        // Contact Form Submission
        const contactForm = document.getElementById('contactForm');

        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(contactForm);
            const data = Object.fromEntries(formData);

            // Here you would normally send the data to your server
            console.log('Form submitted:', data);

            // Show success message
            alert('Thank you for contacting us! We will get back to you within 24 hours.');
            
            // Reset form
            contactForm.reset();
            charCount.textContent = '0';
            
            // Reset custom dropdown
            document.querySelectorAll('.contact-dropdown-header span').forEach(span => {
                span.textContent = 'Select a subject';
            });
            document.querySelectorAll('.contact-dropdown-item').forEach(item => {
                item.classList.remove('selected');
            });
        });

        // Smooth scroll to map
        document.querySelector('a[href="#map"]').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('map').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });
    </script>
</body>
</html>
