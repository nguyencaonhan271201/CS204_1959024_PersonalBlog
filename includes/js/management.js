let btn_users = document.getElementById("btn-users")
let btn_posts = document.getElementById("btn-posts");
let board = document.getElementById("management");
let mode = 0;

document.addEventListener("DOMContentLoaded", function() {
    //Default
    btn_posts.disabled = false;
    btn_users.disabled = true;
    initializeHeader(0);
})

btn_users.addEventListener("click", function() {
    btn_posts.disabled = false;
    btn_users.disabled = true;
    initializeHeader(0);
})

btn_posts.addEventListener("click", function() {
    btn_posts.disabled = true;
    btn_users.disabled = false;
    initializeHeader(1);
})

function initializeHeader(currentMode) {
    mode = currentMode;
    switch (currentMode) {
        case 0:
            board_header_html = `
                <th>Profile Image</th>
                <th>Username</th>
                <th>Display Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Posts</th>
                <th>Created</th>
                <th>Managament</th>
            `;
            break;
        case 1:
            board_header_html = `
                <th>Cover</th>
                <th class='title'>Title</th>
                <th>Tag</th>
                <th class="posted">Author</th>
                <th class="posted">Posted</th>
                <th>Rates</th>
                <th class="management">Managament</th>
            `;
            break;
    }
    board.querySelector("thead tr").innerHTML = board_header_html;
    managementAjax(currentMode);
}  

function managementAjax(type) {
    let type_param = type == 0? "users" : "posts";
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././func/adminRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200 && this.readyState == 4) {
            let result = JSON.parse(this.responseText);
            let rows = board.querySelectorAll("tbody tr");
            rows.forEach((row) => {
                row.remove();
            })
            switch (type) {
                case 0:
                    outputTableUsers(result);
                    break;
                case 1:
                    outputTablePosts(result);
                    break;
            }
        }
    }
    xhr.send(`type=${type_param}`);
}

function outputTableUsers(result) {
    result.forEach((user) => {
        let deleteButton = user['user_role'] == 2? 
        `<a class="btn btn-danger mt-1 mb-1 delete-button" data-id="${user['ID']}" role="button">Delete</a>` : '';
        let role = user['user_role'] == 2? "User" : (user['username'] == "admin"? "Senior Admin" : "Admin");
        let otherRole = user['user_role'] != 2? "User" :  "Admin";
        let editButton = user['username'] == 'admin'? '' : `<a class="btn btn-warning mt-1 mb-1 edit-role-btn" data-id="${user['ID']}" data-role="${user['user_role']}" role="button">Set ${otherRole}</a>`;
        let newrow = board.querySelector("tbody").insertRow();
        let html = `
            <td class="text-center"><img class="table-profile rounded-circle" alt="" src="${user['user_img']}"></img></td>
            <td>${user['username']}</td>
            <td>${user['display_name']}</td>
            <td>${user['email']}</td>
            <td class="text-center">${role}</td>
            <td class="text-center"><a href="posts.php?user=${user['ID']}">${user['posts_count']}</a></td>
            <td class="text-center">${user['date_created']}</td>
            <td class="text-center">
                ${editButton}
                <a class="btn btn-info mt-1 mb-1" href="profile.php?id=${user['ID']}" role="button">Info</a>
                ${deleteButton}
            </td>
        `;
        newrow.innerHTML = html;
    })
    setTimeout(check, 300);
    setTimeout(check, 1000);

    setEventListener(0);
    setEditEventListener();
}

