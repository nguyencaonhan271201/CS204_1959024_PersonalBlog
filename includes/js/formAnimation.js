let formGet = document.querySelectorAll("form input");

function formAnimationCheck() {
    formGet.forEach((input) => {
        if (input.value == "" || input.value == null || input.value == undefined) {
            setTimeout(() => {
                input.classList.add("shake");
                setTimeout(() => {
                    input.classList.remove("shake");
                }, 500);
            }, 500);
        }
    })
}