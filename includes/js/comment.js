//DOM Variables
let comments = document.querySelector("#comments");
let dom_comment_count = document.querySelector("#comment-count");
let comment_form = document.querySelector("#comment-form");
let comment_input = document.querySelector("#comment-inp");

//Save initial session information
let id = null;
let cover = null;
let display = null;
let role = null;
let post_id = null;
let comment_count = 0;
let commentLoggedIn = false;

function initiateSessionInfo(user_id, img, display_name, user_role, post, loggedIn) {
    id = user_id;
    cover = img;
    display = display_name;
    role = user_role;
    post_id = post;
    commentLoggedIn = loggedIn;

    getComments();
}

function outputComment(author_id, display_name, content, profile_img, time, comment_id, reactions) {
    //Output HTML for a single comment
    let specialBtnBlock = "";
    let editButton = "";
    let editSection = "";
    //Only author is allowed to edit. Admin is only allowed to delete comments.
    if (author_id == id) {
        editButton = `<a class="dropdown-item edit-item" data-id="${comment_id}" href="#">Edit</a>`;
    }
    let reaction_up_class = reactions[2] == 1? "fas fa-thumbs-up" : "far fa-thumbs-up";
    let reaction_down_class = reactions[3] == 1? "fas fa-thumbs-down" : "far fa-thumbs-down";
    let cursor_type = !commentLoggedIn? "cursor-lock" : "";

    if (role == 1 || author_id == id) {
        editSection = `<a class="nav-link dropdown-toggle comment-a" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" 
        aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
            ${editButton}
            <a class="dropdown-item delete-item" data-id="${comment_id}" href="#">Delete</a>
        </div>`;
    }
    //Code to output edit button
    specialBtnBlock = `<div class="col-md-2 col-2 pl-1 text-right d-flex flex-column align-items-end justify-content-start pt-0">
        ${editSection}
    </div>`;
    let replyButton = commentLoggedIn? `<a class="reply-a reply-item" data-id="${comment_id}" href="#">Reply</a>` : "";
    return `
        <div class="col-md-2 col-3 pl-3 pr-3 text-right">
            <a href="profile.php?id=${author_id}"><img src="${profile_img}" alt="" class="comment-img"></a>
        </div>
        <div class="col-md-8 col-7 p-0 text-left comment-content">
            <div>
                <p class="mb-2"><b><a href="profile.php?id=${author_id}">${display_name}</a></b></p>
                <div class="main-content" id="comment${comment_id}"><p class="mb-2">${content}</p></div>
                <p class="font-italic mb-1 time-comment">${time}</p><p class="error mb-2" id="error-${comment_id}"></p>
                <p class="mt-0">
                    <span class="mr-2 font-weight-bold" id="comment-${comment_id}-like-count">${reactions[0]}</span>
                    <i id="comment-${comment_id}-like-icon" class="${cursor_type} comment-like reaction ${reaction_up_class} mr-2" 
                    onclick="javascript:likeComment(${comment_id})"></i>
                    <span class="mr-2 font-weight-bold" id="comment-${comment_id}-dislike-count">${reactions[1]}</span>
                    <i id="comment-${comment_id}-dislike-icon" class="${cursor_type} comment-dislike reaction ${reaction_down_class} mr-2" 
                    onclick="javascript:dislikeComment(${comment_id})"></i>
                    ${replyButton}
                </p>
            </div>
        </div>
        ${specialBtnBlock}        
        <div class="col-11 offset-1 subcomment mt-0" id="comment-${comment_id}-subcomment-block">
            <form method="post" class="mb-4 subcomment-form" id="comment-${comment_id}-reply-form" style="display: none;">
                <div class="input-group">
                    <input class="form-control" type="text" name="comment" placeholder="Write comment...">
                    <button type="submit" name="submit" class="ml-1 btn btn-info"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
                </div>
                <p class="error"></p>
            </form>
            <div class="subcomments"></div>
        </div>
    `;
}

