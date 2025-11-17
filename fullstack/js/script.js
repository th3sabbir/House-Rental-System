function onReady(fn) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fn);
    } else {
        fn();
    }
}

onReady(function () {

    // 1. Dynamic Header on Scroll
    const header = document.querySelector('.main-header');
    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }

    // Mobile menu toggle is handled by initializeMobileToggle() (below). We keep the
    // logic centralized so it works the same across static and AJAX-loaded headers.

    // Extract to function to reuse after AJAX header loads
    function initializeMobileToggle() {
        console.debug('initializeMobileToggle: running');
        const menuToggleEl = document.querySelector('.menu-toggle');
        const navLinksEl = document.querySelector('.nav-links');
        const navActionsEl = document.querySelector('.nav-actions');
        const overlayEl = document.getElementById('mobileMenuOverlay');
        const bodyEl = document.body;

        console.debug('Elements found:', {
            menuToggle: !!menuToggleEl,
            navLinks: !!navLinksEl,
            navActions: !!navActionsEl,
            overlay: !!overlayEl
        });

        if (!menuToggleEl || !navLinksEl || !navActionsEl) {
            console.warn('initializeMobileToggle: Missing required elements');
            return;
        }

        // Prevent duplicate initializations for the same header/menu
        if (menuToggleEl.dataset.mobileSetup === 'true') {
            console.debug('initializeMobileToggle: Already set up, skipping');
            return;
        }
        menuToggleEl.dataset.mobileSetup = 'true';
        console.debug('initializeMobileToggle: Setting up event handlers');

        // Use direct listener for the toggle
        menuToggleEl.addEventListener('click', function(e) {
            console.debug('menu-toggle clicked');
            const mobileMenuWrapper = document.querySelector('.mobile-menu');
            const isOpen = navLinksEl.classList.contains('active') || mobileMenuWrapper?.classList.contains('active');

            if (isOpen) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });

        function openMobileMenu() {
            // support both implementations: .nav-links.active or .mobile-menu.active
            const mobileMenuWrapper = document.querySelector('.mobile-menu');

            navLinksEl.classList.add('active');
            mobileMenuWrapper?.classList.add('active');
            navActionsEl.classList.add('active');
            bodyEl.classList.add('menu-open');
            overlayEl?.classList.add('active');
            const icon = menuToggleEl.querySelector('i');
            if (icon) { icon.classList.remove('fa-bars'); icon.classList.add('fa-times'); }
        }

        function closeMobileMenu() {
            const mobileMenuWrapper = document.querySelector('.mobile-menu');

            navLinksEl.classList.remove('active');
            mobileMenuWrapper?.classList.remove('active');
            navActionsEl.classList.remove('active');
            bodyEl.classList.remove('menu-open');
            overlayEl?.classList.remove('active');
            const icon = menuToggleEl.querySelector('i');
            if (icon) { icon.classList.remove('fa-times'); icon.classList.add('fa-bars'); }
        }

        // navLinks close on link click

        overlayEl?.addEventListener('click', closeMobileMenu);
        navLinksEl.querySelectorAll('a').forEach(link => link.addEventListener('click', closeMobileMenu));

        document.addEventListener('click', function(e) {
                if (bodyEl.classList.contains('menu-open') && 
                !menuToggleEl.contains(e.target) && 
                !navLinksEl.contains(e.target) && 
                !navActionsEl.contains(e.target)) {
                closeMobileMenu();
            }
        });
    }

    // Run initialization for static header
    initializeMobileToggle();
    console.debug('script.js: window.setupMobileMenu is available');

    // Indicate central mobile toggle has been initialized so any other mobile scripts can avoid duplicating handlers
    window.hasCentralMobileToggle = true;

    // A setup method that loader.js can call after it loads the header via AJAX
    window.setupMobileMenu = initializeMobileToggle;

    // Also respond to a dispatched event from loader.js as a last resort for ordering
    document.addEventListener('amarthikanaHeaderLoaded', function() {
        if (typeof window.setupMobileMenu === 'function') {
            window.setupMobileMenu();
        } else {
            initializeMobileToggle();
        }
    });

    // Expose a setup function so the mobile menu can be initialized after AJAX-loaded headers
    window.setupMobileMenu = initializeMobileToggle;

    // 3. Swiper.js Initialization for Testimonials
    const testimonialSlider = document.querySelector('.testimonials-slider');
    if (testimonialSlider && typeof Swiper !== 'undefined') {
        const swiper = new Swiper('.testimonials-slider', {
            loop: true,
            spaceBetween: 30,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    }

    // Optional: Add a ripple effect to the select on click for a modern touch
document.querySelectorAll('.search-form select').forEach(function(select) {
    select.addEventListener('mousedown', function(e) {
        this.style.boxShadow = '0 0 0 4px rgba(22,160,133,0.12)';
        setTimeout(() => { this.style.boxShadow = ''; }, 300);
    });
});

    // 4. Favorite Icon Toggle
    const favoriteIcons = document.querySelectorAll('.favorite-icon');
    favoriteIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            this.classList.toggle('active');
            const heartIcon = this.querySelector('i');
            heartIcon.classList.toggle('far');
            heartIcon.classList.toggle('fas');
        });
    });

    // 5. Fade-in Scroll Animations
    const fadeInSections = document.querySelectorAll('.fade-in-section');
    const options = {
        root: null,
        rootMargin: '0px',
        threshold: 0.15
    };

    const observer = new IntersectionObserver(function(entries, observer) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, options);

    fadeInSections.forEach(section => {
        observer.observe(section);
    });
    
    // 6. Scroll to Top Button
    const scrollToTopBtn = document.getElementById('scrollToTopBtn');
    
    if (scrollToTopBtn) {
        window.onscroll = function() {
            // We can reuse the main scroll event listener
            if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
                scrollToTopBtn.style.display = "block";
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
    }

});


