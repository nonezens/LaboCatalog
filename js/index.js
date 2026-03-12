/* ========================================
   INDEX PAGE JS
   ======================================== */
document.addEventListener('DOMContentLoaded', function() {

    // ========================================
    // TAB NAVIGATION
    // ========================================
    function switchTab(tabName, pushState = true) {
        if (!tabName) {
            tabName = 'home'; // Default to home
        }

        const header = document.querySelector('.site-header');
        if (header) {
            if (tabName === 'home') {
                header.classList.add('header-large');
            } else {
                header.classList.remove('header-large');
            }
        }

        // Get all buttons and contents
        const allNavLinks = document.querySelectorAll('[data-tab]');
        const allContents = document.querySelectorAll('.tab-content');

        // Deactivate all buttons/links and hide all content
        allNavLinks.forEach(link => link.classList.remove('active', 'active-page'));
        allContents.forEach(content => {
            content.classList.remove('active');
            // No need for inline style changes, CSS will handle it.
        });

        // Activate the selected button/link
        const selectedLinks = document.querySelectorAll(`[data-tab="${tabName}"]`);
        selectedLinks.forEach(link => {
            link.classList.add('active');
            // For header links, use 'active-page' style
            if (link.classList.contains('nav-link')) {
                link.classList.add('active-page');
            }
        });
        
        // Show selected content
        const selectedContent = document.getElementById(tabName);
        if (selectedContent) {
            selectedContent.classList.add('active');
        }

        // Update URL hash without jumping
        if (pushState) {
            history.pushState(null, '', `#${tabName}`);
        }
    }

    // Add click handlers to all navigation elements with data-tab
    const navElements = document.querySelectorAll('[data-tab]');
    navElements.forEach(elem => {
        elem.addEventListener('click', function(e) {
            // Prevent default anchor behavior
            e.preventDefault(); 
            const tabName = this.getAttribute('data-tab');
            switchTab(tabName);
        });
    });

    // Handle initial page load based on hash
    const currentHash = window.location.hash.substring(1);
    if (currentHash && document.getElementById(currentHash)) {
        switchTab(currentHash, false);
    } else {
        switchTab('home', false); // Default to home
    }
    
    // Handle back/forward browser buttons
    window.addEventListener('popstate', function() {
        const hash = window.location.hash.substring(1);
        switchTab(hash, false);
    });

    // ========================================
    // ANIMATIONS & DYNAMIC CONTENT
    // ========================================

    // Animate sections on scroll - FIXED SELECTORS
    const animatedElements = document.querySelectorAll('.section-title, .about-text, .card, .cat-card');
    
    const observerOptions = { root: null, rootMargin: '0px', threshold: 0.1 };
    
    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Add a class to trigger CSS animation
                entry.target.classList.add('visible');
                // Stop observing once it's visible
                observer.unobserve(entry.target); 
            }
        });
    }, observerOptions);
    
    animatedElements.forEach(el => observer.observe(el));

    // ========================================
    // NEWS CAROUSEL (SIMPLE)
    // ========================================
    const newsContainer = document.querySelector('.news-carousel-container');
    if (newsContainer) {
        const newsCards = newsContainer.querySelectorAll('.news-card');
        const nextBtn = document.getElementById('news-next');
        const prevBtn = document.getElementById('news-prev');
        let currentNewsIndex = 0;

        function showNewsCard(index) {
            newsCards.forEach((card, i) => {
                card.classList.toggle('active', i === index);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                currentNewsIndex = (currentNewsIndex + 1) % newsCards.length;
                showNewsCard(currentNewsIndex);
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                currentNewsIndex = (currentNewsIndex - 1 + newsCards.length) % newsCards.length;
                showNewsCard(currentNewsIndex);
            });
        }

        // Initially show the first card if it exists
        if(newsCards.length > 0) {
            showNewsCard(currentNewsIndex);
        }
    }

    // ========================================
    // 3D CAROUSEL FOR LATEST ACQUISITIONS
    // ========================================
    (function() {
        "use strict";

        const carousel = document.querySelector('.carousel');
        if (!carousel) return;
        
        const slider = carousel.querySelector('.carousel__slider');
        const items = carousel.querySelectorAll('.carousel__slider__item');
        const prevBtn = carousel.querySelector('.carousel__prev');
        const nextBtn = carousel.querySelector('.carousel__next');
        
        if (!slider || items.length < 2) {
            if(prevBtn) prevBtn.style.display = 'none';
            if(nextBtn) nextBtn.style.display = 'none';
            return;
        }
        
        let width = 320;
        let margin = 20;
        let currIndex = 0;
        let interval;
        const intervalTime = 4000;
        
        function move(index) {
            // This logic ensures the carousel loops endlessly
            if (index < 0) {
                currIndex = items.length - 1;
            } else if (index >= items.length) {
                currIndex = 0;
            } else {
                currIndex = index;
            }
          
            const containerWidth = carousel.offsetWidth;
            const translateX = (containerWidth / 2) - (width / 2) - (currIndex * width);
          
            for(let i = 0; i < items.length; i++) {
                let item = items[i];
                let box = item.querySelector('.item__3d-frame');
                if(i === currIndex) {
                    item.classList.add('carousel__slider__item--active');
                    box.style.transform = "perspective(1200px) rotateY(0deg)"; 
                } else {
                    item.classList.remove('carousel__slider__item--active');
                    let rotation = i < currIndex ? 45 : -45;
                    box.style.transform = `perspective(1200px) rotateY(${rotation}deg) scale(0.9)`;
                }
            }
          
            slider.style.transform = `translate3d(${translateX}px, 0, 0)`;
        }
        
        // Timer function now just resets the interval
        function startTimer() {
            clearInterval(interval);    
            interval = setInterval(() => move(currIndex + 1), intervalTime);    
        }
        
        function handlePrev() {
          move(currIndex - 1);
          startTimer(); // Reset timer on manual interaction
        }
        
        function handleNext() {
          move(currIndex + 1);
          startTimer(); // Reset timer on manual interaction
        }
        
        function resize() {
            width = Math.max(window.innerWidth * 0.25, 280);
            for(let i = 0; i < items.length; i++) {
                let item = items[i];
                item.style.width = (width - margin * 2) + "px";
                item.style.height = (width * 1.2) + "px";
            }
            move(currIndex); // Recalculate position on resize
        }

        // Initialize
        window.addEventListener('resize', resize);
        prevBtn?.addEventListener('click', handlePrev);
        nextBtn?.addEventListener('click', handleNext);
        
        resize();
        startTimer();
        
    })();
});