function replyComment(comment_id) {
    let subcommentBlock = document.querySelector(`#comment-${comment_id}-reply-form`);
    subcommentBlock.style.display = (subcommentBlock.style.display == "none")? "block" : "none";
    check();
}

function editCommentExecute(comment_id) {
    let getBlock = document.querySelector(`#comment${comment_id}`);
    let inputBlock = document.querySelector(`#comment${comment_id} p`);
    let inputValue = inputBlock.innerHTML;
    let originalHTML = getBlock.innerHTML;
    let outputHTML = `<input id="comment-edit-${comment_id}" class="form-control" type="text" name="comment" value="${inputValue}">
    <button class="mt-1 btn btn-success" id="btn-edit-approve-${comment_id}"><i class="fa fa-check" aria-hidden="true"></i> Approve</button>
    <button class="mt-1 btn btn-danger" id="btn-edit-cancel-${comment_id}"> <i class="fa fa-times" aria-hidden="true"></i> Cancel </button>`;
    getBlock.innerHTML = outputHTML;

    document.querySelector(`#btn-edit-cancel-${comment_id}`).addEventListener("click", function(e) {
        e.preventDefault();
        getBlock.innerHTML = originalHTML;
        document.querySelector(`#error-${comment_id}`).innerHTML = "";
    })

    document.querySelector(`#btn-edit-approve-${comment_id}`).addEventListener("click", function(e) {
        e.preventDefault();
        let xhr = new XMLHttpRequest();
        let new_content = document.querySelector(`#comment-edit-${comment_id}`).value;
        xhr.open("POST", "././func/commentRequestHandler.php", true);
        xhr.onload = function() {
            if(this.status == 200 && this.readyState == 4) {
                let message = this.responseText;
                if (message == "true") {
                    //Edit successfully
                    let newHTML = `<p class="mb-2">${new_content}</p>`;
                    getBlock.innerHTML = newHTML;
                    document.querySelector(`#error-${comment_id}`).innerHTML = "";
                } else {
                    document.querySelector(`#error-${comment_id}`).innerHTML = "Error occured!";
                }
            }
        }
        var data = new FormData();
        data.append("edit", "true");
        data.append("content", new_content);
        data.append("comment", comment_id);
        xhr.send(data);
    })
}

function deleteCommentExecute(comment_id) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././func/commentRequestHandler.php", true);
    xhr.onload = function() {
        if(this.status == 200 && this.readyState == 4) {
            let message = this.responseText;
            if (message == "true") {
                //Delete successfully
                document.querySelector(`#error-${comment_id}`).innerHTML = "";
                let getFatherContainer = document.querySelector(`#comment${comment_id}`).parentElement.parentElement.parentElement;
                getFatherContainer.classList.add("shrinkStart");
                setTimeout(function(){
                    getFatherContainer.classList.add("shrinkFinish");
                    setTimeout(function(){
                        getComments();
                    }, 800);
                }, 100);
            } else {
                document.querySelector(`#error-${comment_id}`).innerHTML = "Error occured!";
            }
        }
    }
    var data = new FormData();
    data.append("delete", "true");
    data.append("comment", comment_id);
    xhr.send(data);
}

