<?php 
    include "includes/header.php";

    if(!$_SESSION['signed_in'] || $_SESSION['user_role'] != 1) {
        if (headers_sent()) {
            echo "<script>window.location.href = 'index.php?error_admin=true';</script>";
        }
        else{
            header("Location: index.php?error_admin=true");
        }
    }
?>

<div id="main-container" class="container-fluid mt-4 mb-4">
    <h1 class="text-center">Management</h1>
    <div class="row">
        <div class="offset-md-3 col-md-6 col-sm-12 btn-group align-items-center justify-content-center" role="group">
            <div class="btn-group" role="group">
                <button type="button" id="btn-users" class="btn btn-info" disabled>Users</button>
                <button type="button" id="btn-posts" class="btn btn-info">Posts</button>
            </div>
        </div>
    </div>
    <div class="mt-2 alert alert-success d-none" id="alert-success" role="alert" style="width: 80%; margin: 0 auto;">
        Process completed successfully!
    </div>
    <div class="mt-2 alert alert-danger d-none" id="alert-fail" role="alert" style="width: 80%; margin: 0 auto;">
        Error occured!
    </div>
    <table class="table table-dark table-striped table-bordered table-hover table-responsive-md mt-3" style="width: 100%" id="management">
        <thead class="text-center">
            <tr>
                            
            </tr>
        </thead>
        <tbody>
                        
        </tbody>
    </table>   
</div>

<?php 
    include "includes/footer.php";
?>

<script type="text/javascript" src="includes/js/management.js"></script>