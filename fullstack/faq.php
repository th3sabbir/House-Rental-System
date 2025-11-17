<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - AmarThikana</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/mobile.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        /* FAQ Specific Styles */
        .faq-hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 100px 0 60px;
            text-align: center;
            color: white;
        }

        .faq-hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .faq-hero p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .faq-section {
            padding: 80px 0;
        }

        .faq-search {
            max-width: 600px;
            margin: 0 auto 60px;
            position: relative;
        }

        .faq-search input {
            width: 100%;
            padding: 18px 50px 18px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 50px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .faq-search input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(22, 160, 133, 0.1);
        }

        .faq-search button {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--primary-color);
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s;
        }

        .faq-search button:hover {
            background: var(--secondary-color);
            transform: translateY(-50%) scale(1.05);
        }

        .faq-categories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 60px;
        }

        .category-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid #f0f0f0;
        }

        .category-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .category-card.active {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, rgba(22, 160, 133, 0.05) 0%, rgba(41, 128, 185, 0.05) 100%);
        }

        .category-card i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .category-card h3 {
            font-size: 1.2rem;
            margin-bottom: 8px;
            color: var(--text-color);
        }

        .category-card p {
            font-size: 0.9rem;
            color: #777;
        }

        .faq-list {
            max-width: 900px;
            margin: 0 auto;
        }

        .faq-category-title {
            font-size: 2rem;
            margin-bottom: 30px;
            color: var(--text-color);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .faq-category-title i {
            color: var(--primary-color);
        }

        .faq-item {
            background: white;
            border-radius: 12px;
            margin-bottom: 15px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }

        .faq-item:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .faq-question {
            padding: 25px 30px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: var(--text-color);
            transition: all 0.3s;
        }

        .faq-question:hover {
            color: var(--primary-color);
        }

        .faq-question i {
            transition: transform 0.3s;
            color: var(--primary-color);
        }

        .faq-item.active .faq-question i {
            transform: rotate(180deg);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out, padding 0.3s ease-out;
            padding: 0 30px;
        }

        .faq-item.active .faq-answer {
            max-height: 500px;
            padding: 0 30px 25px;
        }

        .faq-answer p {
            color: #555;
            line-height: 1.8;
        }

        .still-have-questions {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 60px 30px;
            border-radius: 20px;
            text-align: center;
            color: white;
            margin-top: 80px;
        }

        .still-have-questions h2 {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .still-have-questions p {
            font-size: 1.1rem;
            margin-bottom: 25px;
            opacity: 0.9;
        }

        .contact-options {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .contact-btn {
            background: white;
            color: var(--primary-color);
            padding: 15px 35px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .contact-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        @media (max-width: 768px) {
            .faq-hero h1 {
                font-size: 2rem;
            }

            .faq-categories {
                grid-template-columns: 1fr;
            }

            .faq-question {
                padding: 20px;
                font-size: 0.95rem;
            }

            .faq-answer {
                padding: 0 20px;
            }

            .faq-item.active .faq-answer {
                padding: 0 20px 20px;
            }

            .contact-options {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <!-- Header Placeholder -->
    <div id="header-placeholder"></div>

    <!-- FAQ Hero Section -->
    <section class="faq-hero">
        <div class="container">
            <h1>Frequently Asked Questions</h1>
            <p>Find answers to common questions about renting and listing properties</p>
        </div>
    </section>

    <!-- Main FAQ Section -->
    <section class="faq-section">
        <div class="container">
            
            <!-- Search Bar -->
            <div class="faq-search">
                <input type="text" id="faqSearchInput" placeholder="Search for answers...">
                <button type="button"><i class="fas fa-search"></i></button>
            </div>

            <!-- Category Cards -->
            <div class="faq-categories">
                <div class="category-card active" data-category="renters">
                    <i class="fas fa-users"></i>
                    <h3>For Renters</h3>
                    <p>12 Questions</p>
                </div>
                <div class="category-card" data-category="landlords">
                    <i class="fas fa-building"></i>
                    <h3>For Landlords</h3>
                    <p>10 Questions</p>
                </div>
                <div class="category-card" data-category="payments">
                    <i class="fas fa-credit-card"></i>
                    <h3>Payments & Fees</h3>
                    <p>8 Questions</p>
                </div>
                <div class="category-card" data-category="safety">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Safety & Security</h3>
                    <p>6 Questions</p>
                </div>
            </div>

            <!-- FAQ Lists by Category -->
            <div class="faq-list">
                
                <!-- For Renters -->
                <div class="faq-category" data-category="renters">
                    <h2 class="faq-category-title"><i class="fas fa-users"></i> For Renters</h2>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            How do I search for rental properties?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Use our search bar on the homepage to filter properties by location, price range, property type, and number of bedrooms. You can also use advanced filters to narrow down your search based on amenities, move-in date, and more.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            Can I schedule a virtual tour?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Yes! Most of our listings offer virtual tour options. Simply click on the property you're interested in and look for the "Schedule Virtual Tour" button. You can choose a convenient time slot and meet with the landlord via video call.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            What documents do I need to apply for a rental?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Typically, you'll need a valid ID (NID or Passport), proof of income (salary slips or bank statements), employment verification letter, and references. Some landlords may also request a credit report or additional documentation.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            How long does the application process take?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>The application review process typically takes 24-48 hours. Once approved, you'll receive a notification and can proceed with signing the lease agreement and paying the security deposit.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            What is included in the rent?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>This varies by property. Check the listing details for specifics. Generally, rent covers the use of the property. Utilities (water, electricity, gas, internet) may be included or separate. Always clarify with the landlord before signing.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            Can I negotiate the rent?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>While listed prices are set by landlords, some may be open to negotiation, especially for long-term leases or if you're willing to move in quickly. Contact the landlord directly through our messaging system to discuss.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            What if I need to break my lease early?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Lease terms vary, but most require 30-60 days notice. Review your lease agreement for specific early termination clauses. Some landlords may charge a fee or require you to find a replacement tenant.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            Are pets allowed in rental properties?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Pet policies vary by property. Use our "Pet-Friendly" filter when searching, or check the property details. Some landlords allow pets with an additional deposit or monthly fee.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            How do I report maintenance issues?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Once you're a tenant, you can submit maintenance requests through your account dashboard. You can also contact your landlord directly via the messaging system for urgent issues.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            Can I renew my lease?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Most leases offer renewal options. Contact your landlord 60-90 days before your lease expires to discuss renewal terms. Rent may be subject to increase based on market conditions.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            What happens if I miss a rent payment?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Contact your landlord immediately if you anticipate missing a payment. Late fees may apply as specified in your lease. Repeated missed payments could result in eviction proceedings.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            How do I get my security deposit back?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Security deposits are typically returned within 30 days of move-out, minus any deductions for damages beyond normal wear and tear. Ensure you clean the property thoroughly and document its condition with photos.</p>
                        </div>
                    </div>
                </div>

                <!-- For Landlords -->
                <div class="faq-category" data-category="landlords" style="display: none;">
                    <h2 class="faq-category-title"><i class="fas fa-building"></i> For Landlords</h2>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            How do I list my property?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Click on "List Your Property" in the navigation menu. Create an account or log in, then fill out the property details form with information about your rental, upload high-quality photos, and set your price. Your listing will be reviewed and published within 24 hours.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            Is there a fee to list my property?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Basic listings are free! We offer premium listing options with enhanced visibility and featured placement for a monthly fee. Commission fees may apply when you successfully rent your property.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            How do I screen potential tenants?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Our platform provides tenant applications with verified information, employment details, and references. You can also request background checks and credit reports (with tenant consent) through our partner services.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            Can I manage multiple properties?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Yes! Our landlord dashboard allows you to manage multiple properties, track applications, communicate with tenants, and monitor rental income all in one place.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            How do I collect rent payments?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>You can set up automatic rent collection through our integrated payment system. Tenants can pay via bank transfer, mobile banking (bKash, Nagad), or credit/debit card. Funds are transferred to your account within 2-3 business days.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            What if a tenant damages my property?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Document all damages with photos and descriptions. You can deduct repair costs from the security deposit. For disputes, our support team can help mediate, and you may need to pursue legal action for severe cases.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            Can I update my listing after publishing?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Absolutely! You can edit your listing anytime from your dashboard. Update photos, change pricing, modify amenities, or adjust availability with just a few clicks.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            How do I handle lease renewals?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Contact your tenant 60-90 days before lease expiration. You can generate a renewal agreement through our platform with updated terms and pricing. Both parties must agree and sign digitally.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            What insurance do I need?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>We recommend landlord insurance covering property damage, liability, and loss of rental income. Consult with an insurance provider for coverage specific to your property type and location.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            How do I evict a problematic tenant?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Follow Bangladesh rental laws for eviction procedures. Provide written notice as specified in your lease agreement (typically 30-60 days). For legal assistance, consult a property lawyer or legal aid service.</p>
                        </div>
                    </div>
                </div>

                <!-- Payments & Fees -->
                <div class="faq-category" data-category="payments" style="display: none;">
                    <h2 class="faq-category-title"><i class="fas fa-credit-card"></i> Payments & Fees</h2>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            What payment methods are accepted?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>We accept bank transfers, mobile banking (bKash, Nagad, Rocket), credit/debit cards (Visa, Mastercard), and cash payments. All digital payments are processed securely through our payment gateway.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            When is rent due?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Rent is typically due on the 1st of each month, but this can vary by lease agreement. Check your contract for specific due dates and grace periods. Late fees may apply after the grace period.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            How much is the security deposit?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Security deposits typically range from 1-3 months' rent, depending on the property and landlord's policy. This amount is refundable at the end of your lease, minus any deductions for damages or unpaid rent.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            Are there any hidden fees?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>No! All fees are clearly disclosed upfront. These may include application fees, security deposits, first month's rent, and utility setup fees. Review the property listing and lease agreement for a complete breakdown.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            Can I pay rent online?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Yes! Our platform supports online rent payments for added convenience. Set up automatic payments to ensure you never miss a due date. Payment confirmations are sent via email and available in your account.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            What are the late payment penalties?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Late fees vary by lease agreement but typically range from ৳500-৳2000 or 5-10% of monthly rent after a grace period (usually 3-5 days). Check your specific lease for exact terms.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            Do I need to pay utilities separately?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>It depends on the property. Some rentals include utilities in the monthly rent, while others require separate payments. Check the listing details or ask the landlord for clarification before signing the lease.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            How do refunds work?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Security deposit refunds are processed within 30 days of lease termination. Application fees are non-refundable. If you cancel before moving in, refund policies vary by landlord and timing of cancellation.</p>
                        </div>
                    </div>
                </div>

                <!-- Safety & Security -->
                <div class="faq-category" data-category="safety" style="display: none;">
                    <h2 class="faq-category-title"><i class="fas fa-shield-alt"></i> Safety & Security</h2>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            How do you verify property listings?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>We verify all property listings by checking ownership documents, conducting property inspections, and verifying landlord identity. Listings with a "Verified" badge have completed our full verification process.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            Is my personal information safe?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Yes! We use bank-level encryption to protect your data. Your personal information is never shared with third parties without your consent. Read our Privacy Policy for detailed information on data handling.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            How can I avoid rental scams?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Never send money before viewing the property. Only use our platform for payments. Verify the landlord's identity and meet in person when possible. Be wary of deals that seem too good to be true or requests for wire transfers.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            What if I encounter a fraudulent listing?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Report suspicious listings immediately using the "Report" button on the property page or contact our support team. We investigate all reports within 24 hours and remove fraudulent listings promptly.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            Are background checks performed?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Landlords can request background checks through our partner services (with tenant consent). This includes criminal history, credit reports, and rental history verification to ensure safe tenancies.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            How do you handle disputes?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Our support team provides mediation services for disputes between tenants and landlords. We review evidence from both parties and work toward fair resolutions. Serious disputes may require legal intervention.</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Still Have Questions Section -->
            <div class="still-have-questions">
                <h2>Still Have Questions?</h2>
                <p>Our support team is here to help you 24/7</p>
                <div class="contact-options">
                    <a href="#" class="contact-btn">
                        <i class="fas fa-envelope"></i> Email Support
                    </a>
                    <a href="#" class="contact-btn">
                        <i class="fas fa-phone"></i> Call Us
                    </a>
                    <a href="#" class="contact-btn">
                        <i class="fas fa-comments"></i> Live Chat
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
        // FAQ Accordion functionality
        document.addEventListener('DOMContentLoaded', function() {
            const faqItems = document.querySelectorAll('.faq-item');
            
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question');
                
                question.addEventListener('click', () => {
                    const isActive = item.classList.contains('active');
                    
                    // Close all other items
                    faqItems.forEach(otherItem => {
                        otherItem.classList.remove('active');
                    });
                    
                    // Toggle current item
                    if (!isActive) {
                        item.classList.add('active');
                    }
                });
            });

            // Category switching
            const categoryCards = document.querySelectorAll('.category-card');
            const faqCategories = document.querySelectorAll('.faq-category');
            
            categoryCards.forEach(card => {
                card.addEventListener('click', () => {
                    const category = card.dataset.category;
                    
                    // Update active card
                    categoryCards.forEach(c => c.classList.remove('active'));
                    card.classList.add('active');
                    
                    // Show selected category
                    faqCategories.forEach(cat => {
                        if (cat.dataset.category === category) {
                            cat.style.display = 'block';
                        } else {
                            cat.style.display = 'none';
                        }
                    });
                    
                    // Close all FAQs when switching category
                    faqItems.forEach(item => item.classList.remove('active'));
                });
            });

            // Search functionality
            const searchInput = document.getElementById('faqSearchInput');
            
            searchInput.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                
                if (searchTerm === '') {
                    // Reset view
                    faqCategories.forEach(cat => cat.style.display = 'none');
                    faqCategories[0].style.display = 'block';
                    faqItems.forEach(item => {
                        item.style.display = 'block';
                        item.classList.remove('active');
                    });
                    return;
                }
                
                // Show all categories when searching
                faqCategories.forEach(cat => cat.style.display = 'block');
                
                let hasResults = false;
                faqItems.forEach(item => {
                    const question = item.querySelector('.faq-question').textContent.toLowerCase();
                    const answer = item.querySelector('.faq-answer p').textContent.toLowerCase();
                    
                    if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                        item.style.display = 'block';
                        hasResults = true;
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>




