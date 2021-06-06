<?php
    include "includes/header.php";  
    include "func/accountFunctions.php";

    if (!$_SESSION['signed_in']) {
        if (headers_sent()) {
            echo "<script>window.location.href = 'index.php?error_create=true';</script>";
        }
        else{
            header("Location: index.php?error_create=true");
        }
    }

    $errors = [];
    $image = ""; //Variable to keep track of the old profile image in case the user do not update profile image
    $info = []; //To gather info back and display inside inputs

    $info = checkEditProfile($_POST, $_FILES, $errors, $conn, $image);
?>

<div id="main-container" class="container mt-5">
<h1 class="text-center">Edit Profile</h1>
    <?php if(!empty($errors) && !isset($errors["execute_err"])):?>
        <div class="row">
            <div class="col-md-8 col-sm-12 col-12 offset-md-2 alert alert-danger mt-3" role="alert">
                Information for your account is not valid. Please try again.
            </div>
        </div>
        <?php elseif(isset($errors["execute_err"])): ?>
        <div class="row">
            <div class="col-md-8 col-sm-12 col-12 offset-md-2 alert alert-danger mt-3" role="alert">
                <?php echo $errors["execute_err"]; ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="row mb-5 p-1">
        <div class="col-md-8 col-sm-12 col-12 offset-md-2 grey-form">
            <form method="post" action="edit_profile.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input class="form-control" type="text" name="username" placeholder="Username" value="<?php
                        if (isset($info['username'])) {
                            echo $info['username'];
                        }
                    ?>" readonly>
                    <p class="error"><?php if(isset($errors['username'])) {echo $errors['username'];}?></p>
                </div>
                <div class="form-group">
                    <label for="display">Display Name</label>
                    <input class="form-control" type="text" name="display" placeholder="Display Name" value="<?php
                        if (isset($info['display'])) {
                            echo $info['display'];
                        }
                    ?>">
                    <p class="error"><?php if(isset($errors['display'])) {echo $errors['display'];}?></p>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input class="form-control" type="email" name="email" placeholder="Email" value="<?php
                        if (isset($info['email'])) {
                            echo $info['email'];
                        }
                    ?>">
                    <p class="error"><?php if(isset($errors['email'])) {echo $errors['email'];}?></p>
                </div>
                <div class="form-group">
                    <label for="password1">Password</label>
                    <div class="">
                        <input type="checkbox" value="" id="defaultCheck1" onchange="changePasswordTicked(event);">
                        <label for="defaultCheck1"> Change password</label>
                    </div>
                    <input class="form-control" type="password" name="password1" id="password1" placeholder="Password" readonly>
                    <p class="error"><?php if(isset($errors['password1'])) {echo $errors['password1'];}?></p>
                </div>
                <div class="form-group">
                    <label for="password2">Confirm Password</label>
                    <input class="form-control" type="password" name="password2" id="password2" placeholder="Confirm Password" readonly>
                    <p class="error"><?php if(isset($errors['password2'])) {echo $errors['password2'];}?></p>
                </div>
                <div class="form-group">
                    <label for="image">Profile Image <em>(optional)</em></label>
                    <input id="image-choose" class="form-control" type="file" name="image" placeholder="Confirm Password" 
                    accept="image/*" onchange="loadFile(event)">
                    <small class="form-text text-muted">
                        A default profile image will be used in case you do not use your own. Maximum size allowed: 5MB
                    </small>
                    <div class="review-group mt-2 text-center">
                        <?php if(!isset($image) || $image == ""): ?>
                            <img id="review-image" src="assets/images/users/default.jpg">
                        <?php else: ?>
                            <img id="review-image" src="<?php echo $image; ?>">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12 col-12">
                        <button id="create" class="btn btn-info btn-block" role="submit" name="update" value="<?php
                            if (isset($image)) {
                                echo $image;
                            }
                        ?>">Update Profile</button>
                    </div>
                    <div class="col-md-6 col-sm-12 col-12">
                        <a class="btn btn-danger btn-block" role="button" href="index.php">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="includes/js/loadFile.js"></script>
<script>
    function changePasswordTicked(e) {
        let passwordBox = document.querySelector("#password1");
        let passwordConfirm = document.querySelector("#password2");
        passwordBox.value = "";
        passwordConfirm.value = "";
        passwordBox.readOnly = !e.target.checked;
        passwordConfirm.readOnly = !e.target.checked;
    }
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