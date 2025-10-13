<style>
    /* --- Footer Styles --- */
    .main-footer { 
        background-color: #2c3e50;
        padding: 60px 0 20px; 
        color: #ecf0f1;
    }
    
    .footer-grid { 
        display: grid; 
        grid-template-columns: 2fr 1fr 1fr 1.5fr; 
        gap: 40px; 
        margin-bottom: 40px; 
    }
    
    .footer-col .logo { 
        font-family: 'Poppins', sans-serif;
        font-size: 1.75rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 15px; 
        display: flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }
    
    .footer-col .logo i {
        color: #1abc9c;
    }
    
    .footer-col p { 
        font-size: 0.9rem; 
        line-height: 1.7; 
        color: #ecf0f1;
        margin-bottom: 20px;
    }
    
    .footer-col h4 { 
        color: #ffffff;
        margin-bottom: 20px; 
        font-size: 1.1rem; 
        font-weight: 600;
        font-family: 'Poppins', sans-serif;
    }
    
    .footer-col ul { 
        list-style: none; 
        padding: 0;
        margin: 0;
    }
    
    .footer-col ul li { 
        margin-bottom: 10px; 
    }
    
    .footer-col ul a { 
        color: #ecf0f1;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-block;
    }
    
    .footer-col ul a:hover { 
        color: #1abc9c;
        padding-left: 5px; 
        transform: translateX(5px);
    }
    
    .social-icons { 
        display: flex; 
        gap: 15px; 
        margin-top: 20px; 
    }
    
    .social-icons a { 
        width: 40px;
        height: 40px;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ecf0f1;
        font-size: 1rem;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .social-icons a:hover { 
        color: #ffffff; 
        background-color: #1abc9c;
        transform: translateY(-5px);
    }
    
    .newsletter-form { 
        display: flex; 
        position: relative; 
        margin-top: 10px; 
    }
    
    .newsletter-form input { 
        width: 100%; 
        padding: 12px 50px 12px 15px; 
        border: 1px solid rgba(255, 255, 255, 0.2); 
        border-radius: 8px; 
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
        font-size: 0.95rem;
        outline: none;
        transition: all 0.3s ease;
    }
    
    .newsletter-form input::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }
    
    .newsletter-form input:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: #1abc9c;
    }
    
    .newsletter-form button { 
        position: absolute; 
        right: 5px; 
        top: 50%; 
        transform: translateY(-50%); 
        background: #1abc9c; 
        color: white; 
        border: none; 
        width: 36px; 
        height: 36px; 
        border-radius: 6px; 
        cursor: pointer; 
        transition: all 0.3s ease;
    }
    
    .newsletter-form button:hover {
        background: #16a085;
        transform: translateY(-50%) scale(1.05);
    }
    
    .newsletter-form button i {
        pointer-events: none;
    }
    
    .footer-bottom { 
        text-align: center; 
        padding-top: 20px; 
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .footer-bottom p { 
        color: #7f8c8d;
        font-size: 0.9rem; 
        margin: 0;
    }
    
    #scrollToTopBtn {
        background-color: #1abc9c;
        display: none; 
        position: fixed; 
        bottom: 30px; 
        right: 30px; 
        z-index: 999; 
        border: none; 
        outline: none; 
        color: white; 
        cursor: pointer; 
        padding: 0; 
        width: 50px; 
        height: 50px; 
        border-radius: 50%; 
        font-size: 1.2rem; 
        box-shadow: 0 4px 10px rgba(0,0,0,0.2); 
        opacity: 0; 
        transition: opacity 0.4s ease, transform 0.4s ease; 
        transform: translateY(20px);
        align-items: center;
        justify-content: center;
    }
    
    #scrollToTopBtn.show { 
        opacity: 1; 
        transform: translateY(0); 
        display: flex;
    }
    
    #scrollToTopBtn:hover {
        background-color: #16a085;
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(26, 188, 156, 0.4);
    }
    
    #scrollToTopBtn i {
        pointer-events: none;
    }
    
    @media (max-width: 992px) {
        .footer-grid { 
            grid-template-columns: repeat(2, 1fr); 
        }
        .footer-col.about-col { 
            grid-column: 1 / -1; 
        }
    }
    
    @media (max-width: 768px) {
        .footer-grid { 
            grid-template-columns: 1fr; 
        }
        
        .main-footer {
            padding: 40px 0 20px;
        }
        
        .footer-col .logo {
            font-size: 1.5rem;
        }
        
        .footer-col h4 {
            font-size: 1rem;
        }
        
        #scrollToTopBtn {
            bottom: 20px;
            right: 20px;
            width: 45px;
            height: 45px;
            font-size: 1rem;
        }
    }
</style>

<footer class="main-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col about-col">
                <a href="index.php" class="logo">
                    <i class="fa-solid fa-house-chimney-window"></i> 
                    AmarThikana
                </a>
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
                    <li><a href="index.php">Rent</a></li>
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
                    <li><a href="terms-of-service.php">Terms of Service</a></li>
                    <li><a href="privacy-policy.php">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Stay Updated</h4>
                <p>Subscribe to our newsletter for the latest listings and deals.</p>
                <form class="newsletter-form">
                    <input type="email" placeholder="Enter your email" required>
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 AmarThikana. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<button id="scrollToTopBtn" title="Go to top"><i class="fas fa-arrow-up"></i></button>

<script>
    const scrollToTopBtn = document.getElementById('scrollToTopBtn');
    
    window.onscroll = function() {
        if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
            scrollToTopBtn.style.display = "flex";
            setTimeout(() => scrollToTopBtn.classList.add('show'), 10);
        } else {
            scrollToTopBtn.classList.remove('show');
            setTimeout(() => {
                if (!scrollToTopBtn.classList.contains('show')) {
                    scrollToTopBtn.style.display = "none";
                }
            }, 400); 
        }
    };
    
    scrollToTopBtn.onclick = function() {
        window.scrollTo({top: 0, behavior: 'smooth'});
    }
    
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            alert('Thank you for subscribing! We\'ll send updates to ' + email);
            this.reset();
        });
    }
</script>