<?php
    session_start();
    include 'func/db.php';
    include 'func/postFunctions.php';
    if (!$_SESSION['signed_in']) {
        if (headers_sent()) {
            echo "<script>window.location.href = 'index.php?error_create=true';</script>";
        }
        else{
            header("Location: index.php?error_create=true");
        }
    } elseif (!isset($_GET['id'])) {
        if (headers_sent()) {
            echo "<script>window.location.href = 'index.php?error_param=true';</script>";
        }
        else{
            header("Location: index.php?error_param=true");
        }
    }

    checkDeletePost($_GET, $conn);
?>

