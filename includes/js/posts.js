let searchForm = document.querySelector("#search-form");
let searchInput = document.querySelector("#search-input");
let searchPostTitle = document.querySelector("#search-post-title");
let searchPostValue = document.querySelector("#search-post-value");
let postsPaginations = document.querySelector("#posts-pagination");
let JSposts = document.querySelector("#js-posts");
let JSNoneFound = document.querySelector("#js-none-found");

let post_per_page = 5;
let posts = [];
let current_page = 1;
let num_of_pages = 0;
let start = 0;
let end = 0;
let outputPostsHTML = "";
let outputPaginationHTML = "";

document.addEventListener("DOMContentLoaded", function() {
    searchPostTitle.style.display = "none";
    JSNoneFound.style.display = "none";

    searchInput.addEventListener("keyup", function() {
        if (document.querySelector("#php-posts").innerHTML != "") {
            document.querySelector("#php-posts").innerHTML = "";
            document.querySelector("#php-pagination").setAttribute('style', 'display:none !important');
        }
        let phpSearchPost = document.querySelector(".php-search-post");
        if (phpSearchPost != null) {
            phpSearchPost.setAttribute("style", "display: none !important;");
        }
        let input = searchInput.value;
        if (input == "") {
            searchPostTitle.style.display = "none";
        } else {
            searchPostTitle.style.display = "initial";
        }
        ajaxPosts(input);
    })
})

function ajaxPosts(query) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././func/postsRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200 && this.readyState == 4) {
            let result = JSON.parse(this.responseText);
            searchPostValue.innerHTML = query;
            posts = result;
            outputPostsHTML = "";
            outputPaginationHTML = "";
            if (posts.length > 0) {
                paginationPageChange(1);
                outputPosts();
                JSNoneFound.style.display = "none";
            } else {
                JSNoneFound.style.display = "initial";
                JSposts.innerHTML = outputPostsHTML;
                document.querySelector(".pagination").innerHTML = outputPaginationHTML;
                document.querySelector(".pagination-desc").innerHTML = "";
            }
        }
    }
    xhr.send(`q=${query}`);
}

function escapeHTML(unsafeText) {
    let div = document.createElement('div');
    div.innerText = unsafeText;
    let string = div.innerHTML;
    string = string.replace("&lt;h1&gt;", "").replace("&lt;h2&gt;", "").replace("&lt;h3&gt;", "").replace("&lt;h4&gt;", "").replace("&lt;p&gt;", "")
             .replace("&lt;/h1&gt;", "").replace("&lt;/h2&gt;", "").replace("&lt;/h3&gt;", "").replace("&lt;/h4&gt;", "").replace("&lt;/p&gt;", "");
    return string;
}

function outputPosts(start, end) {
    for (let i = start; i <= end; i++) {
        let id = posts[i]['ID'];
        let image = posts[i]['cover'];
        let tag_id = posts[i]['tag'];
        let tag_name = posts[i]['tag_name'];
        let author_id = posts[i]['author'];
        let title = posts[i]['title'];
        let date = posts[i]['date_posted'];
        let author_display = posts[i]['display_name'];
        let body = escapeHTML(posts[i]['content'].substring(0, 100));

        //Stars
        let html = "";
        let sum_stars = parseInt(posts[i]['sum_stars']);
        let count_rates = parseInt(posts[i]['count_rates']);
        let avgStar = 0;
        if (count_rates != 0) {
            avgStar = sum_stars / count_rates;
            avgStar = (Math.round(avgStar * 2) / 2).toFixed(1)
        }

        let fullStar = Math.floor(avgStar);
        let halfStar = parseInt(avgStar) == avgStar? 0 : 1;
        for (let j = 0; j < fullStar; j++) {
            html += "<i class='star fas fa-star'></i>";
        }    
        for (let j = 0; j < halfStar; j++) {
            html += "<i class='star fas fa-star-half-alt'></i>";
        }
        for (let j = 0; j < Math.floor(5 - avgStar); j++) {
            html += "<i class='star far fa-star'></i>";
        }

        outputPostsHTML += `
            <div class='js-post mb-4 row'>
                <div class='col-lg-5 col-md-12 pt-1 pr-1'>
                    <a href='post.php?id=${id}'><img src='${image}' class='post-thumbnail' alt=''>
                    </a>
                </div>
                <div class='col-lg-7 col-md-12 pt-1 pb-1'>
                    <div>
                        <a href='posts.php?tag=${tag_id}'>${tag_name}</a>
                        <h4 class='display-5 mt-2'>${title}</h4>
                        <p class='font-italic mb-2'>${date} by <a href='profile.php?id=${author_id}'>${author_display}</a></p>
                        <span class='mb-2'>${html}</span>
                        <p class='mb-1 mt-2'>${body}</p>
                        <a class='btn btn-outline-info' href='post.php?id=${id}'>Continue Reading...</a>
                    </div>
                </div>
            </div>
        `;
    }
    JSposts.innerHTML = outputPostsHTML;
    document.querySelector("#php-posts").innerHTML = "";
}

function paginationUpdate() {
    outputPaginationHTML = "";
    let num_of_posts = posts.length;
    num_of_pages = Math.ceil(num_of_posts / post_per_page);
    for (let i = 1; i <= num_of_pages; i++) {
        let additional_class = (i == current_page)? "active" : "";
        outputPaginationHTML += `<li class='page-item ${additional_class}' onclick='paginationPageChange(${i})'>
            <a class='page-link' href='#'>${i}</a>
        </li>`;
    }
    document.querySelector(".pagination").innerHTML = outputPaginationHTML;
}

function paginationPageChange(page) {
    outputPostsHTML = "";
    outputPaginationHTML = "";
    current_page = page;
    start = (current_page - 1) * post_per_page;
    end = Math.min(posts.length - 1, start + post_per_page - 1);

    paginationUpdate();

    document.querySelector(".pagination-desc").innerHTML = `<div class='mb-1 d-flex align-items-center'>
        <p class='m-0'>Showing results ${start + 1} - ${end + 1} / ${posts.length}</p>
    </div>`;

    //Display none the UI from PHP
    let paginationDesc = document.querySelector(".pagination-desc");
    let phpPagination = document.querySelector("#php-pagination");
    if (paginationDesc) {
        paginationDesc.style.display = "initial";
    }
    if (phpPagination) {
        phpPagination.setAttribute('style', 'display:none !important');
    }

    outputPosts(start, end);

    check();
}