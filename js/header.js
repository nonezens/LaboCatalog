// Header scroll behavior
document.addEventListener('DOMContentLoaded', function() {
    let lastScrollTop = 0;
    const header = document.getElementById('siteHeader');

    if (header) {
        window.addEventListener('scroll', function() {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            // Check scroll direction
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                // Scrolling DOWN - hide header
                header.style.transform = 'translateY(-100%)';
            } else {
                // Scrolling UP or near top - show header
                header.style.transform = 'translateY(0)';
            }

            // Change color based on scroll position
            if (scrollTop > 100) {
                // Darker gradient when scrolled down
                header.style.background = 'linear-gradient(135deg, rgba(13, 74, 13, 0.95) 0%, rgba(5, 59, 5, 0.95) 100%)';
                header.style.boxShadow = '0 6px 20px rgba(0,0,0,0.4)';
            } else {
                // Original color at top
                header.style.background = 'var(--primary, #137137)';
                header.style.boxShadow = '0 4px 10px rgba(0,0,0,0.2)';
            }

            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
        });
    }
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

    // Handle morphing navigation for same-origin links (add graceful fallback)
    document.body.addEventListener('click', function(e) {
        const a = e.target.closest('a');
        if (!a) return;
        const href = a.getAttribute('href');
        // only handle internal links (no target, not mailto/tel, and same origin)
        if (!href || href.startsWith('mailto:') || href.startsWith('tel:')) return;
        if (href.startsWith('http') && !href.startsWith(window.location.origin)) return;

        // If link has data-no-morph or a target, skip custom morph
        if (a.hasAttribute('data-no-morph') || a.target) return;

        e.preventDefault();

        // Add page-exit class to animate header and content out
        document.body.classList.add('page-exit');

        const rect = a.getBoundingClientRect();
        const cx = rect.left + rect.width / 2;
        const cy = rect.top + rect.height / 2;

        const overlay = createOverlay();
        overlay.style.left = cx + 'px';
        overlay.style.top = cy + 'px';

        // trigger expand
        requestAnimationFrame(() => {
            overlay.classList.add('expand');
        });

        // navigate after animation (but guard if transitionend doesn't fire)
        let handled = false;
        function doNav() {
            if (handled) return; handled = true;
            window.location.href = href;
        }

        overlay.addEventListener('transitionend', function handler() {
            overlay.removeEventListener('transitionend', handler);
            doNav();
        });
        // fallback: navigate after 900ms in case transitionend doesn't fire
        setTimeout(doNav, 900);
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
