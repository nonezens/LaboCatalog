// 1. Logic to show the client-side image preview instantly after file upload
const fileInput = document.getElementById('fileInput');
const imagePreviewContainer = document.getElementById('imagePreviewContainer');
const imagePreview = document.getElementById('imagePreview');

fileInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Check if it's an image
        if (!file.type.startsWith('image/')) {
            alert("Please select an image file.");
            return;
        }

        const reader = new FileReader();

        reader.addEventListener('load', function() {
            // Populate the image preview tag with the file's contents
            imagePreview.src = reader.result;
            // Instantly slide down the preview container
            imagePreviewContainer.style.display = 'block';
        });

        // Read the file as a data URL for client-side display
        reader.readAsDataURL(file);
    } else {
        // If the user clears the file, hide the preview
        imagePreviewContainer.style.display = 'none';
        imagePreview.src = '#';
    }
});

// 2. Logic to toggle the dropdown form open/close
function toggleForm() {
    var form = document.getElementById("addArtifactForm");
    if (form.style.display === "none" || form.style.display === "") {
        form.style.display = "block";
    } else {
        form.style.display = "none";
    }
}

// Smart Errors from previous logic: If there is an error message, keep the form open automatically so they don't have to click it again
if (document.getElementById("addArtifactForm")) {
    // This will be handled by PHP inline check in the HTML
}
