<?php
include "db.php";
session_start();

if(isset($_POST['delete'])) {
    if (isset($_POST['type'])) {
        $type = $_POST['type'];
        $id = $_POST['id'];
        switch ($type) {
            case 'users':
                $stmt = managementDeleteUser($id, $conn);
                break;
            case 'posts':
                $stmt = managementDeletePost($id, $conn);
                break;
        }
        if ($stmt->affected_rows == 1) {
            echo json_encode(True);
        }
        else echo json_encode(False);
    }
} elseif(isset($_POST['edit'])) {
    $get_id = $_POST['get_id'];
    $get_role = $_POST['get_role'];
    $get_role = $get_role == 1? 2 : 1;
    $stmt = managementEditUserRole($get_id, $get_role, $conn);

    if ($stmt->affected_rows == 1) {
        echo json_encode(True);
        //Perform change to session if the current user is changed
        if ($get_id == $_SESSION['user_id']) {
            $_SESSION['user_role'] = $get_role;
        }
    }
    else echo json_encode(False);
} else {
    if (isset($_POST['type'])) {
        $type = $_POST['type'];
        switch ($type) {
            case 'users':
                $results = managementUsers($conn);
                break;
            case 'posts':
                $results = managementPosts($conn);
                break;
        }
        echo json_encode($results);
    }
}