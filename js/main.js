/* ========================================
   ADMIN PAGES ONLY - SCOPED JAVASCRIPT
   Consolidated from manage.js + page-specific logic
   Uses document.querySelector('.admin-page ...') for scoping
   ======================================== */

document.addEventListener('DOMContentLoaded', function() {
    // Scope check: only run on admin pages
    if (!document.body.classList.contains('admin-page')) return;

    // Toggle form visibility (artifacts, departments)
    const toggleBtn = document.getElementById('toggle-form-btn');
    const formWrapper = document.getElementById('add-artifact-wrapper') || document.getElementById('add-category-wrapper');
    const tableContainer = document.querySelector('.table-container');
    
    if (toggleBtn && formWrapper) {
        // Initial state
        if (formWrapper.classList.contains('form-visible')) {
            toggleBtn.textContent = '➖ Cancel';
            if (tableContainer) tableContainer.classList.add('faded');
        }
        
        toggleBtn.addEventListener('click', function() {
            const isVisible = formWrapper.classList.toggle('form-visible');
            if (tableContainer) tableContainer.classList.toggle('faded');
            toggleBtn.textContent = isVisible ? '➖ Cancel' : '➕ Add New Artifact';
            if (isVisible) {
                formWrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    // Image preview (shared)
    const imageInput = document.getElementById('image-input');
    const imagePreview = document.getElementById('image-preview');
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                }
                reader.readAsDataURL(this.files[0]);
            } else {
                imagePreview.style.display = 'none';
                imagePreview.src = '#';
            }
        });
    }

    // Visitors filter (manage_visitors.php)
    const filterMonth = document.getElementById('filterMonth');
    const filterGender = document.getElementById('filterGender');
    const filterRecent = document.getElementById('filterRecent');
    if (filterMonth || filterGender || filterRecent) {
        [filterMonth, filterGender, filterRecent].forEach(el => {
            if (el) el.addEventListener('change', function() {
                var month = document.getElementById('filterMonth')?.value || '';
                var gender = document.getElementById('filterGender')?.value || '';
                var recent = document.getElementById('filterRecent')?.value || '';
                var url = 'manage_visitors.php?';
                var params = [];
                if (month) params.push('filter_month=' + encodeURIComponent(month));
                if (gender) params.push('filter_gender=' + encodeURIComponent(gender));
                if (recent) params.push('filter_recent=' + encodeURIComponent(recent));
                window.location.href = url + params.join('&');
            });
        });
    }

    // News form toggle (manage_news.php)
    const newsToggleBtn = document.getElementById('toggle-add-news-btn');
    const newsForm = document.getElementById('addForm');
    if (newsToggleBtn && newsForm) {
        newsToggleBtn.addEventListener('click', function() {
            if (newsForm.style.display === 'none' || newsForm.style.display === '') {
                newsForm.style.display = 'block';
                setTimeout(() => {
                    newsForm.style.opacity = '1';
                    newsForm.style.transform = 'translateY(0)';
                }, 10);
            } else {
                newsForm.style.opacity = '0';
                newsForm.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    newsForm.style.display = 'none';
                }, 300);
            }
        });
    }

    // Artifact filters (manage_artifacts.php) - simplified client-side for now
    const searchInput = document.getElementById('search');
    const categorySelect = document.getElementById('category');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            // Client-side filter as fallback (full server-side in fetch_artifacts.php)
            const rows = document.querySelectorAll('#artifacts-table tbody tr');
            const term = this.value.toLowerCase();
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            const rows = document.querySelectorAll('#artifacts-table tbody tr');
            rows.forEach(row => {
                const catText = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
                row.style.display = (!this.value || catText.includes(this.value)) ? '' : 'none';
            });
        });
    }

    // Dashboard charts are pure CSS/PHP - no JS needed
    console.log('Admin JS loaded successfully on .admin-page');
});
/* ========================================
   CATEGORIES PAGE JS
   ======================================== */