function outputComments(comments_arr) {
    comments.innerHTML = "";
    for (let i = comments_arr.length - 1; i >= 0; i--) {
        let author = comments_arr[i]['author'];
        let dname = comments_arr[i]['display_name'];
        let content = comments_arr[i]['content'];
        let img = comments_arr[i]['user_img'];
        let time = comments_arr[i]['date_commented'];

        //Format time string to the same as PHP
        let time_string_parts = time.split(" ");
        let date_parts = time_string_parts[0].split("-"); // yyyy, MM, dd
        time = `${date_parts[2]}/${date_parts[1]}/${date_parts[0]} ${time_string_parts[1]}`;

        let id = comments_arr[i]['ID'];
        let reactions = [];
        reactions.push(comments_arr[i]['like_count']);
        reactions.push(comments_arr[i]['dislike_count']);
        reactions.push(comments_arr[i]['self_like_count']);
        reactions.push(comments_arr[i]['self_dislike_count']);

        let parent = comments_arr[i]['get_parent'];

        let commentDiv = document.createElement("div");
        commentDiv.classList.add("row");
        commentDiv.classList.add("comment");
        commentDiv.classList.add("mb-1");
        commentDiv.innerHTML = outputComment(author, dname, content, img, time, id, reactions);
        
        if (parent == 0 && comments != null) {
            comments.prepend(commentDiv);
        } else {
            let getDiv = document.querySelector(`#comment-${parent}-subcomment-block .subcomments`);
            if (getDiv != null) {
                getDiv.prepend(commentDiv);
            }
        }

        let getReplyForm = document.querySelector(`#comment-${id}-reply-form`);
        if (getReplyForm != null) {
            getReplyForm.addEventListener("submit", function(e) {
                e.preventDefault();
                let comment_content = document.querySelector(`#comment-${id}-reply-form div input`).value;
                if (comment_content != "") {
                    ajaxComment(comment_content, id);
                } else {
                    document.querySelector(`#comment-${id}-reply-form p`).innerHTML = "Comment cannot be blank! Please try again";
                    document.querySelector(`#comment-${id}-reply-form div input`).classList.add("shake");
                    setTimeout(() => {
                        document.querySelector(`#comment-${id}-reply-form div input`).classList.remove("shake");
                    }, 500);
                }
            });
        }
    }
    dom_comment_count.innerHTML = comments_arr.length.toString();
    setEventListener();
}

function getComments() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././func/commentRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200 && this.readyState == 4) {
            let result = JSON.parse(this.responseText);
            outputComments(result);
        }
    }
    xhr.send(`get_all=true&post=${post_id}`);
}

function likeComment(commentID) {
    if (commentLoggedIn) {
        let likeDOM = document.querySelector(`#comment-${commentID}-like-icon`);
        let dislikeDOM = document.querySelector(`#comment-${commentID}-dislike-icon`);
        let checkLiked = likeDOM.classList.contains("fas")? "true" : "false";
        let checkDisliked = dislikeDOM.classList.contains("fas")? "true" : "false";
        let likeCountDOM = document.querySelector(`#comment-${commentID}-like-count`);
        let dislikeCountDOM = document.querySelector(`#comment-${commentID}-dislike-count`);

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "././func/commentRequestHandler.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if(this.status == 200 && this.readyState == 4) {
                let message = this.responseText;
                if (message == "true") {
                    checkLiked = checkLiked == "true";
                    checkDisliked = checkDisliked == "true";
                    if (checkLiked) {
                        let likeCount = parseInt(likeCountDOM.innerText);
                        likeCountDOM.innerHTML = (likeCount - 1).toString();
                        likeDOM.classList.remove("fas");
                        likeDOM.classList.add("far");
                    } else if (checkDisliked) {
                        let likeCount = parseInt(likeCountDOM.innerText);
                        likeCountDOM.innerHTML = (likeCount + 1).toString();
                        likeDOM.classList.remove("far");
                        likeDOM.classList.add("fas");

                        let dislikeCount = parseInt(dislikeCountDOM.innerText);
                        dislikeCountDOM.innerHTML = (dislikeCount - 1).toString();
                        dislikeDOM.classList.remove("fas");
                        dislikeDOM.classList.add("far");
                    } else {
                        let likeCount = parseInt(likeCountDOM.innerText);
                        likeCountDOM.innerHTML = (likeCount + 1).toString();
                        likeDOM.classList.remove("far");
                        likeDOM.classList.add("fas");
                    }
                }  
            }
        }
        xhr.send(`comment_react=like&comment=${commentID}&liked=${checkLiked}&disliked=${checkDisliked}`);
    }
}

