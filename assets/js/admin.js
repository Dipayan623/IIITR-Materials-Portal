// admin.js

console.log("Admin Panel Loaded");

// Delete Confirmation

function confirmDelete(){

    return confirm(
        "Are you sure you want to delete this item?"
    );

}

// Logout Confirmation

function confirmLogout(){

    return confirm(
        "Are you sure you want to logout?"
    );

}

// Form Validation

function validateYearForm(){

    const year =
        document.getElementById("year_name");

    if(year.value.trim() === ""){

        alert(
            "Please enter an academic year."
        );

        return false;
    }

    return true;
}

// Upload File Validation

function validateUpload(){

    const file =
        document.getElementById("file");

    if(file.files.length === 0){

        alert(
            "Please select a file."
        );

        return false;
    }

    return true;
}