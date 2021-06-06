<?php
    function getStarRate($id, $conn) {
        $results = getStarRateFromDB($id, $conn);
        $avgStar = 0;
        $is_voted = false;
        if (!empty($results)) {
            $sumStar = 0;
            foreach ($results as $result) {
                $sumStar += intval($result['stars']);
                if (isset($_SESSION['user_id']) && $result['user_ID'] == $_SESSION['user_id'])
                    $is_voted = true;
            }
            $avgStar = $sumStar / count($results);
            $avgStar = round($avgStar / 5, 1) * 5;
        }
        return ['avg' => $avgStar, 'is_voted' => $is_voted, 'total_votes' => count($results)];
    }