/* ========================================
   CATEGORIES PAGE JS
   ======================================== */
function liveFilter() {
    let query = document.getElementById('searchInput').value.toLowerCase();
    let cards = document.querySelectorAll('.cat-card');
    let hasVisibleCards = false;

    cards.forEach(card => {
        let searchableText = card.getAttribute('data-search');

        if (searchableText && searchableText.includes(query)) {
            // Add fade in animation
            card.style.opacity = '0';
            card.style.transform = 'scale(0.95)';
            card.style.display = 'block';
            
            setTimeout(() => {
                card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                card.style.opacity = '1';
                card.style.transform = 'scale(1)';
            }, 50);
            
            hasVisibleCards = true;
        } else {
            // Fade out animation
            card.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.95)';
            
            setTimeout(() => {
                card.style.display = 'none';
                card.style.opacity = '1';
                card.style.transform = 'scale(1)';
            }, 200);
        }
    });

    let noResultsMsg = document.getElementById('noResultsMessage');
    if (noResultsMsg) {
        noResultsMsg.style.opacity = '0';
        setTimeout(() => {
            noResultsMsg.style.display = hasVisibleCards ? 'none' : 'block';
            noResultsMsg.style.transition = 'opacity 0.3s ease';
            noResultsMsg.style.opacity = '1';
        }, 100);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault(); 
        });
    }
});

