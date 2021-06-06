<?php
    include "includes/header.php";  
    include "func/accountFunctions.php";

    $errors = [];

    checkSignUp($_POST, $_FILES, $conn, $errors);
?>

<div id="main-container" class="container mt-5">
    <h1 class="text-center">Sign Up</h1>
    <p class="text-center">Join us and write posts to NCN Blog</p>
    <?php if(!empty($errors) && !isset($errors["execute_err"])):?>
        <div class="row">
            <div class="col-md-8 col-sm-12 col-12 offset-md-2 alert alert-danger mt-3" role="alert">
                Information for your new account is not valid. Please try again.
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
            <form method="post" action="signup.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input class="form-control data-tooltip" type="text" name="username" placeholder="Username" 
                    value="<?php
                        if (isset($_POST['username'])) {
                            echo $_POST['username'];
                        }
                    ?>" data-toggle="tooltip" data-placement="right" title="Username must be at least 6 characters.">
                    <p class="error"><?php if(isset($errors['username'])) {echo $errors['username'];}?></p>
                </div>
                <div class="form-group">
                    <label for="display">Display Name</label>
                    <input class="form-control" type="text" name="display" placeholder="Display Name" value="<?php
                        if (isset($_POST['display'])) {
                            echo $_POST['display'];
                        }
                    ?>" data-toggle="tooltip" data-placement="right" title="Display name must not be a blank string.">
                    <p class="error"><?php if(isset($errors['display'])) {echo $errors['display'];}?></p>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input class="form-control" type="email" name="email" placeholder="Email" value="<?php
                        if (isset($_POST['email'])) {
                            echo $_POST['email'];
                        }
                    ?>">
                    <p class="error"><?php if(isset($errors['email'])) {echo $errors['email'];}?></p>
                </div>
                <div class="form-group">
                    <label for="password1">Password</label>
                    <input class="form-control" type="password" name="password1" placeholder="Password"
                    data-toggle="tooltip" data-placement="right" title="Password length must be at least 6 characters.">
                    <p class="error"><?php if(isset($errors['password1'])) {echo $errors['password1'];}?></p>
                </div>
                <div class="form-group">
                    <label for="password2">Confirm Password</label>
                    <input class="form-control" type="password" name="password2" placeholder="Confirm Password">
                    <p class="error"><?php if(isset($errors['password2'])) {echo $errors['password2'];}?></p>
                </div>
                <div class="form-group">
                    <label for="image">Profile Image <em>(Optional)</em></label>
                    <input id="image-choose" class="form-control" type="file" name="image" accept="image/*" onchange="loadFile(event)">
                    <small class="form-text text-muted">
                        A default profile image will be used in case you do not use your own. Maximum size allowed: 5MB
                    </small>
                    <p class="error"><?php if(isset($errors['profile'])) {echo $errors['profile'];}?></p>
                    <div class="review-group mt-2 text-center d-none">
                        <img id="review-image">
                    </div>
                </div>
                <button class="btn btn-dark btn-block" role="submit" name="submit">Create Account</button>
            </form>
        </div>
    </div>
</div>

<script src="includes/js/loadFile.js"></script>

<?php
    include "includes/footer.php"; 
?>

<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>