document.addEventListener('DOMContentLoaded', () => {
    let newsIndex = 0;
    const newsSlides = document.querySelectorAll('.news-slide');
    const dots = document.querySelectorAll('.dot');
    let newsTimer;

    const showNews = (index) => {
        if (newsSlides.length === 0) {
            return;
        }

        if (index >= newsSlides.length) {
            newsIndex = 0;
        } else if (index < 0) {
            newsIndex = newsSlides.length - 1;
        } else {
            newsIndex = index;
        }

        newsSlides.forEach((slide) => slide.classList.remove('active'));
        dots.forEach((dot) => dot.classList.remove('active'));

        newsSlides[newsIndex].classList.add('active');
        if (dots.length > 0) {
            dots[newsIndex].classList.add('active');
        }
    };

    const startNewsTimer = () => {
        if (newsSlides.length > 1) {
            newsTimer = setInterval(() => {
                showNews(newsIndex + 1);
            }, 5000);
        }
    };

    const goToNewsSlide = (index) => {
        clearInterval(newsTimer);
        showNews(index);
        startNewsTimer();
    };

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            const index = Number(dot.dataset.newsIndex || 0);
            goToNewsSlide(index);
        });
    });

    showNews(newsIndex);
    startNewsTimer();
});
