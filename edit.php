<?php
    include "includes/header.php";
    include "func/postFunctions.php";

    if (!$_SESSION['signed_in']) {
        if (headers_sent()) {
            echo "<script>window.location.href = 'index.php?error_create=true';</script>";
        }
        else{
            header("Location: index.php?error_create=true");
        }
    } elseif (!isset($_GET['id']) && !isset($_POST['update'])) {
        if (headers_sent()) {
            echo "<script>window.location.href = 'index.php?error_param=true';</script>";
        }
        else{
            header("Location: index.php?error_param=true");
        }
    }


    $image = ""; //Keep track of the old cover image in case the author doesn't edit the cover image
    $errors = [];
    $info = []; //To gather info back and display inside inputs

    $info = checkEditPost($_POST, $_GET, $_FILES, $image, $errors, $conn);
?>

<div id="main-container" class="container mt-5">
    <?php if(!$info['post_found']): ?>
        <div class="container d-flex justify-content-center align-items-center" style = "height: 65vh;">
            <h2 class="display-5 text-center">Errors occured: Post not found.</h2>
        </div>
    <?php elseif(!$info['is_author']): ?>
        <div class="container d-flex justify-content-center align-items-center" style = "height: 65vh;">
            <div>
                <h2 class="display-5 text-center">You are not the author of this post.</h2>
                <p class="text-center">Only author or admins can edit the post.</p>
            </div>
        </div>
    <?php else: ?>
        <h1 class="text-center">Edit your post</h1>
        <?php if(isset($errors['empty'])):?>
        <div class="row">
            <div class="col-md-8 col-sm-12 col-12 offset-md-2 alert alert-danger mt-3" role="alert">
                Please fill out all fields!
            </div>
        </div>
        <?php elseif(isset($errors['execute_err'])): ?>
            <div class="row">
                <div class="col-md-8 col-sm-12 col-12 offset-md-2 alert alert-danger mt-3" role="alert">
                    There was a problem occured. Please try again!
                </div>
            </div>
        <?php elseif(isset($errors['image'])): ?>
            <div class="row">
                <div class="col-md-8 col-sm-12 col-12 offset-md-2 alert alert-danger mt-3" role="alert">
                    Cover image is invalid. Please try a different one!
                </div>
            </div>
        <?php endif; ?>
        <div class="row mb-5 p-1">
            <div class="col-md-8 col-sm-12 col-12 offset-md-2 grey-form">
                <form method="post" action="edit.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="image">Post Cover Image <em>(Optional)</em></label>
                        <input id="image-choose" class="form-control" type="file" name="image" accept="image/*" onchange="loadFile(event)">
                        <small class="form-text text-muted">
                            A default profile image will be used in case you do not use your own. Maximum size allowed: 5MB
                        </small>
                        <div class="post-review-group mt-2 text-center">
                        <?php if(!isset($image) || $image == ""): ?>
                            <img id="post-review-image" src="assets/images/posts/default.png">
                        <?php else: ?>
                            <img id="post-review-image" src="<?php echo $image; ?>">
                        <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tag">Tag</label>
                        <select class="form-control form-control-md" name="tag">
                            <?php 
                                foreach($tags as $id => $tag_name) {
                                    $is_selected = $info['tag'] == $id? "selected": "";
                                    echo "<option value={$id} {$is_selected}>{$tag_name}</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="username">Post title</label>
                        <input class="form-control" type="text" name="title" placeholder="Title" value="<?php
                            if (isset($info['title'])) {
                                echo $info['title'];
                            } 
                        ?>">
                    </div>
                    <div class="form-group">
                        <label for="content">Post content</label>
                        <textarea class="form-control d-none" id="editorjs" type="text" name="content" placeholder="Content">
                        <?php
                            if (isset($info['content'])) {
                                echo $info['content'];
                            } 
                        ?>
                        </textarea>
                        <small class="form-text text-muted">
                            Note: Because of some limitations, in case of adding an image to the content of the post, you can add within
                            this structure: <br>&lt;img src='{image-public-URL}'/&gt; <br> in a whole new line. At the first rendering after
                            finishing this process, the above line will be displayed as plain-text. Click Edit and then Update Post
                            again then the image will be shown in the post's content in post page.
                        </small>
                        <!-- <div id="editorjs"></div> -->
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-12 col-12">
                            <button id="create" class="btn btn-info btn-block" role="submit" name="update" value="<?php
                                if (isset($info['get_id'])) {
                                    echo (string)$info['get_id'] . "|" . $image;
                                }
                            ?>">Update Post</button>
                        </div>
                        <div class="col-md-6 col-sm-12 col-12">
                            <a class="btn btn-danger btn-block" role="button" href="post.php?id=<?php if (isset($info['get_id'])) {
                                    echo $info['get_id'];
                            }?>">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="includes/js/loadFile.js"></script>
<script>
    ClassicEditor
        .create( document.querySelector('#editorjs') )
        .then( editor => {
            console.log( editor );
        } )
</script>
<script type="text/javascript" src="includes/js/formAnimation.js"></script>
<?php
    include "includes/footer.php";  

    if (isset($_POST['submit'])) {
        echo "<script>
            formAnimationCheck();
        </script>";
    }
?>

<?php if(!$info['post_found'] || !$info['is_author']): ?>
    <script>
        if (!document.querySelector('footer').classList.contains('fixed-bottom')) {
            document.querySelector('footer').classList.add('fixed-bottom');
        }
    </script>
<?php else: ?>
    <script>
        if (document.querySelector('footer').classList.contains('fixed-bottom')) {
            document.querySelector('footer').classList.remove('fixed-bottom');
        }
    </script>
<?php endif; ?>
