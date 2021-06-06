let starsObject = [];

//Initiate starsObject
for (let i = 0; i < 5; i++) {
    starsObject.push(document.querySelector(`#star${i + 1}`));
}

let uncheckedClass = "star far fa-star";
let checkedClass = "star fas fa-star";
let checkedHalf = "star fas fa-star-half-alt"
let votesCount = document.querySelector('#star-vote-count');
let starCount = 0;
let vote = 0;
let voted = false;
let postID = 0;
let loggedIn = false;
let revoted = false;
let voteUpdated = 0;
let voteRated = 0;

for (let i = 0; i < 5; i++) {
    starsObject[i].addEventListener("mouseover", function() { 
        if (loggedIn) {
            updateStarHover(i);
            if (loggedIn && voted && votesCount.innerHTML != "Thanks for ranking!" && votesCount.innerHTML != "Ranking updated!")
            {
                votesCount.innerHTML = "You have rated!";
            }
        }
    });
    starsObject[i].addEventListener("mouseout", function() {
        if (loggedIn) {
            if (votesCount.innerHTML == "You have rated!") {
                updateStars(starCount);
                votesCount.innerHTML = vote.toString() + " " + (vote == 1? "vote" : "votes");
            } else if (votesCount.innerHTML == "Thanks for ranking!") {
                updateStars(voteRated);
            } else if (votesCount.innerHTML == "Ranking updated!") {
                updateStars(voteUpdated);
            } else {
                updateStars(starCount);
                votesCount.innerHTML = vote.toString() + " " + (vote == 1? "vote" : "votes");
            }
        }        
    });
    starsObject[i].addEventListener("click", function() {
        if (loggedIn) {
            ajaxStarUpload(i + 1);
        }
    });
}

function updateStarHover(i) {
    for (let j = 0; j <= i; j++) {
        starsObject[j].classList = checkedClass;
    }
    for (let j = i + 1; j < 5; j++) {
        starsObject[j].classList = uncheckedClass;
    }
}

function initializeStar(numberOfStars, voteCount, post, isVoted, isLoggedIn) {
    //Update vote count
    starCount = numberOfStars;
    vote = voteCount;
    postID = post;
    voted = isVoted;
    loggedIn = isLoggedIn;
    for (let i = 0; i < Math.floor(numberOfStars); i++) {
        starsObject[i].classList = checkedClass;
    }
    if (numberOfStars > Math.floor(numberOfStars)) {
        starsObject[Math.floor(numberOfStars)].classList = checkedHalf;
    }

    let startIndex = numberOfStars > Math.floor(numberOfStars)? Math.floor(numberOfStars) + 1 : Math.floor(numberOfStars);

    for (let i = startIndex; i < 5; i++) {
        starsObject[i].classList = uncheckedClass;
    }

    if (!isLoggedIn) {
        voted = true; //To not allow further vote for the post
        for (let i = 0; i < 5; i++) {
            starsObject[i].classList.add("cursor-lock");
        }
    } 
    //Update vote count
    votesCount.innerHTML = vote.toString() + " " + (vote == 1? "vote" : "votes");
}

function updateStars(numberOfStars) {
    for (let i = 0; i < Math.floor(numberOfStars); i++) {
        starsObject[i].classList = checkedClass;
    }
    if (numberOfStars > Math.floor(numberOfStars)) {
        starsObject[Math.floor(numberOfStars)].classList = checkedHalf;
    }

    let startIndex = numberOfStars > Math.floor(numberOfStars)? Math.floor(numberOfStars) + 1 : Math.floor(numberOfStars);

    for (let i = startIndex; i < 5; i++) {
        starsObject[i].classList = uncheckedClass;
    }
}

function ajaxStarUpload(star) {
    let mode = voted? "update" : "insert"; 
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././func/starRateRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200) {
            let message = this.responseText;
            if (message == "true") {
                updateStars(star, vote);
                voted = true;
                if (mode == "insert") {
                    votesCount.innerHTML = "Thanks for ranking!";
                    voteRated = star;
                } else {
                    votesCount.innerHTML = "Ranking updated!";
                    revoted = true;
                    voteUpdated = star;
                }
            }            
        }
    }
    xhr.send(`star=${star}&post=${postID}&mode=${mode}`);
}