<?php
include 'partials/header.php';

//fetch post from db if id isset
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $query = "SELECT * FROM posts WHERE category_id=$id ORDER BY date_time DESC";
    $posts = mysqli_query($connection, $query);
} else {
    header('location: ' . ROOT_URL . 'blog.php');
    die();
}
?>
<header class="category__title">

    <h2>
        <?php
        // fetch category from categories table
        $category_id = $id;
        $category_query = "SELECT * FROM categories WHERE id=$id";
        $category_result = mysqli_query($connection, $category_query);
        $category = mysqli_fetch_assoc($category_result);
        echo $category['title']
        ?>
    </h2>
</header>

<!--=================== END OF Category title ==================-->
<?php if (mysqli_num_rows($posts) > 0) : ?>
    <section class="posts">
        <div class="container posts__container">
            <?php while ($post = mysqli_fetch_assoc($posts)) : ?>
                <article class="post">
                    <div class="post__thumbnail">
                        <img src="./images/<?= $post['thumbnail'] ?>" alt="gestion des entreprises">
                    </div>
                    <div class="post__info">
                        <h3 class="post__title"><a href="<?= ROOT_URL ?>post.php?id=<?= $post['id'] ?>&id2=<?= $post['author_id'] ?>"><?= $post['title'] ?></a></h3>
                        <p class="post__body">
                            <?= substr($post['body'], 0, 150) ?>...
                        </p>
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
                    </div>
                </article>
            <?php endwhile ?>
        </div>
    </section>
<?php else : ?>
    <div class="alert__message error" style="text-align: center">
        <p>No posts found for this category</p>
    </div>
<?php endif ?>
<!--=================== END OF Posts Section ==================-->
<section class="category__buttons">
    <div class="container category__buttons-container">
        <?php
        $all_categories_query = "SELECT * FROM categories";
        $all_categories = mysqli_query($connection, $all_categories_query);
        ?>
        <?php while ($cat = mysqli_fetch_assoc($all_categories)) : ?>
            <a href="<?= ROOT_URL ?>category-posts.php?id=<?= $cat['id'] ?>" class="category__button"><?= $cat['title'] ?></a>
        <?php endwhile ?>
    </div>
</section>
<!--=================== END OF Category buttons ==================-->

<?php
include 'partials/footer.php'
?>