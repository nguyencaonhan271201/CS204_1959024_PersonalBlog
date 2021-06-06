<?php
//Database connection
$DB_HOST = "localhost";
$DB_USER = "id16196601_ncn2712";
$DB_PASSWORD = "5[HcTJ]tQ/T7OYp]";
$DB_NAME = "id16196601_cs204_project2";
$DB_PORT = 3306;

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
mysqli_set_charset($conn, 'utf8mb4');

//Functions communicating with database
function getTags($conn, &$tags, &$tags_desc) {
    //If empty then reload $tags from tags table
    $DB_QUERY = "SELECT * FROM tags";
    $results = $conn->query($DB_QUERY);
    $rows = $results->fetch_all(MYSQLI_ASSOC);
    foreach ($rows as $row) {
        $tags[$row['ID']] = $row['tag_name'];
        $tags_desc[$row['ID']] = $row['area'];
    }
}

function getAllPosts($limit, $conn) {
    $DB_QUERY = "SELECT p.ID, p.tag, p.title, p.content, p.author, DATE_ADD(p.date_posted, INTERVAL 7 HOUR) AS date_posted, 
    u.display_name, p.cover,
    (SELECT SUM(stars) FROM stars WHERE stars.post_id = p.ID) as sum_stars,
    (SELECT COUNT(*) FROM stars WHERE stars.post_id = p.ID) as count_rates
    FROM posts as p, users as u WHERE p.author = u.ID ORDER BY p.date_posted DESC LIMIT {$limit}";
    $results = $conn->query($DB_QUERY);
    if ($results) {
        $posts = $results->fetch_all(MYSQLI_ASSOC);
        return $posts;
    }
    //Return empty array if errors occured
    return [];
}

function getPostsByCondition($POST, $GET, &$get_tag, &$user, $conn) {
    if (isset($GET['user'])) {
        //Get user display name
        $get_tag = "&user=" . $GET['user'];
        $user = "";
        $query = "SELECT display_name FROM users WHERE ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $GET['user']);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc()['display_name'];

        $query = "SELECT p.ID, p.tag, p.title, p.content, p.author, DATE_ADD(p.date_posted, INTERVAL 7 HOUR) AS date_posted, 
        u.display_name, p.cover,
        (SELECT SUM(stars) FROM stars WHERE stars.post_id = p.ID) as sum_stars,
        (SELECT COUNT(*) FROM stars WHERE stars.post_id = p.ID) as count_rates
        FROM posts as p, users as u WHERE p.author = u.ID AND author = ? ORDER BY date_posted DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $GET['user']);
    } elseif (isset($GET['tag'])) {
        $get_tag = "&tag=" . $GET['tag'];
        $query = "SELECT p.ID, p.tag, p.title, p.content, p.author, DATE_ADD(p.date_posted, INTERVAL 7 HOUR) AS date_posted,
        u.display_name, p.cover,
        (SELECT SUM(stars) FROM stars WHERE stars.post_id = p.ID) as sum_stars,
        (SELECT COUNT(*) FROM stars WHERE stars.post_id = p.ID) as count_rates
        FROM posts as p, users as u WHERE p.author = u.ID AND tag = ? ORDER BY date_posted DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $GET['tag']);
    } elseif (isset($POST['search'])) {
        $get_tag = "&search?=" . $POST['search'];
        $search = $POST['search'];
        if ($search != "") {
            $search = "%{$search}%";
            $query = "SELECT p.ID, p.tag, p.title, p.content, p.author, DATE_ADD(p.date_posted, INTERVAL 7 HOUR) AS date_posted, 
            u.display_name, p.cover,
            (SELECT SUM(stars) FROM stars WHERE stars.post_id = p.ID) as sum_stars,
            (SELECT COUNT(*) FROM stars WHERE stars.post_id = p.ID) as count_rates
            FROM posts as p, users as u WHERE p.author = u.ID AND title LIKE ? ORDER BY date_posted DESC";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $search);
        }
    } else {
        $query = "SELECT p.ID, p.tag, p.title, p.content, p.author, DATE_ADD(p.date_posted, INTERVAL 7 HOUR) AS date_posted, 
        u.display_name, p.cover,
        (SELECT SUM(stars) FROM stars WHERE stars.post_id = p.ID) as sum_stars,
        (SELECT COUNT(*) FROM stars WHERE stars.post_id = p.ID) as count_rates
        FROM posts as p, users as u WHERE p.author = u.ID ORDER BY date_posted DESC";
        $stmt = $conn->prepare($query);
    }

    if (isset($query)) {
        $stmt->execute();
        $results = $stmt->get_result();
        $posts = $results->fetch_all(MYSQLI_ASSOC);
        return $posts;
    }
    return [];
}

