function toggleGroupFields() {
    var type = document.getElementById("visitor_type").value;
    var groupFields = document.getElementById("group_fields");
    var nameLabel = document.getElementById("name_label");
    var orgInput = document.getElementById("org_input");

    if (type === "Group") {
        groupFields.style.display = "block";
        nameLabel.innerText = "Representative's Full Name";
        orgInput.required = true;
    } else {
        groupFields.style.display = "none";
        nameLabel.innerText = "Full Name";
        orgInput.required = false;
    }
}
