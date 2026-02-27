// Header scroll behavior
document.addEventListener('DOMContentLoaded', function() {
    let lastScrollTop = 0;
    const header = document.getElementById('siteHeader');

    if (header) {
        window.addEventListener('scroll', function() {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            // Check scroll direction
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                // Scrolling DOWN - hide header
                header.style.transform = 'translateY(-100%)';
            } else {
                // Scrolling UP or near top - show header
                header.style.transform = 'translateY(0)';
            }

            // Change color based on scroll position
            if (scrollTop > 100) {
                // Darker gradient when scrolled down
                header.style.background = 'linear-gradient(135deg, rgba(13, 74, 13, 0.95) 0%, rgba(5, 59, 5, 0.95) 100%)';
                header.style.boxShadow = '0 6px 20px rgba(0,0,0,0.4)';
            } else {
                // Original color at top
                header.style.background = 'var(--primary, #137137)';
                header.style.boxShadow = '0 4px 10px rgba(0,0,0,0.2)';
            }

            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
        });
    }
});
