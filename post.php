<?php
    include "includes/header.php";
    include "func/starFunctions.php";
    include "func/reactionFunctions.php";

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        //Get lists of newest posts to display in homepage
        $post = getPost($id, $conn);
        $star_rates_info = getStarRate($id, $conn);
        $reactions_info = getReactions($id, $conn);
    }
?>

<div id="main-container" class="container">
    <?php if(isset($post) && $post != false): ?>
    <div class="post-cover-img-container container-fluid text-center mt-3">
        <img id="post-cover-image" class="post-cover-img" src="<?php echo $post['cover'];?>">
    </div>
    <div class="post-title shadow pt-4 pb-4">
        <a href="posts.php?tag=<?php echo $post['tag']?>"><?php echo $tags[$post['tag']]?></a>
        <h1 class="display-5 mt-2"><?php echo filter_var($post['title'], FILTER_SANITIZE_STRING);?></h1>
        <hr class="my-2">
        <p class="font-italic mb-1">Written by:
            <a href="profile.php?id=<?php echo $post['author']?>">
                <?php echo $post['display_name'];?>
            </a>
        </p>
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="post-times">
                    <p class="text-left font-italic mt-1 mb-1">Posted: <?php 
                            $date = date_create($post['date_posted']);
                            echo date_format($date, "d/m/Y H:i:s");
                    ?></p>
                    <p class="text-left font-italic mb-0">Last Modified: <?php 
                            $modified = date_create($post['date_modified']);
                            echo date_format($modified, "d/m/Y H:i:s");
                    ?></p>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <p class="text-right mb-1">
                    <span id="star-vote-count">0 votes</span>
                    <i id="star1" class="star far fa-star"></i>
                    <i id="star2" class="star far fa-star"></i>
                    <i id="star3" class="star far fa-star"></i>
                    <i id="star4" class="star far fa-star"></i>
                    <i id="star5" class="star far fa-star"></i>
                </p>
                <?php if(!$_SESSION['signed_in']): ?>
                    <p class="text-right">Please login to rate.</p>
                <?php endif; ?>
            
                <?php if(
                    //Author of the post
                    (isset($_SESSION['user_id']) && ($post['author'] == $_SESSION['user_id']))
                    ||
                    //Admin role
                    (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 1))
                ):?>
                    <div class="mt-2 post-btns text-right">
                        <a class="btn btn-warning mr-1" href="edit.php?id=<?php echo $id; ?>" role="button">Edit</a>
                        <a class="btn btn-danger ml-1" role="button" data-toggle="modal" data-target="#exampleModal">Delete</a>

                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-left">
                                Are you sure want to delete this post?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <a role="button" class="btn btn-danger" href="delete.php?id=<?php echo $id; ?>">Confirm</a>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>
                <?php endif;?>
            </div>
        </div>
    </div>
    <div class="post-content mb-3 mt-0">
        <?php echo $post['content'];?>
        <div class="text-right reaction-area mt-4">
            <p>
                <i id="like" class="reaction mr-2 far fa-thumbs-up"></i>
                <span class="font-weight-bold mr-2 " id="like-count">0</span>
                <i id="dislike" class="reaction mr-2 far fa-thumbs-down"></i>
                <span class="font-weight-bold" id="dislike-count">0</span>
            </p>
            <?php if(!$_SESSION['signed_in']): ?>
                <p class="text-right">Please login to react.</p>
            <?php endif; ?>
        </div>
    </div>
    <hr>
    <div class="comment-section">
    <h4>Comments (<span id="comment-count">0</span>)</h4>
        <div class="row mt-4">
            <div class="col-md-8 offset-md-2 col-sm-12 offset-0">
                <?php if(!$_SESSION['signed_in']):?>
                <p class="text-center mb-4">Please login to comment.</p>
                <?php else: ?>
                <form method="post" class="mb-4" id="comment-form">
                    <div class="input-group">
                        <input id="comment-inp" class="form-control" type="text" name="comment" placeholder="Write comment...">
                        <button type="submit" name="submit" class="ml-1 btn btn-info"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
                    </div>
                    <p class="error" id="comment-error"></p>
                </form>
                <?php endif; ?>
                <div id="comments">
                    <!-- Output comments -->
                </div>
            </div>
        </div>
        <!-- Comment form -->
        
        
    </div>
    <?php else: ?>
    <div class="container d-flex justify-content-center align-items-center" style = "height: 65vh;">
        <h2 class="display-5 text-center">Errors occured: Posts cannot be loaded.</h2>
    </div>
    <?php endif; ?>
</div>

<!--Footer-->
<?php
    include "includes/footer.php";  
?>

<!--Javascript-->
<script src="includes/js/star.js" type="text/javascript"></script>
<script src="includes/js/reaction.js" type="text/javascript"></script>
<script src="includes/js/comment.js" type="text/javascript"></script>
<script>
    //By default
    (() => {
        document.querySelector('footer').classList.add('fixed-bottom');
    })();

    let post_image = document.querySelector('#post-cover-image');
    post_image.onload = function() {
        check();
    }
</script>

<!--JS for communicating between PHP and JS-->
<?php 
    //Get value from database and call JS functions to initiate state for star rating
    $signed_in = $_SESSION['signed_in'] ? "true" : "false";
    $is_voted = $star_rates_info['is_voted']? "true" : "false";
    $id = intval($id);
    echo "<script>
        initializeStar({$star_rates_info['avg']}, {$star_rates_info['total_votes']}, {$id}, {$is_voted}, {$signed_in});
    </script>";

    //Get value from database and call JS functions to initiate state for reaction
    $liked = $reactions_info['liked']? "true" : "false";
    $disliked = $reactions_info['disliked']? "true" : "false";
    $up_vote = $reactions_info['up'];
    $down_vote = $reactions_info['down'];
    echo "<script>
        initiateReactionState({$up_vote}, {$down_vote}, {$liked}, {$disliked}, {$signed_in}, {$id});
    </script>";

    //Initiate session info for comments
    if(!$_SESSION['signed_in']) {
        echo "<script>
            initiateSessionInfo(-1, '', '', -1, {$id});
        </script>";
    } else {
        $user_id = $_SESSION['user_id'];
        $display = $_SESSION['name'];
        $img = $_SESSION['profile_img'];
        $role = $_SESSION['user_role'];
        echo "<script>
            initiateSessionInfo({$user_id}, '{$display}', '{$img}', {$role}, {$id}, {$signed_in});
        </script>";
    }
?>