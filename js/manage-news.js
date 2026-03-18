function toggleForm() {
    var form = document.getElementById("addNewsForm");
    if (form.style.display === "none" || form.style.display === "") {
        form.style.display = "block";
    } else {
        form.style.display = "none";
    }
}
// PHP will handle the error state inline