function searchPosts($search, $conn) {
    $query = "SELECT p.ID, p.tag, t.tag_name, p.title, p.content, p.author, 
    DATE_ADD(p.date_posted, INTERVAL 7 HOUR) AS date_posted, u.display_name, p.cover,
    (SELECT SUM(stars) FROM stars WHERE stars.post_id = p.ID) as sum_stars,
    (SELECT COUNT(*) FROM stars WHERE stars.post_id = p.ID) as count_rates
    FROM posts as p, users as u, tags as t WHERE p.author = u.ID AND p.tag = t.ID AND title LIKE ? ORDER BY date_posted DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $results = $stmt->get_result();
    $posts = $results->fetch_all(MYSQLI_ASSOC);
    return $posts;
}

function insertUser($username, $display, $email, $hash, $image_url, $conn) {
    $query = "INSERT INTO users(username, display_name, email, password, user_img) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $username, $display, $email, $hash, $image_url);
    $stmt->execute();
    return $stmt;    
}

function getUserInfo($username, $conn) {
    $query = "SELECT ID, username, display_name, email, user_role, user_img, password, DATE_ADD(date_created, INTERVAL 7 HOUR) as date_created
    FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    return $stmt->get_result();
}

function updateUserInfo($username, $display, $email, $password1, $image, $conn) {
    if ($password1 != "")
    {
        $hash = password_hash($password1, PASSWORD_DEFAULT);
        $query = "UPDATE users SET display_name = ?, email = ?, password = ?, user_img = ? WHERE ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $display, $email, $hash, $image, $_SESSION['user_id']);
    } else {
        $query = "UPDATE users SET display_name = ?, email = ?, user_img = ? WHERE ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $display, $email, $image, $_SESSION['user_id']);
    }
    $stmt->execute();
    return $stmt; 
}

function getPost($id, $conn) {
    $DB_QUERY = "SELECT p.ID, p.tag, p.title, p.content, p.author, DATE_ADD(p.date_posted, INTERVAL 7 HOUR) AS date_posted, 
    DATE_ADD(p.date_modified, INTERVAL 7 HOUR) AS date_modified, u.display_name, p.cover,
    (SELECT SUM(stars) FROM stars WHERE stars.post_id = p.ID) as sum_stars,
    (SELECT COUNT(*) FROM stars WHERE stars.post_id = p.ID) as count_rates
    FROM posts as p, users as u WHERE p.author = u.ID AND p.ID = ?";
    $stmt = $conn->prepare($DB_QUERY);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $post = $result->fetch_assoc();
        return $post;
    }
    return false; //Cannot get post
}

function insertPost($title, $content, $author, $tag, $image, $conn) {
    $query = "INSERT INTO posts(title, content, author, tag, cover) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $title, $content, $author, $tag, $image);
    $stmt->execute();    
    return $stmt;
}

function deletePost($id, $conn) {
    $query = "DELETE FROM posts WHERE ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();    
    return $stmt;
}

function updatePost($title, $content, $author, $tag, $image, $post_id, $conn) {
    $query = ("UPDATE posts SET title = ?, content = ?, tag = ?, cover = ?, date_modified = CURRENT_TIMESTAMP() WHERE ID = ?");
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $title, $content, $tag, $image, $post_id);
    $stmt->execute();   
    return $stmt; 
}

function getProfileFromID($id, $conn) {
    $query = "SELECT ID, username, display_name, email, user_role, user_img, password, DATE_ADD(date_created, INTERVAL 7 HOUR) as date_created
    FROM users WHERE ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $profile = $result->fetch_assoc();
        return $profile;
    }
    return false;
}

