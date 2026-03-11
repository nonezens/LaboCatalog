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
    // NEWS SWIPE CARD STACK - Auto swipe every 10 seconds
    // ========================================
    const newsCardStack = document.getElementById('newsCardStack');
    let swipeCards = [];
    let currentSwipeIndex = 0;
    let isSwiping = false;
    let startX = 0;
    let currentX = 0;
    let autoSwipeInterval = null;
    let animationFrameId = null;
    
    if (newsCardStack) {
        // Get all swipe cards
        swipeCards = [...document.querySelectorAll('.swipe-card')];
        
        const getDurationFromCSS = (variableName, element = document.documentElement) => {
            const value = getComputedStyle(element)?.getPropertyValue(variableName)?.trim();
            if (!value) return 400;
            if (value.endsWith("ms")) return parseFloat(value);
            if (value.endsWith("s")) return parseFloat(value) * 1000;
            return parseFloat(value) || 400;
        };
        
        const getActiveCard = () => swipeCards[0];
        
        const updatePositions = () => {
            swipeCards.forEach((card, i) => {
                card.style.setProperty('--i', i + 1);
                card.style.setProperty('--swipe-x', '0px');
                card.style.setProperty('--swipe-rotate', '0deg');
                card.style.opacity = '1';
                card.classList.remove('swiping', 'swiped-left', 'swiped-right');
            });
            updateIndicators();
        };
        
        const updateIndicators = () => {
            const indicators = document.querySelectorAll('.indicator');
            indicators.forEach((ind, i) => {
                ind.classList.toggle('active', i === currentSwipeIndex);
            });
        };
        
        const applySwipeStyles = (deltaX) => {
            const card = getActiveCard();
            if (!card) return;
            card.classList.add('swiping');
            card.style.setProperty('--swipe-x', `${deltaX}px`);
            card.style.setProperty('--swipe-rotate', `${deltaX * 0.15}deg`);
            card.style.opacity = 1 - Math.min(Math.abs(deltaX) / 200, 1) * 0.5;
        };
        
        const handleStart = (clientX) => {
            if (isSwiping) return;
            // Pause auto swipe on manual interaction
            if (autoSwipeInterval) {
                clearInterval(autoSwipeInterval);
                autoSwipeInterval = null;
            }
            isSwiping = true;
            startX = currentX = clientX;
            const card = getActiveCard();
            card && (card.style.transition = 'none');
        };
        
        const handleMove = (clientX) => {
            if (!isSwiping) return;
            cancelAnimationFrame(animationFrameId);
            animationFrameId = requestAnimationFrame(() => {
                currentX = clientX;
                const deltaX = currentX - startX;
                applySwipeStyles(deltaX);
                
                if (Math.abs(deltaX) > 50) handleEnd();
            });
        };
        
        const handleEnd = () => {
            if (!isSwiping) return;
            cancelAnimationFrame(animationFrameId);
            
            const deltaX = currentX - startX;
            const threshold = 50;
            const duration = getDurationFromCSS('--swipe-swap-duration');
            const card = getActiveCard();
            
            if (card) {
                card.style.transition = `transform ${duration}ms ease, opacity ${duration}ms ease`;
                
                if (Math.abs(deltaX) > threshold) {
                    const direction = Math.sign(deltaX);
                    
                    // Animate card swiping out
                    card.classList.add(direction > 0 ? 'swiped-right' : 'swiped-left');
                    card.style.setProperty('--swipe-x', `${direction * 500}px`);
                    card.style.setProperty('--swipe-rotate', `${direction * 20}deg`);
                    
                    setTimeout(() => {
                        // Move card to end and reset
                        swipeCards = [...swipeCards.slice(1), card];
                        currentSwipeIndex = (currentSwipeIndex + 1) % swipeCards.length;
                        updatePositions();
                    }, duration);
                } else {
                    applySwipeStyles(0);
                    card.classList.remove('swiping');
                }
            }
            
            isSwiping = false;
            startX = currentX = 0;
            
            // Resume auto swipe after manual interaction
            startAutoSwipe();
        };
        
        // Add event listeners
        newsCardStack.addEventListener('pointerdown', ({ clientX }) => handleStart(clientX));
        newsCardStack.addEventListener('pointermove', ({ clientX }) => handleMove(clientX));
        newsCardStack.addEventListener('pointerup', handleEnd);
        newsCardStack.addEventListener('pointerleave', () => {
            if (isSwiping) handleEnd();
        });
        
        // Touch support
        newsCardStack.addEventListener('touchstart', (e) => {
            handleStart(e.touches[0].clientX);
        }, { passive: true });
        
        newsCardStack.addEventListener('touchmove', (e) => {
            handleMove(e.touches[0].clientX);
        }, { passive: true });
        
        newsCardStack.addEventListener('touchend', handleEnd);
        
        // Keyboard support
        document.addEventListener('keydown', (e) => {
            if (!newsCardStack) return;
            if (e.key === 'ArrowLeft') {
                swipeLeft();
            } else if (e.key === 'ArrowRight') {
                swipeRight();
            }
        });
        
        const swipeLeft = () => {
            if (isSwiping || swipeCards.length === 0) return;
            const card = getActiveCard();
            if (!card) return;
            
            if (autoSwipeInterval) {
                clearInterval(autoSwipeInterval);
                autoSwipeInterval = null;
            }
            
            isSwiping = true;
            const duration = getDurationFromCSS('--swipe-swap-duration');
            card.style.transition = `transform ${duration}ms ease, opacity ${duration}ms ease`;
            card.classList.add('swiped-left');
            card.style.setProperty('--swipe-x', '-500px');
            card.style.setProperty('--swipe-rotate', '-20deg');
            
            setTimeout(() => {
                swipeCards = [...swipeCards.slice(1), card];
                currentSwipeIndex = (currentSwipeIndex + 1) % swipeCards.length;
                updatePositions();
                isSwiping = false;
                startAutoSwipe();
            }, duration);
        };
        
        const swipeRight = () => {
            if (isSwiping || swipeCards.length === 0) return;
            const card = getActiveCard();
            if (!card) return;
            
            if (autoSwipeInterval) {
                clearInterval(autoSwipeInterval);
                autoSwipeInterval = null;
            }
            
            isSwiping = true;
            const duration = getDurationFromCSS('--swipe-swap-duration');
            card.style.transition = `transform ${duration}ms ease, opacity ${duration}ms ease`;
            card.classList.add('swiped-right');
            card.style.setProperty('--swipe-x', '500px');
            card.style.setProperty('--swipe-rotate', '20deg');
            
            setTimeout(() => {
                swipeCards = [...swipeCards.slice(1), card];
                currentSwipeIndex = (currentSwipeIndex + 1) % swipeCards.length;
                updatePositions();
                isSwiping = false;
                startAutoSwipe();
            }, duration);
        };
        
        // Indicator click support
        const indicators = document.querySelectorAll('.indicator');
        indicators.forEach((ind, index) => {
            ind.addEventListener('click', () => {
                if (index === currentSwipeIndex || isSwiping) return;
                
                if (autoSwipeInterval) {
                    clearInterval(autoSwipeInterval);
                    autoSwipeInterval = null;
                }
                
                // Swipe cards until we reach the target index
                const cardsToSwipe = (index - currentSwipeIndex + swipeCards.length) % swipeCards.length;
                
                const swipeMultiple = (count) => {
                    if (count <= 0) {
                        startAutoSwipe();
                        return;
                    }
                    swipeLeft();
                    setTimeout(() => swipeMultiple(count - 1), getDurationFromCSS('--swipe-swap-duration') + 100);
                };
                
                swipeMultiple(cardsToSwipe);
            });
        });
        
        // Button navigation support
        const prevBtn = document.getElementById('swipePrevBtn');
        const nextBtn = document.getElementById('swipeNextBtn');
        
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                if (autoSwipeInterval) {
                    clearInterval(autoSwipeInterval);
                    autoSwipeInterval = null;
                }
                swipeLeft();
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                if (autoSwipeInterval) {
                    clearInterval(autoSwipeInterval);
                    autoSwipeInterval = null;
                }
                swipeRight();
            });
        }
        
        // Auto swipe function
        const startAutoSwipe = () => {
            if (autoSwipeInterval || swipeCards.length <= 1) return;
            
            autoSwipeInterval = setInterval(() => {
                if (!isSwiping) {
                    swipeLeft();
                }
            }, 10000); // 10 seconds auto swipe
        };
        
        // Initialize
        updatePositions();
        startAutoSwipe();
        
        // Pause on hover
        newsCardStack.addEventListener('mouseenter', () => {
            if (autoSwipeInterval) {
                clearInterval(autoSwipeInterval);
                autoSwipeInterval = null;
            }
        });
        
        newsCardStack.addEventListener('mouseleave', () => {
            if (!autoSwipeInterval) {
                startAutoSwipe();
            }
        });
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

