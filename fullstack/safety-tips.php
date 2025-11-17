<?php
// Start session and connect to database at the very beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Safety Tips - Amarthikana</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    
    <link rel="stylesheet" href="css/style.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        /* Hero Section - Same as properties.php */
        .safety-hero {
            background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);
            padding: 100px 0 60px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .safety-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
        }

        .safety-hero-content {
            position: relative;
            z-index: 1;
        }

        .safety-hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .safety-hero p {
            font-size: 1.2rem;
            opacity: 0.95;
        }

        .safety-content {
            background: #f8f9fa;
            padding: 60px 0;
        }

        .intro-text {
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 40px;
            border-left: 4px solid #1abc9c;
            font-size: 1rem;
            line-height: 1.8;
            color: #555;
        }

        .tips-section {
            margin-bottom: 50px;
        }

        .tip-item {
            background: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #1abc9c;
        }

        .tip-item h3 {
            color: #2c3e50;
            font-size: 1.2rem;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .tip-item p {
            color: #555;
            line-height: 1.7;
            font-size: 0.95rem;
        }

        .features-section {
            background: white;
            padding: 40px;
            border-radius: 8px;
            margin-bottom: 40px;
        }

        .features-section h2 {
            text-align: center;
            color: #2c3e50;
            font-size: 1.8rem;
            margin-bottom: 30px;
            font-weight: 700;
        }

        .features-list {
            list-style: none;
            padding: 0;
        }

        .features-list li {
            padding: 15px 20px;
            margin-bottom: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #1abc9c;
        }

        .features-list li strong {
            color: #1abc9c;
            font-weight: 600;
        }

        .closing-message {
            background: white;
            padding: 40px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #1abc9c;
            margin-bottom: 40px;
        }

        .closing-message h2 {
            color: #2c3e50;
            font-size: 1.8rem;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .closing-message p {
            color: #555;
            font-size: 1rem;
            line-height: 1.8;
            margin-bottom: 25px;
        }

        .cta-button {
            display: inline-block;
            background: #1abc9c;
            color: white;
            padding: 12px 35px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .cta-button:hover {
            background: #16a085;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .safety-hero h1 {
                font-size: 2rem;
            }

            .safety-hero p {
                font-size: 1rem;
            }

            .intro-text,
            .tip-item,
            .features-section,
            .closing-message {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Header Placeholder -->
    <div id="header-placeholder"></div>

    <!-- Hero Section -->
    <section class="safety-hero">
        <div class="container safety-hero-content">
            <h1><i class="fas fa-shield-alt"></i> Quick Safety Tips</h1>
            <p>Your guide to safe and secure property transactions on Amarthikana</p>
        </div>
    </section>

    <!-- Safety Content -->
    <section class="safety-content">
        <div class="container">
            <!-- Introduction -->
            <div class="intro-text">
                <p>As Bangladesh's leading property rental platform, <strong>Amarthikana</strong> connects landlords and tenants, making property transactions more accessible and convenient. While the platform ensures a secure experience, users should also adopt safety measures to protect themselves from potential risks. Here are some quick safety tips for navigating Amarthikana effectively and securely.</p>
            </div>

            <!-- Tips Section -->
            <div class="tips-section">
                <div class="tip-item">
                    <h3>1. Verify Property Listings</h3>
                    <p>Not all listings are created equal. On Amarthikana, check the details carefully, including property descriptions, photos, and contact information. If anything seems unclear, request additional details or images from the advertiser.</p>
                </div>

                <div class="tip-item">
                    <h3>2. Visit Properties in Person</h3>
                    <p>Never finalize a deal based solely on online information. Schedule an on-site visit to verify the property's condition, location, and amenities. For added safety, visit during daylight hours and bring a friend or family member along.</p>
                </div>

                <div class="tip-item">
                    <h3>3. Check Ownership Documents</h3>
                    <p>Before committing to a property, ensure the seller or landlord provides valid ownership documents. Verify these with local authorities to confirm there are no disputes or legal issues tied to the property.</p>
                </div>

                <div class="tip-item">
                    <h3>4. Avoid Advance Payments Without Verification</h3>
                    <p>Do not make payments, deposits, or transfer money until you have thoroughly verified the property and finalized legal agreements. Use secure and traceable payment methods to protect your financial transactions.</p>
                </div>

                <div class="tip-item">
                    <h3>5. Protect Personal Information</h3>
                    <p>Be cautious about sharing sensitive personal information like your national ID, financial details, or address. Share these only when necessary and with verified parties through Amarthikana's secure channels.</p>
                </div>

                <div class="tip-item">
                    <h3>6. Be Wary of Unrealistic Offers</h3>
                    <p>Deals that sound too good to be true are often scams. Cross-check the prices of similar properties on Amarthikana and in the local market to determine if the offer is legitimate.</p>
                </div>

                <div class="tip-item">
                    <h3>7. Use Amarthikana's Features for Safe Communication</h3>
                    <p>Communicate with landlords and tenants through Amarthikana's internal messaging system. This adds a layer of security and keeps your personal contact information private until you feel comfortable sharing it.</p>
                </div>

                <div class="tip-item">
                    <h3>8. Report Suspicious Ads or Activity</h3>
                    <p>If you come across misleading ads, fake listings, or suspicious behavior, report them to Amarthikana immediately. The platform's team takes user safety seriously and acts promptly to address such issues.</p>
                </div>

                <div class="tip-item">
                    <h3>9. Secure a Legal Agreement</h3>
                    <p>When renting or purchasing a property, always draft and sign a legal agreement. This document should outline terms, conditions, and responsibilities for both parties, ensuring clarity and protection.</p>
                </div>

                <div class="tip-item">
                    <h3>10. Stay Updated with Amarthikana's Safety Features</h3>
                    <p>Amarthikana is designed with user safety in mind, offering verified listings, secure messaging systems, and dedicated customer support available to assist with any concerns or issues during your property search.</p>
                </div>
            </div>

            <!-- Platform Features -->
            <div class="features-section">
                <h2>Amarthikana Safety Features</h2>
                <ul class="features-list">
                    <li><strong>Verified Listings:</strong> Ensuring authenticity in property advertisements.</li>
                    <li><strong>Secure Messaging:</strong> Protected communication channels for privacy.</li>
                    <li><strong>Customer Support:</strong> Available to assist with any concerns during your search.</li>
                </ul>
            </div>

            <!-- Closing Message -->
            <div class="closing-message">
                <h2>Final Thoughts</h2>
                <p>Amarthikana is your trusted partner for renting or listing property in Bangladesh. By following these quick safety tips, you can make your property search both safe and successful. Stay alert, trust your instincts, and enjoy the convenience of a secure property marketplace!</p>
                <a href="index.php" class="cta-button"><i class="fas fa-arrow-right"></i> Start Your Property Search</a>
            </div>
        </div>
    </section>

    <!-- Footer Placeholder -->
    <div id="footer-placeholder"></div>

    <!-- Scripts -->
    <script src="js/loader.js"></script>
    <script src="js/script.js"></script>

</body>
</html>
