/**
 * Mobile-Specific JavaScript for AmarThikana
 * Handles touch interactions, gestures, and mobile-optimized features
 */

(function() {
    'use strict';

    // Detect if device is mobile
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    const isTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

    if (!isMobile && !isTouch) {
        return; // Exit if not a mobile device
    }

    // Add mobile class to body
    document.body.classList.add('mobile-device');

    /**
     * Enhanced Mobile Navigation
     */
    class MobileNav {
        constructor() {
            this.menuToggle = document.querySelector('.menu-toggle');
            this.navLinks = document.querySelector('.nav-links');
            this.navActions = document.querySelector('.nav-actions');
            this.body = document.body;
            this.overlay = null;
            
            this.init();
        }

        init() {
            // If central mobile toggler exists, skip local toggling to avoid duplicate handlers
            if (window.hasCentralMobileToggle) {
                console.debug('MobileNav: central toggle present, skipping MobileNav init');
                return;
            }
            if (!this.menuToggle) return;

            // Create overlay
            this.createOverlay();
            
            // Menu toggle handler
            this.menuToggle.addEventListener('click', () => this.toggleMenu());
            
            // Close menu when clicking on a link
            if (this.navLinks) {
                const links = this.navLinks.querySelectorAll('a');
                links.forEach(link => {
                    link.addEventListener('click', () => this.closeMenu());
                });
            }
            
            // Close menu when clicking overlay
            if (this.overlay) {
                this.overlay.addEventListener('click', () => this.closeMenu());
            }
            
            // Prevent body scroll when menu is open
            this.preventBodyScroll();
        }

        createOverlay() {
            this.overlay = document.createElement('div');
            this.overlay.className = 'mobile-nav-overlay';
            document.body.appendChild(this.overlay);
        }

        toggleMenu() {
            const isOpen = this.navLinks?.classList.contains('active');
            
            if (isOpen) {
                this.closeMenu();
            } else {
                this.openMenu();
            }
        }

        openMenu() {
            this.navLinks?.classList.add('active');
            this.navActions?.classList.add('active');
            this.overlay?.classList.add('active');
            this.body.classList.add('menu-open');
            
            const icon = this.menuToggle.querySelector('i');
            icon?.classList.remove('fa-bars');
            icon?.classList.add('fa-times');
        }

        closeMenu() {
            this.navLinks?.classList.remove('active');
            this.navActions?.classList.remove('active');
            this.overlay?.classList.remove('active');
            this.body.classList.remove('menu-open');
            
            const icon = this.menuToggle.querySelector('i');
            icon?.classList.remove('fa-times');
            icon?.classList.add('fa-bars');
        }

        preventBodyScroll() {
            let scrollY = 0;
            
            // Store scroll position when menu opens
            this.body.addEventListener('menu-open', () => {
                scrollY = window.scrollY;
                this.body.style.position = 'fixed';
                this.body.style.top = `-${scrollY}px`;
                this.body.style.width = '100%';
            });
            
            // Restore scroll position when menu closes
            this.body.addEventListener('menu-close', () => {
                this.body.style.position = '';
                this.body.style.top = '';
                this.body.style.width = '';
                window.scrollTo(0, scrollY);
            });
        }
    }

    /**
     * Touch Swipe Handler for Image Galleries
     */
    class SwipeHandler {
        constructor(element, options = {}) {
            this.element = element;
            this.options = {
                threshold: 50, // minimum distance for swipe
                restraint: 100, // maximum distance perpendicular to swipe
                allowedTime: 300, // maximum time for swipe
                ...options
            };
            
            this.startX = 0;
            this.startY = 0;
            this.distX = 0;
            this.distY = 0;
            this.startTime = 0;
            
            this.init();
        }

        init() {
            this.element.addEventListener('touchstart', (e) => this.handleTouchStart(e), { passive: true });
            this.element.addEventListener('touchmove', (e) => this.handleTouchMove(e), { passive: true });
            this.element.addEventListener('touchend', (e) => this.handleTouchEnd(e), { passive: true });
        }

        handleTouchStart(e) {
            const touch = e.touches[0];
            this.startX = touch.pageX;
            this.startY = touch.pageY;
            this.startTime = new Date().getTime();
        }

        handleTouchMove(e) {
            // Prevent default if moving horizontally
            if (Math.abs(this.distX) > Math.abs(this.distY)) {
                e.preventDefault();
            }
        }

        handleTouchEnd(e) {
            const touch = e.changedTouches[0];
            this.distX = touch.pageX - this.startX;
            this.distY = touch.pageY - this.startY;
            const elapsedTime = new Date().getTime() - this.startTime;

            if (elapsedTime <= this.options.allowedTime) {
                if (Math.abs(this.distX) >= this.options.threshold && Math.abs(this.distY) <= this.options.restraint) {
                    const direction = this.distX < 0 ? 'left' : 'right';
                    this.onSwipe(direction);
                }
            }
        }

        onSwipe(direction) {
            // Dispatch custom event
            const event = new CustomEvent('swipe', { detail: { direction } });
            this.element.dispatchEvent(event);
        }
    }

    /**
     * Lazy Loading Images for Mobile
     */
    class LazyLoader {
        constructor() {
            this.images = document.querySelectorAll('img[data-src]');
            this.init();
        }

        init() {
            if ('IntersectionObserver' in window) {
                this.observeImages();
            } else {
                this.loadAllImages();
            }
        }

        observeImages() {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.loadImage(entry.target);
                        imageObserver.unobserve(entry.target);
                    }
                });
            }, {
                rootMargin: '50px'
            });

            this.images.forEach(img => imageObserver.observe(img));
        }

        loadImage(img) {
            const src = img.getAttribute('data-src');
            if (!src) return;
            
            img.src = src;
            img.removeAttribute('data-src');
            img.classList.add('loaded');
        }

        loadAllImages() {
            this.images.forEach(img => this.loadImage(img));
        }
    }

    /**
     * Smooth Scroll with Momentum
     */
    class SmoothScroll {
        constructor() {
            this.init();
        }

        init() {
            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', (e) => {
                    const href = anchor.getAttribute('href');
                    if (href === '#' || href === '#top') {
                        e.preventDefault();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        return;
                    }

                    const target = document.querySelector(href);
                    if (target) {
                        e.preventDefault();
                        const headerOffset = 80;
                        const elementPosition = target.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        }
    }

    /**
     * Touch-Friendly Property Card Interactions
     */
    class PropertyCardTouch {
        constructor() {
            this.cards = document.querySelectorAll('.property-card');
            this.init();
        }

        init() {
            this.cards.forEach(card => {
                // Add touch feedback
                card.addEventListener('touchstart', () => {
                    card.classList.add('touching');
                }, { passive: true });

                card.addEventListener('touchend', () => {
                    setTimeout(() => {
                        card.classList.remove('touching');
                    }, 150);
                }, { passive: true });

                // Handle favorite icon on cards
                const favoriteIcon = card.querySelector('.favorite-icon');
                if (favoriteIcon) {
                    favoriteIcon.addEventListener('touchend', (e) => {
                        e.stopPropagation();
                        e.preventDefault();
                        this.toggleFavorite(favoriteIcon, card);
                    });
                }
            });
        }

        toggleFavorite(icon, card) {
            // Add haptic feedback if available
            if (navigator.vibrate) {
                navigator.vibrate(50);
            }

            const propertyId = card.getAttribute('data-property-id');
            icon.classList.toggle('active');
            
            // You can add AJAX call here to save favorite
            console.log('Toggle favorite for property:', propertyId);
        }
    }

    /**
     * Viewport Height Fix for Mobile Browsers
     */
    function fixViewportHeight() {
        const setVH = () => {
            const vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
        };

        setVH();
        window.addEventListener('resize', setVH);
        window.addEventListener('orientationchange', setVH);
    }

    /**
     * Debounce Function
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * Handle Screen Orientation Changes
     */
    function handleOrientation() {
        const orientation = window.screen.orientation?.type || 
                          (window.innerWidth > window.innerHeight ? 'landscape' : 'portrait');
        
        document.body.setAttribute('data-orientation', orientation);
        
        // Reload certain components on orientation change
        window.addEventListener('orientationchange', debounce(() => {
            location.reload(); // Optional: remove if not needed
        }, 500));
    }

    /**
     * Prevent Double-Tap Zoom on Buttons
     */
    function preventDoubleTapZoom() {
        const elements = document.querySelectorAll('button, .btn, a, input[type="submit"]');
        
        elements.forEach(element => {
            let lastTap = 0;
            element.addEventListener('touchend', (e) => {
                const currentTime = new Date().getTime();
                const tapLength = currentTime - lastTap;
                
                if (tapLength < 300 && tapLength > 0) {
                    e.preventDefault();
                }
                
                lastTap = currentTime;
            }, { passive: false });
        });
    }

    /**
     * Image Gallery Swipe Navigation
     */
    function initGallerySwipe() {
        const galleries = document.querySelectorAll('.property-gallery, .image-gallery');
        
        galleries.forEach(gallery => {
            const swipeHandler = new SwipeHandler(gallery);
            
            gallery.addEventListener('swipe', (e) => {
                const direction = e.detail.direction;
                const prevBtn = gallery.querySelector('.gallery-nav.prev');
                const nextBtn = gallery.querySelector('.gallery-nav.next');
                
                if (direction === 'left' && nextBtn) {
                    nextBtn.click();
                } else if (direction === 'right' && prevBtn) {
                    prevBtn.click();
                }
            });
        });
    }

    /**
     * Form Input Enhancement
     */
    function enhanceFormInputs() {
        // Auto-focus prevention on mobile
        const inputs = document.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            // Remove autofocus on mobile
            input.removeAttribute('autofocus');
            
            // Add clear button to text inputs
            if (input.type === 'text' || input.type === 'email' || input.type === 'tel') {
                input.addEventListener('input', () => {
                    if (input.value.length > 0) {
                        input.classList.add('has-value');
                    } else {
                        input.classList.remove('has-value');
                    }
                });
            }
        });
    }

    /**
     * Toast Notification for Mobile
     */
    class MobileToast {
        static show(message, type = 'info', duration = 3000) {
            const toast = document.createElement('div');
            toast.className = `toast-notification toast-${type}`;
            toast.textContent = message;
            
            document.body.appendChild(toast);
            
            // Trigger animation
            setTimeout(() => toast.classList.add('show'), 10);
            
            // Auto-hide
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }
    }

    // Make toast available globally
    window.MobileToast = MobileToast;

    /**
     * Initialize All Mobile Features
     */
    function init() {
        console.log('Initializing mobile features...');
        
        // Initialize core features
        new MobileNav();
        new LazyLoader();
        new SmoothScroll();
        new PropertyCardTouch();
        
        // Initialize utility features
        fixViewportHeight();
        handleOrientation();
        preventDoubleTapZoom();
        initGallerySwipe();
        enhanceFormInputs();
        
        // Add performance optimization
        if ('requestIdleCallback' in window) {
            requestIdleCallback(() => {
                console.log('Mobile optimizations loaded');
            });
        }
    }

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose utilities globally if needed
    window.SwipeHandler = SwipeHandler;
    window.LazyLoader = LazyLoader;

})();
