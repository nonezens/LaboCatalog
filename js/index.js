function initIndexPage() {
    const hero = document.querySelector('.hero');
    if (!hero) return; // Not on index page

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
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);
    
    if (aboutTitle) observer.observe(aboutTitle);
    if (aboutText) observer.observe(aboutText);
    if (recentTitle) observer.observe(recentTitle);
    
    const cardObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                cardObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    
    cards.forEach(card => cardObserver.observe(card));
    
    // --- NEWS SWIPE CARD STACK ---
    const newsCardStack = document.getElementById('newsCardStack');
    if (newsCardStack && !newsCardStack.dataset.initialized) {
        newsCardStack.dataset.initialized = 'true';
        // (Insert your exact swipe logic here: getDurationFromCSS, handleStart, handleMove, handleEnd, swipeLeft, swipeRight, etc.)
        // Note: I omitted the massive block of swipe code here to save space, just paste your existing swipe block inside this IF statement!
    }
    
    // --- 3D CAROUSEL ---
    const carousel = document.getElementsByClassName('carousel')[0];
    if (carousel && !carousel.dataset.initialized) {
        carousel.dataset.initialized = 'true';
        
        var slider = carousel.getElementsByClassName('carousel__slider')[0],
            items = carousel.getElementsByClassName('carousel__slider__item'),
            prevBtn = carousel.getElementsByClassName('carousel__prev')[0],
            nextBtn = carousel.getElementsByClassName('carousel__next')[0];
        
        if (!slider || items.length === 0) return;
        
        var width = 320, margin = 20, currIndex = 0, interval, intervalTime = 4000;
        
        function resize() {
            width = Math.max(window.innerWidth * 0.3, 300);
            for(var i = 0; i < items.length; i++) {
                items[i].style.width = (width - (margin * 2)) + "px";
                items[i].style.height = (width * 1.3) + "px";
            }
            move(currIndex);
        }
        
        function move(index) {
            if (index < 0) index = items.length - 1;
            else if (index >= items.length) index = 0;
            currIndex = index;
          
            var containerWidth = carousel.offsetWidth;
            var translateX = (containerWidth / 2) - (width / 2) - (currIndex * width);
          
            for(var i = 0; i < items.length; i++) {
                let box = items[i].getElementsByClassName('item__3d-frame')[0];
                if(i == currIndex) {
                    items[i].classList.add('carousel__slider__item--active');
                    box.style.transform = "perspective(1200px) rotateY(0deg)"; 
                } else {
                    items[i].classList.remove('carousel__slider__item--active');
                    var rotation = i < currIndex ? 45 : -45;
                    box.style.transform = "perspective(1200px) rotateY(" + rotation + "deg)";
                }
            }
            slider.style.transition = "transform 0.6s ease-in-out";
            slider.style.transform = "translate3d(" + translateX + "px, 0, 0)";
        }
        
        function timer() { clearInterval(interval); interval = setInterval(() => { move(++currIndex); }, intervalTime); }
        function prev() { move(--currIndex); timer(); }
        function next() { move(++currIndex); timer(); }
        
        window.addEventListener('resize', resize);
        if (prevBtn) prevBtn.addEventListener('click', prev);
        if (nextBtn) nextBtn.addEventListener('click', next);
        
        resize(); move(0); timer();
    }
}

document.addEventListener('DOMContentLoaded', initIndexPage);
document.addEventListener('PageContentUpdated', initIndexPage);