function loadFile(event) {
    let review_img = document.getElementById("review-image");
    if (review_img === null) {
        review_img = document.getElementById("post-review-image");
    }
    let get_url = URL.createObjectURL(event.target.files[0]);
    review_img.src = get_url;
    review_img.onload = function() {
        URL.revokeObjectURL(review_img.src)
    }
    let review_group = document.querySelector(".review-group");
    if (review_group.classList.contains("d-none")) {
        review_group.classList.remove("d-none");
    }
}