<?php
    function validatePostImage($FILES, &$error) {
        $file = $FILES['image'];

        $fname = $file['name'];
        $ftype = $file['type'];
        $ftmp = $file['tmp_name'];
        $ferr = $file['error'];
        $fsize = $file['size'];
        $allowed_ext = ['png', 'jpg', 'jpeg', '.bmp', '.PNG', '.JPG', '.JPEG', '.BMP'];

        //Check if there are errors on upload
        if ($ferr != 0) {
            $error = true;
            return "";
        }

        //Check file type and extension
        $ftype = explode("/", $ftype);
        if ($ftype[0] != 'image' || !in_array(end($ftype), $allowed_ext)) {
            $error = true;
            return "";
        }

        //Check file size
        if ($fsize > 5242880) {
            $error = true;
            return "";
        }

        //Error by upload constraints of server
        if ($file['error'] == 1) {
            $error = true;
            return "";
        }

        //Pass all the test
        $newFilename = uniqid('', true) . "." . end($ftype);
        $dest = "assets/images/posts/" . $newFilename;
        if(move_uploaded_file($ftmp, $dest)) {
            return $dest;
        }         
    }