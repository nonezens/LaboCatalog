document.addEventListener('DOMContentLoaded', () => {
    let currentGalleryIndex = 0;

    const moveGallery = (direction) => {
        const track = document.getElementById('galleryTrack');
        if (!track) {
            return;
        }

        const cards = track.querySelectorAll('.gallery-card');
        if (cards.length === 0) {
            return;
        }

        let visibleItems = 3;
        if (window.innerWidth <= 768) {
            visibleItems = 1;
        } else if (window.innerWidth <= 992) {
            visibleItems = 2;
        }

        const maxIndex = Math.max(0, cards.length - visibleItems);
        currentGalleryIndex += direction;

        if (currentGalleryIndex > maxIndex) {
            currentGalleryIndex = 0;
        }
        if (currentGalleryIndex < 0) {
            currentGalleryIndex = maxIndex;
        }

        const cardWidth = cards[0].getBoundingClientRect().width;
        const gap = 20;
        const moveAmount = currentGalleryIndex * (cardWidth + gap);

        track.style.transform = `translateX(-${moveAmount}px)`;
    };

    document.querySelectorAll('[data-gallery-dir]').forEach((button) => {
        button.addEventListener('click', () => {
            const direction = Number(button.dataset.galleryDir || 0);
            moveGallery(direction);
        });
    });

    window.addEventListener('resize', () => {
        currentGalleryIndex = 0;
        const track = document.getElementById('galleryTrack');
        if (track) {
            track.style.transform = 'translateX(0px)';
        }
    });
});
