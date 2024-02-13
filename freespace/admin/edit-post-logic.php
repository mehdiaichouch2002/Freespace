<?php
require 'config/database.php';

// make sure edit btn clicked
if (isset($_POST['submit'])) {
    //get updates from data
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $previous_thumbnail_name = filter_var($_POST['previous_thumbnail_name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $title = filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $body = filter_var($_POST['body'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category_id = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
    $is_featured = $is_featured = isset($_POST['is_featured']) ? filter_var($_POST['is_featured'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $thumbnail = $_FILES['thumbnail'];

    // validate inputs values :
    if (!$title) {
        $_SESSION['add-post'] = "Couldn't update post. enter a title";
    } elseif (!$category_id) {
        $_SESSION['add-post'] = "Couldn't update post. give a category";
    } elseif (!$body) {
        $_SESSION['add-post'] = "Couldn't update post. enter a body";
    } else {
        // delete existing thumbnail if new thumbnail is available
        if ($thumbnail['name']) {
            $previous_thumbnail_path = "../images/" . $previous_thumbnail_name;
            if ($previous_thumbnail_path) {
                unlink($previous_thumbnail_path);
            }

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
    }

    if (isset($_SESSION['edit-post'])) {
        // redirect back to manage form page if there was any probleme
        header('location:' . ROOT_URL . 'admin/');
        die();
    } else {
        // set thumbnail name if a new one was uploaded ,else keep old thumbnail name
        $thumbnail_to_insert = $thumbnail_name ?? $previous_thumbnail_name;

        $query = "UPDATE posts SET title='$title',body='$body',thumbnail='$thumbnail_to_insert',category_id=$category_id,is_featured=$is_featured WHERE id=$id LIMIT 1";
        $result = mysqli_query($connection, $query);
    }
    if (!mysqli_errno($connection)) {
        $_SESSION['edit-post-success'] = "Post updated successfully";
    }
}
header('location: ' . ROOT_URL . 'admin/');
die();
