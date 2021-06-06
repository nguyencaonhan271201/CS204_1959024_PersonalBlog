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
    }

    $errors = [];
    checkCreatePost($_POST, $_FILES, $errors, $conn);
?>

<div id="main-container" class="container mt-5">
    <h1 class="text-center">Create your post</h1>
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
    <div class="row mb-5 mt-3 p-1">
        <div class="col-md-8 col-sm-12 col-12 offset-md-2 grey-form">
            <form method="post" action="create.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="image">Post Cover Image <em>(Optional)</em></label>
                    <input id="image-choose" class="form-control" type="file" name="image" accept="image/*" onchange="loadFile(event)">
                    <small class="form-text text-muted">
                        A default profile image will be used in case you do not use your own. Maximum size allowed: 5MB
                    </small>
                    <p class="error"><?php if(isset($errors['cover'])) {echo $errors['cover'];}?></p>
                    <div class="review-group mt-2 text-center d-none">
                        <img id="post-review-image">
                    </div>
                </div>
                <div class="form-group">
                    <label for="tag">Tag</label>
                    <select class="form-control form-control-md" name="tag">
                        <?php 
                            foreach($tags as $id => $tag_name) {
                                $is_selected = (isset($_POST['tag']) && $_POST['tag'] == $id)? "selected": "";
                                echo "<option value={$id} {$is_selected}>{$tag_name}</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="username">Post title</label>
                    <input class="form-control" type="text" name="title" placeholder="Title" value="<?php
                        if (isset($_POST['title'])) {
                            echo $_POST['title'];
                        }
                    ?>">
                </div>
                <div class="form-group">
                    <label for="content">Post content</label>
                    <textarea class="form-control d-none" id="editorjs" type="text" name="content" placeholder="Content">
                    <?php
                        if (isset($_POST['content'])) {
                            echo $_POST['content'];
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
                <button id="create" class="btn btn-dark btn-block" role="submit" name="create">Create Post</button>
            </form>
        </div>
    </div>
</div>

<script>
    ClassicEditor
        .create( document.querySelector('#editorjs'))
        .then( editor => {
            console.log( editor );
        } )
        .catch( error => {
            console.error( error );
        } ); 
</script>
<script src="includes/js/loadFile.js"></script>
<script type="text/javascript" src="includes/js/formAnimation.js"></script>

<?php
    include "includes/footer.php";  

    if (isset($_POST['submit'])) {
        echo "<script>
            formAnimationCheck();
        </script>";
    }
?>