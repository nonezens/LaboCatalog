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
