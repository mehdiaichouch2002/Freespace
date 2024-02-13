<?php
include 'partials/header.php';

//fetch post and comments from db if id isset
if (isset($_GET['id'])) {
    //fetch post
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $query = "SELECT * FROM posts WHERE id=$id";
    $result = mysqli_query($connection, $query);
    $post = mysqli_fetch_assoc($result);

    //fetch comments
    $id2 = filter_var($_GET['id2'], FILTER_SANITIZE_NUMBER_INT);
    $query2 = "SELECT * FROM comments WHERE post_id=$id ORDER BY date_time";
    $comments = mysqli_query($connection, $query2);
} else {
    header('location: ' . ROOT_URL . 'blog.php');
    die();
}
?>
<section class="singlepost">
    <?php if (isset($_SESSION['add-comment-success'])) :  // shows if add category was successfully
    ?>
        <div class="alert__message success container">
            <p>
                <?=
                $_SESSION['add-comment-success'];
                unset($_SESSION['add-comment-success']);
                ?>
            </p>
        </div>
    <?php elseif (isset($_SESSION['delete-comment-success'])) :  // shows if add category was successfully
    ?>
        <div class="alert__message success container">
            <p>
                <?=
                $_SESSION['delete-comment-success'];
                unset($_SESSION['delete-comment-success']);
                ?>
            </p>
        </div>
    <?php elseif (isset($_SESSION['edit-comment'])) :  // shows if add category was successfully
    ?>
        <div class="alert__message error container">
            <p>
                <?=
                $_SESSION['edit-comment'];
                unset($_SESSION['edit-comment']);
                ?>
            </p>
        </div>
    <?php elseif (isset($_SESSION['edit-comment-success'])) :  // shows if add category was successfully
    ?>
        <div class="alert__message success container">
            <p>
                <?=
                $_SESSION['edit-comment-success'];
                unset($_SESSION['edit-comment-success']);
                ?>
            </p>
        </div>
    <?php endif ?>
    <div class="container singlepost__container">
        <h2><?= $post['title'] ?></h2>
        <div class="post__author">
            <?php
            $author_id = $post['author_id'];
            $author_query = "SELECT * FROM users WHERE id=$author_id";
            $author_result = mysqli_query($connection, $author_query);
            $author = mysqli_fetch_assoc($author_result);
            ?>
            <div class="post__author-avatar">
                <img src="./images/<?= $author['avatar'] ?>">
            </div>
            <div class="post_author-info">
                <h5>By : <?= "{$author['firstname']}  {$author['lastname']}" ?></h5>
                <small><?= date("M d, Y - H:i", strtotime($post['date_time'])) ?></small>
                <small style="color: antiquewhite;">
                    <?php
                    $days_ago = date_diff(new DateTime(), new DateTime($post['date_time']))->days;
                    if ($days_ago >= 365) {
                        $years_ago = floor($days_ago / 365);
                        echo $years_ago . " year" . ($years_ago > 1 ? "s" : "") . " ago";
                    } elseif ($days_ago >= 30) {
                        $months_ago = floor($days_ago / 30);
                        echo $months_ago . " month" . ($months_ago > 1 ? "s" : "") . " ago";
                    } else {
                        echo $days_ago . " day" . ($days_ago > 1 ? "s" : "") . " ago";
                    }
                    ?>
                </small>

            </div>
        </div>
        <div class="singlepost__thumbnail">
            <img src="./images/<?= $post['thumbnail'] ?>" alt="">
        </div>
        <p>
            <?= $post['body'] ?>
        </p>
        <div class="comment__bar">
            <button class="comment__btn">Show Comments <i class="uil uil-comment-download"></i></button>
            <!--* Comments block -->
            <div class="allcomments__block">
                <?php while ($comment = mysqli_fetch_assoc($comments)) : ?>
                    <div class="d-flex align-items-center justify-content-center vh-100 singlecomment__block">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="d-flex px-3 py-4 border border-bottom-0 border-light rounded-top">
                                    <?php
                                    $commenter_id = $comment['author_id'];
                                    $commenter_query = "SELECT * FROM users WHERE id=$commenter_id";
                                    $commenter_result = mysqli_query($connection, $commenter_query);
                                    $commenter = mysqli_fetch_assoc($commenter_result);
                                    ?>
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm rounded-circle">
                                            <img class="avatar-img" src="./images/<?= $commenter['avatar'] ?>">
                                        </div>
                                    </div>
                                    <div class="post__author-info">
                                        <h5>By : <?= "{$commenter['firstname']}  {$commenter['lastname']}" ?></h5>
                                        <small><?= date("M d, Y - H:i", strtotime($comment['date_time'])) ?></small>
                                        <?php
                                        $comment_date = strtotime($comment['date_time']);
                                        $now = time();
                                        $diff = $now - $comment_date;
                                        if ($diff < 60) {
                                            $time_ago = $diff . " seconds ago";
                                        } elseif ($diff < 3600) {
                                            $time_ago = floor($diff / 60) . " minutes ago";
                                        } elseif ($diff < 86400) {
                                            $time_ago = floor($diff / 3600) . " hours ago";
                                        } elseif ($diff < 2592000) {
                                            $days = floor($diff / 86400);
                                            $time_ago = $days > 1 ? $days . " days ago" : "Yesterday";
                                        } elseif ($diff < 31536000) {
                                            $months = floor($diff / 2592000);
                                            $time_ago = $months > 1 ? $months . " months ago" : "Last month";
                                        } else {
                                            $years = floor($diff / 31536000);
                                            $time_ago = $years > 1 ? $years . " years ago" : "Last year";
                                        }
                                        ?>

                                        <small style="color: antiquewhite;">&nbsp;&nbsp;<?= $time_ago ?></small>

                                    </div>
                                    <form method="post" action="<?= ROOT_URL ?>edit-comment.php">
                                        <textarea style="width: 100%;" readonly id="com_content" name="com_content" class="main__comment"><?= "{$comment['content']}" ?></textarea>
                                        <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                        <?php
                                        $current_id = $_SESSION['user-id'];
                                        $current_user_query = "SELECT * FROM users WHERE id=$current_id";
                                        $current_user_result = mysqli_query($connection, $current_user_query);
                                        $current_user = mysqli_fetch_assoc($current_user_result);
                                        ?>
                                        <?php if ($current_user['is_admin'] || $current_user['id'] == $author['id'] || $current_user['id'] == $comment['author_id']) : ?>
                                            <div class="delete_edite">
                                                <?php if ($current_user['id'] == $comment['author_id']) : ?>
                                                    <button id="edit" name="submit" type="submit" class="edite_btn" style="font-family:Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;">Modifier <i class="uil uil-comment-edit"></i></button>
                                                <?php endif; ?>
                                                <a href="<?= ROOT_URL ?>delete-comment.php?id=<?= $comment['id'] ?>" onclick="return confirm('Are you sure?')" style="font-family:Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;" class="delete_btn">Supprimer <i class="uil uil-trash"></i></a>
                                            </div>
                                        <?php endif; ?>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile ?>
                <form action="<?= ROOT_URL ?>comment-logic.php" class="comment__bar-container" method="post">
                    <div>
                        <i class="uil uil-comments-alt"></i>
                        <input type="text" required name="comment" placeholder="Leave a comment">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <button type="submit" name="submit" class="btn"><i class="uil uil-comment-alt-lines"></i></button>
                    </div>
                </form>
            </div>
            <!--* End of Comments block -->
        </div>
    </div>

</section>

<!--=================== END OF Single Post ==================-->

<?php
include 'partials/footer.php'
?>
<script>
    const editButtons = document.querySelectorAll('.edite_btn');
    editButtons.forEach(button => {
        let clicked = false;
        button.addEventListener('click', () => {
            const commentBlock = button.closest('.singlecomment__block');
            const textarea = commentBlock.querySelector('.main__comment');
            const submitButton = commentBlock.querySelector('button[type="submit"]');
            if (!clicked) {
                textarea.removeAttribute('readonly');
                button.innerHTML = 'Valider <i class="uil uil-check"></i>';
                button.style.color = 'lightgreen';
                button.type = 'button';
                clicked = true;
            } else {
                button.type = 'submit';
                submitButton.click();
            }
        });
    });
</script>