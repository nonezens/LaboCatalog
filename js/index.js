/* ========================================
   INDEX PAGE JS
   ======================================== */
document.addEventListener('DOMContentLoaded', function() {
<<<<<<< Updated upstream
    // Set hash
    history.replaceState(null, null, '#home');
=======


    // ========================================
    // TAB NAVIGATION
    // ========================================
    function switchTab(tabName, pushState = true) {
        if (!tabName) {
            tabName = 'home'; // Default to home
        }

        const header = document.querySelector('.site-header');
        if (header) {
            if (tabName === 'home') {
                header.classList.add('header-large');
            } else {
                header.classList.remove('header-large');
            }
        }

        // Get all buttons and contents
        const allNavLinks = document.querySelectorAll('[data-tab]');
        const allContents = document.querySelectorAll('.tab-content');

        // Deactivate all buttons/links and hide all content
        allNavLinks.forEach(link => link.classList.remove('active', 'active-page'));
        allContents.forEach(content => {
            content.classList.remove('active');
            // No need for inline style changes, CSS will handle it.
        });

        // Activate the selected button/link
        const selectedLinks = document.querySelectorAll(`[data-tab="${tabName}"]`);
        selectedLinks.forEach(link => {
            link.classList.add('active');
            // For header links, use 'active-page' style
            if (link.classList.contains('nav-link')) {
                link.classList.add('active-page');
            }
        });
        
        // Show selected content
        const selectedContent = document.getElementById(tabName);
        if (selectedContent) {
            selectedContent.classList.add('active');
        }

        // Update URL hash without jumping
        if (pushState) {
            history.pushState(null, '', `#${tabName}`);
        }
    }

    // Add click handlers to ALL navigation elements with data-tab (top tabs + sidebar)
    document.querySelectorAll('[data-tab]').forEach(elem => {
        elem.addEventListener('click', function(e) {
            e.preventDefault(); 
            const tabName = this.getAttribute('data-tab');
            switchTab(tabName);
        });
    });

    // Handle initial page load based on hash
    const currentHash = window.location.hash.substring(1);
    if (currentHash && document.getElementById(currentHash)) {
        switchTab(currentHash, false);
    } else {
        switchTab('home', false); // Default to home
    }
>>>>>>> Stashed changes
    
    // Animate sections on scroll
    const aboutTitle = document.getElementById('aboutTitle');
    const aboutText = document.getElementById('aboutText');
    const recentTitle = document.getElementById('recentTitle');
    const cards = document.querySelectorAll('.card');
    
    const observerOptions = { root: null, rootMargin: '0px', threshold: 0.2 };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                if (entry.target.id === 'aboutTitle') {
                    entry.target.classList.add('visible');
                } else if (entry.target.id === 'aboutText') {
                    entry.target.classList.add('visible');
                } else if (entry.target.id === 'recentTitle') {
                    entry.target.classList.add('visible');
                }
            }
        });
    }, observerOptions);
    
    if (aboutTitle) observer.observe(aboutTitle);
    if (aboutText) observer.observe(aboutText);
    if (recentTitle) observer.observe(recentTitle);
    
    // Animate cards when visible
    const cardObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                cardObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    
    cards.forEach(card => cardObserver.observe(card));
});

