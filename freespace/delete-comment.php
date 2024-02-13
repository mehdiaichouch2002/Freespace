<?php
require 'config/database.php';

if (isset($_GET['id']) && isset($_SESSION['user-id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $user_id = $_SESSION['user-id'];

    $query2 = "SELECT * FROM comments WHERE id= $id LIMIT 1";
    $result2 = mysqli_query($connection, $query2);
    $post = mysqli_fetch_assoc($result2);
    // delete category 
    $query = "DELETE FROM comments WHERE id=$id LIMIT 1";
    $result = mysqli_query($connection, $query);
    $_SESSION['delete-comment-success'] = "Comment deleted successfully";
} else {
    header('location: ' . ROOT_URL . 'signin.php');
    die();
}
header('location: ' . ROOT_URL . 'post.php?id=' . $post['post_id'] . '&id2=' . $user_id);
die();
