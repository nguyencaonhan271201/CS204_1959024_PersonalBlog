<?php
    function outputPosts($posts, $tags) {
        for ($i = 0; $i < count($posts); $i++) {
            $title = filter_var($posts[$i]['title'], FILTER_SANITIZE_STRING);
            $body = filter_var(substr($posts[$i]['content'], 0, 100), FILTER_SANITIZE_STRING) . '...';
            $tag_id = $posts[$i]['tag'];
            $date = date_format(date_create($posts[$i]['date_posted']), 'd/m/Y H:m:s');
            $id = $posts[$i]['ID'];
            $image = filter_var($posts[$i]['cover'], FILTER_SANITIZE_STRING);

            $html = outputHTMLStars($posts[$i]);

            echo "<div class='new-post mb-4 row'>
                <div class='col-md-4 col-sm-12 pt-1'>
                    <a href='post.php?id={$id}'><img src='{$image}' class='post-thumbnail' alt=''>
                    </a>
                </div>
                <div class='col-md-8 col-sm-12 pt-1 pb-1'>
                    <div>
                        <a href='posts.php?tag={$tag_id}'>{$tags[$tag_id]}</a>
                        <h4 class='display-5 mt-2'>{$title}</h4>
                        <p class='mb-2'><i class='mr-2'>{$date}</i>|<span class='ml-2'>{$html}</span></p>
                        <p>{$body}</p>
                        <a class='btn btn-outline-info' href='post.php?id={$id}'>Continue Reading...</a>
                    </div>
                </div>
            </div>";
        }
    }

    function getProfile($GET, $conn) {
        //If defined ID, get profile from ID
        if (isset($GET['id'])) {
            $id = $GET['id'];
        } else {
            $id = $_SESSION['user_id'];
        }
        return getProfileFromID($id, $conn);
    }

    function getPosts($GET, $conn) {
        //If defined ID, get profile from ID
        if (isset($GET['id'])) {
            $id = $GET['id'];
        } else {
            $id = $_SESSION['user_id'];
        }
        return getPostsFromID($id, $conn);
    }