<?php
require 'config/database.php';

//get from data if submit button was clicked

if (isset($_POST['submit'])) {

    $author_id = $_SESSION['user-id'];
    $title = filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $body = filter_var($_POST['body'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category_id = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
    $is_featured =$is_featured = isset($_POST['is_featured']) ? filter_var($_POST['is_featured'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $thumbnail = $_FILES['thumbnail'];


    // validate inputs values :
    if (!$title) {
        $_SESSION['add-post'] = 'Please enter post title';
    } elseif (!$category_id) {
        $_SESSION['add-post'] = 'Please enter select post category';
    } elseif (!$body) {
        $_SESSION['add-post'] = 'Please enter post body';
    } elseif (!$thumbnail['name']) {
        $_SESSION['add-post'] = 'Please choose a post thumbnail';
    } else {
        // rename the image
        $time = time(); // to make image unique
        $thumbnail_name = $time . $thumbnail['name'];
        $thumbnail_tmp_name = $thumbnail['tmp_name'];
        $thumbnail_destination_path = '../images/' . $thumbnail_name;

        // make sure file is image : 
        $allowed_files = ['png', 'jpg', 'jpeg'];
        $extention = explode('.', $thumbnail_name);
        $extention = end($extention);

        if (in_array($extention, $allowed_files)) {
            // make sure image size not (2mb+)
            if ($thumbnail['size'] < 2000000) {
                // upload avatar
                move_uploaded_file($thumbnail_tmp_name, $thumbnail_destination_path);
            } else {
                $_SESSION['add-post'] = 'File size too big. Should be less than 2mb';
            }
        } else {
            $_SESSION['add-post'] = 'File should be png, jpg or jpeg';
        }
    }
    // redirect back with form data to add-post page if there was any probleme
    if (isset($_SESSION['add-post'])) {
        // pass form data back to signup page
        $_SESSION['add-post-data'] = $_POST;
        header('location:' . ROOT_URL . 'admin/add-post.php');
        die();
    } else {
        // insert post into db
        $query = "INSERT INTO posts (title,body,thumbnail,category_id,author_id,is_featured) VALUES ('$title','$body','$thumbnail_name',$category_id,$author_id,$is_featured)";
        $result = mysqli_query($connection, $query);

        if (!mysqli_errno($connection)) {
            $_SESSION['add-post-success'] = "New post added successfully";
            header('location: ' . ROOT_URL . 'admin/');
            die();
        }
    }
}
header('location: ' . ROOT_URL . 'admin/add-post.php');
