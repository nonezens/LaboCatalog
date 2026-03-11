/* ========================================
   HEADER JS - Shared navigation functionality
   ======================================== */
document.addEventListener("DOMContentLoaded", function() {
    const hamburger = document.getElementById("hamburger-menu");
    const navLinks = document.getElementById("nav-links");

    // Hamburger menu toggle
    if (hamburger) {
        hamburger.addEventListener("click", function() {
            navLinks.classList.toggle("active");
            hamburger.classList.toggle("open");
        });
    }

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

