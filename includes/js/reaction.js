let chosenUp = "reaction mr-2 fas fa-thumbs-up";
let notChosenUp = "reaction mr-2 far fa-thumbs-up";
let chosenDown = "reaction mr-2 fas fa-thumbs-down";
let notChosenDown = "reaction mr-2 far fa-thumbs-down";

let liked = false;
let disliked = false;
let isLoggedIn = true;

let thumbsUp = document.querySelector("#like");
let thumbsDown = document.querySelector("#dislike");
let like_count = document.querySelector("#like-count");
let dislike_count = document.querySelector("#dislike-count");
let upCount = 0;
let downCount = 0;
let post = 0;

function like() {
    liked = true;
    disliked = false;
    updateIcon();
}

function removeReaction() {
    liked = false;
    disliked = false;
    updateIcon();
}

function dislike() {
    liked = false;
    disliked = true;
    updateIcon();
}

function updateIcon() {
    thumbsUp.classList = liked ? chosenUp : notChosenUp;
    thumbsDown.classList = disliked ? chosenDown : notChosenDown;
    if (!isLoggedIn) {
        thumbsUp.classList.add("cursor-lock");
        thumbsDown.classList.add("cursor-lock");
    }
}

thumbsUp.addEventListener("click", function() {
    if (isLoggedIn) {
        if (liked) {
            ajaxReactionUpload(3);
        } else {
            ajaxReactionUpload(1);
        }
    }
});

thumbsDown.addEventListener("click", function() {
    if (isLoggedIn) {
        if (disliked) {
            ajaxReactionUpload(4);
        } else {
            ajaxReactionUpload(2);
        }
    }
});

function updateNumber() {
    like_count.innerHTML = upCount.toString();
    dislike_count.innerHTML = downCount.toString();
}

function initiateReactionState(up, down, isLiked, isDisliked, loggedIn, id) {
    upCount = up;
    downCount = down;
    liked = isLiked;
    disliked = isDisliked;
    isLoggedIn = loggedIn;
    post = id;
    updateNumber();
    updateIcon();
}

function ajaxReactionUpload(type) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././func/reactionRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200) {
            let message = JSON.parse(this.responseText);
            switch (message) {
                case "0":
                    //Remove like
                    upCount--;
                    removeReaction();
                    break;
                case "1":
                    //Remove dislike
                    downCount--;
                    removeReaction();
                    break;
                case "2":
                    upCount++;
                    if (disliked)
                        downCount--;
                    like();
                    break;
                case "3":
                    downCount++;
                    if (liked)
                        upCount--;
                    dislike();
                    break;
                default:
                    //Error
                    //Do nothing, just do not update UI
                    break;
            }
            updateNumber();
        }
    }
    xhr.send(`type=${type}&post=${post}&liked=${liked}&disliked=${disliked}`);
}