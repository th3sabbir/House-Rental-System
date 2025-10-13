<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - AmarThikana</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        /* Page-specific styles */
        .page-header {
            background: linear-gradient(135deg, rgba(22, 160, 133, 0.9), rgba(41, 128, 185, 0.9)),
                        url('https://images.pexels.com/photos/1643383/pexels-photo-1643383.jpeg?auto=compress&cs=tinysrgb&w=1600');
            background-size: cover;
            background-position: center;
            padding: 120px 0 80px;
            text-align: center;
            color: white;
        }

        .page-header h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .page-header p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto;
        }

        .about-section {
            padding: 80px 0;
        }

        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            margin-bottom: 60px;
        }

        .about-image {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        .about-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .about-text h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            color: var(--text-dark);
        }

        .about-text p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--text-medium);
            margin-bottom: 1.5rem;
        }

        .mission-vision {
            background: var(--background-light);
            padding: 60px 0;
        }

        .mv-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .mv-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .mv-card i {
            font-size: 3rem;
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
        }

        .mv-card h3 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }

        .mv-card p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--text-medium);
        }

        .values-section {
            padding: 80px 0;
        }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            margin-top: 40px;
        }

        .value-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .value-card:hover {
            transform: translateY(-10px);
        }

        .value-card i {
            font-size: 2.5rem;
            color: var(--secondary-color);
            margin-bottom: 1rem;
        }

        .value-card h4 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }

        .value-card p {
            font-size: 1rem;
            line-height: 1.6;
            color: var(--text-medium);
        }

        .team-section {
            background: var(--background-light);
            padding: 80px 0;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
            margin-top: 40px;
        }

        .team-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }

        .team-card:hover {
            transform: translateY(-10px);
        }

        .team-image {
            width: 100%;
            height: 250px;
            overflow: hidden;
        }

        .team-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .team-info {
            padding: 20px;
            text-align: center;
        }

        .team-info h4 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .team-info p {
            font-size: 0.95rem;
            color: var(--secondary-color);
            margin-bottom: 1rem;
        }

        .team-social {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .team-social a {
            color: var(--text-medium);
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .team-social a:hover {
            color: var(--secondary-color);
        }

        .cta-section {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            padding: 80px 0;
            text-align: center;
            color: white;
        }

        .cta-section h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .cta-section p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        .btn-white {
            background: white;
            color: var(--secondary-color);
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-white:hover {
            background: var(--background-light);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .about-content,
            .mv-grid {
                grid-template-columns: 1fr;
            }

            .values-grid {
                grid-template-columns: 1fr;
            }

            .team-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .page-header h1 {
                font-size: 2rem;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <!-- Header -->
    <!-- <header class="main-header scrolled">
        <nav class="navbar container">
            <a href="index.php" class="logo"><i class="fa-solid fa-house-chimney-window"></i> AmarThikana</a>
            <ul class="nav-links">
                <li><a href="index.php">Rent</a></li>
                <li><a href="#">List Your Property</a></li>
                <li><a href="about.php" class="active">About Us</a></li>
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
<div id="header-placeholder"></div>
    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>About AmarThikana</h1>
            <p>We're on a mission to make renting simple, transparent, and trustworthy for everyone.</p>
        </div>
    </section>

    <!-- About Content -->
    <section class="about-section">
        <div class="container">
            <div class="about-content">
                <div class="about-image">
                    <img src="https://images.pexels.com/photos/3183197/pexels-photo-3183197.jpeg?auto=compress&cs=tinysrgb&w=800" alt="About Us">
                </div>
                <div class="about-text">
                    <h2>Our Story</h2>
                    <p>AmarThikana was founded in 2025 with a simple vision: to revolutionize the rental experience in Bangladesh. We recognized the challenges both renters and landlords face in finding the perfect match, and we set out to create a platform that bridges that gap.</p>
                    <p>Today, we're proud to serve thousands of happy customers across Dhaka and beyond, offering a seamless, secure, and transparent rental marketplace that benefits everyone.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="mission-vision">
        <div class="container">
            <div class="mv-grid">
                <div class="mv-card">
                    <i class="fas fa-bullseye"></i>
                    <h3>Our Mission</h3>
                    <p>To empower renters and landlords with the tools, transparency, and trust they need to make informed decisions and build lasting relationships.</p>
                </div>
                <div class="mv-card">
                    <i class="fas fa-eye"></i>
                    <h3>Our Vision</h3>
                    <p>To become Bangladesh's most trusted rental platform, where every transaction is smooth, every property is verified, and every customer is satisfied.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Values -->
    <section class="values-section">
        <div class="container">
            <h2 class="section-title">Our Core Values</h2>
            <div class="values-grid">
                <div class="value-card">
                    <i class="fas fa-shield-alt"></i>
                    <h4>Trust & Transparency</h4>
                    <p>We believe in open communication and verified listings to build trust between renters and landlords.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-users"></i>
                    <h4>Customer First</h4>
                    <p>Our customers are at the heart of everything we do. Their satisfaction drives our innovation.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-rocket"></i>
                    <h4>Innovation</h4>
                    <p>We continuously evolve our platform with cutting-edge technology to enhance user experience.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-handshake"></i>
                    <h4>Integrity</h4>
                    <p>We operate with honesty and fairness in all our dealings, ensuring a level playing field for all.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-headset"></i>
                    <h4>Support</h4>
                    <p>Our dedicated team is available 24/7 to assist you with any questions or concerns.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-globe"></i>
                    <h4>Community</h4>
                    <p>We're building a community of responsible renters and landlords who care about quality living.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <!-- <section class="team-section">
        <div class="container">
            <h2 class="section-title">Meet Our Team</h2>
            <p class="section-subtitle">The passionate people behind AmarThikana</p>
            <div class="team-grid">
                <div class="team-card">
                    <div class="team-image">
                        <img src="https://images.pexels.com/photos/2379004/pexels-photo-2379004.jpeg?auto=compress&cs=tinysrgb&w=400" alt="Team Member">
                    </div>
                    <div class="team-info">
                        <h4>Rafiqul Islam</h4>
                        <p>CEO & Founder</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
                <div class="team-card">
                    <div class="team-image">
                        <img src="https://images.pexels.com/photos/1239291/pexels-photo-1239291.jpeg?auto=compress&cs=tinysrgb&w=400" alt="Team Member">
                    </div>
                    <div class="team-info">
                        <h4>Ayesha Rahman</h4>
                        <p>Head of Operations</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
                <div class="team-card">
                    <div class="team-image">
                        <img src="https://images.pexels.com/photos/91227/pexels-photo-91227.jpeg?auto=compress&cs=tinysrgb&w=400" alt="Team Member">
                    </div>
                    <div class="team-info">
                        <h4>Kamal Ahmed</h4>
                        <p>Lead Developer</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-github"></i></a>
                        </div>
                    </div>
                </div>
                <div class="team-card">
                    <div class="team-image">
                        <img src="https://images.pexels.com/photos/774909/pexels-photo-774909.jpeg?auto=compress&cs=tinysrgb&w=400" alt="Team Member">
                    </div>
                    <div class="team-info">
                        <h4>Nusrat Jahan</h4>
                        <p>Customer Success Manager</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Ready to Find Your Perfect Home?</h2>
            <p>Join thousands of satisfied renters and landlords who trust AmarThikana</p>
            <div class="cta-buttons">
                <a href="index.php" class="btn-white">Browse Properties</a>
                <a href="signup.php" class="btn btn-primary">Get Started</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    
    <div id="footer-placeholder"></div>
    <script src="js/loader.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
