// ========== NEWS CAROUSEL ==========
let currentNewsIndex = 0;
let newsAutoPlayInterval;

function showNewsSlide(index) {
    const slides = document.querySelectorAll('.news-slide');
    const dots = document.querySelectorAll('.dot');
    
    if (slides.length === 0) return;
    
    // Wrap around
    if (index >= slides.length) currentNewsIndex = 0;
    else if (index < 0) currentNewsIndex = slides.length - 1;
    else currentNewsIndex = index;
    
    // Remove active class from all
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    // Add active class to current
    slides[currentNewsIndex].classList.add('active');
    if (dots[currentNewsIndex]) dots[currentNewsIndex].classList.add('active');
}

function nextNewsSlide() {
    clearInterval(newsAutoPlayInterval);
    showNewsSlide(currentNewsIndex + 1);
    startNewsAutoPlay();
}

function prevNewsSlide() {
    clearInterval(newsAutoPlayInterval);
    showNewsSlide(currentNewsIndex - 1);
    startNewsAutoPlay();
}

function goToNewsSlide(index) {
    clearInterval(newsAutoPlayInterval);
    showNewsSlide(index);
    startNewsAutoPlay();
}

function startNewsAutoPlay() {
    const slides = document.querySelectorAll('.news-slide');
    if (slides.length <= 1) return;
    
    newsAutoPlayInterval = setInterval(() => {
        currentNewsIndex++;
        if (currentNewsIndex >= slides.length) currentNewsIndex = 0;
        showNewsSlide(currentNewsIndex);
    }, 6000); // Change slide every 6 seconds
}

// Initialize news carousel
document.addEventListener('DOMContentLoaded', function() {
    // Setup news carousel buttons
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    const dots = document.querySelectorAll('.dot');
    
    if (prevBtn) prevBtn.addEventListener('click', prevNewsSlide);
    if (nextBtn) nextBtn.addEventListener('click', nextNewsSlide);
    
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => goToNewsSlide(index));
    });
    
    // Start autoplay
    startNewsAutoPlay();
    
    // Stop autoplay on hover
    const carousel = document.querySelector('.news-carousel');
    if (carousel) {
        carousel.addEventListener('mouseenter', () => clearInterval(newsAutoPlayInterval));
        carousel.addEventListener('mouseleave', () => startNewsAutoPlay());
    }
    
    // ========== GALLERY SLIDER ==========
    const galleryTrack = document.getElementById('galleryTrack');
    const prevNavBtn = document.querySelector('.prev-nav');
    const nextNavBtn = document.querySelector('.next-nav');
    
    if (galleryTrack && prevNavBtn && nextNavBtn) {
        let currentGalleryIndex = 0;
        
        function updateGalleryPosition() {
            const items = galleryTrack.querySelectorAll('.gallery-item');
            if (items.length === 0) return;
            
            // Determine how many items to show based on viewport
            let itemsPerView = 3;
            if (window.innerWidth <= 1024) itemsPerView = 2;
            if (window.innerWidth <= 768) itemsPerView = 1;
            
            // Calculate maximum index
            const maxIndex = Math.max(0, items.length - itemsPerView);
            
            // Clamp current index
            if (currentGalleryIndex > maxIndex) currentGalleryIndex = maxIndex;
            if (currentGalleryIndex < 0) currentGalleryIndex = 0;
            
            // Get the first item's width and gap
            const firstItem = items[0];
            const itemWidth = firstItem.getBoundingClientRect().width;
            const gap = 20; // matches CSS gap
            
            // Calculate translation
            const translateAmount = currentGalleryIndex * (itemWidth + gap);
            galleryTrack.style.transform = `translateX(-${translateAmount}px)`;
        }
        
        function moveGallery(direction) {
            const items = galleryTrack.querySelectorAll('.gallery-item');
            let itemsPerView = 3;
            if (window.innerWidth <= 1024) itemsPerView = 2;
            if (window.innerWidth <= 768) itemsPerView = 1;
            
            const maxIndex = Math.max(0, items.length - itemsPerView);
            
            currentGalleryIndex += direction;
            
            // Loop around
            if (currentGalleryIndex > maxIndex) currentGalleryIndex = 0;
            if (currentGalleryIndex < 0) currentGalleryIndex = maxIndex;
            
            updateGalleryPosition();
        }
        
        prevNavBtn.addEventListener('click', () => moveGallery(-1));
        nextNavBtn.addEventListener('click', () => moveGallery(1));
        
        // Reset on window resize
        window.addEventListener('resize', () => {
            currentGalleryIndex = 0;
            updateGalleryPosition();
        });
        
        // Initial position
        updateGalleryPosition();
    }
});
