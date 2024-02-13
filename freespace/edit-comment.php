<?php
require 'config/database.php';
if (isset($_POST['submit']) && isset($_SESSION['user-id']) && isset($_POST['comment_id'])) {
    $user_id = $_SESSION['user-id'];
    $content = filter_var($_POST['com_content'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $comment_id = filter_var($_POST['comment_id'], FILTER_SANITIZE_NUMBER_INT);

    // Check if the comment belongs to the logged-in user
    $query = "SELECT author_id, post_id FROM comments WHERE id = '$comment_id'";
    $result = mysqli_query($connection, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $author_id = $row['author_id'];
        $post_id = $row['post_id'];

        if (empty($content)) {
            $_SESSION['edit-comment'] = "Comment cannot be empty.";
        } elseif ($user_id == $author_id) {
            $query = "UPDATE comments SET content = '$content' WHERE id = '$comment_id'";
            $result = mysqli_query($connection, $query);
            if (!mysqli_errno($connection)) {
                $_SESSION['edit-comment-success'] = "Comment edited successfully!";
            } else {
                $_SESSION['edit-comment'] = "Failed to edit comment.";
            }
        } else {
            $_SESSION['edit-comment'] = "You can only edit your own comments.";
        }
    } else {
        $_SESSION['edit-comment'] = "Comment not found.";
    }
} else {
    $_SESSION['edit-comment'] = "Invalid request.";
}

header('location: ' . ROOT_URL . 'post.php?id=' . $post_id . '&id2=' . $user_id);
die();
