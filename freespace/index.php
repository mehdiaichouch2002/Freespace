<?php
include 'partials/header.php';

//fetch featured posts from db :
$featured_query = "SELECT * FROM posts WHERE is_featured=1";
$featured_result = mysqli_query($connection, $featured_query);
// fetch posts from posts table
$query = "SELECT * FROM posts ORDER BY date_time DESC";
$posts = mysqli_query($connection, $query)
?>

<!-- show featured posts if ther's any -->
<?php if (mysqli_num_rows($featured_result) >= 1) : ?>
    <div class="swiper mySwiper">
        <div class="swiper-wrapper">
            <?php while ($featured = mysqli_fetch_assoc($featured_result)) : ?>
                <div class="swiper-slide">
                    <section class="featured">
                        <div class="container featured__container">
                            <div class="post__thumbnail">
                                <img src="./images/<?= $featured['thumbnail'] ?>">
                            </div>
                            <div class="post__info">
                                <?php
                                // fetch category from categories table
                                $category_id = $featured['category_id'];
                                $category_query = "SELECT * FROM categories WHERE id=$category_id";
                                $category_result = mysqli_query($connection, $category_query);
                                $category = mysqli_fetch_assoc($category_result);
                                ?>
                                <a href="<?= ROOT_URL ?>category-posts.php?id=<?= $featured['category_id'] ?>" class="category__button"><?= $category['title'] ?></a>
                                <h2 class="post__title"><a href="<?= ROOT_URL ?>post.php?id=<?= $featured['id'] ?>&id2=<?= $featured['author_id'] ?>"><?= $featured['title'] ?></a></h2>
                                <p class="body"><?= substr($featured['body'], 0, 300) ?>...</p>
                                <div class="post__author">
                                    <?php
                                    $author_id = $featured['author_id'];
                                    $author_query = "SELECT * FROM users WHERE id=$author_id";
                                    $author_result = mysqli_query($connection, $author_query);
                                    $author = mysqli_fetch_assoc($author_result);
                                    ?>
                                    <div class="post__author-avatar">
                                        <img src="./images/<?= $author['avatar'] ?>">
                                    </div>
                                    <div class="post__author-info">
                                        <h5>By : <?= "{$author['firstname']}  {$author['lastname']}" ?></h5>
                                        <small><?= date("M d, Y - H:i", strtotime($featured['date_time'])) ?></small>
                                        <?php
                                        $featured_date = strtotime($featured['date_time']);
                                        $now = time();
                                        $seconds_diff = $now - $featured_date;

                                        if ($seconds_diff >= 31536000) { // More than 365 days
                                            $time_diff = floor($seconds_diff / 31536000);
                                            $unit = $time_diff > 1 ? "years" : "year";
                                        } elseif ($seconds_diff >= 2592000) { // More than 30 days
                                            $time_diff = floor($seconds_diff / 2592000);
                                            $unit = $time_diff > 1 ? "months" : "month";
                                        } elseif ($seconds_diff >= 86400) { // More than 1 day
                                            $time_diff = floor($seconds_diff / 86400);
                                            $unit = $time_diff > 1 ? "days" : "day";
                                        } else { // Less than 1 day
                                            $time_diff = floor($seconds_diff / 60);
                                            $unit = $time_diff > 1 ? "minutes" : "minute";
                                        }

                                        $time_ago = $time_diff . " " . $unit . " ago";
                                        ?>

                                        <small style="color: antiquewhite;">
                                            &nbsp;&nbsp;<?= $time_ago ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            <?php endwhile ?>
        </div>
        <div class="swiper-button-next next"></div>
        <div class="swiper-button-prev prev"></div>
        <div class="swiper-pagination"></div>
    </div>
<?php endif ?>

<!--=================== END OF Featured ==================-->
<section class="posts <?= mysqli_num_rows($featured_result) > 0 ? '' : 'section__extra-margin' ?>">
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
                                $date_diff = date_diff(date_create($post['date_time']), date_create());
                                if ($date_diff->days >= 365) {
                                    $year_diff = floor($date_diff->days / 365);
                                    echo $year_diff == 1 ? $year_diff . " year ago" : $year_diff . " years ago";
                                } elseif ($date_diff->days >= 30) {
                                    $month_diff = floor($date_diff->days / 30);
                                    echo $month_diff == 1 ? $month_diff . " month ago" : $month_diff . " months ago";
                                } else {
                                    echo $date_diff->days == 0 ? "Today" : $date_diff->days . " day ago";
                                }
                                ?>
                            </small>
                        </div>
                    </div>
                </div>
            </article>
        <?php endwhile ?>
    </div>
    <div id="spinner" class="container">
        <div class="loader">Loading</div>
    </div>

</section>

<!--=================== END OF Posts Section ==================-->
<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>

<script src="<?=ROOT_URL?>js/index.js"></script>
<script src="<?=ROOT_URL?>js/main.js"></script>
