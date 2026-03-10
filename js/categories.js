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
            card.style.display = 'block'; 
            hasVisibleCards = true;
        } else {
            card.style.display = 'none'; 
        }
    });

    let noResultsMsg = document.getElementById('noResultsMessage');
    if (noResultsMsg) {
        if (hasVisibleCards) {
            noResultsMsg.style.display = 'none';
        } else {
            noResultsMsg.style.display = 'block';
        }
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

