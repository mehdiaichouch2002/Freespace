<?php 
require 'config/database.php';

if (isset($_POST['submit']) && isset($_SESSION['user-id']) && isset($_POST['post_id'])) {
    $user_id = $_SESSION['user-id'];
    $content = filter_var($_POST['comment'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $post_id = filter_var($_POST['post_id'],FILTER_SANITIZE_NUMBER_INT);

    $query = "INSERT INTO comments (content,post_id,author_id) VALUES ('$content','$post_id','$user_id')";
    $result = mysqli_query($connection, $query);

    if (!mysqli_errno($connection)) {
        $_SESSION['add-comment-success'] = "Comment added successfully!";
        header('location: ' . ROOT_URL . 'post.php?id='.$post_id.'&id2='.$user_id);
        die();
    }
} else {
    header('location: ' . ROOT_URL . 'signin.php');
    die();
}
header('location: ' . ROOT_URL . 'post.php?id='.$post_id.'&id2='.$user_id);
die();