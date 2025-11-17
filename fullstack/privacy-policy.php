<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - AmarThikana</title>
    
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
        /* Privacy Policy Specific Styles */
        .privacy-hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 100px 0 60px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .privacy-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
        }

        .privacy-hero-content {
            position: relative;
            z-index: 1;
        }

        .privacy-hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .privacy-hero p {
            font-size: 1.2rem;
            opacity: 0.95;
            max-width: 800px;
            margin: 0 auto;
        }

        .privacy-content {
            padding: 80px 0;
        }

        .privacy-container {
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

        .privacy-section {
            background: white;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            border-left: 4px solid var(--primary-color);
            scroll-margin-top: 100px;
        }

        .privacy-section h2 {
            font-size: 1.8rem;
            color: var(--text-color);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .privacy-section h2 i {
            color: var(--primary-color);
            font-size: 1.5rem;
        }

        .privacy-section h3 {
            font-size: 1.3rem;
            color: var(--text-color);
            margin-top: 25px;
            margin-bottom: 15px;
        }

        .privacy-section h4 {
            font-size: 1.1rem;
            color: var(--text-color);
            margin-top: 20px;
            margin-bottom: 12px;
        }

        .privacy-section p {
            color: #555;
            line-height: 1.8;
            margin-bottom: 15px;
        }

        .privacy-section ul, .privacy-section ol {
            margin-bottom: 20px;
            padding-left: 25px;
        }

        .privacy-section ul li, .privacy-section ol li {
            color: #555;
            line-height: 1.8;
            margin-bottom: 10px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .data-table th,
        .data-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .data-table th {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            font-weight: 600;
        }

        .data-table tr:hover {
            background: #f8f9fa;
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

        .security-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .security-box i {
            color: #28a745;
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .security-box p {
            margin: 0;
            color: #155724;
            font-weight: 500;
        }

        .rights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 25px 0;
        }

        .rights-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 3px solid var(--primary-color);
        }

        .rights-card i {
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .rights-card h4 {
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: var(--text-color);
        }

        .rights-card p {
            font-size: 0.9rem;
            color: #666;
            line-height: 1.6;
        }

        .contact-privacy {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 50px;
            border-radius: 20px;
            text-align: center;
            color: white;
            margin-top: 50px;
        }

        .contact-privacy h2 {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .contact-privacy p {
            font-size: 1.1rem;
            margin-bottom: 25px;
            opacity: 0.95;
        }

        .contact-privacy .btn {
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

        .contact-privacy .btn:hover {
            background: transparent;
            color: white;
            transform: translateY(-3px);
        }

        @media (max-width: 768px) {
            .privacy-hero h1 {
                font-size: 2rem;
            }

            .privacy-hero p {
                font-size: 1rem;
            }

            .privacy-section {
                padding: 25px;
            }

            .privacy-section h2 {
                font-size: 1.4rem;
            }

            .contact-privacy {
                padding: 30px 20px;
            }

            .contact-privacy .btn {
                margin: 10px 5px;
                padding: 12px 25px;
            }

            .data-table th,
            .data-table td {
                padding: 10px;
                font-size: 0.9rem;
            }

            .rights-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <!-- Header Placeholder -->
    <div id="header-placeholder"></div>

    <!-- Privacy Hero Section -->
    <section class="privacy-hero">
        <div class="container privacy-hero-content">
            <h1>Privacy Policy</h1>
            <p>Your privacy is important to us. Learn how we collect, use, and protect your information</p>
        </div>
    </section>

    <!-- Privacy Content Section -->
    <section class="privacy-content">
        <div class="container">
            <div class="privacy-container">
                
                <!-- Last Updated -->
                <div class="last-updated">
                    <p><i class="fas fa-calendar-alt"></i> Last Updated: January 1, 2025</p>
                </div>

                <!-- Table of Contents -->
                <div class="table-of-contents">
                    <h3><i class="fas fa-list"></i> Table of Contents</h3>
                    <ol>
                        <li><a href="#introduction">Introduction</a></li>
                        <li><a href="#information-collect">Information We Collect</a></li>
                        <li><a href="#how-use">How We Use Your Information</a></li>
                        <li><a href="#sharing">Information Sharing & Disclosure</a></li>
                        <li><a href="#cookies">Cookies & Tracking Technologies</a></li>
                        <li><a href="#security">Data Security</a></li>
                        <li><a href="#retention">Data Retention</a></li>
                        <li><a href="#rights">Your Privacy Rights</a></li>
                        <li><a href="#children">Children's Privacy</a></li>
                        <li><a href="#international">International Data Transfers</a></li>
                        <li><a href="#third-party">Third-Party Services</a></li>
                        <li><a href="#changes">Changes to Privacy Policy</a></li>
                        <li><a href="#contact">Contact Us</a></li>
                    </ol>
                </div>

                <!-- Introduction -->
                <div class="privacy-section" id="introduction">
                    <h2><i class="fas fa-info-circle"></i> 1. Introduction</h2>
                    <p>
                        Welcome to AmarThikana. This Privacy Policy explains how we collect, use, disclose, and safeguard 
                        your information when you use our website, mobile application, and related services (collectively, 
                        the "Service").
                    </p>
                    
                    <p>
                        We are committed to protecting your privacy and ensuring you have a positive experience on our platform. 
                        This policy outlines our practices regarding data collection and usage, and explains your rights concerning 
                        your personal information.
                    </p>

                    <div class="highlight-box">
                        <i class="fas fa-info-circle"></i>
                        <p>
                            By using AmarThikana, you agree to the collection and use of information in accordance with this 
                            Privacy Policy. If you do not agree with our policies and practices, please do not use our Service.
                        </p>
                    </div>

                    <h3>Who We Are</h3>
                    <ul>
                        <li><strong>Company Name:</strong> AmarThikana</li>
                        <li><strong>Address:</strong> 123 Gulshan Avenue, Dhaka 1212, Bangladesh</li>
                        <li><strong>Email:</strong> privacy@amarthikana.com</li>
                        <li><strong>Phone:</strong> +880 1712-345678</li>
                    </ul>
                </div>

                <!-- Information We Collect -->
                <div class="privacy-section" id="information-collect">
                    <h2><i class="fas fa-database"></i> 2. Information We Collect</h2>
                    <p>
                        We collect several types of information to provide and improve our Service. The information we collect 
                        includes:
                    </p>

                    <h3>2.1 Personal Information</h3>
                    <p>Information that identifies you personally, which you provide when creating an account or using our Service:</p>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Data Type</th>
                                <th>Examples</th>
                                <th>Purpose</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Identity Information</strong></td>
                                <td>Full name, date of birth, gender, NID/Passport number</td>
                                <td>Account creation, verification</td>
                            </tr>
                            <tr>
                                <td><strong>Contact Information</strong></td>
                                <td>Email address, phone number, mailing address</td>
                                <td>Communication, notifications</td>
                            </tr>
                            <tr>
                                <td><strong>Financial Information</strong></td>
                                <td>Bank account details, payment card information</td>
                                <td>Payment processing</td>
                            </tr>
                            <tr>
                                <td><strong>Property Information</strong></td>
                                <td>Property details, photos, rental history</td>
                                <td>Listing management</td>
                            </tr>
                            <tr>
                                <td><strong>Employment Information</strong></td>
                                <td>Employer name, income, employment status</td>
                                <td>Tenant verification</td>
                            </tr>
                        </tbody>
                    </table>

                    <h3>2.2 Automatically Collected Information</h3>
                    <p>Information automatically collected when you use our Service:</p>
                    <ul>
                        <li><strong>Device Information:</strong> Device type, operating system, browser type, unique device identifiers</li>
                        <li><strong>Usage Data:</strong> Pages viewed, features used, time spent, search queries</li>
                        <li><strong>Location Data:</strong> IP address, approximate geographic location</li>
                        <li><strong>Log Data:</strong> Access times, error logs, referring URLs</li>
                        <li><strong>Cookies & Similar Technologies:</strong> Session cookies, preference cookies, analytics cookies</li>
                    </ul>

                    <h3>2.3 Information from Third Parties</h3>
                    <p>We may receive information about you from:</p>
                    <ul>
                        <li>Social media platforms (when you connect your account)</li>
                        <li>Background check providers (with your consent)</li>
                        <li>Credit reporting agencies (for tenant screening)</li>
                        <li>Payment processors</li>
                        <li>Public databases and records</li>
                    </ul>

                    <h3>2.4 User-Generated Content</h3>
                    <ul>
                        <li>Property reviews and ratings</li>
                        <li>Messages and communications between users</li>
                        <li>Photos and videos you upload</li>
                        <li>Comments and feedback</li>
                    </ul>
                </div>

                <!-- How We Use Your Information -->
                <div class="privacy-section" id="how-use">
                    <h2><i class="fas fa-cogs"></i> 3. How We Use Your Information</h2>
                    <p>We use the collected information for various purposes:</p>

                    <h3>3.1 To Provide Our Service</h3>
                    <ul>
                        <li>Create and manage your account</li>
                        <li>Process rental applications and transactions</li>
                        <li>Facilitate communication between landlords and tenants</li>
                        <li>Verify user identity and conduct background checks</li>
                        <li>Process payments and manage billing</li>
                        <li>Provide customer support</li>
                    </ul>

                    <h3>3.2 To Improve Our Service</h3>
                    <ul>
                        <li>Analyze usage patterns and trends</li>
                        <li>Conduct research and development</li>
                        <li>Test new features and functionality</li>
                        <li>Monitor and improve platform performance</li>
                        <li>Personalize your experience</li>
                    </ul>

                    <h3>3.3 For Marketing & Communication</h3>
                    <ul>
                        <li>Send promotional offers and updates (with your consent)</li>
                        <li>Provide personalized recommendations</li>
                        <li>Send transactional emails (receipts, confirmations)</li>
                        <li>Notify you of important changes or updates</li>
                        <li>Conduct surveys and gather feedback</li>
                    </ul>

                    <h3>3.4 For Safety & Security</h3>
                    <ul>
                        <li>Detect and prevent fraud and abuse</li>
                        <li>Enforce our Terms of Service</li>
                        <li>Protect against security threats</li>
                        <li>Resolve disputes and troubleshoot problems</li>
                        <li>Comply with legal obligations</li>
                    </ul>

                    <div class="security-box">
                        <i class="fas fa-shield-alt"></i>
                        <p>
                            We process your personal information only when we have a legal basis to do so, including consent, 
                            contractual necessity, legal obligations, or legitimate business interests.
                        </p>
                    </div>
                </div>

                <!-- Information Sharing -->
                <div class="privacy-section" id="sharing">
                    <h2><i class="fas fa-share-alt"></i> 4. Information Sharing & Disclosure</h2>
                    <p>
                        We do not sell your personal information to third parties. However, we may share your information in 
                        the following circumstances:
                    </p>

                    <h3>4.1 With Other Users</h3>
                    <ul>
                        <li>When you apply for a property, landlords can view your profile and application details</li>
                        <li>When you list a property, tenants can view property information and your contact details</li>
                        <li>Public reviews and ratings you post are visible to all users</li>
                    </ul>

                    <h3>4.2 With Service Providers</h3>
                    <p>We share information with third-party service providers who perform services on our behalf:</p>
                    <ul>
                        <li><strong>Payment Processors:</strong> To process transactions (bKash, Nagad, Stripe)</li>
                        <li><strong>Cloud Storage:</strong> To store data securely (AWS, Google Cloud)</li>
                        <li><strong>Analytics Providers:</strong> To analyze platform usage (Google Analytics)</li>
                        <li><strong>Communication Services:</strong> To send emails and SMS (SendGrid, Twilio)</li>
                        <li><strong>Background Check Services:</strong> To verify tenant information</li>
                    </ul>

                    <h3>4.3 For Legal Reasons</h3>
                    <p>We may disclose your information when required by law or to:</p>
                    <ul>
                        <li>Comply with legal process (subpoenas, court orders)</li>
                        <li>Respond to government requests</li>
                        <li>Enforce our Terms of Service</li>
                        <li>Protect our rights, property, or safety</li>
                        <li>Prevent fraud or illegal activity</li>
                    </ul>

                    <h3>4.4 Business Transfers</h3>
                    <p>
                        In the event of a merger, acquisition, or sale of assets, your information may be transferred to the 
                        acquiring entity. We will notify you of any such change.
                    </p>

                    <div class="warning-box">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>
                            We require all third-party service providers to respect the security of your personal data and 
                            treat it in accordance with applicable laws.
                        </p>
                    </div>
                </div>

                <!-- Cookies -->
                <div class="privacy-section" id="cookies">
                    <h2><i class="fas fa-cookie-bite"></i> 5. Cookies & Tracking Technologies</h2>
                    <p>
                        We use cookies and similar tracking technologies to track activity on our Service and store certain information.
                    </p>

                    <h3>Types of Cookies We Use</h3>

                    <h4>Essential Cookies</h4>
                    <p>Required for the Service to function properly:</p>
                    <ul>
                        <li>Session management and authentication</li>
                        <li>Security and fraud prevention</li>
                        <li>Load balancing</li>
                    </ul>

                    <h4>Functional Cookies</h4>
                    <p>Enhance your experience:</p>
                    <ul>
                        <li>Remember your preferences and settings</li>
                        <li>Store language and location preferences</li>
                        <li>Personalize content</li>
                    </ul>

                    <h4>Analytics Cookies</h4>
                    <p>Help us understand how users interact with our Service:</p>
                    <ul>
                        <li>Track page views and user behavior</li>
                        <li>Measure performance and engagement</li>
                        <li>Identify popular features</li>
                    </ul>

                    <h4>Advertising Cookies</h4>
                    <p>Used to deliver relevant advertisements:</p>
                    <ul>
                        <li>Target ads based on your interests</li>
                        <li>Measure ad effectiveness</li>
                        <li>Limit ad frequency</li>
                    </ul>

                    <h3>Managing Cookies</h3>
                    <p>You can control cookies through your browser settings:</p>
                    <ul>
                        <li>Accept or reject cookies</li>
                        <li>Delete existing cookies</li>
                        <li>Block third-party cookies</li>
                        <li>Set preferences for specific websites</li>
                    </ul>

                    <div class="highlight-box">
                        <i class="fas fa-info-circle"></i>
                        <p>
                            Note that blocking or deleting cookies may limit your ability to use certain features of our Service.
                        </p>
                    </div>
                </div>

                <!-- Data Security -->
                <div class="privacy-section" id="security">
                    <h2><i class="fas fa-lock"></i> 6. Data Security</h2>
                    <p>
                        We implement appropriate technical and organizational security measures to protect your personal information 
                        against unauthorized access, alteration, disclosure, or destruction.
                    </p>

                    <h3>Security Measures</h3>
                    <ul>
                        <li><strong>Encryption:</strong> Data encrypted in transit (SSL/TLS) and at rest (AES-256)</li>
                        <li><strong>Access Controls:</strong> Role-based access and multi-factor authentication</li>
                        <li><strong>Firewalls:</strong> Network security and intrusion detection systems</li>
                        <li><strong>Regular Audits:</strong> Security assessments and penetration testing</li>
                        <li><strong>Employee Training:</strong> Security awareness and data protection training</li>
                        <li><strong>Secure Development:</strong> Security-focused coding practices</li>
                        <li><strong>Incident Response:</strong> Dedicated team for security incidents</li>
                    </ul>

                    <h3>Your Responsibility</h3>
                    <p>You can help keep your account secure by:</p>
                    <ul>
                        <li>Using a strong, unique password</li>
                        <li>Enabling two-factor authentication</li>
                        <li>Not sharing your login credentials</li>
                        <li>Logging out of shared devices</li>
                        <li>Reporting suspicious activity immediately</li>
                    </ul>

                    <div class="security-box">
                        <i class="fas fa-shield-alt"></i>
                        <p>
                            While we strive to protect your personal information, no method of transmission over the internet 
                            or electronic storage is 100% secure. We cannot guarantee absolute security.
                        </p>
                    </div>
                </div>

                <!-- Data Retention -->
                <div class="privacy-section" id="retention">
                    <h2><i class="fas fa-clock"></i> 7. Data Retention</h2>
                    <p>
                        We retain your personal information only for as long as necessary to fulfill the purposes outlined in 
                        this Privacy Policy, unless a longer retention period is required by law.
                    </p>

                    <h3>Retention Periods</h3>
                    <ul>
                        <li><strong>Account Information:</strong> Retained while your account is active, plus 6 months after closure</li>
                        <li><strong>Transaction Records:</strong> Retained for 7 years for tax and accounting purposes</li>
                        <li><strong>Communication Logs:</strong> Retained for 2 years for customer service and dispute resolution</li>
                        <li><strong>Property Listings:</strong> Retained for 1 year after removal or until account closure</li>
                        <li><strong>Analytics Data:</strong> Aggregated and anonymized after 2 years</li>
                    </ul>

                    <h3>Data Deletion</h3>
                    <p>When data is deleted:</p>
                    <ul>
                        <li>Data is securely erased from active systems</li>
                        <li>Backup copies are removed during routine cycles</li>
                        <li>Some information may be retained in anonymized form for analytics</li>
                        <li>Legal obligations may require retention of certain data</li>
                    </ul>
                </div>

                <!-- Your Privacy Rights -->
                <div class="privacy-section" id="rights">
                    <h2><i class="fas fa-user-shield"></i> 8. Your Privacy Rights</h2>
                    <p>
                        You have certain rights regarding your personal information. Depending on your location, these may include:
                    </p>

                    <div class="rights-grid">
                        <div class="rights-card">
                            <i class="fas fa-eye"></i>
                            <h4>Right to Access</h4>
                            <p>Request a copy of the personal information we hold about you</p>
                        </div>

                        <div class="rights-card">
                            <i class="fas fa-edit"></i>
                            <h4>Right to Correction</h4>
                            <p>Request correction of inaccurate or incomplete personal information</p>
                        </div>

                        <div class="rights-card">
                            <i class="fas fa-trash"></i>
                            <h4>Right to Deletion</h4>
                            <p>Request deletion of your personal information (subject to legal obligations)</p>
                        </div>

                        <div class="rights-card">
                            <i class="fas fa-download"></i>
                            <h4>Right to Portability</h4>
                            <p>Receive your data in a structured, machine-readable format</p>
                        </div>

                        <div class="rights-card">
                            <i class="fas fa-ban"></i>
                            <h4>Right to Object</h4>
                            <p>Object to processing of your personal information for certain purposes</p>
                        </div>

                        <div class="rights-card">
                            <i class="fas fa-pause"></i>
                            <h4>Right to Restriction</h4>
                            <p>Request restriction of processing in certain circumstances</p>
                        </div>
                    </div>

                    <h3>How to Exercise Your Rights</h3>
                    <p>To exercise any of these rights, you can:</p>
                    <ul>
                        <li>Access your account settings and make changes directly</li>
                        <li>Contact us at privacy@amarthikana.com</li>
                        <li>Call our privacy hotline at +880 1712-345678</li>
                        <li>Submit a written request to our office address</li>
                    </ul>

                    <div class="highlight-box">
                        <i class="fas fa-info-circle"></i>
                        <p>
                            We will respond to your request within 30 days. We may need to verify your identity before 
                            processing your request.
                        </p>
                    </div>

                    <h3>Marketing Preferences</h3>
                    <p>You can opt out of marketing communications by:</p>
                    <ul>
                        <li>Clicking "Unsubscribe" in marketing emails</li>
                        <li>Adjusting notification settings in your account</li>
                        <li>Contacting customer support</li>
                    </ul>
                </div>

                <!-- Children's Privacy -->
                <div class="privacy-section" id="children">
                    <h2><i class="fas fa-child"></i> 9. Children's Privacy</h2>
                    <p>
                        Our Service is not intended for individuals under the age of 18. We do not knowingly collect personal 
                        information from children.
                    </p>

                    <div class="warning-box">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>
                            If we become aware that we have collected personal information from a child under 18 without 
                            parental consent, we will take steps to delete that information immediately.
                        </p>
                    </div>

                    <p>
                        If you are a parent or guardian and believe your child has provided us with personal information, 
                        please contact us at privacy@amarthikana.com.
                    </p>
                </div>

                <!-- International Data Transfers -->
                <div class="privacy-section" id="international">
                    <h2><i class="fas fa-globe"></i> 10. International Data Transfers</h2>
                    <p>
                        Your information may be transferred to and processed in countries other than Bangladesh. These countries 
                        may have different data protection laws.
                    </p>

                    <h3>Safeguards</h3>
                    <p>When transferring data internationally, we ensure:</p>
                    <ul>
                        <li>Transfers are to countries with adequate data protection laws</li>
                        <li>Standard contractual clauses are in place</li>
                        <li>Service providers are bound by data protection obligations</li>
                        <li>Appropriate technical and organizational measures are implemented</li>
                    </ul>

                    <h3>Data Processing Locations</h3>
                    <p>Your data may be processed in:</p>
                    <ul>
                        <li>Bangladesh (primary data center)</li>
                        <li>Singapore (backup and disaster recovery)</li>
                        <li>United States (cloud services and analytics)</li>
                        <li>European Union (payment processing)</li>
                    </ul>
                </div>

                <!-- Third-Party Services -->
                <div class="privacy-section" id="third-party">
                    <h2><i class="fas fa-external-link-alt"></i> 11. Third-Party Services</h2>
                    <p>
                        Our Service may contain links to third-party websites, apps, and services. We are not responsible for 
                        the privacy practices of these third parties.
                    </p>

                    <h3>Third-Party Integrations</h3>
                    <ul>
                        <li><strong>Social Media:</strong> Facebook, Twitter, Instagram login and sharing</li>
                        <li><strong>Maps:</strong> Google Maps for property locations</li>
                        <li><strong>Payment Gateways:</strong> bKash, Nagad, Stripe, PayPal</li>
                        <li><strong>Analytics:</strong> Google Analytics, Mixpanel</li>
                        <li><strong>Customer Support:</strong> Zendesk, Intercom</li>
                    </ul>

                    <div class="highlight-box">
                        <i class="fas fa-info-circle"></i>
                        <p>
                            We encourage you to review the privacy policies of any third-party services before providing 
                            your information to them.
                        </p>
                    </div>
                </div>

                <!-- Changes to Privacy Policy -->
                <div class="privacy-section" id="changes">
                    <h2><i class="fas fa-sync-alt"></i> 12. Changes to Privacy Policy</h2>
                    <p>
                        We may update this Privacy Policy from time to time to reflect changes in our practices, technology, 
                        legal requirements, or other factors.
                    </p>

                    <h3>Notification of Changes</h3>
                    <p>When we make changes, we will:</p>
                    <ul>
                        <li>Update the "Last Updated" date at the top of this policy</li>
                        <li>Send email notifications for material changes</li>
                        <li>Display a prominent notice on our platform</li>
                        <li>Provide a summary of key changes</li>
                    </ul>

                    <h3>Your Continued Use</h3>
                    <p>
                        Your continued use of our Service after changes are posted constitutes acceptance of the updated 
                        Privacy Policy. If you do not agree with the changes, you should discontinue use of the Service.
                    </p>

                    <div class="highlight-box">
                        <i class="fas fa-info-circle"></i>
                        <p>
                            We recommend reviewing this Privacy Policy periodically to stay informed about how we protect 
                            your information.
                        </p>
                    </div>
                </div>

                <!-- Contact -->
                <div class="privacy-section" id="contact">
                    <h2><i class="fas fa-envelope"></i> 13. Contact Us</h2>
                    <p>
                        If you have any questions, concerns, or complaints about this Privacy Policy or our data practices, 
                        please contact us:
                    </p>

                    <h3>Privacy Department</h3>
                    <ul>
                        <li><strong>Email:</strong> privacy@amarthikana.com</li>
                        <li><strong>Phone:</strong> +880 1712-345678</li>
                        <li><strong>Mailing Address:</strong><br>
                            Privacy Officer<br>
                            AmarThikana<br>
                            123 Gulshan Avenue<br>
                            Dhaka 1212, Bangladesh
                        </li>
                        <li><strong>Business Hours:</strong> Sunday - Thursday, 9:00 AM - 6:00 PM (GMT+6)</li>
                    </ul>

                    <h3>Response Time</h3>
                    <p>
                        We aim to respond to all privacy inquiries within 5 business days. For urgent matters, please call 
                        our privacy hotline.
                    </p>

                    <h3>Complaints</h3>
                    <p>
                        If you believe we have not adequately addressed your privacy concerns, you have the right to lodge a 
                        complaint with the relevant data protection authority in Bangladesh.
                    </p>
                </div>

                <!-- Contact Privacy -->
                <div class="contact-privacy">
                    <h2>Questions About Privacy?</h2>
                    <p>Our privacy team is here to help you understand how we protect your data</p>
                    <a href="mailto:privacy@amarthikana.com" class="btn">
                        <i class="fas fa-envelope"></i> Email Privacy Team
                    </a>
                    <a href="help-center.php" class="btn">
                        <i class="fas fa-question-circle"></i> Visit Help Center
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




