<?php 
    function outputPosts($posts, $tags) {
        for ($i = 3; $i < count($posts); $i++) {
            $title = filter_var($posts[$i]['title'], FILTER_SANITIZE_STRING);
            $body = filter_var(substr($posts[$i]['content'], 0, 100), FILTER_SANITIZE_STRING) . '...';
            $author_id = $posts[$i]['author'];
            $author_display = $posts[$i]['display_name'];
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
                        <p class='font-italic mb-2'>{$date} by <a href='profile.php?id={$author_id}'>{$author_display}</a></p>
                        <span class='mb-2'>{$html}</span>
                        <p class='mb-1 mt-2'>{$body}</p>
                        <a class='btn btn-outline-info' href='post.php?id={$id}'>Continue Reading...</a>
                    </div>
                </div>
            </div>";
        }
    }

    function outputSemiCards($posts, $tags) {
        for ($i = 1; $i < min(3, count($posts)); $i++) {
            $cover = $posts[$i]['cover'];
            $tag_id = $posts[$i]['tag'];
            $tag_name = $tags[$tag_id];
            $title = filter_var($posts[$i]['title'], FILTER_SANITIZE_STRING);
            $author = $posts[$i]['author'];
            $display_name = $posts[$i]['display_name'];
            $ID = $posts[$i]['ID'];
            $date_posted = date_format(date_create($posts[$i]['date_posted']), 'd/m/Y H:i:s');
            $summary = filter_var(substr($posts[$i]['content'], 0, 100), FILTER_SANITIZE_STRING);

            $html = outputHTMLStars($posts[$i]);

            echo "
            <div class='col-md-6 col-sm-12 pr-md-4 pr-sm-3 mb-4'>
                <div class='card home-card' style='height: 100%;'>
                    <div class='card-body p-0'>
                        <a href='post.php?id={$ID}'><img class='card-img-top' src='{$cover}' alt=''></a>
                        <div class='p-3'>
                            <a href='posts.php?tag={$tag_id}'>{$tag_name}</a>
                            <h4 class='display-5 card-title mt-2'>{$title}</h4>
                            <h6 class='card-subtitle mb-2 text-muted'>
                                <a href='profile.php?id={$author}' class='mr-1'>
                                    <i>{$display_name}</i>
                                </a>
                                | <span class='ml-1'>{$html}</span>
                            </h6>
                            <p class='card-text'>{$summary}...</p>
                            <h6 class='card-subtitle mb-3 text-muted font-italic font-weight-light'>
                                {$date_posted}
                            </h6>
                            <a class='btn btn-info' href='post.php?id={$ID}'>Continue Reading...</a>
                        </div>
                    </div>
                </div>
            </div>";
        }
    }

    function outputCarouselItems($posts, $tags) {
        for ($i = 0; $i < min(5, count($posts)); $i++) {
            $cover = $posts[$i]['cover'];
            $tag_id = $posts[$i]['tag'];
            $tag_name = $tags[$tag_id];
            $title = filter_var($posts[$i]['title'], FILTER_SANITIZE_STRING);
            $author = $posts[$i]['author'];
            $display_name = $posts[$i]['display_name'];
            $ID = $posts[$i]['ID'];
            $date_posted = date_format(date_create($posts[$i]['date_posted']), 'd/m/Y H:i:s');
            $summary = filter_var(substr($posts[$i]['content'], 0, 100), FILTER_SANITIZE_STRING);
            $carouselClass = "";
            if ($i == 0) {
                $carouselClass = "active";
            }
            echo "<div class='carousel-item {$carouselClass} newest jumbotron rounded-4 p-0 text-black' style='background: 
                    url({$cover});
                    background-size: cover;
                    background-repeat: no-repeat;
                    background-position: center;
                '>
                <div class='p-lg-5 p-4 container' style='background: 
                    linear-gradient(
                    115deg,
                    rgba(255, 255, 255, 0.6),
                    rgba(255, 255, 255, 0.7)
                    )
                '>
                    <a href='posts.php?tag={$tag_id}'>${tag_name}</a>
                    <h1 class='display-5 mt-2'>{$title}</h1>
                    <p class='lead'>{$summary}...</p>
                    <hr class='my-2'>
                    <p class='font-italic mb-1'>
                        <a href='profile.php?id={$author}'>
                            {$display_name}
                        </a>
                    </p>
                    <p class='font-italic mt-1'><?php 
                        {$date_posted}
                    ?></p>
                    <a class='btn btn-info' href='post.php?id={$ID}'>Continue Reading...</a>
                </div>
            </div>";
        }
    }
?>