// main.js

console.log("IIITR Materials Loaded");

// Confirm Download

function confirmDownload(fileName){

    return confirm(
        "Download: " + fileName + " ?"
    );

}

// Go Back Button

function goBack(){

    window.history.back();

}

// Search Filter

function filterItems(){

    const input =
        document.getElementById("searchInput");

    const filter =
        input.value.toLowerCase();

    const cards =
        document.querySelectorAll(".filter-card");

    cards.forEach(card => {

        const text =
            card.textContent.toLowerCase();

        if(text.includes(filter)){
            card.style.display = "";
        }
        else{
            card.style.display = "none";
        }

    });

}