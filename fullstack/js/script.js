document.addEventListener('DOMContentLoaded', function () {

    // 1. Dynamic Header on Scroll
    const header = document.querySelector('.main-header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // 2. Mobile Menu Toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    const navActions = document.querySelector('.nav-actions');

    menuToggle.addEventListener('click', () => {
        navLinks.classList.toggle('active');
        navActions.classList.toggle('active');
        
        const icon = menuToggle.querySelector('i');
        if (navLinks.classList.contains('active')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    });

    // 3. Swiper.js Initialization for Testimonials
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

});