/* ========================================
   EXHIBIT DETAIL PAGE JS
   ======================================== */
document.addEventListener('DOMContentLoaded', function() {
    // Trigger animations on page load with staggered delays
    const imageContainer = document.getElementById('imageContainer');
    const infoContainer = document.getElementById('infoContainer');
    const titleElement = document.getElementById('titleElement');
    const badgeElement = document.getElementById('badgeElement');
    const metaElement = document.getElementById('metaElement');
    const descElement = document.getElementById('descElement');
    const backLink = document.getElementById('backLink');

    if (imageContainer) {
        setTimeout(function() {
            imageContainer.classList.add('visible');
        }, 100);
    }
    
    if (infoContainer) {
        setTimeout(function() {
            infoContainer.classList.add('visible');
        }, 200);
    }
    
    if (titleElement) {
        setTimeout(function() {
            titleElement.classList.add('visible');
        }, 300);
    }
    
    if (badgeElement) {
        setTimeout(function() {
            badgeElement.classList.add('visible');
        }, 400);
    }
    
    if (metaElement) {
        setTimeout(function() {
            metaElement.classList.add('visible');
        }, 500);
    }
    
    if (descElement) {
        setTimeout(function() {
            descElement.classList.add('visible');
        }, 600);
    }
    
    if (backLink) {
        setTimeout(function() {
            backLink.classList.add('visible');
        }, 700);
    }

    // Modal functionality
    const modal = document.getElementById("imageModal");
    const img = document.getElementById("exhibitImage");
    const modalImg = document.getElementById("modalImage");
    const closeBtn = document.getElementById("modalClose");

    if (img && modal) {
        img.onclick = function() {
            modal.classList.add('active');
            if (modalImg) modalImg.src = this.src;
        }
    }

    if (closeBtn && modal) {
        closeBtn.onclick = function() {
            modal.classList.remove('active');
        }
    }

    if (modal) {
        modal.onclick = function(event) {
            if (event.target === modal) {
                modal.classList.remove('active');
            }
        }
    }

    // Escape key to close modal
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById("imageModal");
        if (e.key === 'Escape' && modal) {
            modal.classList.remove('active');
        }
    });
});