function outputTablePosts(result) {
    result.forEach((post) => {
        let newrow = board.querySelector("tbody").insertRow();

        //Stars
        let starsHTML = "";
        let sum_stars = parseInt(post['sum_stars']);
        let count_rates = parseInt(post['count_rates']);
        let avgStar = 0;
        if (count_rates != 0) {
            avgStar = sum_stars / count_rates;
            avgStar = (Math.round(avgStar * 2) / 2).toFixed(1)
        }

        let fullStar = Math.floor(avgStar);
        let halfStar = parseInt(avgStar) == avgStar? 0 : 1;
        for (let j = 0; j < fullStar; j++) {
            starsHTML += "<i class='star fas fa-star'></i>";
        }    
        for (let j = 0; j < halfStar; j++) {
            starsHTML += "<i class='star fas fa-star-half-alt'></i>";
        }
        for (let j = 0; j < Math.floor(5 - avgStar); j++) {
            starsHTML += "<i class='star far fa-star'></i>";
        }

        let html = `
            <td class="text-center"><img class="table-post" alt="" src="${post['cover']}"></img></td>
            <td>${post['title']}</td>
            <td class="text-center"><a href="posts.php?tag=${post['tag_id']}">${post['tag_name']}</a></td>
            <td class="text-center"><a href="profile.php?id=${post['author']}">${post['display_name']}</a></td>
            <td class="text-center">${post['date_posted']}</td>
            <td class="text-center">
                ${starsHTML}
                <p>Comments: ${post['count_comments']}</p>
                <p><span class="mr-1 font-weight-bold">${post['count_likes']}</span>
                <i class="comment-like reaction fas fa-thumbs-up mr-1"></i>
                <span class="mr-1 font-weight-bold">${post['count_dislikes']}</span>
                <i class="comment-dislike reaction fas fa-thumbs-down mr-1"></i></p>
            </td>
            <td class="text-center">
                <a class="btn btn-info mt-1 mb-1" href="post.php?id=${post['ID']}" role="button">View</a>
                <a class="btn btn-warning mt-1 mb-1" href="edit.php?id=${post['ID']}" role="button">Edit</a>
                <a class="btn btn-danger mt-1 mb-1 delete-button" data-id="${post['ID']}" role="button">Delete</a>
            </td>
        `;
        newrow.innerHTML = html;
    })
    setTimeout(check, 300);
    setTimeout(check, 1000);

    setEventListener(1);
}

function deleteAJAX(type, deleteID) {
    let type_param = type == 0? "users" : "posts";
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././func/adminRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200 && this.readyState == 4) {
            let message = this.responseText;
            if (message == "true") {
                document.querySelector("#alert-success").classList.remove("d-none");
                document.querySelector("#alert-success").style.display = "block";
                managementAjax(type);
            } else {
                document.querySelector("#alert-fail").classList.remove("d-none");
                document.querySelector("#alert-fail").style.display = "block";
            }
            alertBoxAnimation();
        }
    }
    xhr.send(`delete=true&type=${type_param}&id=${deleteID}`);
}

function alertBoxAnimation() {
    let alert_box = document.querySelectorAll('.alert');
    //Display alert box only for 5 seconds
    alert_box.forEach((box) => {
        if (box.style.display != "none") {
            setTimeout(() => {
                box.classList.add("shrinkStartAlert");
                setTimeout(function(){
                    box.classList.add("shrinkFinish");
                    setTimeout(function(){
                        box.classList.add = 'd-none';
                        box.style.display = "none";
                    }, 800);
                }, 100);
            }, 5000);
        }
    })
}

function setEventListener(type) {
    document.querySelectorAll(".delete-button").forEach((button) => {
        button.addEventListener("click", function(e) {
            let get_id = e.target.getAttribute("data-id");
            deleteAJAX(type, get_id);
        })
    })
}

function setEditEventListener() {
    document.querySelectorAll(".edit-role-btn").forEach((button) => {
        button.addEventListener("click", function(e) {
            let get_id = e.target.getAttribute("data-id");
            let get_role = e.target.getAttribute("data-role");
            editRoleAJAX(get_id, get_role);
        })
    })
}

function editRoleAJAX(get_id, get_role) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././func/adminRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200 && this.readyState == 4) {
            let message = this.responseText;
            if (message == "true") {
                //Refresh the page to ensure that the process is completed (change layout based on new session's information)
                location.reload();
            } else {
                document.querySelector("#alert-fail").classList.remove("d-none");
                document.querySelector("#alert-fail").style.display = "block";
            }
            alertBoxAnimation();
        }
    }
    xhr.send(`edit=true&get_id=${get_id}&get_role=${get_role}`);
}