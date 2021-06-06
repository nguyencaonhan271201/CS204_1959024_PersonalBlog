<?php
    function getReactions($id, $conn) {
        $results = getReactionsFromDB($id, $conn);
        $is_liked = false;
        $is_disliked = false;
        $up_count = 0;
        $down_count = 0;
        if (!empty($results)) {
            foreach ($results as $result) {
                if ($result['type'] == 1) {
                    $up_count++;
                } else {
                    $down_count++;
                }
                if (isset($_SESSION['user_id']) && $result['user_ID'] == $_SESSION['user_id'])
                {
                    if ($result['type'] == 1) {
                        $is_liked = true;
                    } else {
                        $is_disliked = true;
                    }
                }
            }
        }
        return ['liked' => $is_liked, 'disliked' => $is_disliked, 'up' => $up_count, 'down' => $down_count];
    }