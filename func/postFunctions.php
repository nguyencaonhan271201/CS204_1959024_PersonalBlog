<?php
include "postValidate.php";

function checkCreatePost($POST, $FILE, &$errors, $conn) {
    if (isset($POST['create'])) {
        $tag = $POST['tag'];
        $title = $POST['title'];
        $content = $POST['content'];
        $author = $_SESSION['user_id'];
        $image_error = false;

        if($FILE['image']['tmp_name'] == '') {
            $image_url = "assets/images/posts/default.png";
        } else {
            $image_url = validatePostImage($FILE, $image_error);
        }

        if ($title == "" || $content == "") {
            $errors['empty'] = true;
        } elseif ($image_error) {
            $errors['image'] = true;
        } else {
            $stmt = insertPost($title, $content, $author, $tag, $image_url, $conn);
            if ($stmt->affected_rows == 1) {
                if (headers_sent()) {
                    echo "<script>window.location.href = 'post.php?id={$stmt->insert_id}';</script>";
                }
                else{
                    header("Location: post.php?id={$stmt->insert_id}");
                }
            } else {
                $errors['execute_arr'] = true;
            }
        }
    }
}

function checkDeletePost($GET, $conn) {
    if (isset($GET['id'])) {
        $id = $GET['id'];
        $post = getPost($id, $conn);
        if ($post != false) {
            if ($_SESSION['user_id'] == $post['author'] || $_SESSION['user_role'] == 1) {
                $stmt = deletePost($id, $conn);
                if ($stmt->affected_rows == 1) {
                    if (headers_sent()) {
                        echo "<script>window.location.href = 'index.php?deleted=true';</script>";
                    }
                    else{
                        header("Location: index.php?deleted=true");
                    }
                } else {
                    if (headers_sent()) {
                        echo "<script>window.location.href = 'delete.php?error_delete_right=true';</script>";
                    }
                    else{
                        header("Location: delete.php?error_delete_right=true");
                    }    
                }
            } else {
                //Not the author of the post or admin
                if (headers_sent()) {
                    echo "<script>window.location.href = 'index.php?error_delete_right=true';</script>";
                }
                else{
                    header("Location: index.php?error_delete_right=true");
                }
            }
        } else {
            //Post not found
            if (headers_sent()) {
                echo "<script>window.location.href = 'index.php?error_param=true';</script>";
            }
            else{
                header("Location: index.php?error_param=true");
            }
        }
    }
}

function checkEditPost($POST, $GET, $FILES, &$image, &$errors, $conn) {
    $post_found = false; //Check if a post is found
    $is_author = false; //Set to true if the current user has edit permission (is author of the post or is admin)
    $get_id = -1; //Default
    if (isset($GET['id'])) {
        $get_id = $GET['id'];
        $post = getPost($get_id, $conn);
        if ($post != false) {
            $post_found = true;
            if ($_SESSION['user_id'] == $post['author'] || $_SESSION['user_role'] == 1) {
                $is_author = true;
                $title = $post['title'];
                $content = $post['content'];
                $tag = $post['tag'];
                $image = $post['cover'];
            }
        }
    } else if (isset($POST['update'])) {
        $post_found = true;
        $is_author = true;
        $tag = $POST['tag'];
        $title = $POST['title'];
        $content = $POST['content'];
        $author = $_SESSION['user_id'];
        $get_update = explode("|", $POST['update']); //Split information sent via POST value of submit button
        $get_id = $get_update[0];
        $image = $get_update[1];
        $image_error = false;
        if ($FILES['image']['tmp_name'] != '') {
            $image = validatePostImage($FILES, $image_error);
        } elseif ($FILES['image']['tmp_name'] == '' && $image == '') {
            $image = "assets/images/posts/default.png"; //Default
        } elseif ($FILES['image']['error'] == 1) {
            $image_error = true;
        }



        if ($title == "" || $content == "") {
            $errors['empty'] = true;
        } elseif ($image_error) {
            $errors['image'] = true;
        } else {
            $stmt = updatePost($title, $content, $author, $tag, $image, $get_id, $conn);
            //affected_rows = -1 in case of error
            //affected_rows = 0 in case no changes were made, = 1 in case there are changes made
            if ($stmt->affected_rows != -1 &&  $stmt->errno == 0) {
                if (headers_sent()) {
                    echo "<script>window.location.href = 'post.php?id={$get_id}';</script>";
                }
                else{
                    header("Location: post.php?id={$get_id}");
                }
            } else {
                $errors['execute_err'] = true;
            }
        }
    }
    return ["title" => $title, "content" => $content, "tag" => $tag, "post_found" => $post_found, "is_author" => $is_author, "get_id" => $get_id];
}
?>