function dislikeComment(commentID) {
    if (commentLoggedIn) {
        let likeDOM = document.querySelector(`#comment-${commentID}-like-icon`);
        let dislikeDOM = document.querySelector(`#comment-${commentID}-dislike-icon`);
        let checkLiked = likeDOM.classList.contains("fas")? "true" : "false";
        let checkDisliked = dislikeDOM.classList.contains("fas")? "true" : "false";
        let likeCountDOM = document.querySelector(`#comment-${commentID}-like-count`);
        let dislikeCountDOM = document.querySelector(`#comment-${commentID}-dislike-count`);

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "././func/commentRequestHandler.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if(this.status == 200 && this.readyState == 4) {
                let message = this.responseText;
                if (message == "true") {
                    checkLiked = checkLiked == "true";
                    checkDisliked = checkDisliked == "true";
                    if (checkDisliked) {
                        let dislikeCount = parseInt(dislikeCountDOM.innerText);
                        dislikeCountDOM.innerHTML = (dislikeCount - 1).toString();
                        dislikeDOM.classList.remove("fas");
                        dislikeDOM.classList.add("far");
                    } else if (checkLiked) {
                        let dislikeCount = parseInt(dislikeCountDOM.innerText);
                        dislikeCountDOM.innerHTML = (dislikeCount + 1).toString();
                        dislikeDOM.classList.remove("far");
                        dislikeDOM.classList.add("fas");

                        let likeCount = parseInt(likeCountDOM.innerText);
                        likeCountDOM.innerHTML = (likeCount - 1).toString();
                        likeDOM.classList.remove("fas");
                        likeDOM.classList.add("far");
                    } else {
                        let dislikeCount = parseInt(dislikeCountDOM.innerText);
                        dislikeCountDOM.innerHTML = (dislikeCount + 1).toString();
                        dislikeDOM.classList.remove("far");
                        dislikeDOM.classList.add("fas");
                    }
                }  
            }
        }
        xhr.send(`comment_react=dislike&comment=${commentID}&liked=${checkLiked}&disliked=${checkDisliked}`);
    }
}

function ajaxComment(comment_content, parent) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././func/commentRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200 && this.readyState == 4) {
            let message = this.responseText;
            if (message == "true") {
            //Add new comment successfully
                getComments();
                if (parent == 0) {
                    document.querySelector("#comment-error").innerHTML = "";
                    comment_input.value = "";
                } else {
                    document.querySelector(`#comment-${parent}-reply-form p`).innerHTML = "";
                    document.querySelector(`#comment-${parent}-reply-form div input`).value = "";
                }
            } else {
                if (parent == 0)
                    document.querySelector("#comment-error").innerHTML = "Error occured! Please try again";
                else
                    document.querySelector(`#comment-${parent}-reply-form p`).innerHTML = "Error occured! Please try again";
            }
        }
    }
    xhr.send(`add=true&content=${comment_content}&post=${post_id}&parent=${parent}`);    
}

//Event listener
//Form submit event handler
if (comment_form != null) {
    comment_form.addEventListener("submit", function(e) {
        e.preventDefault();
        let comment_content = comment_input.value;
        if (comment_content != "") {
            ajaxComment(comment_content, 0);
        } else {
            document.querySelector("#comment-error").innerHTML = "Comment cannot be blank! Please try again";
            comment_input.classList.add("shake");
            setTimeout(() => {
                comment_input.classList.remove("shake");
            }, 500);
        }
    }); 
}

function setEventListener() {
    document.querySelectorAll(".reply-item").forEach((button) => {
        button.addEventListener("click", function(e) {
            e.preventDefault();
            let get_id = e.target.getAttribute("data-id");
            replyComment(get_id);
        })
    })
    
    document.querySelectorAll(".edit-item").forEach((button) => {
        button.addEventListener("click", function(e) {
            e.preventDefault();
            let get_id = e.target.getAttribute("data-id");
            editCommentExecute(get_id);
        })
    })

    document.querySelectorAll(".delete-item").forEach((button) => {
        button.addEventListener("click", function(e) {
            e.preventDefault();
            let get_id = e.target.getAttribute("data-id");
            deleteCommentExecute(get_id);
        })
    })
}