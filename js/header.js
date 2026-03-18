document.addEventListener("DOMContentLoaded", function() {
    const hamburger = document.getElementById("hamburger-menu");
    const navLinks = document.getElementById("nav-links");
    const navItems = document.querySelectorAll('.nav-link');

    // Hamburger menu toggle
    hamburger.addEventListener("click", function() {
        navLinks.classList.toggle("active");
        hamburger.classList.toggle("open");
    });

    // Close menu when a link is clicked
    navItems.forEach(link => {
        link.addEventListener('click', function() {
            navLinks.classList.remove("active");
            hamburger.classList.remove("open");
            
            // Update active link styling
            updateActiveLink();
        });
    });

    // Update active link based on current hash
    function updateActiveLink() {
        const currentHash = window.location.hash.slice(1) || 'home';
        
        navItems.forEach(link => {
            link.classList.remove('active-page');
            if (link.getAttribute('data-section') === currentHash) {
                link.classList.add('active-page');
            }
        });
    }

    // Listen for hash changes
    window.addEventListener('hashchange', updateActiveLink);
    
    // Initial update
    updateActiveLink();
});

