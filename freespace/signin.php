<?php
require 'config/constants.php';
$username_email = $_SESSION['signin-data']['username_email'] ?? null;
$password = $_SESSION['signin-data']['password'] ?? null;

unset($_SESSION['signin-data']);

// Generate a random token for the form
$token = bin2hex(random_bytes(16));

// Store the token in the session
$_SESSION['csrf_token'] = $token;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home page</title>
    <!-- Style -->
    <link rel="stylesheet" href="<?= ROOT_URL ?>css/style.css">
    <!-- Iconscout CDN -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <!-- Google Font : Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>

    <section class="form__section">
        <div class="container form__section-container">
            <h2>Sign In</h2>
            <?php if (isset($_SESSION['signup-success'])) : ?>
                <div class="alert__message success">
                    <p>
                        <?=
                        $_SESSION['signup-success'];
                        unset($_SESSION['signup-success']);
                        ?>
                    </p>
                </div>
            <?php elseif (isset($_SESSION['signin'])) : ?>
                <div class="alert__message error">
                    <p>
                        <?=
                        $_SESSION['signin'];
                        unset($_SESSION['signin']);
                        ?>
                    </p>
                </div>
            <?php endif ?>
            <form action="<?= ROOT_URL ?>signin-logic.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= $token ?>">
                <input type="text" name="username_email" value="<?= $username_email ?>" placeholder="Username Or Email">
                <input type="password" name="password" value="<?= $password ?>" placeholder="Create Password">
                <div class="g-recaptcha" data-sitekey="6Ldy_xklAAAAAPvIqFKaf_cQfuGGljJIunUwRLl8"></div>
                <button type="submit" name="submit" class="btn">Sign In</button>
                <small>Don't have an account? <a href="<?= ROOT_URL ?>signup.php">Sign Up</a></small>
            </form>
        </div>
    </section>


    <script src="main.js"></script>
</body>

</html>