// 1. Logic for the Dropdown Toggle Button
function toggleForm() {
    var form = document.getElementById("addDepartmentForm");
    if (form.style.display === "none" || form.style.display === "") {
        form.style.display = "block";
    } else {
        form.style.display = "none";
    }
}

// Keep form open if there is an error
if (document.getElementById("addDepartmentForm")) {
    // PHP will handle this inline
}

// 2. Logic for the Image Preview
const fileInput = document.getElementById('fileInput');
const imagePreviewContainer = document.getElementById('imagePreviewContainer');
const imagePreview = document.getElementById('imagePreview');

fileInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        if (!file.type.startsWith('image/')) {
            alert("Please select an image file.");
            return;
        }

        const reader = new FileReader();

        reader.addEventListener('load', function() {
            imagePreview.src = reader.result;
            imagePreviewContainer.style.display = 'block';
        });

        reader.readAsDataURL(file);
    } else {
        imagePreviewContainer.style.display = 'none';
        imagePreview.src = '#';
    }
});
