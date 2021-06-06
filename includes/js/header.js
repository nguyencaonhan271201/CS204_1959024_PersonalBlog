let btnClicked = false;

document.addEventListener("DOMContentLoaded", function() {
    let toggler = document.querySelector(".navbar-toggler");
    toggler.addEventListener("click", function() {
        let navIcon = document.querySelector("#nav-icon3");
        btnClicked = !btnClicked;
        if (btnClicked) {
            navIcon.classList.add("open");
        } else {
            navIcon.classList.remove("open");
        }
    }); 
});