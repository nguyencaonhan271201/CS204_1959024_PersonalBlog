<?php 
    //Handle connection to database
    include "./func/db.php";
    header('Content-Type: text/html; charset=utf-8');

    session_start();
    if(!isset($_SESSION['signed_in'])) {
        $_SESSION['signed_in'] = false;
    }

    $site_name = basename($_SERVER['PHP_SELF'], ".php");

    $header_tags = [ "index" => "Home", "post" => "Post", "posts" => "Posts", "signin" => "Sign In", "signup" => "Sign Up", "logout" => "Log out",
                    "create" => "Create a post", "edit" => "Edit a post", "delete" => "Delete a post", "edit_profile" => "Edit profile",
                    "profile" => "Profile", "admin" => "Management"];

    $tags = [];
    $tags_desc = [];

    //Constant list of available tags for blog
    if (count($tags) == 0) {
        getTags($conn, $tags, $tags_desc);
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>NCN Blog | <?php echo $header_tags[$site_name];?></title>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

        <!-- Additional CSS -->
        <link rel="stylesheet" href="./includes/css/style.css">

        <?php if($site_name == "create" || $site_name == "edit"): ?>       
        <!-- Text editor library -->
        <script src="https://cdn.ckeditor.com/ckeditor5/27.1.0/classic/ckeditor.js"></script>
        <?php endif; ?>

        <?php if($site_name == "posts" || $site_name == "index"): ?>
        <link rel="stylesheet" href="./includes/css/tooltip.css">
        <?php endif; ?>

        <!-- Icon -->
        <link rel="icon" href="./assets/icon.ico" type="image/x-icon">
    </head>
    <body>
        <!--Navbar-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <img src="./assets/logo.png" width="auto" height="45" class="d-inline-block align-top" alt="">
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <div id="nav-icon3">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item <?php echo $site_name == 'index'? 'active' : '';?>">
                            <a class="nav-link" href="index.php"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
                        </li>
                        <li class="nav-item <?php echo $site_name == 'posts'? 'active' : '';?>">
                            <a class="nav-link" href="posts.php"><i class="fas fa-blog"></i> Posts</a>
                        </li>
                    </ul>
                    <?php if($site_name != "signin" && $site_name != "signup"): ?>
                        <ul class="navbar-nav float-lg-right">
                            <?php if(!$_SESSION['signed_in']): ?>
                                <li class="nav-item active">
                                    <a class="nav-link sign-in" href="signin.php"><i class="fa fa-sign-in-alt" aria-hidden="true"></i> Sign in</a>
                                </li>
                                <li class="nav-item active">
                                    <a class="nav-link join-us" href="signup.php"><i class="fa fa-user-plus" aria-hidden="true"></i> Join us</a>
                                </li>
                            <?php elseif($_SESSION['signed_in']): ?>
                                <li class="nav-item active dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <img class="rounded-circle d-inline-block profile-img" src="<?php echo $_SESSION['profile_img'];?>"> 
                                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="posts.php?user=<?php echo $_SESSION['user_id']; ?>">Your posts</a>
                                        <a class="dropdown-item" href="create.php">Create a post</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="profile.php">Profile</a>
                                        <a class="dropdown-item" href="edit_profile.php">Edit profile</a>
                                        <?php if($_SESSION['user_role'] == 1): ?>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="admin.php">Management</a>
                                        <?php endif; ?>
                                    </div>
                                </li>
                                <li class="nav-item active d-flex align-items-center">
                                    <a class="nav-link" href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
                                </li>
                            <?php endif;?>
                        </ul>
                    <?php endif;?>
                </div>
            </div>
        </nav>

<script>
    let prevScrollpos = window.pageYOffset;
    window.onscroll = function() {
        let currentScrollPos = window.pageYOffset;
        if (prevScrollpos > currentScrollPos) {
            document.querySelector(".navbar").style.top="-1px";
        } else {
            document.querySelector(".navbar").style.top="-20vh";
        }
        prevScrollpos = currentScrollPos;
    }
</script>

<script type="text/javascript" src="includes/js/header.js"></script>