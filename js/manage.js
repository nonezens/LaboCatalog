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

// Filter functionality for manage_artifacts.php
function initArtifactFilters() {
    const searchInput = document.getElementById('search');
    const categoryInput = document.getElementById('category');
    const dateInput = document.getElementById('date');
    const artifactsTable = document.getElementById('artifacts-table');
    let debounceTimer;

    if (!searchInput || !artifactsTable) return;

    function fetchArtifacts() {
        // Add fade out animation
        artifactsTable.style.transition = 'opacity 0.2s ease';
        artifactsTable.style.opacity = '0';
        
        setTimeout(() => {
            artifactsTable.innerHTML = '<table><tbody><tr><td colspan="5" style="text-align: center; padding: 20px;">Loading...</td></tr></tbody></table>';
            
            let formData = new FormData();
            formData.append('search', searchInput.value);
            formData.append('category_id', categoryInput ? categoryInput.value : '');
            formData.append('artifact_year', dateInput ? dateInput.value : '');

            fetch('fetch_artifacts.php', { method: 'POST', body: formData })
            .then(response => response.text())
            .then(data => { 
                artifactsTable.innerHTML = data;
                // Add fade in animation
                artifactsTable.style.transition = 'opacity 0.3s ease';
                artifactsTable.style.opacity = '1';
            })
            .catch(error => {
                console.error('Error:', error);
                artifactsTable.innerHTML = '<table><tbody><tr><td colspan="5" style="text-align: center; padding: 20px; color: red;">An error occurred.</td></tr></tbody></table>';
                artifactsTable.style.transition = 'opacity 0.3s ease';
                artifactsTable.style.opacity = '1';
            });
        }, 200);
    }

    function handleInput() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchArtifacts, 400);
    }

    searchInput.addEventListener('keyup', handleInput);
    if (categoryInput) categoryInput.addEventListener('change', fetchArtifacts);
    if (dateInput) dateInput.addEventListener('keyup', handleInput);
}

// Initialize filters when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initArtifactFilters();
});

