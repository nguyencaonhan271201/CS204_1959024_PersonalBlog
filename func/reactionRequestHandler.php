<?php
    include('db.php');
    session_start();

    if (isset($_POST['type'])) {
        $type = $_POST['type'];
        $post = $_POST['post'];
        $liked = $_POST['liked'] == "true";
        $disliked = $_POST['disliked'] == "true";
        if ($type >= 3) {
            $stmt = deleteReaction($post, $_SESSION['user_id'], $conn);
            if ($stmt->affected_rows == 1) {
                if ($type == 3) {
                    echo json_encode("0");
                } else {
                    echo json_encode("1");
                }
            }
        } else {
            $stmt = insertReaction($type, $post, $_SESSION['user_id'], $conn, $liked, $disliked);
            if ($stmt->affected_rows == 1) {
                if ($type == 1) {
                    echo json_encode("2");
                } else {
                    echo json_encode("3");
                }
            }
        }

    }