function liveFilter() {
    let query = document.getElementById('searchInput').value.toLowerCase();
    let cards = document.querySelectorAll('.cat-card');
    let hasVisibleCards = false;

    cards.forEach(card => {
        let searchableText = card.getAttribute('data-search');

        if (searchableText && searchableText.includes(query)) {
            // Add fade in animation
            card.style.opacity = '0';
            card.style.transform = 'scale(0.95)';
            card.style.display = 'block';
            
            setTimeout(() => {
                card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                card.style.opacity = '1';
                card.style.transform = 'scale(1)';
            }, 50);
            
            hasVisibleCards = true;
        } else {
            // Fade out animation
            card.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.95)';
            
            setTimeout(() => {
                card.style.display = 'none';
                card.style.opacity = '1';
                card.style.transform = 'scale(1)';
            }, 200);
        }
    });

    let noResultsMsg = document.getElementById('noResultsMessage');
    if (noResultsMsg) {
        noResultsMsg.style.opacity = '0';
        setTimeout(() => {
            noResultsMsg.style.display = hasVisibleCards ? 'none' : 'block';
            noResultsMsg.style.transition = 'opacity 0.3s ease';
            noResultsMsg.style.opacity = '1';
        }, 100);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault(); 
        });
    }
});

/* ========================================
   EXHIBIT DETAIL PAGE JS
   ======================================== */
document.addEventListener('DOMContentLoaded', function() {
    // Trigger animations on page load with staggered delays
    const imageContainer = document.getElementById('imageContainer');
    const infoContainer = document.getElementById('infoContainer');
    const titleElement = document.getElementById('titleElement');
    const badgeElement = document.getElementById('badgeElement');
    const metaElement = document.getElementById('metaElement');
    const descElement = document.getElementById('descElement');
    const backLink = document.getElementById('backLink');

    if (imageContainer) {
        setTimeout(function() {
            imageContainer.classList.add('visible');
        }, 100);
    }
    
    if (infoContainer) {
        setTimeout(function() {
            infoContainer.classList.add('visible');
        }, 200);
    }
    
    if (titleElement) {
        setTimeout(function() {
            titleElement.classList.add('visible');
        }, 300);
    }
    
    if (badgeElement) {
        setTimeout(function() {
            badgeElement.classList.add('visible');
        }, 400);
    }
    
    if (metaElement) {
        setTimeout(function() {
            metaElement.classList.add('visible');
        }, 500);
    }
    
    if (descElement) {
        setTimeout(function() {
            descElement.classList.add('visible');
        }, 600);
    }
    
    if (backLink) {
        setTimeout(function() {
            backLink.classList.add('visible');
        }, 700);
    }

    // Modal functionality with morphing animation
    const modal = document.getElementById("imageModal");
    const img = document.getElementById("exhibitImage");
    const modalImg = document.getElementById("modalImage");
    const closeBtn = document.getElementById("modalClose");

    if (img && modal) {
        img.onclick = function() {
            // Get the original image position for morphing effect
            const rect = img.getBoundingClientRect();
            
            // Set initial morph position
            modalImg.style.transformOrigin = `${rect.left + rect.width/2}px ${rect.top + rect.height/2}px`;
            modalImg.style.transform = 'scale(0.1)';
            
            // Set the image source
            modalImg.src = this.src;
            
            // Show modal with animation
            modal.classList.add('active');
            
            // Animate the morph effect
            requestAnimationFrame(function() {
                modalImg.style.transformOrigin = 'center center';
                modalImg.style.transform = 'scale(1)';
            });
        }
    }

    if (closeBtn && modal) {
        closeBtn.onclick = function() {
            closeModal();
        }
    }

    if (modal) {
        modal.onclick = function(event) {
            if (event.target === modal) {
                closeModal();
            }
        }
    }
    
    // Close modal function with reverse morph animation
    function closeModal() {
        const modalImg = document.getElementById("modalImage");
        const modal = document.getElementById("imageModal");
        
        if (modalImg && modal) {
            // Reverse morph animation
            modalImg.style.transform = 'scale(0.1)';
            
            setTimeout(function() {
                modal.classList.remove('active');
                modalImg.style.transform = 'scale(1)';
            }, 200);
        }
    }

    // Escape key to close modal
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById("imageModal");
        if (e.key === 'Escape' && modal && modal.classList.contains('active')) {
            closeModal();
        }
    });
});

/* ========================================
   EXHIBITS PAGE JS
   ======================================== */
document.addEventListener('DOMContentLoaded', function() {
    // Title animation
    const pageTitle = document.getElementById('pageTitle');
    const searchForm = document.getElementById('searchForm');
    
    if (pageTitle) {
        setTimeout(() => pageTitle.classList.add('visible'), 100);
    }
    if (searchForm) {
        setTimeout(() => searchForm.classList.add('visible'), 200);
    }
    
    // Set hash
    history.replaceState(null, null, '#artifacts');
    
    // Card scroll animations
    const cardLinks = document.querySelectorAll('.card-link');
    const observerOptions = { root: null, rootMargin: '0px', threshold: 0.1 };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    cardLinks.forEach(link => observer.observe(link));
});

