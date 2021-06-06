<?php
    include('db.php');
    session_start();

    //Get all comments
    if (isset($_POST['get_all'])) {
        $post = $_POST['post'];
        $result = getAllComments($post, $conn);
        echo json_encode($result);
    }

    //Add new comment
    if (isset($_POST['add'])) {
        $content = $_POST['content'];
        $post = $_POST['post'];
        $parent = $_POST['parent'];
        $stmt = insertComment($content, $_SESSION['user_id'], $parent, $post, $conn);
        if ($stmt->affected_rows == 1) {
            echo json_encode(True);
        }
        else echo json_encode(False);
    }

    //Delete comment
    if (isset($_POST['delete'])) {
        $comment = $_POST['comment'];
        $stmt = deleteComment($comment, $conn);
        if ($stmt->affected_rows > 0) {
            echo json_encode(True);
        }
        else echo json_encode(False);
    }

    //Edit comment
    if (isset($_POST['edit'])) {
        $id = $_POST['comment'];
        $content = $_POST['content'];
        $stmt = updateComment($id, $content, $conn);
        if ($stmt->affected_rows != -1 && $stmt->errno == 0) {
            echo json_encode(True);
        }
        else echo json_encode(False);
    }

    //Comment reactions
    if (isset($_POST['comment_react'])) {
        $type = $_POST['comment_react'] == "like"? 1 : 2;
        $id = $_POST['comment'];
        $liked = $_POST['liked'] == "true";
        $disliked = $_POST['disliked'] == "true";
        //Check if insert, update or delete
        if (!$liked && !$disliked) {
            $stmt = insertCommentReaction($id, $_SESSION['user_id'], $type, $conn);
        } elseif (($liked && $type == 1) || ($disliked && $type == 2)) {
            $stmt = deleteCommentReaction($id, $_SESSION['user_id'], $conn);
        } else {
            $stmt = updateCommentReaction($id, $_SESSION['user_id'], $type, $conn);
        }
        if ($stmt->affected_rows == 1) {
            echo json_encode(True);
        } else {
            echo json_encode(False);
        }
    }