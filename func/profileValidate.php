<?php
    function validateProfileImage($FILES, &$errors) {
        //Continue if there is profile image
        $file = $FILES['image'];

        $fname = $file['name'];
        $ftype = $file['type'];
        $ftmp = $file['tmp_name'];
        $ferr = $file['error'];
        $fsize = $file['size'];
        $allowed_ext = ['png', 'jpg', 'jpeg', '.bmp', '.PNG', '.JPG', '.JPEG', '.BMP'];

        //Check if there are errors on upload
        if ($ferr != 0) {
            $errors['profile'] = "File error.";
            $profileErr = true;
        }

        //Check file type and extension
        $ftype = explode("/", $ftype);
        if ($ftype[0] != 'image' || !in_array(end($ftype), $allowed_ext)) {
            $errors['profile'] = "Images only.";
            $profileErr = true;
        }

        //Check file size
        if ($fsize > 5242880) {
            $errors['profile'] = "File is too large.";
            $profileErr = true;
        }

        //Error by upload constraints of server
        if ($file['error'] == 1) {
            $errors['profile'] = "Upload error.";
            $profileErr = true;
        }

        if (!isset($profileErr)) {
            $newFilename = uniqid('', true) . "." . end($ftype);
            $dest = "assets/images/users/" . $newFilename;
            if(move_uploaded_file($ftmp, $dest)) {
                return $dest;
            }
        }        
    }

    function validateProfileInput($POST, &$errors, $conn, $checkDuplicate = true) {
        $username = $POST['username'];
        $display = $POST['display'];
        $email = $POST['email'];

        $password1 = $POST['password1'];
        $password2 = $POST['password2'];
        
        //Check username
        if ($username != "admin" && strlen($username) < 6) {
            $errors["username"] = "Username must be at least 6 characters.";
        }

        //Check display name
        if (strlen(trim($display)) == 0) {
            $errors["display"] = "Display name is not valid.";
        }

        //Check email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors["email"] = "Email is not valid.";
        }

        //Check password if password has value
        if (strlen($password1) < 6) {
            $errors["password1"] = "Password length must be at least 6 characters.";
        } else if ($password2 != $password1) {
            $errors["password2"] = "Password confirmation is wrong.";
        }
        
        if ($checkDuplicate) {
            //Check for users with same username
            $query = "SELECT * FROM users WHERE username = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $username);
            $stmt->execute(); 
            if ($stmt->num_rows > 0) {
                $errors["execute_err"] = "Username already exists. Try again with a different one.";
            }
        }
    }