// Live filter function with animation
function liveFilter() {
    let query = document.getElementById('searchInput').value.toLowerCase();
    let cardLinks = document.querySelectorAll('.card-link');
    let hasVisibleCards = false;

    cardLinks.forEach(link => {
        let searchableText = link.getAttribute('data-search');
        if (searchableText && searchableText.includes(query)) {
            // Add fade in animation
            link.style.opacity = '0';
            link.style.transform = 'scale(0.95)';
            link.style.display = 'flex';
            
            setTimeout(() => {
                link.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                link.style.opacity = '1';
                link.style.transform = 'scale(1)';
            }, 50);
            
            hasVisibleCards = true;
        } else {
            // Fade out animation
            link.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
            link.style.opacity = '0';
            link.style.transform = 'scale(0.95)';
            
            setTimeout(() => {
                link.style.display = 'none';
                link.style.opacity = '1';
                link.style.transform = 'scale(1)';
            }, 200);
        }
    });

    let noResultsMsg = document.getElementById('noResultsMessage');
    if (noResultsMsg) {
        noResultsMsg.style.opacity = '0';
        setTimeout(() => {
            noResultsMsg.style.display = hasVisibleCards ? 'none' : 'block';
            noResultsMsg.style.transition = 'opacity 0.3s ease';
            noResultsMsg.style.opacity = '1';
        }, 100);
    }
}

// Prevent form submission (for AJAX-style filtering)
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault(); 
        });
    }
});

/* ========================================
   HEADER JS - Shared navigation functionality
   ======================================== */
