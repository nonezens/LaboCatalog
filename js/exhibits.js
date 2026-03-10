/* ========================================
   EXHIBITS PAGE JS
   ======================================== */
document.addEventListener('DOMContentLoaded', function() {
    // Title animation
    const pageTitle = document.getElementById('pageTitle');
    const searchForm = document.getElementById('searchForm');
    
    if (pageTitle) {
        setTimeout(() => pageTitle.classList.add('visible'), 100);
    }
    if (searchForm) {
        setTimeout(() => searchForm.classList.add('visible'), 200);
    }
    
    // Set hash
    history.replaceState(null, null, '#artifacts');
    
    // Card scroll animations
    const cardLinks = document.querySelectorAll('.card-link');
    const observerOptions = { root: null, rootMargin: '0px', threshold: 0.1 };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    cardLinks.forEach(link => observer.observe(link));
});

// Live filter function
function liveFilter() {
    let query = document.getElementById('searchInput').value.toLowerCase();
    let cardLinks = document.querySelectorAll('.card-link');
    let hasVisibleCards = false;

    cardLinks.forEach(link => {
        let searchableText = link.getAttribute('data-search');
        if (searchableText && searchableText.includes(query)) {
            link.style.display = 'flex';
            link.classList.remove('visible');
            void link.offsetWidth;
            link.classList.add('visible');
            hasVisibleCards = true;
        } else {
            link.style.display = 'none';
        }
    });

    let noResultsMsg = document.getElementById('noResultsMessage');
    if (noResultsMsg) {
        noResultsMsg.style.display = hasVisibleCards ? 'none' : 'block';
    }
}

// Prevent form submission (for AJAX-style filtering)
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault(); 
        });
    }
});

