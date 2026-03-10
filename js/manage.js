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

