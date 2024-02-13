<?php
require 'config/database.php';

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    // fetch user from db :
    $query = "SELECT * FROM users WHERE id=$id";
    $result = mysqli_query($connection, $query);
    $user = mysqli_fetch_assoc($result);

    // make sure we got only 1 user 
    if (mysqli_num_rows($result) == 1) {
        $avatar_name = $user['avatar'];
        $avatar_path = '../images/'.$avatar_name;
        // delete image if it is available : 
        if($avatar_path){
            unlink($avatar_path);
        }
    }

    // fetch all thumbnails of the user's posts and delete them 
    $thumbnail_query = "SELECT thumbnail FROM posts WHERE author_id=$id";
    $thumbnail_result = mysqli_query($connection,$thumbnail_query);
    if(mysqli_num_rows($thumbnail_result)>0){
        while($thumbnail = mysqli_fetch_assoc($thumbnail_result)){
            $thumbnail_path = '../images/'.$thumbnail['thumbnail'];
            //delete thumbnail from images folder if exists 
            if($thumbnail_path){
                unlink($thumbnail_path);
            }
        }
    }
    
    // delete user from db 
    $delete_user_query = "DELETE FROM users WHERE id=$id";
    $delete_user_result = mysqli_query($connection,$delete_user_query);
    if(mysqli_errno($connection)){
        $_SESSION['delete-user'] = "Couldn't delete user {$user['username']}";
    } else {
        $_SESSION['delete-user-success'] = "User  {$user['username']} deleted successfully";
    }
}
header('location: '.ROOT_URL.'admin/manage-users.php');
