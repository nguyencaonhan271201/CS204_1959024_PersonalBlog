<?php
    function outputPosts($posts, $tags, $start, $end) {
        for ($i = $start; $i <= $end; $i++) {
            $title = filter_var($posts[$i]['title'], FILTER_SANITIZE_STRING);
            $body = filter_var(substr($posts[$i]['content'], 0, 100), FILTER_SANITIZE_STRING) . '...';
            $author_id = $posts[$i]['author'];
            $author_display = $posts[$i]['display_name'];
            $tag_id = $posts[$i]['tag'];
            $date = date_format(date_create($posts[$i]['date_posted']), "d/m/Y H:m:s");
            $id = $posts[$i]['ID'];
            $image = filter_var($posts[$i]['cover'], FILTER_SANITIZE_STRING);
            $html = outputHTMLStars($posts[$i]);
            echo "<div class='post mb-4 row'>
                <div class='col-lg-5 col-md-12 pt-1 pr-2 pl-2'>
                    <a href='post.php?id={$id}'><img src='{$image}' class='post-thumbnail' alt=''>
                    </a>
                </div>
                <div class='col-lg-7 col-md-12 pt-1 pb-1'>
                    <div>
                        <a href='posts.php?tag={$tag_id}'>{$tags[$tag_id]}</a>
                        <h4 class='display-5 mt-2'>{$title}</h4>
                        <p class='font-italic mb-2'>{$date} by <a href='profile.php?id={$author_id}'>{$author_display}</a></p>
                        <span class='mb-2'>{$html}</span>
                        <p class='mb-1 mt-2'>{$body}</p>
                        <a class='btn btn-outline-info' href='post.php?id={$id}'>Continue Reading...</a>
                    </div>
                </div>
            </div>";
        }
    }

    function outputPaginationDescription($posts, $start, $end, $post_per_page) {
        $num_of_posts = count($posts);
        $num_of_pages = ceil($num_of_posts / $post_per_page); 
        $real_start = $start + 1;
        $real_end = $end + 1;
        echo "<div class='mb-1 d-flex align-items-center' id='php-pagination'>
            <p class='m-0'>Showing results {$real_start} - {$real_end} / {$num_of_posts}</p>
        </div>";
        return $num_of_pages;
    }

    function outputPagination($current_page, $num_of_pages, $get_tag) {
        for ($i = 1; $i <= $num_of_pages; $i++) {
            $additional_class = $i == $current_page? "active" : "";
            echo "<li class='page-item {$additional_class}' onclick='updatePagination({$i}, \"{$get_tag}\")'>
                <a class='page-link' href='#'>$i</a>
            </li>";
        }
    }