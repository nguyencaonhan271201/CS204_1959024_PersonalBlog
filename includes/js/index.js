let currentItem = 0;
let outputFirstPost = false;

function isInViewport(el) {
    if (el === document.querySelectorAll(".new-post").item(0)) {
        console.log(el);
        console.log(el.getBoundingClientRect());
        console.log(el.getBoundingClientRect().top >= (el.getBoundingClientRect().height / 2))
    }
    const rect = el.getBoundingClientRect();
    if (rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)) {
        if (!el.classList.contains("fly-in")) {
            el.classList.add("fly-in");
        }    
    }

    console.log(rect);
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
        if (!outputFirstPost) {
            let checkElement = document.querySelector('.new-post-hr');
            if (isInViewportMobile(checkElement)) {
                document.querySelectorAll(".new-post").item(0).classList.add("fly-in");
                check();
                outputFirstPost = true;
            }
        } else {
            let checkElement = document.querySelectorAll(".new-post").item(currentItem);
            if (checkElement != null && isInViewportMobile(checkElement)) {
                currentItem++;
                let getElement = document.querySelectorAll(".new-post").item(currentItem);
                if (getElement != null) {
                    document.querySelectorAll(".new-post").item(currentItem).classList.add("fly-in");
                    check();
                }
            }
        }
    } else {
        document.querySelectorAll(".new-post").forEach((item) => isInViewport(item));
        if (!outputFirstPost) {
            let checkElement = document.querySelector('.new-post-hr');
            if (isInViewport(checkElement)) {
                document.querySelectorAll(".new-post").item(0).classList.add("fly-in");
                check();
                outputFirstPost = true;
            }
        } else {
            let checkElement = document.querySelectorAll(".new-post").item(currentItem);
            if (checkElement != null && isInViewport(checkElement)) {
                currentItem++;
                let getElement = document.querySelectorAll(".new-post").item(currentItem);
                if (getElement != null) {
                    document.querySelectorAll(".new-post").item(currentItem).classList.add("fly-in");
                    check();
                }
            }
        }
    }
});