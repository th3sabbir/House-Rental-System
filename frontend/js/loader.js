// Load Header and Footer
$(document).ready(function(){
    // Load Header
    $("#header-placeholder").load("header.html", function(response, status, xhr) {
        if (status == "error") {
            console.log("Error loading header: " + xhr.status + " " + xhr.statusText);
        } else {
            // Set active navigation link based on current page
            setActiveNavLink();
            
            // Re-initialize header scroll and mobile menu after loading
            initializeHeader();
        }
    });
    
    // Load Footer
    $("#footer-placeholder").load("footer.html", function(response, status, xhr) {
        if (status == "error") {
            console.log("Error loading footer: " + xhr.status + " " + xhr.statusText);
        }
    });
});

// Set active navigation link based on current page
function setActiveNavLink() {
    const currentPage = window.location.pathname.split("/").pop() || 'index.html';
    
    // Remove active class from all links
    $('.nav-links a').removeClass('active');
    
    // Add active class to current page link
    if (currentPage === "" || currentPage === "index.html") {
        $("#nav-rent").addClass("active");
    } else if (currentPage === "properties.html") {
        $("#nav-properties").addClass("active");
    } else if (currentPage === "about-us.html") {
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
    
    // Mobile Menu Toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    const navActions = document.querySelector('.nav-actions');

    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
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
    }
}