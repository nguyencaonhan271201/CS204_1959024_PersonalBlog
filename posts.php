<?php
    include "includes/header.php";
    include "func/postsFunctions.php";

    //Variables for pagination execution
    $get_tag = "";
    $current_page = 1;
    $post_per_page = 5;

    if (isset($_GET['page'])) {
        $current_page = $_GET['page'];
    }

    $start = $post_per_page * ($current_page - 1);
    $end = 0; //Will be calculated later

    $user = "";    

    $posts = getPostsByCondition($_POST, $_GET, $get_tag, $user, $conn);
?>

<div id="main-container" class="container">
    <h2 class="mt-4">Posts</h2>
    <?php if(isset($_GET['tag'])):?>
    <h5 class="font-weight-light php-search-post">Showing Posts of tag <span class="text-blue"><?php echo $tags[$_GET['tag']];?></span></h5>
    <?php elseif(isset($_GET['user'])):?>
    <h5 class="font-weight-light php-search-post">Showing Posts of user <span class="text-blue"><?php echo htmlspecialchars($user);?></span></h5>
    <?php elseif(isset($_POST['search'])):?>
    <h5 class="font-weight-light php-search-post">Showing Posts contain "<span class="text-blue"><?php echo htmlspecialchars($_POST['search']);?></span>"</h5>
    <?php endif; ?>
    <h5 class="font-weight-light" id="search-post-title">Showing Posts contain "<span id="search-post-value" class="text-blue">
    </span>"</h5>
    <hr class="my-3">
    <div class="row mb-5">
        <div class="col-lg-8 col-md-12 offset-0 order-lg-1 order-2">
            <?php if(empty($posts)): ?>
            <h2 class="display-5">No results found</h2>
            <?php endif; ?>
            <h2 class="display-5" id="js-none-found">No results found</h2>
            <!-- Case there are available posts -->
            <div id="js-posts"></div>
            <div id="php-posts">
                <?php 
                    $end = min(count($posts) - 1, $start + $post_per_page - 1);
                    outputPosts($posts, $tags, $start, $end);               
                ?>
            </div>
            <nav class="posts-pagination">
                <div class="pagination-desc">

                </div>
                <?php if(!empty($posts)) {
                    $num_of_pages = outputPaginationDescription($posts, $start, $end, $post_per_page);
                }
                ?>
                <ul class="pagination">
                    <?php 
                        if(!empty($posts)) {
                            outputPagination($current_page, $num_of_pages, $get_tag);
                        }
                    ?>
                </ul>
            </nav>
        </div>
        <div class="col-lg-4 col-md-10 offset-lg-0 offset-md-1 order-lg-2 order-1 d-flex justify-content-center">
            <div class="container-fluid mb-3">
                <div class="search-post p-3">
                    <h5>Search</h5>
                    <form method="post" action="posts.php" id="search-form">
                        <div class="form-group mb-2">
                            <input class="form-control" type="text" name="search" id="search-input">
                        </div>
                        <button type="submit" name="submit" class="btn btn-info btn-block mt-1 d-none"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
                    </form>
                </div>
                <div class="p-2 posts-tags">
                    <h5 class="mt-2">Filter by tag</h5>
                    <div class="tags d-flex justify-content-start">
                        <ul class="navbar-nav text-left inline">
                            <?php 
                                foreach($tags as $id => $tag_name) {
                                    $tooltip_titles = $tags_desc[$id] . '...';
                                    echo "<li class='nav-link mr-4' data-tooltip='{$tooltip_titles}'
                                    data-tooltip-location='right'>
                                    <a href = 'posts.php?tag={$id}'>{$tag_name}</a></li>";
                                }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="includes/js/posts.js"></script>
<script>
    function updatePagination(page, get_tag) {
        location.replace(`<?php echo $site_name?>.php?page=${page}${get_tag}`);
    }
</script>

<?php
    include "includes/footer.php";
?>