function getPostsFromID($id, $conn) {
    $query = "SELECT p.ID, p.tag, p.title, p.content, p.author, p.cover, DATE_ADD(p.date_posted, INTERVAL 7 HOUR) AS date_posted, 
    (SELECT SUM(stars) FROM stars WHERE stars.post_id = p.ID) as sum_stars,
    (SELECT COUNT(*) FROM stars WHERE stars.post_id = p.ID) as count_rates
    FROM posts as p
    WHERE p.author = ? ORDER BY date_posted DESC LIMIT 5";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $results = $stmt->get_result();
    $posts = $results->fetch_all(MYSQLI_ASSOC);
    return $posts;
}

function insertStarRank($star, $post, $user_id, $conn) {
    $query = "INSERT INTO stars(post_ID, user_ID, stars) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $post, $user_id, $star);
    $stmt->execute();
    return $stmt;
}

function updateStarRank($star, $post, $user_id, $conn) {
    $query = "UPDATE stars SET stars = ? WHERE post_ID = ? AND user_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $star, $post, $user_id);
    $stmt->execute();
    return $stmt;
}

function getStarRateFromDB($id, $conn) {
    $query = "SELECT * FROM stars WHERE post_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $results = $stmt->get_result();
    $rates = $results->fetch_all(MYSQLI_ASSOC);
    return $rates;
}

function insertReaction($type, $post, $user_id, $conn, $liked, $disliked) {
    if ($liked || $disliked) {
        $query = "UPDATE reactions SET type = ? WHERE post_ID = ? AND user_ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $type, $post, $user_id);
    } else {
        $query = "INSERT INTO reactions(post_ID, user_ID, type) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $post, $user_id, $type);
    }
    $stmt->execute();
    return $stmt;
}

function deleteReaction($post, $user_id, $conn) {
    $query = "DELETE FROM reactions WHERE post_ID = ? AND user_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $post, $user_id);
    $stmt->execute();
    return $stmt;
}

function getReactionsFromDB($id, $conn) {
    $query = "SELECT * FROM reactions WHERE post_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $results = $stmt->get_result();
    $reactions = $results->fetch_all(MYSQLI_ASSOC);
    return $reactions;
}

function getAllComments($id, $conn) {
    if($_SESSION['signed_in']) {
        $query = "SELECT c.ID, c.content, c.author, c.post, DATE_ADD(c.date_commented, INTERVAL 7 HOUR) as date_commented, 
                u.display_name, u.user_img, IFNULL(c.parent, 0) AS get_parent, 
                (SELECT COUNT(*) FROM comment_reactions WHERE comment_ID = c.ID AND type = 1) AS like_count,
                (SELECT COUNT(*) FROM comment_reactions WHERE comment_ID = c.ID AND type = 2) AS dislike_count,
                (SELECT COUNT(*) FROM comment_reactions WHERE comment_ID = c.ID AND user_ID = ? AND type = 1) AS self_like_count,
                (SELECT COUNT(*) FROM comment_reactions WHERE comment_ID = c.ID AND user_ID = ? AND type = 2) AS self_dislike_count
                FROM comments c, users u WHERE c.post = ? AND c.author = u.ID
                ORDER BY date_commented DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $_SESSION['user_id'], $_SESSION['user_id'], $id);
    } else {
        $query = "SELECT c.ID, c.content, c.author, c.post, DATE_ADD(c.date_commented, INTERVAL 7 HOUR) as date_commented,
                u.display_name, u.user_img, IFNULL(c.parent, 0) AS get_parent,
                (SELECT COUNT(*) FROM comment_reactions WHERE comment_ID = c.ID AND type = 1) AS like_count,
                (SELECT COUNT(*) FROM comment_reactions WHERE comment_ID = c.ID AND type = 2) AS dislike_count,
                0 AS self_like_count, 0 AS self_dislike_count
                FROM comments c, users u WHERE c.post = ? AND c.author = u.ID ORDER BY date_commented DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
    }
    $stmt->execute();
    $results = $stmt->get_result();
    $comments = $results->fetch_all(MYSQLI_ASSOC);
    return $comments;
}

