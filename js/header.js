// Header scroll behavior
document.addEventListener('DOMContentLoaded', function() {
    const header = document.getElementById('siteHeader');
    if (!header) return;

    let lastScrollTop = 0;
    let ticking = false;

    function handleScroll() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        // Hide/show header
        if (scrollTop > lastScrollTop && scrollTop > 100) {
            header.classList.add('header-hidden');
        } else {
            header.classList.remove('header-hidden');
        }

        // Change header style when scrolled
        if (scrollTop > 100) {
            header.classList.add('header-scrolled');
        } else {
            header.classList.remove('header-scrolled');
        }

        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
        ticking = false;
    }

    window.addEventListener('scroll', function() {
        if (!ticking) {
            window.requestAnimationFrame(function() {
                handleScroll();
                ticking = true;
            });
            ticking = true;
        }
    });
});

// Morphing page transition and 'open file' modal behavior
document.addEventListener('DOMContentLoaded', function() {
    // Create reusable overlay element
    function createOverlay() {
        const ov = document.createElement('div');
        ov.className = 'morph-overlay';
        document.body.appendChild(ov);
        return ov;
    }

    function playMorphAnimation(cx, cy, callback) {
        document.body.classList.add('page-exit');
        const overlay = createOverlay();
        overlay.style.left = cx + 'px';
        overlay.style.top = cy + 'px';

        requestAnimationFrame(() => {
            overlay.classList.add('expand');
        });

        let handled = false;
        function onDone() {
            if (handled) return;
            handled = true;
            if (callback) callback();
        }

        overlay.addEventListener('transitionend', onDone);
        setTimeout(onDone, 900); // Fallback
    }

    function playBookAnimation(callback) {
        document.body.classList.add('page-exit');

        const overlay = document.createElement('div');
        overlay.className = 'book-transition-overlay';

        const book = document.createElement('div');
        book.className = 'book';

        const cover = document.createElement('div');
        cover.className = 'book-page cover';
        cover.innerHTML = '<span>Opening Artifact...</span>';

        const innerPage = document.createElement('div');
        innerPage.className = 'book-page inner-page';

        book.appendChild(cover);
        book.appendChild(innerPage);
        overlay.appendChild(book);
        document.body.appendChild(overlay);

        requestAnimationFrame(() => {
            overlay.classList.add('visible');
            setTimeout(() => {
                overlay.classList.add('book-open');
            }, 50);
        });

        let handled = false;
        function onDone() {
            if (handled) return;
            handled = true;
            if (callback) callback();
        }

        cover.addEventListener('transitionend', onDone);
        setTimeout(onDone, 1200); // Fallback
    }

    // Handle morphing navigation for same-origin links
    document.body.addEventListener('click', function(e) {
        const a = e.target.closest('a');
        if (!a) return;

        const href = a.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:') || (href.startsWith('http') && !href.startsWith(window.location.origin))) {
            return;
        }

        if (a.hasAttribute('data-no-morph') || a.target) return;

        e.preventDefault();

        if (a.classList.contains('book-link')) {
            playBookAnimation(() => {
                window.location.href = href;
            });
        } else {
            const rect = a.getBoundingClientRect();
            playMorphAnimation(rect.left + rect.width / 2, rect.top + rect.height / 2, () => {
                window.location.href = href;
            });
        }
    });

    // Handle morphing for form submissions
    document.body.addEventListener('submit', function(e) {
        const form = e.target.closest('.morph-form');
        if (!form) return;

        e.preventDefault();
        
        // Find the submit button to animate from its position
        const submitButton = form.querySelector('button[type="submit"]');
        let rect;
        if (submitButton) {
            rect = submitButton.getBoundingClientRect();
        } else {
            // Fallback to form's position
            rect = form.getBoundingClientRect();
        }

        playMorphAnimation(rect.left + rect.width / 2, rect.top + rect.height / 2, () => {
            form.submit();
        });
    });

    // 'Open file' buttons/links: animate into a notebook-style modal with an iframe
    function openFileModal(url, sourceRect, title) {
        const modal = document.createElement('div');
        modal.className = 'file-modal';

        const sheet = document.createElement('div');
        sheet.className = 'file-sheet';

        const toolbar = document.createElement('div');
        toolbar.className = 'file-toolbar';
        const t = document.createElement('div'); t.className = 'title';
        t.textContent = title || url;
        toolbar.appendChild(t);
        const actions = document.createElement('div'); actions.className = 'actions';
        const openBtn = document.createElement('button'); openBtn.textContent = 'Open in new tab';
        const closeBtn = document.createElement('button'); closeBtn.className = 'file-close'; closeBtn.textContent = 'Close';
        actions.appendChild(openBtn); actions.appendChild(closeBtn);
        toolbar.appendChild(actions);
        sheet.appendChild(toolbar);

        const iframe = document.createElement('iframe');
        iframe.className = 'file-iframe';
        iframe.src = url;
        iframe.setAttribute('loading', 'lazy');
        sheet.appendChild(iframe);

        modal.appendChild(sheet);
        document.body.appendChild(modal);

        // Position animation starting from sourceRect (if provided)
        if (sourceRect) {
            const start = {
                left: sourceRect.left + 'px',
                top: sourceRect.top + 'px',
                width: sourceRect.width + 'px',
                height: sourceRect.height + 'px'
            };
            sheet.style.left = start.left;
            sheet.style.top = start.top;
            sheet.style.width = start.width;
            sheet.style.height = start.height;
            sheet.style.borderRadius = '8px';

            // animate to center/full
            requestAnimationFrame(() => {
                sheet.classList.add('sheet-open');
            });
        } else {
            sheet.classList.add('sheet-open');
        }

        function closeModal() {
            // reverse animation: add sheet-close and remove after transition
            sheet.classList.remove('sheet-open');
            sheet.classList.add('sheet-close');
            sheet.addEventListener('transitionend', () => modal.remove(), { once: true });
        }

        openBtn.addEventListener('click', function() { window.open(url, '_blank'); });
        closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (ev) => { if (ev.target === modal) closeModal(); });
    }

    // Attach handler to elements with class 'open-file'
    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.open-file');
        if (!btn) return;
        e.preventDefault();
        const href = btn.getAttribute('href') || btn.dataset.src;
        const title = btn.dataset.title || btn.getAttribute('title') || btn.textContent.trim();
        if (!href) return;
        const rect = btn.getBoundingClientRect();
        openFileModal(href, rect, title);
    });
    
    // When page loads, mark page as loaded to trigger enter animations
    requestAnimationFrame(() => {
        setTimeout(() => document.body.classList.add('page-loaded'), 50);
    });
});
