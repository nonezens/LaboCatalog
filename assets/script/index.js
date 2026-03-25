 // 1. NEWS CAROUSEL LOGIC
        let newsIndex = 0;
        const newsSlides = document.querySelectorAll('.news-slide');
        const dots = document.querySelectorAll('.dot');
        let newsTimer;

        function showNews(index) {
            if (newsSlides.length === 0) return;
            if (index >= newsSlides.length) { newsIndex = 0; }
            else if (index < 0) { newsIndex = newsSlides.length - 1; }
            else { newsIndex = index; }

            newsSlides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));

            newsSlides[newsIndex].classList.add('active');
            if(dots.length > 0) dots[newsIndex].classList.add('active');
        }

        function currentNewsSlide(index) {
            clearInterval(newsTimer); 
            showNews(index);
            startNewsTimer(); 
        }

        function startNewsTimer() {
            if (newsSlides.length > 1) {
                newsTimer = setInterval(() => { showNews(newsIndex + 1); }, 5000); 
            }
        }
        showNews(newsIndex);
        startNewsTimer();

        // 2. MULTI-ITEM GALLERY SLIDER LOGIC
        let currentGalleryIndex = 0;

        function moveGallery(direction) {
            const track = document.getElementById('galleryTrack');
            const cards = track.querySelectorAll('.gallery-card');
            if (cards.length === 0) return;

            let visibleItems = 3; 
            if (window.innerWidth <= 768) { visibleItems = 1; } 
            else if (window.innerWidth <= 992) { visibleItems = 2; } 

            const maxIndex = cards.length - visibleItems;

            currentGalleryIndex += direction;
            
            if (currentGalleryIndex > maxIndex) { currentGalleryIndex = 0; }
            if (currentGalleryIndex < 0) { currentGalleryIndex = maxIndex; }

            const cardWidth = cards[0].getBoundingClientRect().width;
            const gap = 20; 
            const moveAmount = currentGalleryIndex * (cardWidth + gap);

            track.style.transform = `translateX(-${moveAmount}px)`;
        }

        window.addEventListener('resize', () => {
            currentGalleryIndex = 0;
            const track = document.getElementById('galleryTrack');
            if(track) track.style.transform = `translateX(0px)`;
        });