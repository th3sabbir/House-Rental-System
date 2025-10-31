<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - AmarThikana</title>
    
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
        /* Terms of Service Specific Styles */
        .terms-hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 100px 0 60px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .terms-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
        }

        .terms-hero-content {
            position: relative;
            z-index: 1;
        }

        .terms-hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .terms-hero p {
            font-size: 1.2rem;
            opacity: 0.95;
            max-width: 800px;
            margin: 0 auto;
        }

        .terms-content {
            padding: 80px 0;
        }

        .terms-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .last-updated {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 40px;
            text-align: center;
            border-left: 4px solid var(--primary-color);
        }

        .last-updated i {
            color: var(--primary-color);
            margin-right: 10px;
        }

        .last-updated p {
            margin: 0;
            color: #666;
            font-weight: 600;
        }

        .table-of-contents {
        background: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 40px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        border-left: 4px solid var(--primary-color);
    }

    .table-of-contents h3 {
        font-size: 1.3rem;
        color: var(--text-color);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .table-of-contents h3 i {
        color: var(--primary-color);
    }

    .table-of-contents ol {
        counter-reset: item;
        list-style: none;
        padding-left: 0;
    }

    .table-of-contents ol li {
        counter-increment: item;
        margin-bottom: 8px;
    }

    .table-of-contents ol li a {
        color: #555;
        text-decoration: none;
        display: flex;
        align-items: center;
        padding: 12px 15px;
        border-radius: 8px;
        transition: all 0.3s ease;
        background: transparent;
        border: 1px solid transparent;
    }

    .table-of-contents ol li a:hover {
        color: var(--primary-color);
        background: linear-gradient(135deg, rgba(22, 160, 133, 0.08) 0%, rgba(41, 128, 185, 0.08) 100%);
        border: 1px solid rgba(22, 160, 133, 0.2);
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(22, 160, 133, 0.15);
    }

    .table-of-contents ol li a::before {
        content: counter(item) ". ";
        color: var(--primary-color);
        font-weight: 600;
        margin-right: 12px;
        min-width: 25px;
    }

        .terms-section {
            background: white;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            border-left: 4px solid var(--primary-color);
            scroll-margin-top: 100px;
        }

        .terms-section h2 {
            font-size: 1.8rem;
            color: var(--text-color);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .terms-section h2 i {
            color: var(--primary-color);
            font-size: 1.5rem;
        }

        .terms-section h3 {
            font-size: 1.3rem;
            color: var(--text-color);
            margin-top: 25px;
            margin-bottom: 15px;
        }

        .terms-section h4 {
            font-size: 1.1rem;
            color: var(--text-color);
            margin-top: 20px;
            margin-bottom: 12px;
        }

        .terms-section p {
            color: #555;
            line-height: 1.8;
            margin-bottom: 15px;
        }

        .terms-section ul, .terms-section ol {
            margin-bottom: 20px;
            padding-left: 25px;
        }

        .terms-section ul li, .terms-section ol li {
            color: #555;
            line-height: 1.8;
            margin-bottom: 10px;
        }

        .highlight-box {
            background: #e7f3ff;
            border-left: 4px solid #2196f3;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .highlight-box i {
            color: #2196f3;
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .highlight-box p {
            margin: 0;
            color: #0d47a1;
            font-weight: 500;
        }

        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .warning-box i {
            color: #ffc107;
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .warning-box p {
            margin: 0;
            color: #856404;
            font-weight: 500;
        }

        .acceptance-box {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 50px;
            border-radius: 20px;
            text-align: center;
            color: white;
            margin-top: 50px;
        }

        .acceptance-box h2 {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .acceptance-box p {
            font-size: 1.1rem;
            margin-bottom: 25px;
            opacity: 0.95;
        }

        .acceptance-box .btn {
            background: white;
            color: var(--primary-color);
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            border: 2px solid white;
            margin: 0 10px;
        }

        .acceptance-box .btn:hover {
            background: transparent;
            color: white;
            transform: translateY(-3px);
        }

        @media (max-width: 768px) {
            .terms-hero h1 {
                font-size: 2rem;
            }

            .terms-hero p {
                font-size: 1rem;
            }

            .terms-section {
                padding: 25px;
            }

            .terms-section h2 {
                font-size: 1.4rem;
            }

            .acceptance-box {
                padding: 30px 20px;
            }

            .acceptance-box .btn {
                margin: 10px 5px;
                padding: 12px 25px;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <!-- Header Placeholder -->
    <div id="header-placeholder"></div>

    <!-- Terms Hero Section -->
    <section class="terms-hero">
        <div class="container terms-hero-content">
            <h1>Terms of Service</h1>
            <p>Please read these terms carefully before using AmarThikana</p>
        </div>
    </section>

    <!-- Terms Content Section -->
    <section class="terms-content">
        <div class="container">
            <div class="terms-container">
                
                <!-- Last Updated -->
                <div class="last-updated">
                    <p><i class="fas fa-calendar-alt"></i> Last Updated: January 1, 2025</p>
                </div>

                <!-- Table of Contents -->
                <div class="table-of-contents">
                    <h3><i class="fas fa-list"></i> Table of Contents</h3>
                    <ol>
                        <li><a href="#agreement">Agreement to Terms</a></li>
                        <li><a href="#accounts">User Accounts & Registration</a></li>
                        <li><a href="#use">Acceptable Use</a></li>
                        <li><a href="#listings">Property Listings</a></li>
                        <li><a href="#transactions">Transactions & Payments</a></li>
                        <li><a href="#fees">Fees & Charges</a></li>
                        <li><a href="#landlord">Landlord Obligations</a></li>
                        <li><a href="#tenant">Tenant Obligations</a></li>
                        <li><a href="#intellectual">Intellectual Property</a></li>
                        <li><a href="#privacy">Privacy & Data Protection</a></li>
                        <li><a href="#termination">Termination & Suspension</a></li>
                        <li><a href="#liability">Limitation of Liability</a></li>
                        <li><a href="#disputes">Dispute Resolution</a></li>
                        <li><a href="#modifications">Modifications to Terms</a></li>
                        <li><a href="#contact">Contact Information</a></li>
                    </ol>
                </div>

                <!-- Agreement to Terms -->
                <div class="terms-section" id="agreement">
                    <h2><i class="fas fa-file-contract"></i> 1. Agreement to Terms</h2>
                    <p>
                        By accessing or using the AmarThikana platform ("Service"), you agree to be bound by these Terms of Service 
                        ("Terms"). If you disagree with any part of these terms, you may not access the Service.
                    </p>
                    
                    <div class="highlight-box">
                        <i class="fas fa-info-circle"></i>
                        <p>
                            These Terms constitute a legally binding agreement between you and AmarThikana. Please read them carefully.
                        </p>
                    </div>

                    <h3>Eligibility</h3>
                    <p>You must meet the following requirements to use our Service:</p>
                    <ul>
                        <li>Be at least 18 years of age</li>
                        <li>Have the legal capacity to enter into binding contracts</li>
                        <li>Not be prohibited from using the Service under Bangladesh law</li>
                        <li>Provide accurate and complete registration information</li>
                    </ul>
                </div>

                <!-- User Accounts -->
                <div class="terms-section" id="accounts">
                    <h2><i class="fas fa-user-circle"></i> 2. User Accounts & Registration</h2>
                    
                    <h3>Account Creation</h3>
                    <p>To use certain features of our Service, you must create an account by providing:</p>
                    <ul>
                        <li>Valid email address or phone number</li>
                        <li>Secure password</li>
                        <li>Accurate personal information</li>
                        <li>Valid government-issued ID (for verification)</li>
                    </ul>

                    <h3>Account Security</h3>
                    <p>You are responsible for:</p>
                    <ul>
                        <li>Maintaining the confidentiality of your account credentials</li>
                        <li>All activities that occur under your account</li>
                        <li>Notifying us immediately of any unauthorized access</li>
                        <li>Ensuring your account information remains current and accurate</li>
                    </ul>

                    <div class="warning-box">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>
                            Never share your password or login credentials. We will never ask for your password via email or phone.
                        </p>
                    </div>

                    <h3>Account Types</h3>
                    <ul>
                        <li><strong>Tenant Accounts:</strong> For individuals seeking rental properties</li>
                        <li><strong>Landlord Accounts:</strong> For property owners listing rentals</li>
                        <li><strong>Agency Accounts:</strong> For property management companies</li>
                    </ul>
                </div>

                <!-- Acceptable Use -->
                <div class="terms-section" id="use">
                    <h2><i class="fas fa-check-circle"></i> 3. Acceptable Use</h2>
                    
                    <h3>Permitted Uses</h3>
                    <p>You may use our Service to:</p>
                    <ul>
                        <li>Search for and view rental property listings</li>
                        <li>List properties for rent (landlords only)</li>
                        <li>Communicate with other users regarding properties</li>
                        <li>Submit rental applications</li>
                        <li>Process payments through our platform</li>
                    </ul>

                    <h3>Prohibited Activities</h3>
                    <p>You agree NOT to:</p>
                    <ul>
                        <li>Post false, misleading, or fraudulent listings</li>
                        <li>Discriminate based on race, religion, gender, or other protected classes</li>
                        <li>Harass, threaten, or abuse other users</li>
                        <li>Use the Service for illegal activities</li>
                        <li>Scrape, data mine, or extract information without permission</li>
                        <li>Attempt to circumvent security measures</li>
                        <li>Impersonate any person or entity</li>
                        <li>Upload viruses or malicious code</li>
                        <li>Spam or send unsolicited communications</li>
                        <li>Engage in price manipulation or bid rigging</li>
                    </ul>

                    <div class="warning-box">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>
                            Violation of these terms may result in immediate account suspension or termination, and may be 
                            reported to law enforcement authorities.
                        </p>
                    </div>
                </div>

                <!-- Property Listings -->
                <div class="terms-section" id="listings">
                    <h2><i class="fas fa-building"></i> 4. Property Listings</h2>
                    
                    <h3>Listing Requirements</h3>
                    <p>All property listings must:</p>
                    <ul>
                        <li>Contain accurate and truthful information</li>
                        <li>Include current, unaltered photos of the property</li>
                        <li>Specify accurate pricing and availability</li>
                        <li>Comply with local housing laws and regulations</li>
                        <li>Not discriminate against any protected class</li>
                    </ul>

                    <h3>Landlord Responsibilities</h3>
                    <ul>
                        <li>Verify ownership or authorization to list the property</li>
                        <li>Maintain accurate listing information</li>
                        <li>Respond to inquiries in a timely manner</li>
                        <li>Honor advertised terms and conditions</li>
                        <li>Remove listings when properties are no longer available</li>
                    </ul>

                    <h3>Listing Approval</h3>
                    <p>
                        We reserve the right to review, approve, reject, or remove any listing at our discretion. 
                        We may verify property ownership and listing accuracy before publication.
                    </p>

                    <div class="highlight-box">
                        <i class="fas fa-info-circle"></i>
                        <p>
                            Listings with verified badges have undergone additional verification processes including 
                            ownership verification and property inspection.
                        </p>
                    </div>
                </div>

                <!-- Transactions & Payments -->
                <div class="terms-section" id="transactions">
                    <h2><i class="fas fa-credit-card"></i> 5. Transactions & Payments</h2>
                    
                    <h3>Platform Role</h3>
                    <p>
                        AmarThikana serves as a facilitator for rental transactions but is not a party to any rental 
                        agreements between landlords and tenants. All agreements are directly between users.
                    </p>

                    <h3>Payment Processing</h3>
                    <ul>
                        <li>We use third-party payment processors to handle transactions</li>
                        <li>All payments are subject to verification and fraud prevention checks</li>
                        <li>Payment methods include bank transfer, mobile banking, and credit/debit cards</li>
                        <li>We do not store credit card information on our servers</li>
                    </ul>

                    <h3>Security Deposits</h3>
                    <p>Security deposits are handled according to Bangladesh rental laws:</p>
                    <ul>
                        <li>Deposits must be held in accordance with local regulations</li>
                        <li>Refund timelines and conditions are set by landlords</li>
                        <li>Disputes must be resolved between landlord and tenant</li>
                    </ul>

                    <div class="warning-box">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>
                            Never send payments outside of our secure platform. Beware of wire transfer scams and 
                            advance payment requests before viewing properties.
                        </p>
                    </div>
                </div>

                <!-- Fees & Charges -->
                <div class="terms-section" id="fees">
                    <h2><i class="fas fa-dollar-sign"></i> 6. Fees & Charges</h2>
                    
                    <h3>Service Fees</h3>
                    <ul>
                        <li><strong>Basic Listings:</strong> Free for standard property listings</li>
                        <li><strong>Premium Listings:</strong> Monthly fees for featured placement and enhanced visibility</li>
                        <li><strong>Transaction Fees:</strong> Percentage-based fees on completed rentals</li>
                        <li><strong>Payment Processing:</strong> Standard processing fees apply to all transactions</li>
                    </ul>

                    <h3>Fee Structure</h3>
                    <ul>
                        <li>All fees are clearly displayed before confirmation</li>
                        <li>Fees are non-refundable unless otherwise stated</li>
                        <li>We reserve the right to modify fees with 30 days notice</li>
                        <li>Promotional pricing may be available for limited periods</li>
                    </ul>

                    <h3>Taxes</h3>
                    <p>
                        You are responsible for all applicable taxes on rental income or service fees. We may collect 
                        and remit taxes where required by law.
                    </p>
                </div>

                <!-- Landlord Obligations -->
                <div class="terms-section" id="landlord">
                    <h2><i class="fas fa-home"></i> 7. Landlord Obligations</h2>
                    
                    <p>As a landlord using our platform, you agree to:</p>

                    <h3>Legal Compliance</h3>
                    <ul>
                        <li>Comply with all applicable housing laws and regulations</li>
                        <li>Maintain valid property ownership or management authorization</li>
                        <li>Ensure properties meet safety and habitability standards</li>
                        <li>Obtain all necessary permits and licenses</li>
                    </ul>

                    <h3>Fair Housing</h3>
                    <ul>
                        <li>Not discriminate based on race, religion, gender, disability, or family status</li>
                        <li>Provide equal access to all qualified applicants</li>
                        <li>Follow fair screening and selection processes</li>
                    </ul>

                    <h3>Disclosure Requirements</h3>
                    <ul>
                        <li>Disclose all known defects or issues with the property</li>
                        <li>Provide accurate information about utilities and amenities</li>
                        <li>Inform tenants of lease terms and conditions</li>
                        <li>Communicate any fees or deposits clearly</li>
                    </ul>
                </div>

                <!-- Tenant Obligations -->
                <div class="terms-section" id="tenant">
                    <h2><i class="fas fa-user-check"></i> 8. Tenant Obligations</h2>
                    
                    <p>As a tenant using our platform, you agree to:</p>

                    <h3>Application Accuracy</h3>
                    <ul>
                        <li>Provide truthful and accurate information in rental applications</li>
                        <li>Submit required documentation promptly</li>
                        <li>Authorize background and credit checks as needed</li>
                        <li>Disclose all relevant information to landlords</li>
                    </ul>

                    <h3>Property Viewing</h3>
                    <ul>
                        <li>Respect scheduled viewing times</li>
                        <li>Treat properties with care during tours</li>
                        <li>Arrive on time or notify of delays</li>
                        <li>Ask questions and clarify doubts before applying</li>
                    </ul>

                    <h3>Lease Agreements</h3>
                    <ul>
                        <li>Read and understand all lease terms before signing</li>
                        <li>Honor all commitments and agreements</li>
                        <li>Pay rent and deposits on time</li>
                        <li>Maintain the property in good condition</li>
                    </ul>
                </div>

                <!-- Intellectual Property -->
                <div class="terms-section" id="intellectual">
                    <h2><i class="fas fa-copyright"></i> 9. Intellectual Property</h2>
                    
                    <h3>Platform Content</h3>
                    <p>
                        All content on AmarThikana, including text, graphics, logos, images, software, and design, 
                        is the property of AmarThikana or its licensors and is protected by copyright and trademark laws.
                    </p>

                    <h3>User Content</h3>
                    <p>By posting content on our platform, you grant us:</p>
                    <ul>
                        <li>A non-exclusive, worldwide license to use, reproduce, and display your content</li>
                        <li>The right to modify content for formatting and technical requirements</li>
                        <li>Permission to use content for marketing and promotional purposes</li>
                    </ul>

                    <h3>Content Rights</h3>
                    <p>You represent and warrant that:</p>
                    <ul>
                        <li>You own or have rights to all content you post</li>
                        <li>Your content does not infringe on third-party rights</li>
                        <li>You have permission to use all photos and materials</li>
                    </ul>
                </div>

                <!-- Privacy & Data Protection -->
                <div class="terms-section" id="privacy">
                    <h2><i class="fas fa-shield-alt"></i> 10. Privacy & Data Protection</h2>
                    
                    <p>Your privacy is important to us. Our use of your personal data is governed by our Privacy Policy.</p>

                    <h3>Data Collection</h3>
                    <p>We collect and process:</p>
                    <ul>
                        <li>Account information and credentials</li>
                        <li>Property listing data</li>
                        <li>Communication records</li>
                        <li>Payment and transaction information</li>
                        <li>Usage data and analytics</li>
                    </ul>

                    <h3>Data Security</h3>
                    <ul>
                        <li>We implement industry-standard security measures</li>
                        <li>Data is encrypted during transmission and storage</li>
                        <li>Access is restricted to authorized personnel only</li>
                        <li>Regular security audits are conducted</li>
                    </ul>

                    <div class="highlight-box">
                        <i class="fas fa-info-circle"></i>
                        <p>
                            For detailed information about how we collect, use, and protect your data, 
                            please review our Privacy Policy.
                        </p>
                    </div>
                </div>

                <!-- Termination -->
                <div class="terms-section" id="termination">
                    <h2><i class="fas fa-times-circle"></i> 11. Termination & Suspension</h2>
                    
                    <h3>Termination by You</h3>
                    <p>You may terminate your account at any time by:</p>
                    <ul>
                        <li>Accessing account settings and selecting "Delete Account"</li>
                        <li>Contacting our support team</li>
                        <li>Ceasing to use the Service</li>
                    </ul>

                    <h3>Termination by Us</h3>
                    <p>We may suspend or terminate your account immediately if you:</p>
                    <ul>
                        <li>Violate these Terms of Service</li>
                        <li>Engage in fraudulent or illegal activities</li>
                        <li>Abuse or harass other users</li>
                        <li>Fail to pay applicable fees</li>
                        <li>Provide false or misleading information</li>
                    </ul>

                    <h3>Effects of Termination</h3>
                    <ul>
                        <li>Access to your account will be revoked</li>
                        <li>Active listings will be removed</li>
                        <li>Pending transactions may be canceled</li>
                        <li>Certain data may be retained as required by law</li>
                    </ul>

                    <div class="warning-box">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>
                            Termination does not relieve you of obligations incurred prior to termination, 
                            including payment obligations and active lease agreements.
                        </p>
                    </div>
                </div>

                <!-- Limitation of Liability -->
                <div class="terms-section" id="liability">
                    <h2><i class="fas fa-ban"></i> 12. Limitation of Liability</h2>
                    
                    <h3>Disclaimer of Warranties</h3>
                    <p>
                        THE SERVICE IS PROVIDED "AS IS" AND "AS AVAILABLE" WITHOUT WARRANTIES OF ANY KIND, 
                        EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO WARRANTIES OF MERCHANTABILITY, 
                        FITNESS FOR A PARTICULAR PURPOSE, AND NON-INFRINGEMENT.
                    </p>

                    <h3>Limitation of Liability</h3>
                    <p>
                        TO THE MAXIMUM EXTENT PERMITTED BY LAW, AMARTHIKANA SHALL NOT BE LIABLE FOR:
                    </p>
                    <ul>
                        <li>Indirect, incidental, or consequential damages</li>
                        <li>Loss of profits, revenue, or data</li>
                        <li>Property damage or personal injury</li>
                        <li>Damages arising from user interactions or transactions</li>
                        <li>Damages from unauthorized access or data breaches</li>
                    </ul>

                    <h3>Maximum Liability</h3>
                    <p>
                        Our total liability for any claim shall not exceed the amount you paid us in the 12 months 
                        preceding the claim, or ৳10,000, whichever is less.
                    </p>
                </div>

                <!-- Dispute Resolution -->
                <div class="terms-section" id="disputes">
                    <h2><i class="fas fa-balance-scale"></i> 13. Dispute Resolution</h2>
                    
                    <h3>User Disputes</h3>
                    <p>
                        Disputes between users (landlords and tenants) are the responsibility of the parties involved. 
                        We may provide mediation assistance but are not obligated to resolve disputes.
                    </p>

                    <h3>Disputes with AmarThikana</h3>
                    <p>For disputes with our company:</p>
                    <ul>
                        <li>First contact our support team to attempt resolution</li>
                        <li>If unresolved, disputes may be submitted to mediation</li>
                        <li>Both parties agree to good-faith negotiation</li>
                    </ul>

                    <h3>Governing Law</h3>
                    <p>
                        These Terms are governed by the laws of Bangladesh. Any legal action must be brought in the 
                        courts of Dhaka, Bangladesh.
                    </p>

                    <h3>Arbitration</h3>
                    <p>
                        By using our Service, you agree to binding arbitration for disputes that cannot be resolved 
                        through negotiation or mediation, in accordance with Bangladesh arbitration laws.
                    </p>
                </div>

                <!-- Modifications -->
                <div class="terms-section" id="modifications">
                    <h2><i class="fas fa-sync-alt"></i> 14. Modifications to Terms</h2>
                    
                    <p>
                        We reserve the right to modify these Terms at any time. Changes will be effective immediately 
                        upon posting to our website.
                    </p>

                    <h3>Notice of Changes</h3>
                    <ul>
                        <li>Material changes will be communicated via email or platform notification</li>
                        <li>The "Last Updated" date will reflect the most recent revision</li>
                        <li>Continued use after changes constitutes acceptance</li>
                    </ul>

                    <h3>Your Options</h3>
                    <p>If you disagree with modified terms, you must:</p>
                    <ul>
                        <li>Discontinue use of the Service</li>
                        <li>Close your account</li>
                        <li>Cease all platform activities</li>
                    </ul>

                    <div class="highlight-box">
                        <i class="fas fa-info-circle"></i>
                        <p>
                            We recommend reviewing these Terms periodically to stay informed of any updates or changes.
                        </p>
                    </div>
                </div>

                <!-- Contact -->
                <div class="terms-section" id="contact">
                    <h2><i class="fas fa-envelope"></i> 15. Contact Information</h2>
                    
                    <p>For questions about these Terms of Service, please contact us:</p>

                    <h3>Customer Support</h3>
                    <ul>
                        <li><strong>Email:</strong> legal@amarthikana.com</li>
                        <li><strong>Phone:</strong> +880 1712-345678</li>
                        <li><strong>Address:</strong> 123 Gulshan Avenue, Dhaka 1212, Bangladesh</li>
                        <li><strong>Hours:</strong> Sunday - Thursday, 9:00 AM - 6:00 PM (GMT+6)</li>
                    </ul>

                    <h3>Legal Department</h3>
                    <p>
                        For legal inquiries, formal notices, or compliance matters, please write to:<br>
                        <strong>Legal Department, AmarThikana</strong><br>
                        123 Gulshan Avenue, Dhaka 1212, Bangladesh
                    </p>
                </div>

                <!-- Acceptance -->
                <div class="acceptance-box">
                    <h2>Agreement & Acceptance</h2>
                    <p>
                        By using AmarThikana, you acknowledge that you have read, understood, and agree to be bound 
                        by these Terms of Service
                    </p>
                    <a href="signup.php" class="btn">
                        <i class="fas fa-check-circle"></i> I Accept - Sign Up
                    </a>
                    <a href="index.php" class="btn">
                        <i class="fas fa-arrow-left"></i> Go Back
                    </a>
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
        // Smooth scroll for table of contents
        document.querySelectorAll('.table-of-contents a').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetSection = document.querySelector(targetId);
                
                if (targetSection) {
                    targetSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>




