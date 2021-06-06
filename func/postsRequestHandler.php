<?php
    include('db.php');

    //Get all posts by search query
    if (isset($_POST['q'])) {
        $query = "%{$_POST['q']}%";
        $result = searchPosts($query, $conn);
        echo json_encode($result);
    }