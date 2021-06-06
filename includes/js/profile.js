let currentItem = 0;
let outputFirstPost = false;

function isInViewport(el) {
    const rect = el.getBoundingClientRect();
    if (rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)) {
        if (!el.classList.contains("fly-in")) {
            el.classList.add("fly-in");
        }    
    }

    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

function isInViewportMobile(el) {
    const rect = el.getBoundingClientRect();
    if (rect.bottom <= window.innerHeight && rect.top >= 0) {
        if (!el.classList.contains("fly-in")) {
            el.classList.add("fly-in");
        }    
    }

    return (
        rect.bottom <= window.innerHeight && rect.top >= 0
    );
}


let mobile = window.matchMedia("(max-width: 600px)")

window.addEventListener("scroll", function() {
    if (mobile.matches) {
        document.querySelectorAll(".new-post").forEach((item) => isInViewportMobile(item));
        let checkElement = document.querySelectorAll(".new-post").item(currentItem);
        if (checkElement != null && isInViewportMobile(checkElement)) {
            checkElement.classList.add("fly-in");
            check();
            currentItem++;
        }
    } else {
        document.querySelectorAll(".new-post").forEach((item) => isInViewport(item));
        let checkElement = document.querySelectorAll(".new-post").item(currentItem);
        if (checkElement != null && isInViewport(checkElement)) {
            checkElement.classList.add("fly-in");
            check();
            currentItem++;
        }
    }
});