document.addEventListener("DOMContentLoaded", function() {
    const hamburger = document.getElementById("hamburger-menu");
    const navLinks = document.getElementById("nav-links");

    // Hamburger menu toggle
    if (hamburger) {
        hamburger.addEventListener("click", function() {
            navLinks.classList.toggle("active");
            hamburger.classList.toggle("open");
        });
    }

    // Hash URL handling
    if (window.location.hash) {
        const hash = window.location.hash.substring(1);
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active-page');
            if (link.getAttribute('href') && link.getAttribute('href').includes(hash + '.php')) {
                link.classList.add('active-page');
            }
        });
    }

    // Update active state on nav click
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function() {
            document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active-page'));
            this.classList.add('active-page');
        });
    });

    // Handle browser back/forward
    window.addEventListener('popstate', function() {
        const hash = window.location.hash.substring(1);
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active-page');
            if (link.getAttribute('href') && link.getAttribute('href').includes(hash + '.php')) {
                link.classList.add('active-page');
            }
        });
    });
});

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
        const prevBtn = carousel.querySelector('.carousel__prev');
        const nextBtn = carousel.querySelector('.carousel__next');
        
        if (!slider) return;

        let items = Array.from(slider.children);
        
        if (items.length < 2) {
            if (prevBtn) prevBtn.style.display = 'none';
            if (nextBtn) nextBtn.style.display = 'none';
            return;
        }

        // Settings
        const CLONE_COUNT = 3; // Number of items to clone from each end
        let width = 320;
        let margin = 20;
        let currIndex = CLONE_COUNT; // Start on the first "real" item
        let interval;
        const intervalTime = 4000;
        let isTransitioning = false;
        let isJumping = false; // Guard flag to prevent multiple jumps during transitionend

        // 1. Clone items for the infinite loop effect
        function cloneItems() {
            if (items.length <= CLONE_COUNT) { // Not enough items to clone
                return;
            }
            const itemsToPrepend = items.slice(items.length - CLONE_COUNT).map(item => item.cloneNode(true));
            const itemsToAppend = items.slice(0, CLONE_COUNT).map(item => item.cloneNode(true));
            
            slider.append(...itemsToAppend);
            slider.prepend(...itemsToPrepend);
            
            // Update items array
            items = Array.from(slider.children);
        }
        
        // 2. Core function to move the slider
        function move(index, withTransition = true) {
            if (isTransitioning) return;
            
            if(withTransition) {
                isTransitioning = true;
            }
            
            currIndex = index;
            
            const containerWidth = carousel.offsetWidth;
            const translateX = (containerWidth / 2) - (width / 2) - (currIndex * width);
            
            slider.style.transition = withTransition ? 'transform 0.6s ease-in-out' : 'none';
            slider.style.transform = `translate3d(${translateX}px, 0, 0)`;

            // Update item styles (rotation, active class)
            for (let i = 0; i < items.length; i++) {
                let item = items[i];
                let box = item.querySelector('.item__3d-frame');
                if (i === currIndex) {
                    item.classList.add('carousel__slider__item--active');
                    if (box) box.style.transform = "perspective(1200px) rotateY(0deg)";
                } else {
                    item.classList.remove('carousel__slider__item--active');
                    let rotation = i < currIndex ? 45 : -45;
                    if (box) box.style.transform = `perspective(1200px) rotateY(${rotation}deg) scale(0.9)`;
                }
            }
        }

        // 3. Handle the "jump" when the slider reaches the cloned section
        function handleTransitionEnd() {
            isTransitioning = false;
            
            // Guard against multiple jumps
            if (isJumping) return;
            
            const originalItemsCount = items.length - 2 * CLONE_COUNT;
            
            // If there are no original items (should not happen), bail out
            if (originalItemsCount <= 0) return;
            
            let shouldJump = false;
            let targetIndex = currIndex;
            
            // Jump to the start if we've reached the end clones
            if (currIndex >= originalItemsCount + CLONE_COUNT) {
                targetIndex = CLONE_COUNT + (currIndex - (originalItemsCount + CLONE_COUNT));
                shouldJump = true;
            }
            // Jump to the end if we've reached the start clones
            else if (currIndex < CLONE_COUNT) {
                targetIndex = originalItemsCount + currIndex;
                shouldJump = true;
            }
            
            if (shouldJump) {
                isJumping = true;
                currIndex = targetIndex;
                move(currIndex, false);
                // Reset jumping flag after a short delay to allow the DOM to update
                setTimeout(() => {
                    isJumping = false;
                }, 50);
            }
        }
        
        // 4. Timer logic
        function startTimer() {
            clearInterval(interval);
            interval = setInterval(handleNext, intervalTime);
        }
        
        // 5. Event Handlers
        function handlePrev() {
            if (isTransitioning) return;
            move(currIndex - 1);
            startTimer();
        }
        
        function handleNext() {
            if (isTransitioning) return;
            move(currIndex + 1);
            startTimer();
        }
        
        // 6. Resize handler
        function resize() {
            width = Math.max(window.innerWidth * 0.25, 280);
            for (let i = 0; i < items.length; i++) {
                let item = items[i];
                item.style.width = (width - margin * 2) + "px";
                item.style.height = (width * 1.2) + "px";
            }
            move(currIndex, false); // Recalculate position on resize
        }

        // Initialize
        cloneItems();
        resize();
        startTimer();
        
        slider.addEventListener('transitionend', handleTransitionEnd);
        prevBtn?.addEventListener('click', handlePrev);
        nextBtn?.addEventListener('click', handleNext);
        window.addEventListener('resize', resize);
        
    })();
});
/* ========================================
   MANAGE ARTIFACTS & DEPARTMENTS JS
   ======================================== */
document.addEventListener('DOMContentLoaded', function() {
    // Toggle form visibility
    const toggleBtn = document.getElementById('toggle-form-btn');
    const formWrapper = document.getElementById('add-artifact-wrapper') || document.getElementById('add-category-wrapper');
    const tableContainer = document.querySelector('.table-container');

    // Set initial button text if form is already visible on page load (e.g., due to a PHP error message)
    if (formWrapper && formWrapper.classList.contains('form-visible')) {
        if (toggleBtn) toggleBtn.textContent = '➖ Cancel';
        if (tableContainer) tableContainer.classList.add('faded');
    }

    if (toggleBtn && formWrapper) {
        toggleBtn.addEventListener('click', function() {
            const isVisible = formWrapper.classList.toggle('form-visible');
            if (tableContainer) tableContainer.classList.toggle('faded');
            
            if (toggleBtn) {
                toggleBtn.textContent = isVisible ? '➖ Cancel' : '➕ Add New Artifact';
            }
            if (isVisible) {
                // Smooth scroll to the form when it opens
                formWrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    // Image preview functionality
    const imageInput = document.getElementById('image-input');
    const imagePreview = document.getElementById('image-preview');

    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                }
                reader.readAsDataURL(this.files[0]);
            } else {
                imagePreview.style.display = 'none';
                imagePreview.src = '#';
            }
        });
    }
});

