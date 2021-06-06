<?php 
    include("includes/header.php");
    include("func/profileFunctions.php");

    //Get profile of user by $user_id
    $profile = getProfile($_GET, $conn);
    //Get posts of user
    $posts = getPosts($_GET, $conn);
?>

<div id="main-container" class="container mt-4 mb-4">
    <?php if($profile == false): ?>
    <div class="container d-flex justify-content-center align-items-center" style = "height: 65vh;">
        <h2 class="display-5 text-center">Errors occured: Profile cannot be found.</h2>
    </div>
    <?php else: ?>
        <?php if(isset($_SESSION['user_id']) && $profile['ID'] == $_SESSION['user_id']): ?>
            <h1 class="text-center">Your profile</h1>
        <?php else: ?>
            <h1 class="text-center">Profile</h1>
        <?php endif; ?>
    <div class="row mt-3">
        <div class="col-md-4 offset-md-0 col-8 offset-2 p-4 d-flex align-items-center">
            <img src="<?php echo $profile['user_img'];?>" alt="" class="rounded-circle profile-img-large">
        </div>
        <div class="profile-info col-md-8 offset-md-0 col-sm-8 offset-sm-2 p-4 d-flex align-items-center justify-content-start">
            <div class="profile-info-content">
                <h1><?php echo $profile['display_name']; ?></h1>
                <h4 class="font-weight-light font-italic">Username: <?php echo $profile['username']; ?></h4>
                <h4 class="font-weight-light font-italic">Email: <?php echo $profile['email']; ?></h4>
                <h4 class="font-weight-light font-italic">Account created: <?php echo date_format(date_create($profile['date_created']), 'd/m/Y H:i:s');?></h4>
                <h4 class="font-weight-light font-italic">Account Role: <?php echo $profile['user_role'] == 2? "User" : 
                ($profile['username'] == 'admin'? "Senior Admin" : "Admin")?></h4>
                <?php if(isset($_SESSION['user_id']) && $profile['ID'] == $_SESSION['user_id']): ?>
                    <a href="edit_profile.php?id=<?php echo $_SESSION['user_id']?>" class="btn btn-info">Edit your profile</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <h2 class="text-right">Recent posts</h2>
    <hr class="my-3">
    <div class="row p-2">
        <div class="">
            <?php 
                if(!empty($posts)) {
                    outputPosts($posts, $tags);
                } else {
                    echo '<h2 class="display-5">No recent posts found</h2>';
                }
            ?>
        </div>
    </div>
        <?php if(!empty($posts)): ?>
            <div class="text-center">
                <a href="posts.php?user=<?php echo $profile['ID']?>" class="btn btn-info">See all posts</a>
            </div>
        <?php endif; ?>    
    <?php endif; ?>
</div>

<?php 
    include("includes/footer.php");
?>

<script>
    let profile_image = document.querySelector(".profile-img-large");
    let get_width = profile_image.width;
    profile_image.setAttribute("height", `${profile_image.width}px !important`);

    check();
    setTimeout(check, 1000);
</script>

<script type="text/javascript" src="includes/js/profile.js"></script>