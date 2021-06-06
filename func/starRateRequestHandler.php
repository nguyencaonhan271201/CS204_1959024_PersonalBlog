<?php
    include('db.php');
    session_start();

    if (isset($_POST['star'])) {
        $star = $_POST['star'];
        $post = $_POST['post'];
        $mode = $_POST['mode'];
        if ($mode == 'insert') {
            $stmt = insertStarRank($star, $post, $_SESSION['user_id'], $conn);
        } else {
            $stmt = updateStarRank($star, $post, $_SESSION['user_id'], $conn);
        }
        if (($mode == 'insert' && $stmt->affected_rows == 1) || ($mode == 'update' && $stmt->affected_rows != -1)) {
            echo json_encode(True);
        }
        else echo json_encode(False);
    }