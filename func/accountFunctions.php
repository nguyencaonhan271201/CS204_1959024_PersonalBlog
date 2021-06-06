<?php
    include "profileValidate.php";
    
    function checkSignUp($POST, $FILE, $conn, &$errors) {
        //Check sign up information
        if (isset($POST['submit']))
        {
            validateProfileInput($POST, $errors, $conn);

            if($FILE['image']['tmp_name'] == '') {
                $image_url = "assets/images/users/default.jpg";
            } else {
                $image_url = validateProfileImage($FILE, $errors);
            }

            //Passed all the tests
            if (empty($errors)) {
                //Everything is good. Add user to database
                $username = $POST['username'];
                $display = $POST['display'];
                $email = $POST['email'];
                $password1 = $POST['password1'];
                $hash = password_hash($password1, PASSWORD_DEFAULT);
                $stmt = insertUser($username, $display, $email, $hash, $image_url, $conn);

                //Check execute result
                //affected_rows = -1 in case of error
                //affected_rows = 0 in case no changes were made, = 1 in case there are changes made
                if ($stmt->affected_rows != 1) {
                    $errors['execute_err'] = "Server error. Please try again later!";
                } else {
                    $_SESSION['signed_in'] = true;
                    $_SESSION['username'] = $username;
                    $_SESSION['name'] = $display;
                    $_SESSION['user_role'] = 2;
                    $_SESSION['user_id'] = $stmt->insert_id;
                    $_SESSION['profile_img'] = $image_url;
                    if (headers_sent()) {
                        echo "<script>window.location.href = 'index.php';</script>";
                    }
                    else{
                        header("Location: index.php");
                    }
                }
            }
        }
    }

    function checkSignIn($POST, $conn, &$errors) {
        //Check sign up information
        if (isset($POST['submit']))
        {
            $username = $POST['username'];
            $password = $POST['password'];

            //Check username
            if ($username == "") {
                $errors["username"] = "This field cannot be empty.";
            }

            //Check password
            if ($password == "") {
                $errors["password"] = "This field cannot be empty.";
            }

            //Pass all the tests
            if (empty($errors)) {
                $results = getUserInfo($username, $conn);
                //Check
                if ($results->num_rows == 0) {
                    $error = true;
                } else {
                    $row = $results->fetch_assoc();
                    //Valid sign in information
                    if (!password_verify($password, $row['password'])) {
                        $errors["wrong_pass"] = "Password is not correct.";
                    } else {
                        //Sign in allowed
                        $_SESSION['signed_in'] = true;
                        $_SESSION['username'] = $row['username'];
                        $_SESSION['name'] = $row['display_name'];
                        $_SESSION['user_role'] = $row['user_role'];
                        $_SESSION['user_id'] = $row['ID'];
                        $_SESSION['profile_img'] = $row['user_img'];
                        if (headers_sent()) {
                            echo "<script>window.location.href = 'index.php';</script>";
                        }
                        else{
                            header("Location: index.php");
                        }
                    }
                }
            }
        }
    }

    function checkEditProfile($POST, $FILES, &$errors, $conn, &$image) {
        if (!isset($POST['update'])) {
            $get_id = $_SESSION['user_id'];
            $row = getProfileFromID($_SESSION['user_id'], $conn);
            if (!empty($row)) {
                $username = $row['username'];
                $display = $row['display_name'];
                $email = $row['email'];
                $image = $row['user_img'];
            }
        } else {
            $username = $POST['username'];
            $display= $POST['display'];
            $email = $POST['email'];
            validateProfileInput($POST, $errors, $conn, false);
            $image = $POST['update'];
            if ($FILES['image']['tmp_name'] != '') {
                $image = validateProfileImage($FILES, $image_error);
            } elseif ($FILES['image']['tmp_name'] == '' && $image == '') {
                $image = "assets/images/users/default.jpg"; //Default
            } elseif ($FILES['image']['error'] == 1) {
                $errors['profile'] = "Upload error.";
            }

            //Cancel errors if don't update password ($password == "")
            if ($POST['password1'] == "") {
                unset($errors['password1']);
            }
    
            //Pass all the tests
            if (empty($errors)) {
                //Everything is good. Update user information
                $password1 = $POST['password1'];
                $stmt = updateUserInfo($username, $display, $email, $password1, $image, $conn);
    
                //Check execute result
                if ($stmt->affected_rows == -1 || $stmt->errno > 0) {
                    $errors['execute_err'] = "Server error. Please try again later!";
                } else {
                    $_SESSION['name'] = $display;
                    $_SESSION['profile_img'] = $image;
                    if (headers_sent()) {
                        echo "<script>window.location.href = 'index.php?edited_profile=true'</script>";
                    }
                    else{
                        header("Location: index.php?edited_profile=true");
                    }
                }
            }
        }
        return ["username" => $username, "display" => $display, "email" => $email];
    }