// Filter functionality for manage_artifacts.php with enhanced animations
function initArtifactFilters() {
    const searchInput = document.getElementById('search');
    const categoryInput = document.getElementById('category');
    const dateInput = document.getElementById('date');
    const artifactsTable = document.getElementById('artifacts-table');
    let debounceTimer;

    if (!searchInput || !artifactsTable) return;

    // Create typing indicator element
    const typingIndicator = document.createElement('div');
    typingIndicator.className = 'typing-indicator';
    searchInput.parentElement.style.position = 'relative';
    searchInput.parentElement.appendChild(typingIndicator);

    function fetchArtifacts() {
        // Add fade out animation
        artifactsTable.style.transition = 'opacity 0.2s ease';
        artifactsTable.style.opacity = '0';
        
        // Hide typing indicator
        typingIndicator.style.display = 'none';
        
        setTimeout(() => {
            artifactsTable.innerHTML = '<table><tbody><tr><td colspan="5" style="text-align: center; padding: 20px;" class="table-loading">Loading...</td></tr></tbody></table>';
            
            let formData = new FormData();
            formData.append('search', searchInput.value);
            formData.append('category_id', categoryInput ? categoryInput.value : '');
            formData.append('artifact_year', dateInput ? dateInput.value : '');

            fetch('fetch_artifacts.php', { method: 'POST', body: formData })
            .then(response => response.text())
            .then(data => { 
                // Add fade in animation with slide effect
                artifactsTable.innerHTML = data;
                
                // Add animation class to table rows
                const rows = artifactsTable.querySelectorAll('tbody tr');
                rows.forEach((row, index) => {
                    row.classList.add('filter-result-enter');
                    row.style.animationDelay = (index * 0.05) + 's';
                });
                
                // Fade in
                artifactsTable.style.transition = 'opacity 0.3s ease';
                artifactsTable.style.opacity = '1';
            })
            .catch(error => {
                console.error('Error:', error);
                artifactsTable.innerHTML = '<table><tbody><tr><td colspan="5" style="text-align: center; padding: 20px; color: red;">An error occurred.</td></tr></tbody></table>';
                artifactsTable.style.transition = 'opacity 0.3s ease';
                artifactsTable.style.opacity = '1';
            });
        }, 300);
    }

    function handleInput() {
        // Show typing indicator
        typingIndicator.style.display = 'block';
        
        // Debounce the search
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchArtifacts, 500);
    }

    // Add event listeners with animations
    searchInput.addEventListener('keyup', handleInput);
    searchInput.addEventListener('focus', function() {
        this.parentElement.classList.add('input-focused');
    });
    searchInput.addEventListener('blur', function() {
        this.parentElement.classList.remove('input-focused');
    });
    
    if (categoryInput) {
        categoryInput.addEventListener('change', function() {
            // Add selection animation
            this.style.animation = 'successPop 0.3s ease';
            setTimeout(() => { this.style.animation = ''; }, 300);
            fetchArtifacts();
        });
    }
    if (dateInput) dateInput.addEventListener('keyup', handleInput);
}

// Initialize filters when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initArtifactFilters();
});

// Enhanced form toggle for manage_news.php and manage_departments.php
function initFormToggle(buttonId, formId) {
    const toggleBtn = document.getElementById(buttonId);
    const form = document.getElementById(formId);
    
    if (toggleBtn && form) {
        toggleBtn.addEventListener('click', function() {
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
                // Add animation class
                form.classList.add('form-add-animation');
                // Small delay to allow display:block to apply before opacity transition
                setTimeout(function() {
                    form.style.opacity = '1';
                    form.style.transform = 'translateY(0)';
                }, 10);
            } else {
                form.style.opacity = '0';
                form.style.transform = 'translateY(-10px)';
                setTimeout(function() {
                    form.style.display = 'none';
                    form.classList.remove('form-add-animation');
                }, 300);
            }
        });
    }
}

// Initialize form toggles
document.addEventListener('DOMContentLoaded', function() {
    // For manage_news.php
    initFormToggle('toggle-add-news-btn', 'addForm');
    
    // For manage_departments.php
    initFormToggle('toggle-form-btn', 'add-category-wrapper');
});


