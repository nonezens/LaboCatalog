/* ========================================
   INDEX PAGE JS
   ======================================== */
document.addEventListener('DOMContentLoaded', function() {
    // Set hash
    history.replaceState(null, null, '#home');
    
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

