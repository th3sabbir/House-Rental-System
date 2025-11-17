// Load Header and Footer
$(document).ready(function(){
    // Load Header
    $("#header-placeholder").load("header.php", function(response, status, xhr) {
        if (status == "error") {
            console.log("Error loading header: " + xhr.status + " " + xhr.statusText);
        } else {
            // Set active navigation link based on current page
            setActiveNavLink();
            
            // Re-initialize header scroll after loading
            initializeHeader();
            // Re-initialize mobile menu toggle (for AJAX-loaded header)
            // If the central script hasn't loaded yet, poll until it becomes available
            (function trySetupMobile(retries = 40, delay = 50) {
                if (typeof window.setupMobileMenu === 'function') {
                    try {
                        window.setupMobileMenu();
                    } catch (e) {
                        console.warn('setupMobileMenu failed:', e);
                    }
                } else if (retries > 0) {
                    setTimeout(() => trySetupMobile(retries - 1, delay), delay);
                } else {
                    // last effort: dispatch an event that script.js may pick up
                    document.dispatchEvent(new Event('amarthikanaHeaderLoaded'));
                }
            })();
        }
    });
    
    // Load Footer
    $("#footer-placeholder").load("footer.php", function(response, status, xhr) {
        if (status == "error") {
            console.log("Error loading footer: " + xhr.status + " " + xhr.statusText);
        }
    });
});

// Set active navigation link based on current page
function setActiveNavLink() {
    const currentPage = window.location.pathname.split("/").pop() || 'index.php';
    
    // Remove active class from all links
    $('.nav-links a').removeClass('active');
    
    // Add active class to current page link
    if (currentPage === "" || currentPage === "index.php") {
        $("#nav-rent").addClass("active");
    } else if (currentPage === "properties.php") {
        $("#nav-properties").addClass("active");
    } else if (currentPage === "about-us.php") {
        $("#nav-about").addClass("active");
    }
}

// Initialize header functionality after it's loaded
function initializeHeader() {
    // Dynamic Header on Scroll
    window.addEventListener('scroll', function() {
        const header = document.querySelector('.main-header');
        if (header) {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        }
    });
    
    // The mobile menu logic is now handled directly in header.php
    // No need to duplicate it here
}


