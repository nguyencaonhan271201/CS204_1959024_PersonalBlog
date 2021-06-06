<?php
    include "includes/header.php";
    include "func/indexFunctions.php";
    //Get lists of newest posts to display in homepage
    $posts = getAllPosts(13, $conn);    
?>

<div id="main-container" class="container">
    <!--List of tags-->
    <div class="tags d-lg-flex justify-content-center">
        <ul class="navbar-nav navbar-expand-sm justify-content-center">
            <li class="title nav-link mr-4">Tags:</li>
            <?php 
                foreach($tags as $id => $tag_name) {
                    $tooltip_titles = $tags_desc[$id] . '...';
                    echo "<li class='nav-link mr-4 tag' data-tooltip='{$tooltip_titles}'
                    data-tooltip-location='bottom'><a href = 'posts.php?tag={$id}'>{$tag_name}</a></li>";
                }
            ?>
        </ul>
    </div>

    <!--Alert display-->
    <?php if(isset($_GET['error_create'])):?>
        <div class="alert alert-danger" role="alert">
            You are entrying page that is only accessible after signed in.
        </div>
    <?php elseif(isset($_GET['error_param'])): ?>
        <div class="alert alert-danger" role="alert">
            You are entrying page with wrong or missing parameters.
        </div>
    <?php elseif(isset($_GET['error_delete_right'])): ?>
        <div class="alert alert-danger" role="alert">
            You cannot delete others' posts.
        </div>
    <?php elseif(isset($_GET['error_delete'])): ?>
        <div class="alert alert-danger" role="alert">
            There was a problem occured. Please try again!
        </div>
    <?php elseif(isset($_GET['deleted'])): ?>
        <div class="alert alert-success" role="alert">
            Your post has been deleted successfully!
        </div>
    <?php elseif(isset($_GET['edited_profile'])): ?>
        <div class="alert alert-success" role="alert">
            Your profile has been edited successfully!
        </div>
    <?php elseif(isset($_GET['error_admin'])): ?>
        <div class="alert alert-danger" role="alert">
            Admin only! Users are not allowed to enter!
        </div>
    <?php endif;?>

    <?php if(!empty($posts)): ?>
    <div class="carousel-container" style="background: transparent;">
            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <?php 
                    outputCarouselItems($posts, $tags);
                ?>
            </div>
            <div class="carousel-controls row">
                <div class="col-5">
                    <a class="carousel-control-prev d-flex align-items-center justify-content-center" href="#carouselExampleControls" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                </div>
                <div class="col-5 offset-1">
                    <a class="carousel-control-next d-flex align-items-center justify-content-center" href="#carouselExampleControls" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
        </div>
        
    </div>
    <?php endif; ?>
    <!-- Only display 2 next nearest posts if there are more than 3 available -->
    <?php if(!empty($posts) && count($posts) > 3): ?>
    <div class="row">
        <?php 
            outputSemiCards($posts, $tags);
        ?>
    </div>
    <?php endif; ?>
    <?php if(!empty($posts)):?>
    <h2>New posts</h2>
    <hr class="my-3 new-post-hr">
    <div class="row">
        <div class="col-md-12 col-sm-12 offset-0 order-md-0 order-2 mb-4">
            <?php 
                outputPosts($posts, $tags);
            ?>
        </div>
    </div>

    <?php else: ?>
    <div class="container d-flex justify-content-center align-items-center" style = "height: 65vh;">
        <h2 class="display-5 text-center">Errors occured: Posts cannot be loaded.</h2>
    </div>

    <?php endif; ?>
</div>

<script>
    let alert_box = document.querySelector('.alert');
    //Display alert box only for 5 seconds
    if (alert_box != undefined) {
        setTimeout(() => {
            alert_box.classList.add("shrinkStartAlert");
            setTimeout(function(){
                alert_box.classList.add("shrinkFinish");
                setTimeout(function(){
                    alert_box.style.display = 'none';
                }, 800);
            }, 100);
        }, 5000);
    }
</script>
        
<?php
    include "includes/footer.php";
?> 

<script type="text/javascript" src="includes/js/index.js"></script>