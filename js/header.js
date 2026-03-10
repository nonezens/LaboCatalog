/* ========================================
   HEADER JS - Shared navigation functionality
   ======================================== */
document.addEventListener("DOMContentLoaded", function() {
    const hamburger = document.getElementById("hamburger-menu");
    const navLinks = document.getElementById("nav-links");
    const morphOverlay = document.getElementById("morphOverlay");

    // Hamburger menu toggle
    if (hamburger) {
        hamburger.addEventListener("click", function() {
            navLinks.classList.toggle("active");
            hamburger.classList.toggle("open");
        });
    }

    // Morphing Page Transition
    const navLinksAll = document.querySelectorAll('.nav-link, .site-logo a');
    
    navLinksAll.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href && !href.startsWith('#') && !href.startsWith('javascript')) {
                e.preventDefault();
                
                // Get click position for morph origin
                const rect = this.getBoundingClientRect();
                const x = rect.left + rect.width / 2;
                const y = rect.top + rect.height / 2;
                
                // Set transform origin to click position
                const xPercent = (x / window.innerWidth) * 100;
                const yPercent = (y / window.innerHeight) * 100;
                morphOverlay.style.transformOrigin = xPercent + '% ' + yPercent + '%';
                
                // Trigger morph in
                morphOverlay.classList.add('active');
                
                setTimeout(() => {
                    window.location.href = href;
                }, 400);
            }
        });
    });

    // Hash URL handling
    if (window.location.hash) {
        const hash = window.location.hash.substring(1);
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active-page');
            if (link.getAttribute('href') && link.getAttribute('href').includes(hash + '.php')) {
                link.classList.add('active-page');
            }
        });
    }

    // Update active state on nav click
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function() {
            document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active-page'));
            this.classList.add('active-page');
        });
    });

    // Handle browser back/forward
    window.addEventListener('popstate', function() {
        const hash = window.location.hash.substring(1);
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active-page');
            if (link.getAttribute('href') && link.getAttribute('href').includes(hash + '.php')) {
                link.classList.add('active-page');
            }
        });
    });
});