function insertComment($content, $author, $parent, $post, $conn) {
    $parent = $parent == 0? NULL : $parent;
    $query = "INSERT INTO comments(content, author, post, parent) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("siis", $content, $author, $post, $parent);
    $stmt->execute();
    return $stmt;
}

function deleteComment($id, $conn) {
    $query = "DELETE FROM comments WHERE ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt;
}

function updateComment($id, $content, $conn) {
    $query = "UPDATE comments SET content = ? WHERE ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $content, $id);
    $stmt->execute();
    return $stmt;
}

function insertCommentReaction($comment_id, $user_id, $type, $conn) {
    $query = "INSERT INTO comment_reactions(comment_ID, user_ID, type) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $comment_id, $user_id, $type);
    $stmt->execute();
    return $stmt;
}

function updateCommentReaction($comment_id, $user_id, $type, $conn) {
    $query = "UPDATE comment_reactions SET type = ? WHERE comment_ID = ? AND user_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $type, $comment_id, $user_id);
    $stmt->execute();
    return $stmt;
}

function deleteCommentReaction($comment_id, $user_id, $conn) {
    $query = "DELETE FROM comment_reactions WHERE comment_ID = ? AND user_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $comment_id, $user_id);
    $stmt->execute();
    return $stmt;
}

function outputHTMLStars($post) {
    //Calculate average stars
    $sum_stars = intval($post['sum_stars']);
    $count_rates = $post['count_rates'];
    if ($count_rates != 0) {
        $avgStar = $sum_stars / $count_rates;
        $avgStar = round($avgStar / 5, 1) * 5;
    } else {
        $avgStar = 0;
    }

    //HTML Output for display stars
    $fullStar = floor($avgStar);
    $halfStar = floor($avgStar) == $avgStar? 0 : 1;
    $emptyStar = floor(5 - $avgStar);
    $html = "";
    for ($j = 0; $j < $fullStar; $j++) {
        $html .= "<i class='star fas fa-star'></i>";
    }    
    for ($j = 0; $j < $halfStar; $j++) {
        $html .= "<i class='star fas fa-star-half-alt'></i>";
    }
    for ($j = 0; $j < $emptyStar; $j++) {
        $html .= "<i class='star far fa-star'></i>";
    }

    return $html;
}

function managementUsers($conn) {
    $query = "SELECT u.ID, u.user_img, u.username, u.display_name, u.email, DATE_ADD(u.date_created, INTERVAL 7 HOUR) as date_created, u.user_role,
    (SELECT COUNT(*) FROM posts WHERE posts.author = u.ID) AS posts_count FROM users u ORDER BY u.ID ASC";
    $results = $conn->query($query);
    if ($results) {
        $users = $results->fetch_all(MYSQLI_ASSOC);
        return $users;
    }
    //Return empty array if errors occured
    return [];
}

function managementPosts($conn) {
    $query = "SELECT p.ID, p.title, p.cover, u.ID as author, t.tag_name, t.ID as tag_id, u.display_name, p.date_posted,
    (SELECT SUM(stars) FROM stars WHERE stars.post_id = p.ID) as sum_stars,
    (SELECT COUNT(*) FROM stars WHERE stars.post_id = p.ID) as count_rates,
    (SELECT COUNT(*) FROM comments WHERE comments.post = p.ID) as count_comments,
    (SELECT COUNT(*) FROM reactions WHERE reactions.post_id = p.ID AND reactions.type = 1) as count_likes,
    (SELECT COUNT(*) FROM reactions WHERE reactions.post_id = p.ID AND reactions.type = 2) as count_dislikes
    FROM posts p, tags t, users u WHERE p.tag = t.ID AND p.author = u.ID ORDER BY p.ID ASC";
    $results = $conn->query($query);
    if ($results) {
        $posts = $results->fetch_all(MYSQLI_ASSOC);
        return $posts;
    }
    //Return empty array if errors occured
    return [];
}

function managementDeleteUser($id, $conn) {
    $query = "DELETE FROM users WHERE ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt;
}

function managementDeletePost($id, $conn) {
    $query = "DELETE FROM posts WHERE ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt;
}

function managementEditUserRole($id, $role, $conn) {
    $query = "UPDATE users SET user_role = ? WHERE ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $role, $id);
    $stmt->execute();
    return $stmt;
}