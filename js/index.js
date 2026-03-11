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
    
    // ========================================
    // NEWS CAROUSEL CONTROLS - Auto advance every 5 seconds
    // ========================================
    const newsTrack = document.getElementById('newsCarouselTrack');
    let newsCurrentIndex = 0;
    let newsInterval;
    const newsCardWidth = 300; // card width + gap
    
    if (newsTrack) {
        const newsCards = newsTrack.querySelectorAll('.carousel-card');
        const newsCardCount = newsCards.length;
        
        // Add "is-active" class to all cards initially (hover effect)
        newsCards.forEach(card => card.classList.add('is-active'));
        
        // Use event delegation for better performance - hover effect on news cards
        newsTrack.addEventListener('mouseenter', function(event) {
            const card = event.target.closest('.carousel-card');
            if (card) {
                newsCards.forEach(c => c.classList.remove('is-active'));
                card.classList.add('is-active');
            }
        }, true);
        
        newsTrack.addEventListener('mouseleave', function(event) {
            const card = event.target.closest('.carousel-card');
            if (card) {
                newsCards.forEach(c => c.classList.add('is-active'));
            }
        }, true);
        
        // Function to move carousel to specific card
        function updateNewsCarousel() {
            newsTrack.style.transition = 'transform 0.5s ease-in-out';
            newsTrack.style.transform = `translateX(-${newsCurrentIndex * newsCardWidth}px)`;
        }
        
        // Auto-advance function
        function autoAdvanceNews() {
            newsCurrentIndex++;
            if (newsCurrentIndex >= newsCardCount) {
                newsCurrentIndex = 0;
            }
            updateNewsCarousel();
        }
        
        // Start auto-advance every 5 seconds
        if (newsCardCount > 1) {
            newsInterval = setInterval(autoAdvanceNews, 5000);
            
            // Pause on hover
            newsTrack.addEventListener('mouseenter', () => {
                clearInterval(newsInterval);
            });
            
            // Resume on mouse leave
            newsTrack.addEventListener('mouseleave', () => {
                newsInterval = setInterval(autoAdvanceNews, 5000);
            });
        }
        
        window.moveNewsCarousel = function(direction) {
            newsCurrentIndex += direction;
            
            // Handle wrapping
            if (newsCurrentIndex < 0) {
                newsCurrentIndex = newsCardCount - 1;
            } else if (newsCurrentIndex >= newsCardCount) {
                newsCurrentIndex = 0;
            }
            
            updateNewsCarousel();
            
            // Reset the interval after manual navigation
            if (newsInterval) {
                clearInterval(newsInterval);
                newsInterval = setInterval(autoAdvanceNews, 5000);
            }
        };
    }
    
    // ========================================
    // ACQUISITIONS - Simple Grid Layout (no JS needed)
    // Cards are displayed in a responsive grid
    // ========================================
    
    // ========================================
    // 3D CAROUSEL FOR LATEST ACQUISITIONS
    // ========================================
    (function() {
        "use strict";

        var carousel = document.getElementsByClassName('carousel')[0];
        if (!carousel) return;
        
        var slider = carousel.getElementsByClassName('carousel__slider')[0],
            items = carousel.getElementsByClassName('carousel__slider__item'),
            prevBtn = carousel.getElementsByClassName('carousel__prev')[0],
            nextBtn = carousel.getElementsByClassName('carousel__next')[0];
        
        if (!slider || items.length === 0) return;
        
        var width = 320,
            margin = 20,
            currIndex = 0,
            interval, intervalTime = 4000;
        
        function init() {
            resize();
            move(0);
            bindEvents();
            timer();
        }
        
        function resize() {
            width = Math.max(window.innerWidth * 0.3, 300);
            
            for(var i = 0; i < items.length; i++) {
                let item = items[i];
                item.style.width = (width - (margin * 2)) + "px";
                item.style.height = (width * 1.3) + "px";
            }
            
            // Recalculate position after resize
            move(currIndex);
        }
        
        function move(index) {
            // Loop the index
            if (index < 0) {
                index = items.length - 1;
            } else if (index >= items.length) {
                index = 0;
            }
            currIndex = index;
          
            // Center the active item
            var containerWidth = carousel.offsetWidth;
            var translateX = (containerWidth / 2) - (width / 2) - (currIndex * width);
          
            for(var i = 0; i < items.length; i++) {
                let item = items[i],
                    box = item.getElementsByClassName('item__3d-frame')[0];
                if(i == currIndex) {
                    item.classList.add('carousel__slider__item--active');
                    box.style.transform = "perspective(1200px) rotateY(0deg)"; 
                } else {
                    item.classList.remove('carousel__slider__item--active');
                    var rotation = i < currIndex ? 45 : -45;
                    box.style.transform = "perspective(1200px) rotateY(" + rotation + "deg)";
                }
            }
          
            slider.style.transition = "transform 0.6s ease-in-out";
            slider.style.transform = "translate3d(" + translateX + "px, 0, 0)";
        }
        
        function timer() {
            clearInterval(interval);    
            interval = setInterval(() => {
              move(++currIndex);
            }, intervalTime);    
        }
        
        function prev() {
          move(--currIndex);
          timer();
        }
        
        function next() {
          move(++currIndex);    
          timer();
        }
        
        
        function bindEvents() {
            window.onresize = resize;
            if (prevBtn) {
                prevBtn.addEventListener('click', () => { prev(); });
            }
            if (nextBtn) {
                nextBtn.addEventListener('click', () => { next(); });
            }    
        }

        init();
        
    })();
});

