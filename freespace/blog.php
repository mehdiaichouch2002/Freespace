<?php
include 'partials/header.php';

// fetch posts from posts table
$query = "SELECT * FROM posts ORDER BY date_time DESC ";
$posts = mysqli_query($connection, $query)
?>

<section class="search__bar">
    <form action="<?= ROOT_URL ?>search.php" method="get" class="container search__bar-container">
        <div>
            <i class="uil uil-search"></i>
            <input type="search" name="search" placeholder="Search">
            <button type="submit" name="submit" class="btn">Go</button>
        </div>
    </form>
</section>

<!--=================== END OF Search ==================-->


<section class="posts">
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
    <div style="text-align: center;margin-bottom:3rem" class="container"><button id="load-more" class="btn">Load more...</button></div>
</section>

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