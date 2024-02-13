<?php
require 'partials/header.php';

if (isset($_GET['search']) && isset($_GET['submit'])) {
    $search = filter_var($_GET['search'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $query = "SELECT * FROM posts WHERE title LIKE '%$search%' OR body LIKE '%$search%' ORDER BY date_time DESC";
    $posts = mysqli_query($connection, $query);
} else {
    header('location: ' . ROOT_URL . 'blog.php');
    die();
}
?>


<?php if (mysqli_num_rows($posts) > 0) : ?>
    <div class="alert__message success" style="text-align: center;margin-top:5rem">
        <p>Search Result : <?= mysqli_num_rows($posts) ?></p>
    </div>
    <section class="posts" style="margin-top: 7rem">
        <div class="container posts__container">
            <?php while ($post = mysqli_fetch_assoc($posts)) : ?>
                <article class="post">
                    <div class="post__thumbnail">
                        <img src="./images/<?= $post['thumbnail'] ?>" alt="gestion des entreprises">
                    </div>
                    <div class="post__info">
                        <?php
                        // fetch category from categories table
                        $category_id = $post['category_id'];
                        $category_query = "SELECT * FROM categories WHERE id=$category_id";
                        $category_result = mysqli_query($connection, $category_query);
                        $category = mysqli_fetch_assoc($category_result);
                        ?>
                        <a href="<?= ROOT_URL ?>category-posts.php?id=<?= $post['category_id'] ?>" class="category__button"><?= $category['title'] ?></a>
                        <h3 class="post__title"><a href="<?= ROOT_URL ?>post.php?id=<?= $post['id'] ?>"><?= $post['title'] ?></a></h3>
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
        <div style="text-align: center;margin-bottom:3rem" class="container"><button id="load-more" class="btn">Load more...</button></div>
    </section>
<?php else : ?>
    <div class="alert__message error" style="text-align: center;margin-top:7rem">
        <p>No posts found for <b style="color: red;"><?= '" ' . $_GET['search'] . ' "' ?></b></p>
    </div>
<?php endif ?>

<!--=================== END OF Posts Section ==================-->

<?php require 'partials/footer.php'; ?>

<script>
    var loadMoreBtn = document.getElementById('load-more');
    var articles = document.querySelectorAll('.posts .post');
    var numArticlesToShow = 9;
    var numArticlesShown = 0;

    function showMoreArticles() {
        var i;
        for (i = numArticlesShown; i < numArticlesShown + numArticlesToShow; i++) {
            if (articles[i]) {
                articles[i].style.display = 'block';
            }
        }
        numArticlesShown += numArticlesToShow;
        if (numArticlesShown >= articles.length) {
            loadMoreBtn.style.display = 'none';
        }
    }

    showMoreArticles();

    loadMoreBtn.addEventListener('click', function() {
        showMoreArticles();
    });
</script>